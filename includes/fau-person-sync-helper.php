<?php

class sync_helper {

    public static function get_fields( $id, $univis_id, $defaults ) {
        if( $univis_id && class_exists( 'Univis_Data' ) ) {
            //$person = self::get_univisdata( $univis_id );
            $person = Univis_Data::get_data_by( 'id', $univis_id );
        } else {
            $person = array();
        }
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
        $fields = array();
        foreach( $fields_univis as $key => $value ) {
            if( array_key_exists( $value, $person ) ) {
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
            if( array_key_exists( 'locations', $person ) && array_key_exists( 'location', $person['locations'][0] ) ) {
                $person_location = $person['locations'][0]['location'][0];
                $value = self::sync_univis( $id, $person_location, $key, $value, $defaults );
            } else {
                $value = get_post_meta($id, 'fau_person_'.$key, true);
            }
            $fields[$key] = $value;
        }
        foreach( $fields_univis_officehours as $key => $value ) {
            if( array_key_exists( 'officehours', $person ) && array_key_exists( 'officehour', $person['officehours'][0] ) ) {
                $person_officehours = $person['officehours'][0]['officehour'][0];
                $value = self::sync_univis( $id, $person_officehours, $key, $value, $defaults );
            } else {
                $value = get_post_meta($id, 'fau_person_'.$key, true);                
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
                $value = get_post_meta($id, 'fau_person_'.$key, true);                
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
        return $fields;
    }
    
    /*
    public static function get_univisdata( $univis_id, $firstname=0, $givenname=0 ) {    
    	$univis_url = "http://univis.uni-erlangen.de/prg";
           // Hole Daten von Univis
        if( $univis_id || $firstname || $givenname ) {
        if( $univis_id ) {
            $url = $univis_url."?search=persons&id=".urlencode($univis_id)."&show=xml";
        } elseif( $firstname && $givenname ) {
            $url = $univis_url."?search=persons&name=".urlencode($givenname)."&firstname=".urlencode($firstname)."&show=xml";
        } elseif( $firstname ) {
            $url = $univis_url."?search=persons&firstname=".urlencode($firstname)."&show=xml";
        } elseif( $givenname ) {
            $url = $univis_url."?search=persons&name=".urlencode($givenname)."&show=xml";
        } 
      
		if(!fopen($url, "r")) {
			// Univis Server ist nicht erreichbar
			return array();
		}
		$persArray = xml2array($url);
                if(empty($persArray)) {
                    //echo "Leider konnte die Person nicht gefunden werden.";
                    return array();
                } else {
		$person = $persArray["Person"];

		if(count($persArray) == 0 ) {

			// Keine Person gefunden
			return array();
		}
		// Falls mehrer Personen gefunden wurden, wähle die erste, wenn Abfrage nach UnivIS-ID
		if( $univis_id && $person ) $person = $person[0];
		// Lade Publikationen und Lehrveranstaltungen falls noetig */
/*              if ($this->optionen["Personenanzeige_Publikationen"]) {
			$person["publikationen"] = $this->_ladePublikationen($person["id"]);
		}
		if ($this->optionen["Personenanzeige_Lehrveranstaltungen"]) {
			$person["lehrveranstaltungen"] = $this->_ladeLehrveranstaltungenAlle($person["id"]);
		}
*/
    /*
		return $person;
                }
        } else {
            //echo "Sie haben keine UnivIS-ID angegeben.";
            return array();
        }
    }
     * 
     */
   
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
