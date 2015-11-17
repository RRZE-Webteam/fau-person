<?php

class standort_sync_helper {
    //gibt die Werte des Standorts an, für Standort-Synchronisation $edfaults=1
    public static function get_fields( $id, $standort_id, $defaults ) {
        $standort_sync = 0;
        $fields = array();
        if( $standort_id ) {
            $standort_sync = 1;
        } 
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
        
        
        
        foreach( $fields_standort as $key => $value ) {
            if( $standort_sync ) {
                $value = self::sync_standort( $id, $person, $key, $value, $defaults ); 
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);     
                } else {
                    $value = get_post_meta($id, 'fau_person_'.$key, true);                          
                }
            }
            $fields[$key] = $value;
        }
        return $fields;
    }
    
//$id = ID des Standorteintrags, $person = Array mit Personendaten, $fau_person_var = Bezeichnung Personenplugin, $univis_vat = Bezeichnung UnivIS, $defaults = Default-Wert 1 für Ausgabe der hinterlegten Werte im Personeneingabeformular    
    public static function sync_standort( $id, $person, $fau_person_var, $univis_var, $defaults ) {   
        //wird benötigt, falls jeder einzelne Wert abgefragt werden soll
        if( $defaults ) {
            if( !empty( $person[$univis_var] ) ) {
                $val = sprintf(__('<p class="cmb_metabox_description">[Von Standort angezeigter Wert: %s]</p>', FAU_PERSON_TEXTDOMAIN), $person[$univis_var]);
            } else {
                $val = __('<p class="cmb_metabox_description">[Im Standort ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);
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
