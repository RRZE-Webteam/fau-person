<?php

namespace RRZE\Lib\DIP;

defined('ABSPATH') || exit;

if (!function_exists('__')) {
    function __($txt, $domain)
    {
        return $txt;
    }
}

class Data
{

    protected $api;
    protected $atts;
    protected $dipParam;

    public function __construct($atts)
    {
        $this->setAPI();
        $this->atts = $atts;
    }


    private function getKey(){
        $dipOptions = get_option('_fau_person');

        if (!empty($dipOptions['constants_ApiKey'])){
            return $dipOptions['constants_ApiKey'];
        }elseif(is_multisite()){
            $settingsOptions = get_site_option('rrze_settings');
            if (!empty($settingsOptions->plugins->dip_apiKey)){
                return $settingsOptions->plugins->dip_apiKey;
            }
        }else{
            return '';
        }
    }

    public function getResponse($sParam = NULL){
        $aRet = [
            'valid' => FALSE, 
            'content' => ''
        ];

        $aGetArgs = [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->getKey(),
                ]
            ];

        $apiResponse = wp_remote_get($this->api . $sParam, $aGetArgs);

        echo '<pre>';
        var_dump($apiResponse);
        exit;

        if ($apiResponse['response']['code'] != 200){
            $aRet = [
                'valid' => FALSE, 
                'content' => $apiResponse['response']['code'] . ': ' . $apiResponse['response']['message']
            ];    
        }else{
            $content = json_decode($apiResponse['body'], true);
            $aRet = [
                'valid' => TRUE, 
                'content' => $content
            ];
        }

        return $aRet;
    }


    private function setAPI()
    {
        $this->api = 'https://api.fau.de/ui/dir/pers?q=klein';
    }

    private static function log(string $method, string $logType = 'error', string $msg = '')
    {
        // uses plugin rrze-log
        $pre = __NAMESPACE__ . ' ' . $method . '() : ';
        if ($logType == 'DB') {
            global $wpdb;
            do_action('rrze.log.error', $pre . '$wpdb->last_result= ' . json_encode($wpdb->last_result) . '| $wpdb->last_query= ' . json_encode($wpdb->last_query . '| $wpdb->last_error= ' . json_encode($wpdb->last_error)));
        } else {
            do_action('rrze.log.' . $logType, __NAMESPACE__ . ' ' . $method . '() : ' . $msg);
        }
    }

    public function getData($dataType, $dipParam = null)
    {
        $this->dipParam = urlencode($dipParam);

        if (!$url) {
            return 'Set Campo Org ID in settings.';
        }
        $data = file_get_contents($url);
        if (!$data) {
            CampoAPI::log('getData', 'error', "no data returned using $url");
            return false;
        }
        // $data = json_decode($data, true);
        // $data = $this->mapIt($dataType, $data);
        // $data = $this->dict($data);
        // $data = $this->sortGroup($dataType, $data);
        return $data;
    }



    public static function correctPhone($phone)
    {
        if ((strpos($phone, '+49 9131 85-') !== 0) && (strpos($phone, '+49 911 5302-') !== 0)) {
            if (!preg_match('/\+49 [1-9][0-9]{1,4} [1-9][0-9]+/', $phone)) {
                $phone_data = preg_replace('/\D/', '', $phone);
                $vorwahl_erl = '+49 9131 85-';
                $vorwahl_nbg = '+49 911 5302-';

                switch (strlen($phone_data)) {
                    case '3':
                        $phone = $vorwahl_nbg . $phone_data;
                        break;

                    case '5':
                        if (strpos($phone_data, '06') === 0) {
                            $phone = $vorwahl_nbg . substr($phone_data, -3);
                            break;
                        }
                        $phone = $vorwahl_erl . $phone_data;
                        break;

                    case '7':
                        if (strpos($phone_data, '85') === 0 || strpos($phone_data, '06') === 0) {
                            $phone = $vorwahl_erl . substr($phone_data, -5);
                            break;
                        }

                        if (strpos($phone_data, '5302') === 0) {
                            $phone = $vorwahl_nbg . substr($phone_data, -3);
                            break;
                        }

                    // no break
                    default:
                        if (strpos($phone_data, '9115302') !== false) {
                            $durchwahl = explode('9115302', $phone_data);
                            if (strlen($durchwahl[1]) === 3 || strlen($durchwahl[1]) === 5) {
                                $phone = $vorwahl_nbg . $durchwahl[1];
                            }
                            break;
                        }

                        if (strpos($phone_data, '913185') !== false) {
                            $durchwahl = explode('913185', $phone_data);
                            if (strlen($durchwahl[1]) === 5) {
                                $phone = $vorwahl_erl . $durchwahl[1];
                            }
                            break;
                        }

                        if (strpos($phone_data, '09131') === 0 || strpos($phone_data, '499131') === 0) {
                            $durchwahl = explode('9131', $phone_data);
                            $phone = "+49 9131 " . $durchwahl[1];
                            break;
                        }

                        if (strpos($phone_data, '0911') === 0 || strpos($phone_data, '49911') === 0) {
                            $durchwahl = explode('911', $phone_data);
                            $phone = "+49 911 " . $durchwahl[1];
                            break;
                        }
                }
            }
        }
        return $phone;
    }

    public function getInt($str)
    {
        preg_match_all('/\d+/', $str, $matches);
        return implode('', $matches[0]);
    }

    public function formatCampo($txt)
    {
        $subs = array(
            '/^\-+\s+(.*)?/mi' => '<ul><li>$1</li></ul>', // list
            '/(<\/ul>\n(.*)<ul>*)+/' => '', // list
            '/\*{2}/m' => '/\*/', // **
            '/_{2}/m' => '/_/', // __
            '/\|(.*)\|/m' => '<i>$1</i>', // |itallic|
            '/_(.*)_/m' => '<sub>$1</sub>', // H_2_O
            '/\^(.*)\^/m' => '<sup>$1</sup>', // pi^2^
            '/\[([^\]]*)\]\s{0,1}((http|https|ftp|ftps):\/\/\S*)/mi' => '<a href="$2">$1</a>', // [link text] http...
            '/\[([^\]]*)\]\s{0,1}(mailto:)([^")\s<>]+)/mi' => '<a href="mailto:$3">$1</a>', // find [link text] mailto:email@address.tld but not <a href="mailto:email@address.tld">mailto:email@address.tld</a>
            '/\*(.*)\*/m' => '<strong>$1</strong>', // *bold*
        );

        $txt = preg_replace(array_keys($subs), array_values($subs), $txt);
        $txt = nl2br($txt);
        $txt = make_clickable($txt);
        return $txt;
    }

    private function dict(&$data)
    {
        $fields = [
            'title' => [
                "Dr." => __('Doctor', 'rrze-univis'),
                "Prof." => __('Professor', 'rrze-univis'),
                "Dipl." => __('Diploma', 'rrze-univis'),
                "Inf." => __('Computer Science', 'rrze-univis'),
                "Wi." => __('Business Informatics', 'rrze-univis'),
                "Ma." => __('Math', 'rrze-univis'),
                "Ing." => __('Engineering', 'rrze-univis'),
                "B.A." => __('Bachelor', 'rrze-univis'),
                "M.A." => __('Magister Artium', 'rrze-univis'),
                "phil." => __('Humanities', 'rrze-univis'),
                "pol." => __('Political Science', 'rrze-univis'),
                "nat." => __('Natural Science', 'rrze-univis'),
                "soc." => __('Social Science', 'rrze-univis'),
                "techn." => __('Technical Sciences', 'rrze-univis'),
                "vet.med." => __('Veterinary Medicine', 'rrze-univis'),
                "med.dent." => __('Dentistry', 'rrze-univis'),
                "h.c." => __('honorary', 'rrze-univis'),
                "med." => __('medicine', 'rrze-univis'),
                "jur." => __('law', 'rrze-univis'),
                "rer." => "",
            ],
            'lecture_type' => [
                "awa" => __('Instructions for scientific work (AWA)', 'rrze-univis'),
                "ku" => __('Course (KU)', 'rrze-univis'),
                "ak" => __('Advanced course (AK)', 'rrze-univis'),
                "ex" => __('Excursion (EX)', 'rrze-univis'),
                "gk" => __('Basic course (GK)', 'rrze-univis'),
                "sem" => __('Seminar (SEM)', 'rrze-univis'),
                "es" => __('Exam seminar (ES)', 'rrze-univis'),
                "ts" => __('Theory Seminar (TS)', 'rrze-univis'),
                "ag" => __('Working group (AG)', 'rrze-univis'),
                "mas" => __('Master seminar (MAS)', 'rrze-univis'),
                "gs" => __('Basic seminar (GS)', 'rrze-univis'),
                "us" => __('Training seminar (US)', 'rrze-univis'),
                "as" => __('Advanced seminar (AS)', 'rrze-univis'),
                "hs" => __('Main seminar (HS)', 'rrze-univis'),
                "re" => __('Repetitorium (RE)', 'rrze-univis'),
                "kk" => __('Exam course (KK)', 'rrze-univis'),
                "klv" => __('Clinical visit (KLV)', 'rrze-univis'),
                "ko" => __('Colloquium (KO)', 'rrze-univis'),
                "ks" => __('Combined seminar (KS)', 'rrze-univis'),
                "ek" => __('Introductory course (EK)', 'rrze-univis'),
                "ms" => __('Middle seminar (MS)', 'rrze-univis'),
                "os" => __('Upper seminar (OS)', 'rrze-univis'),
                "pr" => __('Internship (PR)', 'rrze-univis'),
                "prs" => __('Practice seminar (PRS)', 'rrze-univis'),
                "pjs" => __('Project Seminar (PJS)', 'rrze-univis'),
                "ps" => __('Pro seminar (PS)', 'rrze-univis'),
                "sl" => __('Other courses (SL)', 'rrze-univis'),
                "tut" => __('Tutorial (TUT)', 'rrze-univis'),
                "v-ue" => __('Lecture with exercise (V/UE)', 'rrze-univis'),
                "ue" => __('Exercise (UE)', 'rrze-univis'),
                "vorl" => __('Lecture (VORL)', 'rrze-univis'),
                "hvl" => __('Main Lecture (HVL)', 'rrze-univis'),
                "pf" => __('Examination (PF)', 'rrze-univis'),
                "gsz" => __('Committee meeting (GSZ)', 'rrze-univis'),
                "ppu" => __('Propaedeutic Exercise (PPU)', 'rrze-univis'),
                "his" => __('History of Languages Seminar (HIS)', 'rrze-univis'),
                "bsem" => __('Accompanying seminar (BSEM)', 'rrze-univis'),
                "kol" => __('College (KOL)', 'rrze-univis'),
                "mhs" => __('MS (HS, PO 2020) (MHS)', 'rrze-univis'),
                "pgmas" => __('PG Master Seminar (PGMAS)', 'rrze-univis'),
                "pms" => __('PS (MS, PO 2020) (PMS)', 'rrze-univis'),
            ],
            'repeat' => [
                "w1" => "",
                "w2" => __('Every other week', 'rrze-univis'),
                "w3" => __('Every third week', 'rrze-univis'),
                "w4" => __('Every fourth week', 'rrze-univis'),
                "w5" => "",
                "m1" => "",
                "s1" => __('single appointment on', 'rrze-univis'),
                "bd" => __('block event', 'rrze-univis'),
                '0' => __(' Su', 'rrze-univis'),
                '1' => __(' Mo', 'rrze-univis'),
                '2' => __(' Tue', 'rrze-univis'),
                '3' => __(' Wed', 'rrze-univis'),
                '4' => __(' Thu', 'rrze-univis'),
                '5' => __(' Fr', 'rrze-univis'),
                '6' => __(' Sa', 'rrze-univis'),
                '7' => __(' Su', 'rrze-univis'),
            ],
            'publication_type' => [
                "artmono" => __('Article in anthology', 'rrze-univis'),
                "arttagu" => __('Article in proceedings', 'rrze-univis'),
                "artzeit" => __('Article in magazine', 'rrze-univis'),
                "techrep" => __('Internal Report (Technical Report, Research Report)', 'rrze-univis'),
                "hschri" => __('University thesis (dissertation, habilitation thesis, diploma thesis etc.)', 'rrze-univis'),
                "dissvg" => __('Thesis (also published by the publisher)', 'rrze-univis'),
                "monogr" => __('Monograph', 'rrze-univis'),
                "tagband" => __('Conference volume (not published by the publisher)', 'rrze-univis'),
                "schutzr" => __('IPR', 'rrze-univis'),
                ],
            'hstype' => [
                "diss" => __('Dissertation', 'rrze-univis'),
                "dipl" => __('Diploma', 'rrze-univis'),
                "mag" => __('Master\'s thesis', 'rrze-univis'),
                "stud" => __('Study paper', 'rrze-univis'),
                "habil" => __('Habilitation thesis', 'rrze-univis'),
                "masth" => __('Master\'s thesis', 'rrze-univis'),
                "bacth" => __('Bachelor thesis', 'rrze-univis'),
                "intber" => __('Internal Report', 'rrze-univis'),
                "diskus" => __('Discussion paper', 'rrze-univis'),
                "discus" => __('Discussion paper', 'rrze-univis'),
                "forber" => __('Research report', 'rrze-univis'),
                "absber" => __('Final report', 'rrze-univis'),
                "patschri" => __('Patent specification', 'rrze-univis'),
                "offenleg" => __('Disclosure document', 'rrze-univis'),
                "patanmel" => __('Patent application', 'rrze-univis'),
                "gebrmust" => __('Utility model', 'rrze-univis'),
                ],
            'leclanguage' => [
                0 => __('Lecture\'s language German', 'rrze-univis'),
                "D" => __('Lecture\'s language German', 'rrze-univis'),
                "E" => __('Lecture\'s language English', 'rrze-univis'),
                ],
            'sws' => __(' SWS', 'rrze-univis'),
            'schein' => __('Certificate', 'rrze-univis'),
            'ects' => __('ECTS studies', 'rrze-univis'),
            'ects_cred' => __('ECTS credits: ', 'rrze-univis'),
            'beginners' => __('Suitable for beginners', 'rrze-univis'),
            'fruehstud' => __('Early study', 'rrze-univis'),
            'gast' => __('Allowed for guest students', 'rrze-univis'),
            'evaluation' => __('Evaluation', 'rrze-univis'),
            'locations' => '',
            'organizational' => '',
            ];

        foreach ($data as $nr => $row) {
            foreach ($fields as $field => $values) {
                if (isset($data[$nr][$field]) && ($field == 'locations')) {
                    foreach ($data[$nr]['locations'] as $l_nr => $location) {
                        if (!empty($location['tel'])) {
                            $data[$nr]['locations'][$l_nr]['tel'] = self::correctPhone($data[$nr]['locations'][$l_nr]['tel']);
                            $data[$nr]['locations'][$l_nr]['tel_call'] = '+' . self::getInt($data[$nr]['locations'][$l_nr]['tel']);
                        }
                        if (!empty($location['fax'])) {
                            $data[$nr]['locations'][$l_nr]['fax'] = self::correctPhone($data[$nr]['locations'][$l_nr]['fax']);
                        }
                        if (!empty($location['mobile'])) {
                            $data[$nr]['locations'][$l_nr]['mobile'] = self::correctPhone($data[$nr]['locations'][$l_nr]['mobile']);
                            $data[$nr]['locations'][$l_nr]['mobile_call'] = '+' . self::getInt($data[$nr]['locations'][$l_nr]['mobile']);
                        }
                    }
                } elseif ($field == 'repeat') {
                    if (isset($data[$nr]['courses'])) {
                        foreach ($data[$nr]['courses'] as $c_nr => $course) {
                            foreach ($course['term'] as $m_nr => $meeting) {
                                if (isset($data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat'])) {
                                    $data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat'] = str_replace(array_keys($values), array_values($values), $data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat']);
                                }
                            }
                        }
                    } elseif (isset($data[$nr]['officehours'])) {
                        foreach ($data[$nr]['officehours'] as $c_nr => $entry) {
                            if (isset($data[$nr]['officehours'][$c_nr]['repeat'])) {
                                $data[$nr]['officehours'][$c_nr]['repeat'] = trim(str_replace(array_keys($values), array_values($values), $data[$nr]['officehours'][$c_nr]['repeat']));
                            }
                        }
                    }
                } elseif ($field == 'organizational') {
                    if (isset($data[$nr][$field])) {
                        $data[$nr][$field] = self::formatCampo($data[$nr][$field]);
                    }
                } elseif (isset($data[$nr][$field])) {
                    if (in_array($field, ['title'])) {
                        // multi replace
                        $data[$nr][$field . '_long'] = str_replace(array_keys($values), array_values($values), $data[$nr][$field]);
                    } else {
                        if (!is_array($values)) {
                            if ($field == 'sws') {
                                $data[$nr][$field] .= $values;
                            } elseif ($field == 'ects_cred') {
                                $data[$nr][$field] = $values . $data[$nr][$field];
                            } else {
                                $data[$nr][$field] = $values;
                            }
                        } else {
                            if (isset($row[$field]) && isset($values[$row[$field]])) {
                                $data[$nr][$field . '_long'] = $values[$row[$field]];
                                if ($field == 'lecture_type') {
                                    $data[$nr][$field . '_short'] = trim(substr($values[$row[$field]], 0, strpos($values[$row[$field]], '(')));
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

}
