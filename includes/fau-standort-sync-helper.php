<?php

class standort_sync_helper {
    //gibt die Werte des Standorts an, für Standort-Synchronisation $edfaults=1
    public static function get_fields( $id, $standort_id, $defaults ) {
        $fields = array();
        $fields_standort = array(
            'streetAddress' => '',
            'postalCode' => '',
            'addressLocality' => '', 
            'addressCountry' => '',
        );
    
        foreach( $fields_standort as $key => $value ) {
            $value = get_post_meta($id, 'fau_person_'.$key, true);
            $fields[$key] = $value;            
        }
        return $fields;
    }

       
}
