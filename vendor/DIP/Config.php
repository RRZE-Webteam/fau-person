<?php

namespace RRZE\Lib\DIP;

defined('ABSPATH') || exit;

/*
 * Fixe und nicht aenderbare Plugin-Optionen
 * @return array 
 */
class Config
{

    public static function get_Config()
    {
        $apiKey = '';
        $dipOptions = get_option('_fau_person');

        if (!empty($dipOptions['constants_ApiKey'])) {
            $apiKey = $dipOptions['constants_ApiKey'];
        } elseif (is_multisite()) {
            $settingsOptions = get_site_option('rrze_settings');
            if (!empty($settingsOptions->plugins->dip_apiKey)) {
                $apiKey = $settingsOptions->plugins->dip_apiKey;
            }
        }

        $options = array(
            'transient_prefix' => 'dip_data_',
            'api_url' => 'https://api.fau.de/pub/v1/vz/persons/',
            'api_key' => $apiKey,
            'api_transient_expiration' => DAY_IN_SECONDS,
            'api_timeout' => HOUR_IN_SECONDS,
            'api_results_limit' => 100,
        );
        // für ergänzende Optionen aus anderen Plugins
        return apply_filters('dip_config', $options);
    }

    public static function get_fields()
    {
        return [
            'persons'   => [
                'department' => [
                    'keyname'   => ['memberOf', 0, 'memberOf', 'name'],
                    'default'   => '',
                    'type'        => 'string'
                ],
                // 'honorificPrefix' => [
                //     'keyname'   => 'title',
                //     'default'   => '',
                //     'type'        => 'string'
                // ],
                // 'honorificSuffix' => [
                //     'keyname'   => 'atitle',
                //     'default'   => '',
                //     'type'        => 'string'
                // ],
                'givenName' => [
                    'keyname'   => 'givenName',
                    'default'   => '',
                    'type'        => 'string'
                ],
                'familyName' => [
                    'keyname'   => 'familyName',
                    'default'   => '',
                    'type'        => 'string'
                ],
                // 'jobTitle' => [
                //     'keyname'   => 'lastname',
                //     'default'   => '',
                //     'type'        => 'work'
                // ],
                // 'lehrbeauftragter' => [
                //     'keyname'   => 'lehr',
                //     'default'   => '',
                //     'type'        => 'boolstring'
                // ],
                // 'visible' => [
                //     'keyname'   => 'visible',
                //     'default'   => '',
                //     'type'        => 'boolstring'
                // ],
                // 'public' => [
                //     'keyname'   => 'pub_visible',
                //     'default'   => '',
                //     'type'        => 'boolstring'
                // ],
                // 'restrict' => [
                //     'keyname'   => 'restrict',
                //     'default'   => '',
                //     'type'        => 'boolstring'
                // ],
                // 'idm_id' => [
                //     'keyname'   => 'idm_id',
                //     'default'   => '',
                //     'type'        => 'loginstring'
                // ],
                // 'univisid' => [
                //     'keyname'   => 'id',
                //     'default'   => '',
                //     'type'        => 'string'
                // ],
                // 'univiskey' => [
                //     'keyname'   => 'key',
                //     'default'   => '',
                //     'type'        => 'string'
                // ],
                // 'gender' => [
                //     'keyname'   => 'gender',
                //     'default'   => '',
                //     'type'        => 'genderstring'
                // ],
                // 'worksFor' => [
                //     'keyname'   => 'orgunit',
                //     'type'    => 'arraystring',
                // ],
                // 'worksFor_en' => [
                //     'keyname'   => 'orgunit_en',
                //     'type'    => 'arraystring',
                // ],
                // 'location' => [
                //     'type'    => 'array',
                //     'fields' => [
                //         'email' => [
                //             'keyname'   => 'email',
                //             'default'   => '',
                //             'type'        => 'email'
                //         ],
                //         'workLocation' => [
                //             'keyname'   => 'office',
                //             'default'   => '',
                //             'type'        => 'string'
                //         ],
                //         'faxNumber' => [
                //             'keyname'   => 'fax',
                //             'default'   => '',
                //             'type'        => 'faxnumber'
                //         ],
                //         'telephone' => [
                //             'keyname'   => 'tel',
                //             'default'   => '',
                //             'type'        => 'telnumber'
                //         ],
                //         'mobilePhone' => [
                //             'keyname'   => 'mobile',
                //             'default'   => '',
                //             'type'        => 'telnumber'
                //         ],
                //         'pgp' => [
                //             'keyname'   => 'pgp',
                //             'default'   => '',
                //             'type'        => 'string'
                //         ],
                //         'streetAddress' => [
                //             'keyname'   => 'street',
                //             'default'   => '',
                //             'type'        => 'string'
                //         ],
                //         'addressLocality' => [
                //             'keyname'   => 'ort',
                //             'default'   => '',
                //             'type'        => 'string'
                //         ],
                //         'url' => [
                //             'keyname'   => 'url',
                //             'default'   => '',
                //             'type'        => 'url'
                //         ],

                //     ]

                // ],
                // 'hoursAvailable_group'  => [
                //     'keyname'   => 'officehours',
                //     'default'   => '',
                //     'type'        => 'string'
                // ]
            ]
        ];
    }

    // public static function get_keys_fields($name = 'persons')
    // {
    //     $fields_univis = self::get_fields();
    //     $res = array();

    //     if (isset($name)) {
    //         foreach ($fields_univis[$name] as $key => $value) {
    //             if ($value['type'] === 'array') {
    //                 foreach ($fields_univis[$name][$key]['fields'] as $subkey => $subvalue) {
    //                     if (isset($subvalue['default'])) {
    //                         $res[$subkey] = $subvalue['default'];
    //                     }
    //                 }
    //             } elseif ($value['type'] == 'arraystring') {
    //                 $res[$key] = '';
    //             } else {
    //                 if (isset($value['default'])) {
    //                     $res[$key] = $value['default'];
    //                 }
    //             }
    //         }
    //     }
    //     return $res;
    // }

    public static function  fillMap(&$data, $name = 'persons')
    {

        // echo '<pre>';
        // var_dump($data);
        // exit;
        
        $map = self::get_fields();
        $map = (!empty($map[$name]) ? $map[$name] : false);

        if (empty($map)){
            return false;
        }

        $map_ret = array();

        foreach ($map as $k => $aVal) {
            $val = $aVal['keyname'];
            if (is_array($val)) {
                switch (count($val)) {
                    case 2:
                        if (isset($data[$val[0]][$val[1]])) {
                            $map_ret[$k] =  htmlentities($data[$val[0]][$val[1]]);
                        }
                        break;
                    case 3:
                        if (isset($data[$val[0]][$val[1]][$val[2]])) {
                            if (is_array($data[$val[0]][$val[1]][$val[2]])) {
                                $map_ret[$k] = htmlentities(implode(PHP_EOL, $data[$val[0]][$val[1]][$val[2]]));
                            } else {
                                $map_ret[$k] = htmlentities($data[$val[0]][$val[1]][$val[2]]);
                            }
                        }
                        break;
                    case 4:
                        if (isset($data[$val[0]][$val[1]][$val[2]][$val[3]])) {
                            $map_ret[$k] =  htmlentities($data[$val[0]][$val[1]][$val[2]][$val[3]]);
                        }
                        break;
                }
            } elseif (isset($data[$val])) {
                $map_ret[$k] =  $data[$val];
            }
        }
        return $map_ret;
    }
}
