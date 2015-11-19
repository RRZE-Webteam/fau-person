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
            if( $standort_sync ) {
                    $value = self::sync_standort( $id, $standort_id, $key, $defaults );                     
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[Im Standort ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);     
                } else {
                    $value = get_post_meta($id, 'fau_person_'.$key, true);                          
                }
            }
            $fields[$key] = $value;            
        }
        return $fields;
    }
    
//$id = ID des Personeneintrags, $standort_id = ID des Standorteintrags, $fau_person_var = Bezeichnung des Feldes im Personenplugin, $defaults = Default-Wert 1 für Ausgabe der hinterlegten Werte im Personeneingabeformular    
    public static function sync_standort( $id, $standort_id, $fau_person_var, $defaults ) {   
        $value = get_post_meta($standort_id, 'fau_person_'.$fau_person_var, true);
        //wird benötigt, falls jeder einzelne Wert abgefragt werden soll
        if( $defaults ) {
            if( !empty( $value ) ) {
                $val = sprintf(__('<p class="cmb_metabox_description">[Von Standort angezeigter Wert: %s]</p>', FAU_PERSON_TEXTDOMAIN), $value);               
            } else {
                $val = __('<p class="cmb_metabox_description">[Im Standort ist hierfür kein Wert hinterlegt.]</p>', FAU_PERSON_TEXTDOMAIN);
            }
        } else {
            if( !empty( $value ) && get_post_meta($id, 'fau_person_standort_sync', true) ) {
                $val = $value;             
            } else {
                $val = get_post_meta($id, 'fau_person_'.$fau_person_var, true);
            }
        }
        return $val;        
    }
    
    
       
}
