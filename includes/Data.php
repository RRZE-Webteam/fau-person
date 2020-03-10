<?php

namespace FAU_Person;
use \RRZE\Lib\UnivIS;
use \RRZE\Lib\UnivIS\sync_helper;

defined('ABSPATH') || exit;


class Data {
    
    
    private function get_viewsettings() {
	$settings = new Settings(PLUGIN_FILE);
	$settings->onLoaded();
	$options = $settings->options;
	    
	$viewopt = array();
	    
	foreach ($options as $section => $field) {
	    if (substr($section,0,9) === 'constants') {
		$keyname = preg_replace('/constants_/i','',$section);
		$viewopt[$keyname] = $options[$section];
	    }
	} 
	return $viewopt;
    }

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
    public static function get_more_link($targeturl, $linktitle = '', $class = 'person-info-more', $withdiv = true) {
	if (!isset($targeturl)) {
	    return;
	}
	
	$viewopts = self::get_viewsettings();
	$res = '';

	if ($withdiv) {
	    $res .= '<div ';
	    if (!empty($class)) {
		$res .= 'class="'.esc_attr($class).'"';
	    }
	    $res .= '>';
	}
	$res .= '<a href="'.esc_url($targeturl).'"';
	if ($withdiv==false) {
	    if (!empty($class)) {
		$res .= ' class="'.esc_attr($class).'"';
	    }
	}
	if (!empty($linktitle)) {
	    $res .= ' title="'.esc_attr($linktitle).'"';
	}
	$res .= '>';
	if ((isset($viewopts['view_kontakt_linktext'])) && (!empty($viewopts['view_kontakt_linktext']))) {
	     $res .= esc_html($viewopts['view_kontakt_linktext']);
	} else {
	    $res .= __('Mehr', 'fau-person') . ' ›';
	}
	$res .= '</a>';
	if ($withdiv) {
	    $res .= '</div>';
	}
	return $res;
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
    

    public function create_kontakt_image($id = 0, $size = 'person-thumb-page', $class = '', $defaultimage = false, $showlink = false, $linkttitle) {
	if ($id==0) {
	    return;
	}
	$res = '';
	$imagedata = array();
	$alttext = get_the_title($id);
	$alttext = esc_html($alttext);
	$targetlink = '';

	$imagedata['alt'] = $alttext;
	if ($showlink) {
	    $targetlink = get_permalink($id);
	}
	
	
	if (has_post_thumbnail($id)) {	
	    $image_id = get_post_thumbnail_id( $id ); 		
	    $imga = wp_get_attachment_image_src($image_id, $size);
	    if (is_array($imga)) {
		$imgsrcset =  wp_get_attachment_image_srcset($image_id, $size);
		$imgsrcsizes = wp_get_attachment_image_sizes($image_id, $size);
		$imagedata['src'] = $imga[0];
		$imagedata['width'] = $imga[1];
		$imagedata['height'] = $imga[2];
		$imagedata['srcset'] = $imgsrcset;
		$imagedata['sizes'] = $imgsrcsizes;
	    }
         }  elseif ($defaultimage) {
	    $type = get_post_meta($id, 'fau_person_typ', true);
	    if (defined(PLUGIN_FILE)) {
		     $pluginfile = PLUGIN_FILE;
	    } else {
	        $pluginfile = __DIR__;
	    }
	    if ($type == 'realmale') {
		$bild = plugin_dir_url($pluginfile) . 'images/platzhalter-mann.png';
	    } elseif ($type == 'realfemale') {
		$bild = plugin_dir_url($pluginfile ) . 'images/platzhalter-frau.png';
	    } elseif ($type == 'einrichtung') {
		$bild = plugin_dir_url($pluginfile ) . 'images/platzhalter-organisation.png';
	    } else {
		$bild = plugin_dir_url($pluginfile ) . 'images/platzhalter-unisex.png';
	    }
             if ($bild) {
		$imagedata['src'] = $bild;
		$imagedata['width'] = 90;
		$imagedata['height'] = 120;
	    }
                    
	 }         
	$res = Schema::create_Image($imagedata, 'figure', 'image', $class, true, $targetlink, $linkttitle);
         return $res;
    }
    
    
    
    
    public static function fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex = 0, $noborder, $hstart, $bg_color) {
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
        extract($fields);
        if ($showvia !== 0 && !empty($connections))
            $showvia = 1;
        if ($showvia === 0 && !empty($connection_only))
            $connection_only = '';
        
        Main::enqueueForeignThemes();
	$viewopts = self::get_viewsettings();
	   

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
        
	
	$data = $fields;
	$surroundingtag = 'span';
	if ($showtitle==false) {
	    $data['honorificPrefix'] = '';
	}
	if ($showsuffix==false) {
	    $data['honorificSuffix'] = '';
	}
	if ($personlink) {
	    $data['url'] = $personlink;
	    $surroundingtag = 'a';
	}
	if (!empty(get_the_title($id))) {
             $data['name'] = get_the_title($id);
         }
	$fullname = Schema::create_Name($data,'name','',$surroundingtag);
        $hoursavailable_output  = Schema::create_ContactPoint($data);
	
	
        $content = '<div class="fau-person person content-person' . $noborder . $bg_color . '" itemscope itemtype="http://schema.org/Person">';
        if ($compactindex) {
            $content .= '<div class="compactindex">';
	}
        $content .= '<div class="row">';

        if ($showthumb) {
	    $content .= Data::create_kontakt_image($id, 'person-thumb-bigger', "person-thumb", true, true,'');	    
	    $content .= '<div class="person-default-thumb">';
	} else {
	    $content .= '<div class="person-default">';
	}

        $content .= '<h' . $hstart . '>';
        $content .= $fullname;
        $content .= '</h' . $hstart . '>';
	
	
	$datacontent = '';	
	
	if (isset($fields['jobTitle']) && (!empty($fields['jobTitle']))) {
             $datacontent .= '<span class="person-info-position" itemprop="jobTitle">' . $fields['jobTitle'] . '</span><br>';
	}
	$orgadata = array();
	if (isset($fields['worksFor']) && (!empty($fields['worksFor']))) { 
	    $orgadata['name'] = $fields['worksFor'];
	}
	if (isset($fields['department']) && (!empty($fields['department']))) { 
	    $orgadata['department'] = $fields['department'];
	}
	$datacontent .= Schema::create_Organization($orgadata,'p','worksFor','',false,false,false);
	
	
	if (isset($fields['connection_only']) && $fields['connection_only']==false) {
	    $adresse = Schema::create_PostalAdress($fields, 'address','', 'address', true);
	    if (isset($adresse)) {
		$datacontent .= $adresse;
	    } 
	    $datacontent .= Schema::create_contactpointlist($fields, 'ul', '', 'contactlist', 'li',$viewopts);
	      
	}
	if (!empty($datacontent)) {
	     $content .= '<div class="person-info">';
	     $content .= $datacontent;
	     $content .= '</div>';
	}
        

        if ((!empty($connection_text) || !empty($connection_options) || !empty($connections)) && $showvia === 1)
            $content .= self::fau_person_connection($connection_text, $connection_options, $connections, $hstart);

        if (($showoffice && $hoursavailable_output && empty($connection_only)) 
	    || ($showlist && isset($excerpt)) 
	    || (($showsidebar || $extended) && (!empty($description))) 
	    || ($showlink && $personlink)) {


            if (!$compactindex) {
                $content .= '</div><div class="person-default-more">';
	    }
            if ($showoffice && $hoursavailable_output && empty($connection_only)) {
                $content .= '<ul class="person-info">';
                $content .= $hoursavailable_output;
                $content .= '</ul>';
            }

            if ($showlist && isset($excerpt) && (!empty($excerpt))) {
                $content .= '<div class="person-info-description"><p>' . $excerpt . '</p></div>';
	    }
            if (($showdescription || $extended || $showsidebar) && (!empty($description))) {
                $content .= '<div class="person-info-description"><span class="screen-reader-text">' . __('Beschreibung', 'fau-person') . ': </span>' . $description . '</div>';
	    }
            if ($showlink && $personlink) {
		$content .= self::get_more_link($personlink);
            }
        }

        $content .= '</div>';
        $content .= '</div> <!-- /row-->';

        if ($compactindex) {
            $content .= '</div>';   // ende div class compactindex
	}
        $content .= '</div>';
        return $content;
    }

    public static function fau_person_page($id, $is_shortcode = false, $showname = false) {
        $content = '<div class="fau-person person page" itemscope itemtype="http://schema.org/Person">';
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
        extract($fields);

        Main::enqueueForeignThemes();
        $viewopts = self::get_viewsettings();
	   	
        if ( !$is_shortcode || $showname ) {
	    $content .= Schema::create_Name($fields,'name','','h2');
        }
	
        
	$content .= '<div class="person-meta">';
	
	$content .= Data::create_kontakt_image($id, 'person-thumb-page', "person-image alignright", false, false,'');	    

         $content .= '<div class="person-info">';
         if (isset($fields['jobTitle']) && (!empty($fields['jobTitle']))) {
             $content .= '<span class="person-info-position" itemprop="jobTitle">' . $fields['jobTitle'] . '</span><br>';
	}
	$orgadata = array();
	if (isset($fields['worksFor']) && (!empty($fields['worksFor']))) { 
	    $orgadata['name'] = $fields['worksFor'];
	}
	if (isset($fields['department']) && (!empty($fields['department']))) { 
	    $orgadata['department'] = $fields['department'];
	}
	$content .= Schema::create_Organization($orgadata,'p','worksFor','',false,false,false);
	
	
	if (isset($fields['connection_only']) && $fields['connection_only']==false) {
	    $adresse = Schema::create_PostalAdress($fields, 'address','', 'address', true);
	    if (isset($adresse)) {
		$content .= $adresse;
	    } 

	    $content .= Schema::create_contactpointlist($fields, 'ul', '', 'contactlist', 'li',$viewopts);
	      
	}
	if ($fields['connection_only']==false) {
	    $content .=   Schema::create_ContactPoint($fields);
	}
           

	$content .= '</div>';
	$content .= '</div>';

        if (!empty($connection_text) || !empty($connection_options) || !empty($connections)) {
            $content .= self::fau_person_connection($connection_text, $connection_options, $connections, 2);
	}

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

    
    public static function fau_person_shortlist($id, $showdesc = false, $list = false, $showmail = false, $showtelefon = false) {
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);

	$viewopts = self::get_viewsettings();
	 
	    
        if ($fields['link']) {
            $personlink = $fields['link'];
        } else {
            $personlink = get_permalink($id);
        }
        $content = '';
        
	Main::enqueueForeignThemes();

	$data = $fields;
	$surroundingtag = 'span';
	if ($fields['showtitle']==false) {
	    $data['honorificPrefix'] = '';
	}
	if ($fields['showsuffix']==false) {
	    $data['honorificSuffix'] = '';
	}
	if ($personlink) {
	    $data['url'] = $personlink;
	    $surroundingtag = 'a';
	}
	if (!empty(get_the_title($id))) {
             $data['name'] = get_the_title($id);
         }
        if ( $list ) {
            $content .= '<div class="list">';
	}
	if ($showmail) {
	    $fielddata['email'] = $fields['email'];
	}
	if ($showtelefon) {
	    $fielddata['telephone'] = $fields['telephone'];
	}


	
        $content .= '<span class="person-info" itemscope itemtype="http://schema.org/Person">';
        $content .=  Schema::create_Name($data,'name','',$surroundingtag);
		
	if (isset($fields['connection_only']) && $fields['connection_only']==false && $list) {
	    $content .= Schema::create_contactpointlist($fielddata, 'span', '', 'person-info', 'span',$viewopts);
	}
	
	
	if ($showdesc) {
		if (get_post_field('post_excerpt', $id)) {
		    $excerpt = get_post_field('post_excerpt', $id);
		} else {
		    $post = get_post($id);
		    if ($post->post_content) {
			$excerpt = wp_trim_excerpt($post->post_content);
		    }
		}
		if (!empty($excerpt)) {
		    $content .= "<br>" . $excerpt;
		}
	}


        $content .= '</span>';

        if ( $list ) {
            $content .= '</div>';        
	}
        return $content;
    }

   
    
    public static function fau_person_sidebar($id, $title, $showlist = 0, $showinstitution = 0, $showabteilung = 0, $showposition = 0, $showtitle = 0, $showsuffix = 0, $showaddress = 0, $showroom = 0, $showtelefon = 0, $showfax = 0, $showmobile = 0, $showmail = 0, $showwebsite = 0, $showlink = 0, $showdescription = 0, $showoffice = 0, $showthumb = 0, $showvia = false, $hstart = 3) {
        if (!empty($id)) {
            $post = get_post($id);

            $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
            // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
            extract($fields);
	    $viewopts = self::get_viewsettings();
	   
	    Main::enqueueForeignThemes();

             if ($fields['link']) {
                $personlink = $fields['link'];
             } else {
                $personlink = get_permalink($id);
             }
	    $data = $fields;
	    $surroundingtag = 'span';
	    

	    if ($showlink && $personlink) {
		$data['url'] = $personlink;
		$surroundingtag = 'a';
	    }
	    if (!empty(get_the_title($id))) {
		 $data['name'] = get_the_title($id);
	     }
	    $fullname = Schema::create_Name($data,'name','',$surroundingtag);


             $content = '<div class="fau-person person sidebar" itemscope itemtype="http://schema.org/Person">' . "\n";
             $content .= '<div class="side">';         
	
	    
	    if ($showtitle && (!empty($title))) {
                $content .= '<h' . ($hstart-1) . ' class="small">' . $title . '</h' . ($hstart-1) . '>' . "\n";
	    }
	    $content .= '<div class="row">' . "\n";

	    if (has_post_thumbnail($id) && $showthumb) {
		
		$alttext = get_the_title($id);
		$alttext = esc_html($alttext);
		$targettitle = $targetlink = '';
		
		$imagedata['alt'] = $alttext;
		if ($showlink) {
		    $targetlink = $personlink;
		}
		$image_id = get_post_thumbnail_id( $id ); 
		$size = 'person-thumb-bigger';  // 'person-thumb-page', person-thumb-bigger
		
		$imga = wp_get_attachment_image_src($image_id, $size);
		if (is_array($imga)) {
		    $imgsrcset =  wp_get_attachment_image_srcset($image_id, $size);
		    $imgsrcsizes = wp_get_attachment_image_sizes($image_id, $size);
		    $imagedata['src'] = $imga[0];
		    $imagedata['width'] = $imga[1];
		    $imagedata['height'] = $imga[2];
		    $imagedata['srcset'] = $imgsrcset;
		    $imagedata['sizes'] = $imgsrcsizes;
		}
                $content .= Schema::create_Image($imagedata, 'figure', 'image', 'person-thumb', true, $targetlink, $targettitle);
             }            

            $content .= '<div class="person-sidebar">' . "\n";
            $content .= '<h' . $hstart . '>';
            $content .= $fullname;
            $content .= '</h' . $hstart . '>' . "\n";
            
             $content .= '<div class="person-info">';
	     if ($showposition && isset($fields['jobTitle']) && (!empty($fields['jobTitle']))) {
		 $content .= '<span class="person-info-position" itemprop="jobTitle">' . $fields['jobTitle'] . '</span><br>';
	    }
	    $orgadata = array();
	    if ($showinstitution && isset($fields['worksFor']) && (!empty($fields['worksFor']))) { 
		$orgadata['name'] = $fields['worksFor'];
	    }
	    if ($showabteilung && isset($fields['department']) && (!empty($fields['department']))) { 
		$orgadata['department'] = $fields['department'];
	    }
	    $content .= Schema::create_Organization($orgadata,'p','worksFor','',false,false,false);



	    if ($showroom==false) {
		$fields['workLocation'] = '';
	    } 
	    if ($showtelefon==false) {
		$fields['telephone'] = '';
	    } 
	    if ($showfax==false) {
		$fields['faxNumber'] = '';
	    } 
	    if ($showmobile==false) {
		$fields['mobilePhone'] = '';
	    } 
	    if ($showmail==false) {
		$fields['email'] = '';
	    } 
	    if ($showwebsite==false) {
		$fields['url'] = '';
	    } 
	    if ($showdescription==false) {
		$fields['description'] = '';
	    } 

	    if (isset($fields['connection_only']) && $fields['connection_only']==false) {
		$adresse = Schema::create_PostalAdress($fields, 'address','', 'address', true);
		if ($showaddress && isset($adresse)) {
		    $content .= $adresse;
		} 
		$content .= Schema::create_contactpointlist($fields, 'ul', '', 'contactlist', 'li',$viewopts); 
	    }

	    if ($showoffice && $fields['connection_only']==false) {
		$sprechzeitentitletag = 'h'.($hstart+1);
		$content .=   Schema::create_ContactPoint($fields,'div','contactPoint','',$sprechzeitentitletag);
	    }

	    $content .= '</div>';
	

            if ($description && $showdescription) {
                $content .= '<div class="person-info-description"><span class="screen-reader-text">' . __('Beschreibung', 'fau-person') . ': </span>' . $description . '</div>' . "\n";
	    }
            if ($showvia || $connection_only) {
                $content .= self::fau_person_connection($connection_text, $connection_options, $connections, $hstart);
	    }
	    
            $content .= '</div><!-- /sidebar -->';
            $content .= '</div><!-- /row -->';
            $content .= '</div><!-- /side -->';
            $content .= '</div><!-- /fau-person -->';
        }
        return $content;
    }

    public static function fau_person_connection($connection_text, $connection_options, $connections, $hstart) {
        $content = '';
        $contactlist = '';
	$viewopts = self::get_viewsettings();
	

	
        foreach ($connections as $key => $value) {
            extract($connections[$key]);
	   
	    $data = $connections[$key];
	    if (isset($connection_options) && is_array($connection_options)) {
		foreach ($connection_options as $i => $key) {
		    $par[$key] = true;
		}
		if (!isset($par['contactPoint'])) {
		    $data['streetAddress'] = '';
		    $data['addressLocality'] = '';
		    $data['postalCode'] = '';
		    $data['addressRegion']= '';
		    $data['addressCountry'] = '';
		    $data['workLocation'] = '';
		}
		if (!isset($par['hoursAvailable'])) {
		    $data['hoursAvailable']= '';
		    $data['hoursAvailable_group']= '';
		    $data['hoursAvailable_text'] = '';
		}
		if (!isset($par['telephone'])) {
		     $data['telephone'] = '';
		}
		if (!isset($par['faxNumber'])) {
		    $data['faxNumber'] = '';
		} 
		if (!isset($par['email'])) {
		    $data['email'] = '';
		}
	    }
             $contactpoint = '';
	    $surroundingtag = 'span';
	    
	    $contactlist .= '<li itemscope itemtype="http://schema.org/Person">';
	    
            if ($data['link']) {
                $personlink = $data['link'];
            } else {
                $personlink = get_permalink($data['nr']);
            }
	    $oldurl  = $data['url'];
	    if ($personlink) {

		$data['url'] = $personlink;
		$surroundingtag = 'a';
	    }
	    if (!empty(get_the_title($data['nr']))) {
		 $data['name'] = get_the_title($data['nr']);
	     }
	    $fullname = Schema::create_Name($data,'name','',$surroundingtag);
	    $data['url'] = $oldurl;
	    $contactlist .= $fullname;
	     
	    $contactlist .= Schema::create_PostalAdress($data, 'address','', 'address', true);
	    $contactlist .= Schema::create_contactpointlist($data, 'ul', '', 'contactlist', 'li',$viewopts);
	    $contactlist .= Schema::create_ContactPoint($data);
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
	    $content .= Data::create_kontakt_image($id, 'full', "standort-image", false, false,'');	    
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
	    $content .= Data::create_kontakt_image($id, 'full', "standort-image", false, false,'');	  
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