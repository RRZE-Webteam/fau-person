<?php

namespace FAU_Person;
use sync_helper;

defined('ABSPATH') || exit;


class Data {
     

    public static function get_contactdata( $connection=0 ) {            
        $args = array(
            'post_type' => 'person',
            'numberposts' => -1,
            'meta_key' => 'fau_person_typ'
        );

	$personlist = get_posts($args);

        if( $personlist ) {  
            foreach( $personlist as $key => $value) {
                $personlist[$key] = (array) $personlist[$key];      
                $name = $personlist[$key]['post_title'];
                switch ( get_post_meta( $personlist[$key]['ID'], 'fau_person_typ', true ) ) {
                    case 'realperson':
                    case 'realmale':
                    case 'realfemale':
                        if ( get_post_meta( $personlist[$key]['ID'], 'fau_person_familyName', true ) ) {
                            $lastname = get_post_meta( $personlist[$key]['ID'], 'fau_person_familyName', true );
                            if ( get_post_meta( $personlist[$key]['ID'], 'fau_person_givenName', true ) ) {
                                $name = $lastname . ', ' . get_post_meta( $personlist[$key]['ID'], 'fau_person_givenName', true );
                            } elseif ( ltrim( strpos( $name, $lastname ) ) ) {
                                $name = $lastname . ', ' . ltrim( str_replace( $lastname, '', $name ) );
                            } else {
                                $name = $lastname;
                            }
                        } else {
                            if( ltrim( strpos( $name, ' ' ) ) ) {
                                $lastname = ltrim( strrchr( $name, ' ' ) );
                                $firstname = ltrim( str_replace( $lastname, '', $name ) );
                                $name = $lastname . ', ' . $firstname;
                            }                           
                        } 
                        break;
                    default:
                        break;
                }   
                $temp[ $personlist[$key]['ID'] ] = $name; 
            }
            natcasesort($temp);     

            foreach( $temp as $key => $value ) {
                $contactselect[$key] = $key . ': ' . $value;
            }
            // Für Auswahlfeld bei verknüpften Kontakten
            if ( $connection ) {
                $contactselect = array( '0' => __('Kein Kontakt ausgewählt.', 'fau-person') ) + $contactselect;
            }
        } else {
            // falls noch keine Kontakte vorhanden sind
            $contactselect[0] = __('Noch keine Kontakte eingepflegt.', 'fau-person');
        } 
        return $contactselect;  
    }
    
    
    public static function get_standortdata() {      
         $args = array(
            'post_type' => 'standort',
            'numberposts' => -1
        );

	$standortlist = get_posts($args);
        if( $standortlist ) {  
            foreach( $standortlist as $key => $value) {
                $standortlist[$key] = (array) $standortlist[$key];   
                $standortselect[ $standortlist[$key]['ID'] ] = $standortlist[$key]['post_title'];
            }                                                
            asort($standortselect);
            $standortselect = array( '0' => __('Kein Standort ausgewählt.', 'fau-person') ) + $standortselect;

        } else {
            $standortselect[0] = __('Noch kein Standort eingepflegt.', 'fau-person');
        }
        return $standortselect;  
    }
    
     public static function get_default_fau_person_typ( ) {     
        if(isset($_GET["fau_person_typ"]) && $_GET["fau_person_typ"] == 'einrichtung') {
            $default_fau_person_typ = 'einrichtung';
        } else {
            $default_fau_person_typ = 'realperson';
        }
        return $default_fau_person_typ;
    }
    
    
    //gibt die Werte des Standorts an, für Standort-Synchronisation $edfaults=1
    public static function get_fields_standort( $id, $standort_id, $defaults ) {
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
                    $value = __('<p class="cmb2_metabox_description">[Im Standort ist hierfür kein Wert hinterlegt.]</p>', 'fau-person');     
                } elseif ($id) {
                    $value = get_post_meta($id, 'fau_person_'.$key, true);                          
                }
            }
            $fields[$key] = $value;            
        }
        return $fields;
    }
    
    
    // $id = ID des Personeneintrags, 
    // $standort_id = ID des Standorteintrags, 
    // $fau_person_var = Bezeichnung des Feldes im Personenplugin, 
    // $defaults = Default-Wert 1 für Ausgabe der hinterlegten Werte im Personeneingabeformular    
    public static function sync_standort( $id, $standort_id, $fau_person_var, $defaults ) {   
        $value = get_post_meta($standort_id, 'fau_person_'.$fau_person_var, true);
        //wird benötigt, falls jeder einzelne Wert abgefragt werden soll
        if( $defaults ) {
            if( !empty( $value ) ) {
                $val = sprintf(__('<p class="cmb2_metabox_description">[Von Standort angezeigter Wert: %s]</p>', 'fau-person'), $value);               
            } else {
                $val = __('<p class="cmb2_metabox_description">[Im Standort ist hierfür kein Wert hinterlegt.]</p>', 'fau-person');
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
    
    
    public static function get_standort_defaults( $id = 0 ) {
            $post = get_post($id);
            if( !is_null( $post ) && $post->post_type === 'person' && get_post_meta($id, 'fau_person_standort_id', true)) {
                $standort_id = get_post_meta($id, 'fau_person_standort_id', true);
                $standort_default = Data::get_fields_standort($id, $standort_id, 1);
                return $standort_default;        
            } else {
		return Data::get_fields_standort(0,0,0);
	    }
    }
    
    // Sortierung eines Arrays mit Objekten (z.B. bei einer Kategorie) alphabetisch nach Titel oder Nachname, je nach Typ
    public function sort_person_posts( $personlist ) {
        if ( is_array( $personlist ) ) {
            foreach( $personlist as $key => $value) {
                $personlist[$key] = (array) $personlist[$key];
                // Bei Personen Prüfung, ob Nachname im Feld eingetragen ist (ggf. aus UnivIS), wenn nicht letztes Wort von Titel als Nachname angenommen
                switch ( get_post_meta( $personlist[$key]['ID'], 'fau_person_typ', true ) ) {
                    case 'realperson':
                    case 'realmale':
                    case 'realfemale':
                        $fields = sync_helper::get_fields($personlist[$key]['ID'], get_post_meta($personlist[$key]['ID'], 'fau_person_univis_id', true), 0);
                        extract($fields);                   
                        if( !empty( $familyName ) ) {
                            $name = $familyName;
                            if( !empty( $givenName ) ) {
                                $name = $name . ', ' . $givenName;
                            }
                        } else {
                            $name = $personlist[$key]['post_title'];                   
                            if( ltrim( strpos( $name, ' ' ) ) ) {
                                $lastname = ltrim( strrchr( $name, ' ' ) );
                                $name = $lastname . ', ' . ltrim( str_replace( $lastname, '', $name ) );
                            } 
                        }
                        break;
                    default:
                        if( !empty( get_post_meta( $personlist[$key]['ID'], 'fau_person_alternateName', true ) ) ) {
                            $name = get_post_meta( $personlist[$key]['ID'], 'fau_person_alternateName', true );
                        } else {
                            $name = $personlist[$key]['post_title'];
                        }
                        break;
                }
                $temp[$key] = strtolower($name);
            }
            array_multisort($temp, $personlist);
            return $personlist;  
        }
    }
    
    
    public static function fullname_output( $id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, $showtitle, $showsuffix, $alternateName ) {
        $fullname = '<span itemprop="name">';
        if ( $alternateName ) {
            $fullname .= '<span itemprop="alternateName">' . $alternateName . '</span>';
        } else {
            if ( $showtitle && $honorificPrefix )
                $fullname .= '<span itemprop="honorificPrefix">' . $honorificPrefix . '</span> ';
            $fullname .= '<span class="fullname">';
            if ( $givenName && $familyName ) {
                    $fullname .= '<span itemprop="givenName">' . $givenName . "</span> ".'<span itemprop="familyName">' . $familyName . "</span>";
            } elseif (!empty(get_the_title($id))) {
                $fullname .= get_the_title($id);
            }
            $fullname .= '</span>';
            if ( $showsuffix && $honorificSuffix )
                $fullname .= ', <span itemprop="honorificSuffix">' . $honorificSuffix . '</span>';
        }
        $fullname .= '</span>';
        return $fullname;
    }
    
    
    public static function hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text ) {
        if(!empty($hoursAvailable) || !empty($hoursAvailable_group)) {
            $output = '<li class="person-info-office"><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">';
            if(!empty($hoursAvailable_text)) {
                $output .= '<strong>' . $hoursAvailable_text . ':</strong><br>';
            } else {
                $output .= '<span class="screen-reader-text">' . __('Sprechzeiten', 'fau-person') . ': </span>';    
            }
            if ( $hoursAvailable ) {
                $output .= $hoursAvailable;
            }
            if ( $hoursAvailable_group ) {
                if ( $hoursAvailable )  $output .= '<br>';
                $output .= implode('<br>', $hoursAvailable_group);
            }

            $output .= '</span></li>';
            return $output;
        }
    }
    
    
    
    // über $type wird die Ausgabereihenfolge definiert: "page", "connection" oder alles andere
    public static function contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, $showaddress, $showroom, $type ) {
        if( $showaddress ) {
            if( $streetAddress )       
                $street = '<span class="person-info-street" itemprop="streetAddress">' . $streetAddress . '</span>';
            if ( $addressLocality ) {
                $city = '';
                if( $postalCode )
                    $city .= '<span itemprop="postalCode">' . $postalCode . '</span> ';
                $city .= '<span itemprop="addressLocality">' . $addressLocality . '</span>';
            }
            if( $addressCountry )
                $country = '<span class="person-info-country" itemprop="addressCountry">' . $addressCountry . '</span>';
        }
        if( $workLocation && $showroom ) 
            $room = __('Raum', 'fau-person') . ' ' . $workLocation;
        
        if ( !empty($street) || !empty($city) || !empty($country) || !empty($room) ) {
            $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">' . __('Adresse', 'fau-person') . ': <br></span>';
            switch( $type ) {
                case 'page':
                    if( isset($street) || isset($city) || isset($country) )
                        $contactpoint .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
                    if ( isset($street) ) {
                        $contactpoint .= $street;
                        if ( isset($city) || isset($country) ) {       
                            $contactpoint .= '<br>';
                        } else {
                            $contactpoint .= '</div>';
                        }
                    } 
                    if ( isset($city) ) {
                        $contactpoint .= $city;
                        if ( isset($country) ) {
                            $contactpoint .= '<br>';
                        } else {
                            $contactpoint .= '</div>';
                        }
                    } 
                    if ( isset($country) ) {
                            $contactpoint .= $country . '</div>';                        
                    }
                    if ( isset($room) ) {
                        $contactpoint .= '<div class="person-info-room" itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . $room . '</div>';
                    }
                    break;
                case 'connection':
                    if( isset($street) || isset($city) || isset($country) )
                        $contactpoint .= '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';  
                    if ( isset($street) ) {
                        $contactpoint .= $street;
                        if ( isset($city) || isset($country) ) {       
                            $contactpoint .= ', ';
                        } elseif ( isset($room) ) {
                            $contactpoint .= '</span>, ';
                        } else {
                            $contactpoint .= '</span>';
                        }
                    } 
                    if ( isset($city) ) {
                        $contactpoint .= $city;
                        if ( isset($country) ) {
                            $contactpoint .= ', ';
                        } elseif ( isset($room) ) {
                            $contactpoint .= '</span>, ';
                        } else {
                            $contactpoint .= '</span>';
                        }
                    }  
                    if ( isset($country) ) {
                         $contactpoint .= $country . '</span>';
                         if ( isset($room) ) {
                             $contactpoint .= ', ';
                         }
                    }
                    if ( isset($room) ) {
                        $contactpoint .= '<span class="person-info-room" itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . $room . '</span>';
                    }
                    break;                    
                default:   
                    if ( isset($street) ) {
                        $contactpoint .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">' . $street;
                        if ( isset($room) ) {
                            $contactpoint .= '</div>';
                        } elseif ( isset($city) || isset($country) ) {
                            $contactpoint .= '<br>';
                        } else {
                            $contactpoint .= '</div>';
                        }
                    }
                    if ( isset($room) ) {
                        $contactpoint .= '<div class="person-info-room" itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . $room . '</div>';
                        if ( isset($city) || isset($country) ) 
                            $contactpoint .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';                            
                    }
                    if ( !isset($street) && !isset($room) ) {
                        if ( isset($city) || isset($country) ) {
                            $contactpoint .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
                        }                       
                    }
                    if ( isset($city) ) {
                       $contactpoint .= $city;
                        if ( isset($country) ) {
                            $contactpoint .= '<br>';
                        } else {
                            $contactpoint .= '</div>';
                        }                        
                    }
                    if ( isset($country) ) {
                        $contactpoint .= $country . '</div>';
                    }
            }                    
            $contactpoint .= '</li>' . "\n";
            return $contactpoint;
        }
        
    }
    public function get_kontakt_thumb($id = 0, $type = '') {
	$out = '';
	
	
	if (($id > 0) && (has_post_thumbnail($id))) {
                $out = get_the_post_thumbnail($id, 'person-thumb-bigger');
         } else {
	     if (!isset($type)) {
		$type = get_post_meta($id, 'fau_person_typ', true);
	     }
                if ($type == 'realmale') {
                    $bild = plugin_dir_url(__DIR__) . 'images/platzhalter-mann.png';
                } elseif ($type == 'realfemale') {
                    $bild = plugin_dir_url(__DIR__ ) . 'images/platzhalter-frau.png';
                } elseif ($type == 'einrichtung') {
                    $bild = plugin_dir_url(__DIR__ ) . 'images/platzhalter-organisation.png';
                } else {
                    $bild = plugin_dir_url(__DIR__ ) . 'images/platzhalter-unisex.png';
                }
                if ($bild)
                    $out = '<img src="' . $bild . '" width="90" height="120" alt="">';
         }
	
	return $out;
    }
}