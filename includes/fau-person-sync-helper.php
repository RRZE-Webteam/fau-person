<?php

class sync_helper {

    public static function get_fields( $id, $univis_id ) {
        $person = self::get_univisdata( $univis_id );
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
            'postalCode' => '',
            'addressCountry' => '',
            'pubs' => '',
            'link' => '',
            'hoursAvailable' => '',
            'description' => '',
        );
        $fields = array();
        foreach( $fields_univis as $key => $value ) {
            if( array_key_exists( $value, $person ) ) {
                $value = self::sync_univis( $id, $person, $key, $value ); 
            } else {
                $value = get_post_meta($id, 'fau_person_'.$key, true);                
            }
            $fields[$key] = $value;
        }
        foreach( $fields_univis_location as $key => $value ) {
            if( array_key_exists( 'locations', $person ) ) {
                $person_location = $person['locations'][0]['location'][0];
                $value = self::sync_univis( $id, $person_location, $key, $value );
            } else {
                $value = get_post_meta($id, 'fau_person_'.$key, true);
            }
            $fields[$key] = $value;
        }
        foreach( $fields_univis_officehours as $key => $value ) {
            if( array_key_exists( 'officehours', $person ) ) {
                $person_officehours = $person['officehours'][0]['officehour'][0];
                $value = self::sync_univis( $id, $person_officehours, $key, $value );
            } else {
                $value = get_post_meta($id, 'fau_person_'.$key, true);                
            }
            $fields[$key] = $value;
        }
        foreach( $fields_univis_orgunits as $key => $value ) {
            if( array_key_exists( 'orgunits', $person ) ) {
                $person_orgunits = $person['orgunits'];
                $person_orgunit = $person['officehours'][0]['officehour'][0];
                $value = self::sync_univis( $id, $person_orgunit, $key, $value );
            } else {
                $value = get_post_meta($id, 'fau_person_'.$key, true);                
            }
            $fields[$key] = $value;
        }        
        foreach( $fields_fauperson as $key => $value ) {
            $value = get_post_meta($id, 'fau_person_'.$key, true);
            $fields[$key] = $value;            
        }
        return $fields;
    }
    
    
    public static function get_univisdata( $univis_id ) {    
    	$univis_url = "http://univis.uni-erlangen.de/prg";
        if($univis_id) {
		// Hole Daten von Univis
		$url = $univis_url."?search=persons&id=".$univis_id."&show=xml";
		if(!fopen($url, "r")) {
			// Univis Server ist nicht erreichbar
			return -1;
		}
		$persArray = xml2array($url);
                if(empty($persArray)) {
                    echo "Leider konnte die Person nicht gefunden werden.";
                    return array();
                } else {
		$person = $persArray["Person"];

		if(count($persArray) == 0 ) {

			// Keine Person gefunden
			return array();
		}
		// Falls mehrer Personen gefunden wurden, wähle die erste
		if($person) $person = $person[0];
		// Lade Publikationen und Lehrveranstaltungen falls noetig
/*              if ($this->optionen["Personenanzeige_Publikationen"]) {
			$person["publikationen"] = $this->_ladePublikationen($person["id"]);
		}
		if ($this->optionen["Personenanzeige_Lehrveranstaltungen"]) {
			$person["lehrveranstaltungen"] = $this->_ladeLehrveranstaltungenAlle($person["id"]);
		}
*/
		return $person;
                }
        } else {
            echo "Sie haben keine UnivIS-ID angegeben.";
            return array();
        }
    }
   
    public static function sync_univis( $id, $person, $fau_person_var, $univis_var ) {   
        //wird benötigt, falls jeder einzelne Wert abgefragt werden soll
        //if( !empty( $person[$univis_var] ) && get_post_meta($id, 'fau_person_'.$fau_person_var_sync', true) ) {
        if( !empty( $person[$univis_var] ) && get_post_meta($id, 'fau_person_univis_sync', true) ) {
            $val = $person[$univis_var];             
        } else {
            $val = get_post_meta($id, 'fau_person_'.$fau_person_var, true);
        }
        return $val;        
    }
   
    
}