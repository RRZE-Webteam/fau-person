<?php

namespace RRZE\Lib\UnivIS;

/*
 * UnivIS-Data API- und Cache-Funktionen
 */

use RRZE\Lib\UnivIS\Config;
use RRZE\Lib\UnivIS\Sanitizer;

add_action('univis_data_async_task', array('RRZE\Lib\UnivIS\Data', 'async_task'));

add_action('save_post', function () {
    wp_schedule_single_event(time(), 'univis_data_async_task');
});

class Data
{

    const transient_prefix = 'univis_data_';
    protected static $transient_expiration = DAY_IN_SECONDS;
    protected static $timeout = HOUR_IN_SECONDS;
    protected static $results_limit = 100;



    public static function get_person($id)
    {

        if (!Sanitizer::is_valid_id($id)) {
            return false;
        }

        if (($data = self::get_data_cache($id)) !== false) {
            return $data;
        }

        if (($data = self::get_remote_data_by('id', $id)) === false) {
            return false;
        }

        self::set_data_cache($data[0]);

        return $data[0];
    }

    private static function get_data_cache($id)
    {
        if (!Sanitizer::is_valid_id($id)) {
            return false;
        }

        return get_transient(self::transient_prefix . $id);
    }

    private static function set_data_cache($data)
    {
        if (!isset($data['id'])) {
            return false;
        }

        $id = $data['id'];
        if (!Sanitizer::is_valid_id($id)) {
            return false;
        }

        return set_transient(self::transient_prefix . $id, $data, self::$transient_expiration);
    }

    public static function async_task()
    {
        $timestamp = time() + self::$transient_expiration - self::$timeout;

        if ($results = self::get_results_by_timeout($timestamp, self::$results_limit)) {

            foreach ($results as $row) {

                $id = ltrim($row->option_name, '_transient_timeout_' . self::transient_prefix);

                if (($data = self::get_remote_data_by('id', $id)) !== false) {
                    self::set_data_cache($data[0]);
                } else {
                    self::delete_data_cache($id);
                }
            }
        }
    }

    private static function get_results_by_timeout($timestamp, $limit)
    {
        global $wpdb;

        $sql = "SELECT * FROM $wpdb->options WHERE option_name LIKE %s AND option_value < %d ORDER BY option_value ASC LIMIT %d";

        return $wpdb->get_results($wpdb->prepare($sql, $wpdb->esc_like('_transient_timeout_' . self::transient_prefix) . '%', $timestamp, $limit));
    }

    public static function delete_data_cache($id)
    {
        if (!Sanitizer::is_valid_id($id)) {
            return false;
        }

        return delete_transient(self::transient_prefix . $id);
    }

    public static function search_by($field = '', $value = '')
    {
        if ('id' == $field && !Sanitizer::is_valid_id($value)) {
            return false;
        } else {
            $value = trim($value);
        }

        if (!$value) {
            return false;
        } elseif (strlen($value) < 2) {
            return false;
        }

        if (!$data = self::get_remote_data_by($field, $value)) {
            return false;
        }

        return $data;
    }

    public static function search_by_fullname($firstname = '', $lastname = '')
    {
        $firstname = trim($firstname);
        $lastname = trim($lastname);

        if (!$lastname || !$firstname) {
            return false;
        } elseif ((strlen($lastname) < 2) && (strlen($firstname) < 2)) {
            return false;
        }

        if (!$data = self::get_remote_data_by_fullname($firstname, $lastname)) {
            return false;
        }

        return $data;
    }

    private static function get_remote_data_by($field, $value)
    {
        $config = Config::get_Config();
        $apiurl = $config['api_url'];

        switch ($field) {
            case 'id':
                $url = sprintf('%1$s?search=persons&id=%2$d&show=xml', $apiurl, $value);
                break;
            case 'firstname':
                $url = sprintf('%1$s?search=persons&firstname=%2$s&show=xml', $apiurl, urlencode($value));
                break;
            case 'lastname':
                $url = sprintf('%1$s?search=persons&name=%2$s&show=xml', $apiurl, urlencode($value));
                break;
            default:
                return false;
        }

        if (!fopen($url, "r")) {
            return false;
        }

        if (!$persArray = self::xml2array($url)) {
            return false;
        }

        $data = $persArray['Person'];

        if (count($data) == 0) {
            return false;
        }

        return $data;
    }

    private static function get_remote_data_by_fullname($firstname, $lastname)
    {
        $config = Config::get_Config();
        $apiurl = $config['api_url'];
        $url = sprintf('%1$s?search=persons&firstname=%2$s&name=%3$s&show=xml', $apiurl, urlencode($firstname), urlencode($lastname));

        if (!fopen($url, "r")) {
            return false;
        }

        if (!$persArray = self::xml2array($url)) {
            return null;
        }

        $data = $persArray['Person'];

        if (count($data) == 0) {
            return null;
        }

        return $data;
    }

    private static function xml2array($url)
    {
        $sxi = new \SimpleXMLIterator($url, null, true);
        return self::sxi2array($sxi);
    }

    private static function sxi2array($sxi)
    {
        $a = array();

        for ($sxi->rewind(); $sxi->valid(); $sxi->next()) {

            if (!array_key_exists($sxi->key(), $a)) {
                $a[$sxi->key()] = array();
            }

            if ($sxi->hasChildren()) {
                $a[$sxi->key()][] = self::sxi2array($sxi->current());
            } elseif ($sxi->key() === 'orgunit') {
                $a[$sxi->key()][] = strval($sxi->current());
            } elseif ($sxi->key() === 'orgunit_en') {
                $a[$sxi->key()][] = strval($sxi->current());
            } else {
                $a[$sxi->key()] = strval($sxi->current());

                if ($sxi->UnivISRef) {
                    $attributes = (array) $sxi->UnivISRef->attributes();
                    $a[$sxi->key()][] = $attributes["@attributes"];
                }
            }

            if ($sxi->attributes()) {
                $attributes = (array) $sxi->attributes();
                $a["@attributes"] = $attributes["@attributes"];
            }
        }
        return $a;
    }



    public static function get_univisdata($id = 0, $firstname = '', $lastname = '')
    {
        if (!$id && !$firstname && !$lastname) {
            return array();
        }

        if ($id) {
            $result = self::get_person($id);
        } elseif ($firstname && $lastname) {
            $result = self::search_by_fullname($firstname, $lastname);
        } elseif ($firstname) {
            $result = self::search_by('firstname', $firstname);
        } elseif ($lastname) {
            $result = self::search_by('lastname', $lastname);
        } else {
            $result = array();
        }

        return (array) $result;
    }

    //$id = ID des Personeneintrags, 
    //$person = Array mit Personendaten, 
    //$fau_person_var = Bezeichnung Personenplugin, 
    //$univis_var = Bezeichnung UnivIS, 
    //$defaults = Default-Wert 1 für Ausgabe der hinterlegten Werte im Personeneingabeformular als HTML-Hinweis
    public static function sync_univis($id, $person, $fau_person_var, $univis_var, $defaults)
    {
        //wird benötigt, falls jeder einzelne Wert abgefragt werden soll
        //if( !empty( $person[$univis_var] ) && get_post_meta($id, 'fau_person_'.$fau_person_var_sync', true) ) {
        $univisoverwrite = get_post_meta($id, 'fau_person_univis_sync', true);

        if ($defaults) {
            if (!empty($person[$univis_var])) {
                $val = '<p class="cmb2-metabox-description">' . __('Inhalt aus UnivIS:', 'fau-person') . ' <code>' . $person[$univis_var] . '</code>';
                if ($univisoverwrite) {
                    $val .= '<br><strong>' . __('Dieser Inhalt überschreibt den manuellen Eintrag in der Ausgabe.', 'fau-person') . '</strong>';
                }
                $val .= '</p>';
            } else {
                $val = '<p class="cmb2-metabox-description">' . __('In UnivIS ist hierfür kein Wert hinterlegt.', 'fau-person') . '</p>';
            }
        } else {
            if ($univisoverwrite) {
                // Werte aus UnivIS haben Prio

                if (!empty($person[$univis_var])) {
                    $val = $person[$univis_var];
                } else {
                    $val = get_post_meta($id, 'fau_person_' . $fau_person_var, true);
                }
            } else {
                // Werte aus der Post Meta haben Prio
                $val = get_post_meta($id, 'fau_person_' . $fau_person_var, true);
                if (empty($val) && (!empty($person[$univis_var]))) {
                    $val = $person[$univis_var];
                }
            }
        }

        return $val;
    }



    //public static function officehours_repeat( $officehours ) {
    public static function officehours_repeat($repeat, $repeat_submode, $starttime, $endtime, $office, $comment)
    {
        $date = array();

        if (!$repeat_submode) {
            $repeat = strtok($repeat, ' ');
            $repeat_submode = strtok(' ');
            $repeat_submode = explode(',', $repeat_submode);
        }

        if (($repeat) && ($repeat !== '-')) {
            $dict = array(
                'd1' => __('Täglich', 'fau-person'),
                'w1' => __('Jede Woche', 'fau-person'),
                'w2' => __('Alle zwei Wochen', 'fau-person'),
            );

            if (array_key_exists($repeat, $dict))
                array_push($date, $dict[$repeat]);

            if (is_array($repeat_submode) && !empty($repeat_submode[0])) {
                $days_short = array(
                    1 => __('<abbr title="Montag">Mo</abbr>', 'fau-person'),
                    2 => __('<abbr title="Dienstag">Di</abbr>', 'fau-person'),
                    3 => __('<abbr title="Mittwoch">Mi</abbr>', 'fau-person'),
                    4 => __('<abbr title="Donnerstag">Do</abbr>', 'fau-person'),
                    5 => __('<abbr title="Freitag">Fr</abbr>', 'fau-person'),
                    6 => __('<abbr title="Samstag">Sa</abbr>', 'fau-person'),
                    7 => __('<abbr title="Sonntag">So</abbr>', 'fau-person')
                );

                $days_long = array(
                    1 => __('Montag', 'fau-person'),
                    2 => __('Dienstag', 'fau-person'),
                    3 => __('Mittwoch', 'fau-person'),
                    4 => __('Donnerstag', 'fau-person'),
                    5 => __('Freitag', 'fau-person'),
                    6 => __('Samstag', 'fau-person'),
                    7 => __('Sonntag', 'fau-person')
                );
                foreach ($repeat_submode as $value) {
                    if (isset($days_short[$value])) {
                        $days_short[$value] = $days_short[$value] . ',';
                        array_push($date, $days_short[$value]);
                    }
                }
            }
        }
        if ($starttime) {
            $time = Sanitizer::convert_time($starttime);
            if ($endtime) {
                $time .= ' - ' . Sanitizer::convert_time($endtime);
            }
            $time = $time . ',';
            array_push($date, $time);
        }

        if ($office) {
            $office = __('Raum', 'fau-person') . ' ' . $office . ',';
            array_push($date, $office);
        }

        if ($comment !== 0) {
            array_push($date, $comment);
        }

        $officehours = implode(' ', $date);

        return $officehours;
    }
}
