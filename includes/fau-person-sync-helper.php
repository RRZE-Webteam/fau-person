<?php

class sync_helper {
    //gibt die Werte der Person an, Inhalte abhängig von UnivIS, Übergabewerte: ID der Person, UnivIS-ID der Person, Default-Wert 1 für Ausgabe der hinterlegten Werte im Personeneingabeformular
    public static function get_fields( $id, $univis_id, $defaults ) {
        $univis_sync = 0;
        $person = array();
        if( $univis_id && class_exists( 'Univis_Data' ) ) {
            $person = self::get_univisdata( $univis_id );
            $univis_sync = 1;
        } 
        $fields = array();
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
        );
        $fields_univis_officehours = array(
            'workLocation' => 'office', 
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
                $value = self::sync_univis( $id, $person_location, $key, $value, $defaults );
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);
                } else {
                    $value = get_post_meta($id, 'fau_person_'.$key, true);
                }
            }
            $fields[$key] = $value;
        }
        foreach( $fields_univis_officehours as $key => $value ) {
            if( $univis_sync && array_key_exists( 'officehours', $person ) && array_key_exists( 'officehour', $person['officehours'][0] ) ) {
                $person_officehours = $person['officehours'][0]['officehour'][0];
                $value = self::sync_univis( $id, $person_officehours, $key, $value, $defaults );
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);
                } else {
                    $value = get_post_meta($id, 'fau_person_'.$key, true);  
                }
            }
            $fields[$key] = $value;
        }
        foreach( $fields_univis_orgunits as $key => $value ) {
            if( array_key_exists( 'orgunits', $person ) ) {
                $person_orgunits = $person['orgunits'][0]['orgunit'];
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
        $connections = get_post_meta($id, 'fau_person_connection_id', true);
        if( $connections ) {    
            $connection = array();
            foreach( $connections as $ckey => $cvalue ) {
                $connection_fields[$ckey] = sync_helper::get_fields($cvalue, get_post_meta($cvalue, 'fau_person_univis_id', true), 0);
                $connection_fields[$ckey]['nr'] = $cvalue;
                //_rrze_debug($connection_fields);
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
    
    //$id = ID des Personeneintrags, $person = Array mit Personendaten, $fau_person_var = Bezeichnung Personenplugin, $univis_vat = Bezeichnung UnivIS, $defaults = Default-Wert 1 für Ausgabe der hinterlegten Werte im Personeneingabeformular
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

       
}
