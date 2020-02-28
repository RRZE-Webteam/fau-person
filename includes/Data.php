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
    
    public static function fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex = 0, $noborder, $hstart, $bg_color) {

        // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
        extract($fields);
        if ($showvia !== 0 && !empty($connections))
            $showvia = 1;
        if ($showvia === 0 && !empty($connection_only))
            $connection_only = '';
        
        

        if ($link) {
            $personlink = $link;
        } else {
            $personlink = get_permalink($id);
        }

        if (get_post_field('post_excerpt', $id)) {
            $excerpt = get_post_field('post_excerpt', $id);
        } else {
            $post = get_post($id);
            if ($post->post_content)
                $excerpt = wp_trim_excerpt($post->post_content);
        }
        
        $fullname = Data::fullname_output($id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, $showtitle, $showsuffix, $alternateName);
        $contactpoint = Data::contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, $showaddress, $showroom, 'default' );
        // hier Fehlermeldung nicht vorhanden $hoursAvailable_group
        $hoursavailable_output = Data::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text );
        
        $content = '<div class="person content-person' . $noborder . $bg_color . '" itemscope itemtype="http://schema.org/Person">';
        if ($compactindex)
            $content .= '<div class="compactindex">';

        // if( !$compactindex || $showthumb )        
        $content .= '<div class="row">';

        if ($showthumb) {
            $content .= '<div class="span1 span-small person-thumb" itemprop="image" aria-hidden="true" role="presentation">';
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $personlink . '">';

	   
	   $content .= Data::get_kontakt_thumb($id, $type, $pluginFile );
	    
           
            $content .= '</a>';
            $content .= '</div>';
        }

        if ($compactindex) {
            if ($showthumb) {
                $content .= '<div class="span6 person-compact-thumb">';
            } else {
                $content .= '<div class="span7 person-compact">';
            }
        } else {
            if ($showthumb) {
                $content .= '<div class="span3 person-default-thumb">';
            } else {
                $content .= '<div class="span4 person-default">';
            }
        }
        
        $content .= '<h' . $hstart . '>';
        $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
        $content .= '</h' . $hstart . '>';
        $content .= '<ul class="person-info">';
        if ($showposition && $jobTitle)
            $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', 'fau-person') . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>';
        if ($showinstitution && $worksFor)
            $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', 'fau-person') . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>';
        if ($showabteilung && $department)
            //itemprop="department" entfernt da nicht zu Person zugehörig
            $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', 'fau-person') . ': </span>' . $department . '</li>';   
        if (($extended || $showaddress || $showroom) && !empty($contactpoint) && empty($connection_only))
            $content .= $contactpoint;
        if ($showtelefon && $telephone && empty($connection_only))
            $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', 'fau-person') . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
        if ($showmobile && $mobilePhone && empty($connection_only))
            $content .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobil', 'fau-person') . ': </span><span itemprop="telephone">' . $mobilePhone . '</span></li>';
        if ($showfax && $faxNumber && empty($connection_only))
            $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', 'fau-person') . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
        if ($showmail && $email && empty($connection_only))
            $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', 'fau-person') . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
        if ($showwebsite && $url)
            $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', 'fau-person') . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>';
        if ($showpubs && $pubs)
            $content .= '<li class="person-info-pubs"><span class="screen-reader-text">' . __('Publikationen', 'fau-person') . ': </span>' . $pubs . '</li>';
        $content .= '</ul>';


        if ((!empty($connection_text) || !empty($connection_options) || !empty($connections)) && $showvia === 1)
            $content .= self::fau_person_connection($connection_text, $connection_options, $connections, $hstart);

        if (($showoffice && $hoursavailable_output && empty($connection_only)) || ($showlist && isset($excerpt)) || (($showsidebar || $extended) && $description) || ($showlink && $personlink)) {


            if (!$compactindex)
                $content .= '</div><div class="span3 person-default-more">';
            if ($showoffice && $hoursavailable_output && empty($connection_only)) {
                $content .= '<ul class="person-info">';
                //$content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', 'fau-person') . ': </span><div itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</div></li>';
                $content .= $hoursavailable_output;
                $content .= '</ul>';
            }

            if ($showlist && isset($excerpt))
                $content .= '<div class="person-info-description"><p>' . $excerpt . '</p></div>';
            if (($extended || $showsidebar) && $description)
                $content .= '<div class="person-info-description"><span class="screen-reader-text">' . __('Beschreibung', 'fau-person') . ': </span>' . $description . '</div>';
            if ($showlink && $personlink) {
                $content .= '<div class="person-info-more"><a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" class="person-read-more" href="' . $personlink . '">';
                $content .= __('Mehr', 'fau-person') . ' ›</a></div>';
            }
        }


        // if( $compactindex && $showthumb )      
        $content .= '</div>'; // end div row
        // if( !$compactindex || $showthumb )      
        $content .= '</div> <!-- /row-->';

        if ($compactindex)
            $content .= '</div>';   // ende div class compactindex
        $content .= '</div>';
        return $content;
    }

    public static function fau_person_page($id, $is_shortcode=0, $showname=0) {

        $content = '<div class="person page" itemscope itemtype="http://schema.org/Person">';
        // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
        extract($fields);

        if ((strlen($url) > 4) && (strpos($url, "http") === false)) {
            $url = 'https://' . $url;
        }
        if ( !$is_shortcode || $showname ) {
            $fullname = Data::fullname_output($id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, 1, 1, $alternateName);
            $content .= '<h2>' . $fullname . '</h2>';
        }

        $contactpoint = Data::contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, 1, 1, 'page' );
        $hoursavailable_output = Data::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text );
        
	$content .= '<div class="person-meta">';
	if (has_post_thumbnail($id)) {
            $content .= '<div itemprop="image" class="person-image alignright">'; 
            $content .= get_the_post_thumbnail($id, 'person-thumb-page');
            $content .= '</div>';
         }
         $content .= '<ul class="person-info">';
        if ($jobTitle)
            $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', 'fau-person') . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>';
        if ($worksFor)
            $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', 'fau-person') . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>';
        if ($department)
            $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', 'fau-person') . ': </span><span itemprop="worksFor">' . $department . '</span></li>';
        if ($telephone && empty($connection_only))
            $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', 'fau-person') . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
        if ($mobilePhone && empty($connection_only))
            $content .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobil', 'fau-person') . ': </span><span itemprop="telephone">' . $mobilePhone . '</span></li>';
        if ($faxNumber && empty($connection_only))
            $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', 'fau-person') . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
        if ($email && empty($connection_only))
            $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', 'fau-person') . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
        if ($url && empty($connection_only))
            $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', 'fau-person') . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>';

        if (!empty($contactpoint) && empty($connection_only)) {            
            $content .= $contactpoint;
        }
        if ($hoursavailable_output && empty($connection_only))
            $content .= $hoursavailable_output;
            //$content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', 'fau-person') . ': </span><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</span></li>';
        if ($pubs)
            $content .= '<li class="person-info-pubs"><span class="screen-reader-text">' . __('Publikationen', 'fau-person') . ': </span>' . $pubs . '</li>';
        $content .= '</ul>';
	$content .= '</div>';

        if (!empty($connection_text) || !empty($connection_options) || !empty($connections))
            $content .= self::fau_person_connection($connection_text, $connection_options, $connections, 2);


        if ( is_singular( 'person' ) && in_the_loop() ) {
            $post = get_the_content();
        } else {
            $post = get_post($id)->post_content;
        }
        if ($post) {
            $content .= '<div class="desc" itemprop="description">' . PHP_EOL;
            $content .= apply_filters( 'the_content', $post );
            $content .= '</div>';
        }
        $content .= '</div>';

        return $content;
    }

    
    public static function fau_person_shortlist($id, $showlist, $list=0, $showmail=0, $showtelefon=0) {
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        extract($fields);

        if (get_post_field('post_excerpt', $id)) {
            $excerpt = get_post_field('post_excerpt', $id);
        } else {
            $post = get_post($id);
            if ($post->post_content)
                $excerpt = wp_trim_excerpt($post->post_content);
        }

        if ($link) {
            $personlink = $link;
        } else {
            $personlink = get_permalink($id);
        }
        $content = '';
        
        $fullname = Data::fullname_output($id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, 1, 1, $alternateName);
        if ( $list==1 )
            $content .= '<div class="list">';
        $content .= '<span class="person-info">';
        $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
        if ( $telephone && $showtelefon && empty( $connection_only ) && $list==1 )
                $content .= ', <span class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', 'fau-person') . ': </span><span itemprop="telephone">' . $telephone . '</span></span>';
        if ( $email && $showmail && empty( $connection_only ) && $list==1  )
                $content .= ', <span class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', 'fau-person') . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></span>';    
        if ( $showlist && isset( $excerpt ) )
            $content .= "<br>" . $excerpt;
        $content .= '</span>';
        if ( $list==1 )
            $content .= '</div>';        
        return $content;
    }

    // von Widget, also Sidebar über Fakultätsthemes - Ansprechpartner: fau_person_sidebar($id, $title, list 0, inst 1, abtielung 1, posi 1, titel 1, suffix 1, addresse 1, raum 1, tele 1, fax 1, handy 0,                                                                  mail 1, url 1, mehrlink 0, kurzauszug 1, office 0, pubs 0, bild 1, via 0, hstart 3)
    // muss noch eingebaut werden: Wenn shortcode mit sidebar zeige bild ja und wo?     if (theme(FAU-*)  && template =~    else { Bild anzeigen }   if (theme(FAU-*)  && template =~( page.php || page-subnav.php ) && (not option(zeige bild in sidebar)   ) { Zeige kein Bild }   else {       if  template =~( page.php || page-subnav.php )   { binde Bild NACH dem Namen ein} else {    Bild vor dem Namen anzeigen }   }
    
    public static function fau_person_sidebar($id, $title, $showlist = 0, $showinstitution = 0, $showabteilung = 0, $showposition = 0, $showtitle = 0, $showsuffix = 0, $showaddress = 0, $showroom = 0, $showtelefon = 0, $showfax = 0, $showmobile = 0, $showmail = 0, $showwebsite = 0, $showlink = 0, $showdescription = 0, $showoffice = 0, $showpubs = 0, $showthumb = 0, $showvia = false, $hstart = 3) {
        //Überprüfung zur Bildplatzierung in der Sidebar, ob ein FAU-Theme gewählt wurde und welches Template gewählt ist

	$fautheme = \FAU_Person\Helper::isFAUTheme();
	$small_sidebar = false;
	if ($fautheme) {
            if( !is_page_template( array('page-templates/page-portal.php', 'page-templates/page-start.php', 'page-templates/page-start-sub.php'))  ) {
                $small_sidebar = true;
            }
	}
       

        if (!empty($id)) {
            $post = get_post($id);

            // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)            
            $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
            // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
            extract($fields);

	    
            if ($link) {
                $personlink = $link;
            } else {
                $personlink = get_permalink($id);
            }

            $fullname = Data::fullname_output($id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, $showtitle, $showsuffix, $alternateName);
            $contactpoint = Data::contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, $showaddress, $showroom, 'default' );
            $hoursavailable_output = Data::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text );
            
            if (has_post_thumbnail($id) && $showthumb) {
		
		$alttext = get_the_title($id);
		$alttext = esc_html($alttext);
		$altattr = 'alt="'.__('Weitere Informationen zu','fau').' '.$alttext.' '.__('aufrufen','fau').'"';


		$post_thumbnail_id = get_post_thumbnail_id( $id ); 
		$sliderimage = wp_get_attachment_image_src( $post_thumbnail_id, 'person-thumb' );
		$slidersrcset =  wp_get_attachment_image_srcset($post_thumbnail_id, 'person-thumb');

		$imagehtml = '<img src="'.$sliderimage[0].'" '.$altattr.' width="'.$sliderimage[1].'" height="'.$sliderimage[2].'"';
		if ($slidersrcset) {
		    $imagehtml .= 'srcset="'.$slidersrcset.'"';
		}
		$imagehtml .= '>';
		
		
		
		
                $sidebar_thumb = '<div class="span1 person-thumb" itemprop="image" aria-hidden="true">';
                $sidebar_thumb .= '<a href="' . $personlink . '">';
                $sidebar_thumb .= $imagehtml;
                $sidebar_thumb .= '</a>';
                $sidebar_thumb .= '</div>' . "\n";
            }
            
            $content = '<div class="person" itemscope itemtype="http://schema.org/Person">' . "\n";
            $content .= '<div class="side">';
                    
            if (!empty($title))
                $content .= '<h' . ($hstart-1) . ' class="small">' . $title . '</h' . ($hstart-1) . '>' . "\n";

            $content .= '<div class="row">' . "\n";
            
            if ( isset( $sidebar_thumb ) && !isset ( $small_sidebar ) ) {
                $content .= $sidebar_thumb;
            }            

            $content .= '<div class="span3 person-sidebar">' . "\n";
            $content .= '<h' . $hstart . '>';
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
            $content .= '</h' . $hstart . '>' . "\n";
            
            if ( isset( $sidebar_thumb ) && isset ( $small_sidebar ) ) {
                $content .= '</div>';
                $content .= $sidebar_thumb;
                $content .= '<div class="span3 person-sidebar">';
            }
            
            
            $content .= '<ul class="person-info">' . "\n";
            if ($jobTitle && $showposition)
                $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', 'fau-person') . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>' . "\n";
            if ($worksFor && $showinstitution)
                $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', 'fau-person') . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>' . "\n";
            //itemprop="department" entfernt da nicht zu Person zugehörig
            if ($department && $showabteilung)
                $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', 'fau-person') . ': </span>' . $department . '</li>' . "\n";
            if (!empty($contactpoint) && empty($connection_only))
                $content .= $contactpoint;
            if ($telephone && $showtelefon && empty($connection_only))
                $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', 'fau-person') . ': </span><span itemprop="telephone">' . $telephone . '</span></li>' . "\n";
            if ($mobilePhone && $showmobile && empty($connection_only))
                $content .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobil', 'fau-person') . ': </span><span itemprop="telephone">' . $mobilePhone . '</span></li>' . "\n";
            if ($faxNumber && $showfax && empty($connection_only))
                $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', 'fau-person') . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>' . "\n";
            if ($email && $showmail && empty($connection_only))
                $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', 'fau-person') . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>' . "\n";
            if ($url && $showwebsite)
                $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', 'fau-person') . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>' . "\n";
            if ($hoursavailable_output && $showoffice && empty($connection_only))
                $content .= $hoursavailable_output;
                //$content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', 'fau-person') . ': </span><div itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</div></li>';
            $content .= '</ul>' . "\n";
            if ($description && $showdescription)
                $content .= '<div class="person-info-description"><span class="screen-reader-text">' . __('Beschreibung', 'fau-person') . ': </span>' . $description . '</div>' . "\n";
	    
            if ($showvia || $connection_only)
                $content .= self::fau_person_connection($connection_text, $connection_options, $connections, $hstart);
	    
	    
            $content .= '</div>' . "\n";
            $content .= '</div>' . "\n";
            $content .= '</div>' . "\n";
            $content .= '</div>';
        }
        return $content;
    }

    public static function fau_person_connection($connection_text, $connection_options, $connections, $hstart) {

        $content = '';
        $contactlist = '';
        foreach ($connections as $key => $value) {
            extract($connections[$key]);
            $contactpoint = '';

            if ( $connection_options && in_array( 'contactPoint', $connection_options ) ) {
                $showaddress = 1;
                $showroom = 1;
            } else {
                $showaddress = 0;
                $showroom = 0;
            }

            $fullname = Data::fullname_output($nr, $honorificPrefix, $givenName, $familyName, $honorificSuffix, 1, 1, $alternateName);
            $contactpoint = Data::contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, $showaddress, $showroom, 'connection' );
            if( isset($hoursAvailable_text) ) {
                $hoursavailable_output = Data::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text );
            } else {
                $hoursavailable_output = Data::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, '' );
            }
            
            if ($link) {
                $personlink = $link;
            } else {
                $personlink = get_permalink($nr);
            }
            $contactlist .= '<li itemscope itemtype="http://schema.org/Person">';
            $contactlist .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($nr)) . '" href="' . $personlink . '">';
            $contactlist .= $fullname;
            $contactlist .= '</a>';

            if ($connection_options) {
                $cinfo = '';

                if ($telephone && in_array('telephone', $connection_options))
                    $cinfo .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', 'fau-person') . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
                if (isset($mobilePhone) && in_array('telephone', $connection_options))
                    $cinfo .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobiltelefon', 'fau-person') . ': </span><span itemprop="telephone">' . $mobilePhone . '</span></li>';
                if ($faxNumber && in_array('faxNumber', $connection_options))
                    $cinfo .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', 'fau-person') . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
                if ($email && in_array('email', $connection_options))
                    $cinfo .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', 'fau-person') . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
                if (!empty($contactpoint) && in_array('contactPoint', $connection_options))
                    $cinfo .= $contactpoint;
                if ($hoursavailable_output && in_array('hoursAvailable', $connection_options))
                    //$cinfo .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', 'fau-person') . ': </span><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</span></li>';
                    $cinfo .= $hoursavailable_output;
                if (!empty($cinfo)) {
                    $contactlist .= '<ul class="person-info">';
                    $contactlist .= $cinfo;
                    $contactlist .= '</ul>';
                }
            }
            $contactlist .= '</li>';
        }

        if (!empty($contactlist)) {
            $content = '<div class="connection">';
            if ($connection_text) {
                $content .= '<h' . ($hstart+1) . '>' . $connection_text . '</h' . ($hstart+1) . '>';
            }
            $content .= '<ul class="connection-list">';
            $content .= $contactlist;
            $content .= '</ul>';
            $content .= '</div>';
        }

        return $content;
    }
    
    
    public static function create_fau_standort($id, $showfields, $titletag = 'h2') {
	if (!isset($id)) {
	    return;
	}
	$id = sanitize_key($id);
	if (!is_array($showfields)) {
	    return;
	}
	$fields = sync_helper::get_fields( $id, get_post_meta($id, 'fau_person_univis_id', true), 0 );
	$permalink = get_permalink( $id );
	
	if (isset($showfields['kurzbeschreibung']) && ($showfields['kurzbeschreibung'])) {
	    $excerpt = get_post_field( 'post_excerpt', $id );         
	    $fields['description'] = $excerpt;
	}
	if (isset($showfields['adresse']) && ($showfields['adresse'])) {
	    $schemaadr = true;
	} else {
	    $schemaadr = false;
	}
	$schema = Schema::create_Place($fields,'location','','div',true, $schemaadr);
	
	$title = '';
	if (isset($showfields['title']) && ($showfields['title'])) {	
	    if( !empty( get_the_title($id) ) ) {                                                
		$title .= get_the_title($id);
	    }       
	}
                    
	$content = '<div class="fau-person standort" itemscope itemtype="http://schema.org/Organization">';		
	if( !empty( $title ) ) {                                                
              $content .= '<'.$titletag.' itemprop="name">';
	     if (isset($showfields['permalink']) && ($showfields['permalink']) && ($permalink)) { 
		 $content .= '<a href="'.$permalink.'">';
	     }
	     $content .= $title;
	      if (isset($showfields['permalink']) && ($showfields['permalink']) && ($permalink)) { 
		   $content .= '</a>'; 
	      }
	     $content .= '</'.$titletag.'>';
         }

	if( !empty( $schema ) ) {            
	   $content .=  $schema;
	}          
	 
	 
	if (isset($showfields['bild']) && ($showfields['bild']) && has_post_thumbnail($id)) {
	    $content .= '<div class="standort-image" itemprop="image" aria-hidden="true">';	
	    $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $permalink . '">';
	    $content .= get_the_post_thumbnail($id);   
	    $content .= '</a>';
	    $content .= '</div>';
	}

	if (isset($showfields['content']) && ($showfields['content'])) {
	    $post = get_post( $id );
	    if ( $post->post_content )      {
		$content .= '<div class="content">'.$post->post_content.'</div>';
	    }
	}
	$content .= '</div>';
	return $content;
    }
    
     public static function create_fau_standort_plain($id, $showfields, $titletag = '') {
	if (!isset($id)) {
	    return;
	}
	$id = sanitize_key($id);
	if (!is_array($showfields)) {
	    return;
	}
	$fields = sync_helper::get_fields( $id, get_post_meta($id, 'fau_person_univis_id', true), 0 );
	$permalink = get_permalink( $id );
	
	if (isset($showfields['kurzbeschreibung']) && ($showfields['kurzbeschreibung'])) {
	    $excerpt = get_post_field( 'post_excerpt', $id );         
	    $fields['description'] = $excerpt;
	}
	if (isset($showfields['adresse']) && ($showfields['adresse'])) {
	    $schemaadr = true;
	} else {
	    $schemaadr = false;
	}
	$schema = Schema::create_Place($fields,'','','span',false, $schemaadr);
	
	$title = '';
	if (isset($showfields['title']) && ($showfields['title'])) {	
	    if( !empty( get_the_title($id) ) ) {                                                
		$title .= get_the_title($id);
	    }       
	}
                    
	$content = '';		
	if( !empty( $title ) ) {      
	    if (!empty($titletag)) {
		$content .= '<'.$titletag.'>';
	    }
	     if (isset($showfields['permalink']) && ($showfields['permalink']) && ($permalink)) { 
		 $content .= '<a href="'.$permalink.'">';
	     }
	     $content .= $title;
	      if (isset($showfields['permalink']) && ($showfields['permalink']) && ($permalink)) { 
		   $content .= '</a>'; 
	      }
	      if (!empty($titletag)) {
		$content .= '</'.$titletag.'>';
	      }
         }

	if( !empty( $schema ) ) {            
	   $content .=  $schema;
	}          
	 
	 
	if (isset($showfields['bild']) && ($showfields['bild']) && has_post_thumbnail($id)) {
	    $content .= '<div class="standort-image" aria-hidden="true">';	
	    $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $permalink . '">';
	    $content .= get_the_post_thumbnail($id);   
	    $content .= '</a>';
	    $content .= '</div>';
	}

	if (isset($showfields['content']) && ($showfields['content'])) {
	    $post = get_post( $id );
	    if ( $post->post_content )      {
		$content .= '<div class="content">'.$post->post_content.'</div>';
	    }
	}
	return $content;
    }   

}