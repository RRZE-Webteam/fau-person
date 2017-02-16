<?php

//callback-Funktion für Telefonnummer-Admin-Notice ist nötig

class sync_helper {
    //gibt die Werte der Person an, Inhalte abhängig von UnivIS, Übergabewerte: ID der Person, UnivIS-ID der Person, Default-Wert 1 für Ausgabe der hinterlegten Werte im Personeneingabeformular, $ignore_connection=1 wenn die verknüpften Kontakte einer Person ignoriert werden sollen (z.B. wenn die Person selbst schon eine verknüpfte Kontaktperson ist)
    public static function get_fields( $id, $univis_id, $defaults, $ignore_connection=0 ) {
        $univis_sync = 0;
        $person = array();
        if( $univis_id && class_exists( 'Univis_Data' ) ) {
            $person = self::get_univisdata( $univis_id );
            $univis_sync = 1;
        } 
        $fields = array();
        // Ab hier Definition aller Feldzuordnungen, $key ist Name der Metaboxen, $value ist Name in UnivIS
        $fields_univis = array(
            'department' => 'orgname',
            'honorificPrefix' => 'title',
            'honorificSuffix' => 'atitle',
            'givenName' => 'firstname',
            'familyName' => 'lastname',
            'jobTitle' => 'work',            
        );
        $fields_univis_location = array(
            'telephone' => 'tel',
            'faxNumber' => 'fax',
            'email' => 'email',
            'url' => 'url',
            'streetAddress' => 'street',
            'addressLocality' => 'ort', 
            'workLocation' => 'office', 
        );
        $fields_univis_officehours = array(
            'hoursAvailable_group' => 'officehours',
        );
        // Die Detailfelder zu den Sprechzeiten
        $subfields_univis_officehours = array(
            /* von der UnivIS-Doku:
             * repeat mode is encoded in a string
             * syntax: <modechar><numbers><space><args>                  
             * mode  description                  
             * d     daily                  
             * w     weekly
             * m     monthly                  
             * y     yearly                 
             * b     block
             * numbers: number of skips between repeats
             * example:  "d2":      every second day
             * weekly and monthly have additional arguments:  
             * weekly: argument is comma-separated list of weekdays where event is repeated                  
             * example:  "w3 1,2":  every third week on Monday and Tuesday                  
             * also possible: „we“ and „wo"
             * e = even calender week                  
             * o = odd calender week                  
             * monthly: argument has syntax "<submodechar><numbers>"                 
             * submode description                  
             * d       monthly by date                  
             * w       monthly by week                  
             * numbers: monthly by date: number of day (1-31)                  
             * monthly by week: number of week (1-5,e,o))                  
             * special case: 5 = last week of month                
             * examples:  "m1 d23": on the 23rd day of every month
             * "m2 w5":  in the last week of every second month
             * Laut UnivIS-Live-Daten werden für die Sprechzeiten aber nur wöchentlich an verschiedenen Tagen, 2-wöchentlich und täglich verwendet. Sollte noch was anderes benötigt werden, muss nachprogrammiert werden.
             */
            'comment' => 'comment',
            'endtime' => 'endtime',
            'repeat' => 'repeat',
            //'repeat_mode' => 'repeat_mode',
            'repeat_submode' => '',
            'office' => 'office', 
            'starttime' => 'starttime',
        );
        $fields_univis_orgunits = array(
            'worksFor' => 'orgunit',            
        );
        $fields_fauperson = array(
            'contactPoint' => '',
            'typ' => '',
            'alternateName' => '',
            'addressCountry' => '',
            'pubs' => '',
            'link' => '',
            'hoursAvailable' => '',
            'description' => '',
            'mobilePhone' => '',
        );
        $fields_exception = array(
            'postalCode' => '',
        );            
        $fields_connection = array(
            'connection_text' => '',
            'connection_only' => '',
            'connection_options' => array(),
            'connection_honorificPrefix' => 'honorificPrefix',
            'connection_givenName' => 'givenName',
            'connection_familyName' => 'familyName',
            'connection_honorificSuffix' => 'honorificSuffix',
            'connection_alternateName' => 'alternateName',
            'connection_streetAddress' => 'streetAddress',
            'connection_postalCode' => 'postalCode',
            'connection_addressLocality' => 'addressLocality',
            'connection_addressCountry' => 'addressCountry',  
            'connection_workLocation' => 'workLocation',
            'connection_telephone' => 'telephone',
            'connection_faxNumber' => 'faxNumber',         
            'connection_email' => 'email',
            'connection_hoursAvailable' => 'hoursAvailable',
            'connection_nr' => 'nr',
        );
        foreach( $fields_univis as $key => $value ) {
            if( $univis_sync && array_key_exists( $value, $person ) ) {
                if( $value == 'orgname' ) {
                    $language = get_locale();
                    if( strpos( $language, 'en_' ) === 0 && array_key_exists( 'orgname_en', $person ) ) {
                        $value = 'orgname_en';
                    } else {
                        $value = 'orgname';                   
                    }
                }
                $value = self::sync_univis( $id, $person, $key, $value, $defaults ); 
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);     
                } else {
                    $value = get_post_meta($id, 'fau_person_'.$key, true);                          
                }
            }
            $fields[$key] = $value;
        }
        foreach( $fields_univis_location as $key => $value ) {
            if( $univis_sync && array_key_exists( 'locations', $person ) && array_key_exists( 'location', $person['locations'][0] ) ) {
                $person_location = $person['locations'][0]['location'][0];
                if(($key == 'telephone' || $key == 'faxNumber') && !$defaults) {
                    $phone_number = self::sync_univis( $id, $person_location, $key, $value, $defaults );
                    switch ( get_post_meta($id, 'fau_person_telephone_select', true) ) {
                        case 'erl':
                            $value = self::correct_phone_number($phone_number, 'erl');
                            break;
                        case 'nbg':
                            $value = self::correct_phone_number($phone_number, 'nbg');                        
                            break;
                        default:
                            $value = self::correct_phone_number($phone_number, 'standard');                        
                            break;
                    }                    
                } else {
                    $value = self::sync_univis( $id, $person_location, $key, $value, $defaults );
                }
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);
                } else {
                    if($key == 'telephone' || $key == 'faxNumber') {
                        $phone_number = get_post_meta($id, 'fau_person_'.$key, true);
                        switch ( get_post_meta($id, 'fau_person_telephone_select', true) ) {
                        case 'erl':
                            $value = self::correct_phone_number($phone_number, 'erl');
                            break;
                        case 'nbg':
                            $value = self::correct_phone_number($phone_number, 'nbg');                        
                            break;
                        default:
                            $value = self::correct_phone_number($phone_number, 'standard');  
                            break;
                        }
                    } else {                    
                        $value = get_post_meta($id, 'fau_person_'.$key, true);
                    }
                }
            }
            //add_action( 'admin_notices', array( 'FAU_Person', 'admin_notice_phone_number' ) );
            $fields[$key] = $value;
        }

        foreach( $fields_univis_officehours as $key => $value ) {
            // ist eine UnivIS-ID vorhanden?        
            switch ( $univis_sync ) {
                case true:
                    if ( array_key_exists( 'officehours', $person ) && array_key_exists( 'officehour', $person['officehours'][0] ) ) { // sind in UnivIS überhaupt Sprechzeiten hinterlegt?
                        if( get_post_meta($id, 'fau_person_univis_sync', true) || $defaults ) { // ist der Haken zur Synchronisation da bzw. werden die UnivIS-Werte für das Backend abgefragt
                            $person_officehours = $person['officehours'][0]['officehour'];   
                            $officehours = array();
                            foreach ($person_officehours as $num => $num_val) {
                                $repeat = isset( $person_officehours[$num]['repeat'] ) ? $person_officehours[$num]['repeat'] : 0;
                                $repeat_submode = isset( $person_officehours[$num]['repeat_submode'] ) ? $person_officehours[$num]['repeat_submode'] : 0;
                                $starttime = isset( $person_officehours[$num]['starttime'] ) ? $person_officehours[$num]['starttime'] : 0;
                                $endtime = isset( $person_officehours[$num]['endtime'] ) ? $person_officehours[$num]['endtime'] : 0;
                                $office = isset( $person_officehours[$num]['office'] ) ? $person_officehours[$num]['office'] : 0;
                                $comment = isset( $person_officehours[$num]['comment'] ) ? $person_officehours[$num]['comment'] : 0;
                                $officehour = self::officehours_repeat($repeat, $repeat_submode, $starttime, $endtime, $office, $comment);                    
                                array_push($officehours, $officehour);
                            }
                            if ( $defaults ) {
                                $officehours = implode($officehours, '</p></li><li><p class="cmb_metabox_description">');
                                $officehours = sprintf(__('<p class="cmb_metabox_description">[Aus UnivIS angezeigter Wert: </p><ul><li><p class="cmb_metabox_description">%s</p></li></ul><p class="cmb_metabox_description">]</p>', FAU_PERSON_TEXTDOMAIN), $officehours); 
                            }
                            break;
                        }  
                    } else { // in UnivIS stehen keine Sprechzeiten
                        $officehours = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);                                                   
                        break;
                    }                                                              
                default:  // keine UnivIS-ID da bzw. kein Haken bei Datenanzeige aus UnivIS => die Feldinhalte werden ausgegeben
                    $person_officehours = get_post_meta($id, 'fau_person_hoursAvailable_group', true);
                    $officehours = array();
                    if( !empty( $person_officehours ) ) {
                        foreach ( $person_officehours as $num => $num_val ) {                            
                            $repeat = isset( $person_officehours[$num]['repeat'] ) ? $person_officehours[$num]['repeat'] : 0;
                            $repeat_submode = isset( $person_officehours[$num]['repeat_submode'] ) ? $person_officehours[$num]['repeat_submode'] : 0;
                            $starttime = isset( $person_officehours[$num]['starttime'] ) ? $person_officehours[$num]['starttime'] : 0;
                            $endtime = isset( $person_officehours[$num]['endtime'] ) ? $person_officehours[$num]['endtime'] : 0;
                            $office = isset( $person_officehours[$num]['office'] ) ? $person_officehours[$num]['office'] : 0;
                            $comment = isset( $person_officehours[$num]['comment'] ) ? $person_officehours[$num]['comment'] : 0;
                            $officehour = self::officehours_repeat($repeat, $repeat_submode, $starttime, $endtime, $office, $comment);
                            array_push($officehours, $officehour);                                
                        }
                    }
            }
            $fields[$key] = $officehours;
            
        }       
        
        foreach( $fields_univis_orgunits as $key => $value ) {
            $language = get_locale();
            if( strpos( $language, 'en_' ) === 0 && array_key_exists( 'orgunit_ens', $person ) ) {
                $orgunit = 'orgunit_en';
                $orgunits = 'orgunit_ens';
            } else {
                $orgunit = 'orgunit';
                $orgunits = 'orgunits';
            }
            if( array_key_exists( $orgunits, $person ) ) {
                $person_orgunits = $person[$orgunits][0][$orgunit];
                $i = count($person_orgunits);
                if($i>1) {
                    $i = count($person_orgunits)-2;
                } 
                $value = self::sync_univis( $id, $person_orgunits, $key, $i, $defaults );  
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);
                } else {
                    $value = get_post_meta($id, 'fau_person_'.$key, true); 
                }
            }
            $fields[$key] = $value;
        }        
        foreach( $fields_fauperson as $key => $value ) {
            $value = get_post_meta($id, 'fau_person_'.$key, true);
            $fields[$key] = $value;            
        }
        foreach( $fields_exception as $key => $value ) {
            if( $key == 'postalCode' ) {
                if( get_post_meta($id, 'fau_person_univis_sync', true) && array_key_exists( 'locations', $person ) && array_key_exists( 'location', $person['locations'][0] ) && array_key_exists('ort', $person['locations'][0]['location'][0]) ) {
                    $value = '';
                } else {
                    $value = get_post_meta($id, 'fau_person_'.$key, true); 
                }
            }
            $fields[$key] = $value;  
        }
        if( !$ignore_connection ) 
            $connections = get_post_meta($id, 'fau_person_connection_id', true);
        if( !empty( $connections ) ) {    
            $connection = array();
            foreach( $connections as $ckey => $cvalue ) {
                $connection_fields[$ckey] = sync_helper::get_fields($cvalue, get_post_meta($cvalue, 'fau_person_univis_id', true), 0, 1);
                $connection_fields[$ckey]['nr'] = $cvalue;
            }
            foreach ($connection_fields as $key => $value) {    
                foreach( $fields_connection as $fckey => $fcvalue ) {
                    if( $fckey == 'connection_text' || $fckey == 'connection_only' || $fckey == 'connection_options' ) {
                        $value = get_post_meta($id, 'fau_person_'.$fckey, true);
                        $fields[$fckey] = $value;                   
                    } else {
                        $value = $connection_fields[$key][$fcvalue];
                        $connection[$key][$fcvalue] = $value; 
                    }
                }                    
            }
            $fields['connections'] = $connection;
        }

        if( !$defaults && !get_post_meta($id, 'fau_person_univis_sync', true) ) {
            $fields_standort = standort_sync_helper::get_fields( $id, get_post_meta($id, 'fau_person_standort_id', true), 0 );
            $fields = array_merge( $fields, $fields_standort );
        }
        return $fields;
    }

    public static function get_univisdata($id = 0, $firstname = '', $lastname = '' ) {    

        if( !$id && !$firstname && !$lastname ) {
		return array();
	}
        
        if($id) {
        	$result = UnivIS_Data::get_person($id);
        } elseif( $firstname && $lastname ) {
        	$result = UnivIS_Data::search_by_fullname($firstname, $lastname);
        } elseif( $firstname ) {
        	$result = UnivIS_Data::search_by('firstname', $firstname);
        } elseif( $lastname ) {
        	$result = UnivIS_Data::search_by('lastname', $lastname);
        } else {
         	$result = array();
        } 
      
        return (array) $result;
    }

    //$id = ID des Personeneintrags, $person = Array mit Personendaten, $fau_person_var = Bezeichnung Personenplugin, $univis_var = Bezeichnung UnivIS, $defaults = Default-Wert 1 für Ausgabe der hinterlegten Werte im Personeneingabeformular
    public static function sync_univis( $id, $person, $fau_person_var, $univis_var, $defaults ) {   
        //wird benötigt, falls jeder einzelne Wert abgefragt werden soll
        //if( !empty( $person[$univis_var] ) && get_post_meta($id, 'fau_person_'.$fau_person_var_sync', true) ) {
        if( $defaults ) {
            if( !empty( $person[$univis_var] ) ) {
                $val = sprintf(__('<p class="cmb_metabox_description">[Aus UnivIS angezeigter Wert: %s]</p>', FAU_PERSON_TEXTDOMAIN), $person[$univis_var]);
            } else {
                $val = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);
            }
        } else {
            if( !empty( $person[$univis_var] ) && get_post_meta($id, 'fau_person_univis_sync', true) ) {
                $val = $person[$univis_var];             
            } else {
                $val = get_post_meta($id, 'fau_person_'.$fau_person_var, true);
            }
        }
        return $val;        
    }
    
    public static function correct_phone_number( $phone_number, $location ) {
        if( ( strpos( $phone_number, '+49 9131 85-' ) !== 0 ) && ( strpos( $phone_number, '+49 911 5302-' ) !== 0 ) ) {
            if( !preg_match( '/\+49 [1-9][0-9]{1,4} [1-9][0-9]+/', $phone_number ) ) {
                $phone_data = preg_replace( '/\D/', '', $phone_number );
                $vorwahl_erl = '+49 9131 85-';
                $vorwahl_nbg = '+49 911 5302-';
                switch( $location ) {                 
                    case 'erl':
                        if( strlen( $phone_data ) == 5 ) {
                            $phone_number = $vorwahl_erl . $phone_data;
                        } elseif (strlen( $phone_data ) == 7 && strpos( $phone_data, '85') === 0 ) {
                            $phone_number = $vorwahl_erl . substr(  $phone_data, -5);
                        } elseif ( strlen( $phone_data ) == 12 && strpos( $phone_data, '913185') !== FALSE ) {
                            $phone_number = $vorwahl_erl . substr( $phone_data, -5);
                        } 
                        break;
                    case 'nbg':
                        if( strlen( $phone_data ) == 3 ) {
                            $phone_number = $vorwahl_nbg . $phone_data;
                        } elseif ( strlen( $phone_data ) == 7 && strpos( $phone_data, '5302') === 0 ) {
                            $phone_number = $vorwahl_nbg . substr( $phone_data, -3);
                        } elseif ( strlen( $phone_data ) == 11 && strpos( $phone_data, '9115302') !== FALSE ) {
                            $phone_number = $vorwahl_nbg . substr( $phone_data, -3);
                        } 
                        break;
                    case 'standard':
                        switch( strlen( $phone_data ) ) {
                            case '3':
                                $phone_number = $vorwahl_nbg . $phone_data;
                                break;
                            case '5':
                                if( strpos( $phone_data, '06' ) === 0 ) {
                                    $phone_number = $vorwahl_nbg . substr( $phone_data, -3 );
                                    break;
                                }                                 
                                $phone_number = $vorwahl_erl . $phone_data;
                                break;
                            case '7':
                                if( strpos( $phone_data, '85' ) === 0 || strpos( $phone_data, '06' ) === 0 )  {
                                    $phone_number = $vorwahl_erl . substr( $phone_data, -5 );
                                    break;
                                }
                                if( strpos( $phone_data, '5302' ) === 0 ) {
                                    $phone_number = $vorwahl_nbg . substr( $phone_data, -3 );
                                    break;
                                } 
                            default:
                                if( strpos( $phone_data, '9115302' ) !== FALSE ) {
                                    $durchwahl = explode( '9115302', $phone_data );
                                    if( strlen( $durchwahl[1] ) ===  3 ) {
                                        $phone_number = $vorwahl_nbg . substr( $phone_data, -3 );
                                    }
                                    break;
                                }  
                                if( strpos( $phone_data, '913185' ) !== FALSE )  {
                                    $durchwahl = explode( '913185', $phone_data );
                                    if( strlen( $durchwahl[1] ) ===  5 ) {
                                        $phone_number = $vorwahl_erl . substr( $phone_data, -5 );
                                    }
                                    break;
                                }
                                if( strpos( $phone_data, '09131' ) === 0 || strpos( $phone_data, '499131' ) === 0 ) {
                                    $durchwahl = explode( '9131', $phone_data );
                                    $phone_number = "+49 9131 " . $durchwahl[1];
                                    break;
                                }
                                if( strpos( $phone_data, '0911' ) === 0 || strpos( $phone_data, '49911' ) === 0 ) {
                                    $durchwahl = explode( '911', $phone_data );
                                    $phone_number = "+49 911 " . $durchwahl[1];
                                    break;
                                }
                                add_action( 'admin_notices', array( 'FAU_Person', 'admin_notice_phone_number' ) );
                                
                        }
                
                }        
            }
        }
        return $phone_number;
    }

    //public static function officehours_repeat( $officehours ) {
    public static function officehours_repeat( $repeat, $repeat_submode, $starttime, $endtime, $office, $comment ) {
        $date = array();

        if ( !$repeat_submode ) {
            $repeat = strtok($repeat, ' ');
            $repeat_submode = strtok(' ');
            $repeat_submode = explode( ',', $repeat_submode );
        }

        if( $repeat ) {
            $dict = array(
                'd1' => __('Täglich', FAU_PERSON_TEXTDOMAIN),
                'w1' => __('Jede Woche', FAU_PERSON_TEXTDOMAIN),
                'w2' => __('Alle zwei Wochen', FAU_PERSON_TEXTDOMAIN),
            );

            if( array_key_exists( $repeat, $dict ) )
                array_push( $date, $dict[$repeat] );

            if( is_array( $repeat_submode ) && !empty($repeat_submode[0] )) {
                $days_short = array(
                    1 => __('Mo', FAU_PERSON_TEXTDOMAIN),
                    2 => __('Di', FAU_PERSON_TEXTDOMAIN),
                    3 => __('Mi', FAU_PERSON_TEXTDOMAIN),
                    4 => __('Do', FAU_PERSON_TEXTDOMAIN),
                    5 => __('Fr', FAU_PERSON_TEXTDOMAIN),
                    6 => __('Sa', FAU_PERSON_TEXTDOMAIN),
                    7 => __('So', FAU_PERSON_TEXTDOMAIN)
                );

                $days_long = array(
                    1 => __('Montag', FAU_PERSON_TEXTDOMAIN),
                    2 => __('Dienstag', FAU_PERSON_TEXTDOMAIN),
                    3 => __('Mittwoch', FAU_PERSON_TEXTDOMAIN),
                    4 => __('Donnerstag', FAU_PERSON_TEXTDOMAIN),
                    5 => __('Freitag', FAU_PERSON_TEXTDOMAIN),
                    6 => __('Samstag', FAU_PERSON_TEXTDOMAIN),
                    7 => __('Sonntag', FAU_PERSON_TEXTDOMAIN)
                );
                foreach( $repeat_submode as $value ) {
                        $days_short[$value] = $days_short[$value] . ',';
                        array_push($date, $days_short[$value]);
                }
            }
        }
        if ( $starttime ) {
            $time = self::convert_time( $starttime );
            if ( $endtime ) {
                $time .= ' - ' . self::convert_time( $endtime );
            }
            $time = $time . ',';
            array_push($date, $time);
        }

        if ( $office ) {
            $office = __('Raum', FAU_PERSON_TEXTDOMAIN) . ' ' . $office . ',';
            array_push($date, $office);            
        }
        
        if( $comment !== 0 ) {
            array_push($date, $comment);
        }

        $officehours = implode( ' ', $date );
        
        return $officehours;
    }
    
    public static function convert_time($time) {
        if ( strpos( $time, 'PM' ) ) {
            $modtime = explode( ':', rtrim( $time, ' PM' ) );
            $modtime[0] = $modtime[0] + 12;
            $time = implode( $modtime, ':' );
        } elseif ( strpos( $time, 'AM' ) ) {
            $time = rtrim( $time, ' AM');            
        } 
        return $time;
    }
       
}

/*
 * UnivIS-Data API- und Cache-Funktionen
 */

add_action('univis_data_async_task', array('UnivIS_Data', 'async_task'));

add_action('save_post', function() {
    wp_schedule_single_event(time(), 'univis_data_async_task');
} );

class UnivIS_Data {

    const transient_prefix = 'univis_data_';
    
    protected static $univis_api_url = 'http://univis.uni-erlangen.de/prg';
    
    protected static $transient_expiration = WEEK_IN_SECONDS;
    
    protected static $timeout = DAY_IN_SECONDS;
    
    protected static $results_limit = 100;
    
    public static function get_person($id) {

        if (!self::is_valid_id($id)) {
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
    
    private static function get_data_cache($id) {
        if (!self::is_valid_id($id)) {
            return false;
        }
        
        return get_transient(self::transient_prefix . $id);
    }
    
    private static function set_data_cache($data) {
        if (!isset($data['id'])) {
            return false;
        }
        
        $id = $data['id'];
        if (!self::is_valid_id($id)) {
            return false;
        }
        
        return set_transient(self::transient_prefix . $id, $data, self::$transient_expiration);
    }
    
    public static function async_task() {
        $timestamp = time() + self::$transient_expiration - self::$timeout;
        
        if($results = self::get_results_by_timeout($timestamp, self::$results_limit)) {
            
            foreach($results as $row) {                

                $id = ltrim($row->option_name, '_transient_timeout_' . self::transient_prefix);

                if (($data = self::get_remote_data_by('id', $id)) !== false) {
                    self::set_data_cache($data[0]);
                } else {
                    self::delete_data_cache($id);
                }
                
            }
            
        }
        
    }
        
    private static function get_results_by_timeout($timestamp, $limit) {
        global $wpdb;

        $sql = "SELECT * FROM $wpdb->options WHERE option_name LIKE %s AND option_value < %d ORDER BY option_value ASC LIMIT %d";

        return $wpdb->get_results($wpdb->prepare($sql, $wpdb->esc_like('_transient_timeout_' . self::transient_prefix) . '%', $timestamp, $limit));        
    }
    
    public static function delete_data_cache($id) {
        if (!self::is_valid_id($id)) {
            return false;
        }

        return delete_transient(self::transient_prefix . $id);
    }
    
    public static function search_by($field = '', $value = '') {
        if ('id' == $field && !self::is_valid_id($value)) {
            return false;
        } else {
            $value = trim($value);
        }

        if (!$value) {
            return false;
        }

        if (!$data = self::get_remote_data_by($field, $value)) {
            return false;
        }

        return $data;
    }
    
    public static function search_by_fullname($firstname = '', $lastname = '') {
        $firstname = trim($firstname);
        $lastname = trim($lastname);
        
        if (!$lastname || !$firstname) {
            return false;
        }

        if (!$data = self::get_remote_data_by_fullname($firstname, $lastname)) {
            return false;
        }

        return $data;
    }
            
    private static function get_remote_data_by($field, $value) {
        
        switch ($field) {
            case 'id':
                $url = sprintf('%1$s?search=persons&id=%2$d&show=xml', self::$univis_api_url, $value);
                break;
            case 'firstname':
                $url = sprintf('%1$s?search=persons&firstname=%2$s&show=xml', self::$univis_api_url, urlencode($value));
                break;
            case 'lastname':
                $url = sprintf('%1$s?search=persons&name=%2$s&show=xml', self::$univis_api_url, urlencode($value));
                break;
            default:
                return false;
        }

        if (!fopen($url, "r")) {
            return false;
        }

        if(!$persArray = self::xml2array($url)) {
            return false;
        }
        
        $data = $persArray['Person'];
        
        if (count($data) == 0) {
            return false;
        }
        
        return $data;        
    }
    
    private static function get_remote_data_by_fullname($firstname, $lastname) {
        
        $url = sprintf('%1$s?search=persons&firstname=%2$s&name=%3$s&show=xml', self::$univis_api_url, urlencode($firstname), urlencode($lastname));
        
        if (!fopen($url, "r")) {
            return false;
        }

        if(!$persArray = self::xml2array($url)) {
            return null;
        }
        
        $data = $persArray['Person'];
        
        if (count($data) == 0) {
            return null;
        }
        
        return $data;        
    }
    
    private static function xml2array($url) {
        $sxi = new SimpleXmlIterator($url, null, true);
        return self::sxi2array($sxi);
    }

    private static function sxi2array($sxi) {
        $a = array();

        for ($sxi->rewind(); $sxi->valid(); $sxi->next()) {

            if (!array_key_exists($sxi->key(), $a)) {
                $a[$sxi->key()] = array();
            }
            
            if ($sxi->hasChildren()) {
                $a[$sxi->key()][] = self::sxi2array($sxi->current());
            } elseif($sxi->key() === 'orgunit') {
                $a[$sxi->key()][] = strval($sxi->current());
            } elseif($sxi->key() === 'orgunit_en') {
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
    
    private static function is_valid_id($id) {
        $return = ((string)$id === (string)(int)$id);
        if ($return && intval($id) < 1) {
            $return = false;
        }
        return $return;
    }
    
}
