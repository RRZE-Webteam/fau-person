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
        foreach( $fields_fauperson as $key => $value ) {
            $value = get_post_meta($id, 'fau_person_'.$key, true);
            $fields[$key] = $value;            
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
