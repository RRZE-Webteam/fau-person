<?php

namespace FAU_Person;
use RRZE\Lib\UnivIS\Data as UnivIS_Data;
use RRZE\Lib\UnivIS\Config;
use RRZE\Lib\UnivIS\Sanitizer;

defined('ABSPATH') || exit;


class Data {
    
    
    private static function get_viewsettings($lookup = 'constants') {
	$settings = new Settings(PLUGIN_FILE);
	$settings->onLoaded();
	$options = $settings->options;
	    
	$viewopt = array();
	    
	foreach ($options as $section => $field) {
	    if ($lookup == 'sidebar') {
		if (substr($section,0,7) === 'sidebar') {
		    $keyname = preg_replace('/sidebar_/i','',$section);
		    $viewopt[$keyname] = $options[$section];
		}
	    } else {
		if (substr($section,0,9) === 'constants') {
		    $keyname = preg_replace('/constants_/i','',$section);
		    $viewopt[$keyname] = $options[$section];
		}
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
                $standort_default = self::get_fields_standort($id, $standort_id, 1);
                return $standort_default;        
            } else {
		return self::get_fields_standort(0,0,0);
	    }
    }
    
    // Sortierung eines Arrays mit Objekten (z.B. bei einer Kategorie) alphabetisch nach Titel oder Nachname, je nach Typ
    public static function sort_person_posts( $personlist, $sorttype = 'name', $order = 'asc' ) {
        if ( is_array( $personlist ) ) {
            foreach( $personlist as $key => $value) {
                $personlist[$key] = (array) $personlist[$key];
                // Bei Personen Prüfung, ob Nachname im Feld eingetragen ist (ggf. aus UnivIS), wenn nicht letztes Wort von Titel als Nachname angenommen
                switch ( get_post_meta( $personlist[$key]['ID'], 'fau_person_typ', true ) ) {
                    case 'realperson':
                    case 'realmale':
                    case 'realfemale':
                        $fields = self::get_fields($personlist[$key]['ID'], get_post_meta($personlist[$key]['ID'], 'fau_person_univis_id', true), 0);
                
                        if( !empty( $fields['familyName'] ) ) {
                            $name = $fields['familyName'];
			    
                            if ((!empty( $fields['givenName']))  && ($sorttype == 'name')) {
                                $name = $fields['familyName'] . ', ' . $fields['givenName'];
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
	    if ($order=='desc') {
		echo "ORDER DESC";
		  array_multisort($temp, SORT_DESC, SORT_STRING, $personlist);
	    } else {
		  array_multisort($temp, SORT_ASC, SORT_STRING, $personlist);
	    }
          
            return $personlist;  
        }
    }
    

    public static function create_kontakt_image($id = 0, $size = 'person-thumb-page', $class = '', $defaultimage = false, $showlink = false, $linkttitle) {
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
    
 
    
    public static function fau_person_markup($id, $display = array(), $arguments = array()) {
       	if ($id == 0) {
	    return;
	}
	$fields = self::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
	$viewopts = self::get_viewsettings();
	 
	$content = '';
	Main::enqueueForeignThemes();
	
	$fields['permalink'] = get_permalink($id);
	$fields['name'] = get_the_title($id);
	
	if (get_post_field('post_excerpt', $id)) {
	    $fields['description']  = get_post_field('post_excerpt', $id);
	} else {
	    $post = get_post($id);
	    if ($post->post_content) {
		$fields['description']  = wp_trim_excerpt($post->post_content);
	    }
	}

	$data = self::filter_fields($fields, $display);
	

	
	$fullname = Schema::create_Name($data,'name','','a',false,$viewopts);
         $hoursavailable_output  = Schema::create_ContactPoint($data);


	$class = 'fau-person person content-person';
	if ((isset($arguments['class'])) && (!empty($arguments['class']))) {
	    $class .= ' '.esc_attr($arguments['class']);
	}
	if (isset($display['border'])) {
	    if ($display['border']) {
		$class .= ' border';
	    } else {
		$class .= ' noborder';
	    }
	}
	if (isset($arguments['background']) && (!empty($arguments['background']))) {
	    $bg_array = array('grau', 'fau', 'phil', 'med', 'nat', 'tf', 'rw');
	    if (in_array($arguments['background'], $bg_array)) {
		$class .=' background-' . esc_attr($arguments['background']);
	    }
	}
	

	 $content .= '<div class="'.$class.'" itemscope itemtype="http://schema.org/Person">' . "\n";

	     
         if (isset($arguments['format']) && $arguments['format'] =='kompakt') {
            $content .= '<div class="compactindex">';
	}
        $content .= '<div class="row">';

        if (isset($display['bild']) && (!empty($display['bild']))) {
	    $content .= Data::create_kontakt_image($id, 'person-thumb-bigger', "person-thumb", true, true,'');	    
	    $content .= '<div class="person-default-thumb">';
	} else {
	    $content .= '<div class="person-default">';
	}
	if ($arguments['hstart']) {
	    $hstart = intval($arguments['hstart']);
	} else {
	    $hstart = 2;
	}
	if (($hstart <1) || ($hstart > 6)) {
	    $hstart = 2;
         }
	 
        $content .= '<h' . $hstart . '>';
        $content .= $fullname;
        $content .= '</h' . $hstart . '>';
	
	
	$datacontent = '';	
	
	if (isset($data['jobTitle']) && (!empty($data['jobTitle']))) {
             $datacontent .= '<span class="person-info-position" itemprop="jobTitle">' . $data['jobTitle'] . '</span><br>';
	}
	$orgadata = array();
	if (isset($data['worksFor']) && (!empty($data['worksFor']))) { 
	    $orgadata['name'] = $data['worksFor'];
	}
	if (isset($data['department']) && (!empty($data['department']))) { 
	    $orgadata['department'] = $data['department'];
	}
	$datacontent .= Schema::create_Organization($orgadata,'p','worksFor','',false,false,false);
	
	
	if (isset($data['connection_only']) && $data['connection_only']==false) {
	    $adresse = Schema::create_PostalAdress($data, 'address','', 'address', true);
	    if (isset($adresse)) {
		$datacontent .= $adresse;
	    } 
	    $datacontent .= Schema::create_contactpointlist($data, 'ul', '', 'contactlist', 'li',$viewopts);
	      
	}
	if (!empty($datacontent)) {
	     $content .= '<div class="person-info">';
	     $content .= $datacontent;
	     $content .= '</div>';
	}
        
	    
	if ((!empty($data['connection_text']) || !empty($data['connection_options']) || !empty($data['connections'])) && isset($display['ansprechpartner']) && $display['ansprechpartner']==true) {
	    $content .= self::fau_person_connection($data['connection_text'], $data['connection_options'], $data['connections'], $hstart);
	}

	
	 if (!isset($arguments['format']) || $arguments['format'] !=='kompakt') {
                $content .= '</div><div class="person-default-more">';
	}
	    
	    
	    
	if (isset($data['connection_only']) && ($data['connection_only']==false) && isset($display['hoursAvailable']) && ($display['hoursAvailable'])) {
	    $content .=   Schema::create_ContactPoint($data);
	}
	
	if (!empty($data['description']) && isset($display['kurzbeschreibung']) && (!empty($display['kurzbeschreibung']))) {
             $content .= '<div class="person-info-description" itemprop="description"><p>' . $data['description'] . '</p></div>' . "\n";
	}



            if (isset($display['link']) && (!empty($display['link']))) {
		$content .= self::get_more_link($data['permalink'] );
            }
      

        $content .= '</div>';
        $content .= '</div> <!-- /row-->';

        if (isset($arguments['format']) && $arguments['format'] =='kompakt') {
            $content .= '</div>';   // ende div class compactindex
	}
        $content .= '</div>';
        return $content;
    }

    public static function fau_person_page($id, $display = array(), $arguments= array(), $is_shortcode = false) {
    
        $fields = self::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);

        Main::enqueueForeignThemes();
        $viewopts = self::get_viewsettings();
	   	
	$data = self::filter_fields($fields, $display);


	if ((isset($arguments['hstart'])) && (!empty($arguments['hstart']))) {
	    $hstart = intval($arguments['hstart']);
	} else {
	    $hstart = 2;
	}
	if (($hstart <1) || ($hstart > 6)) {
	    $hstart = 2;
         }
	 
	
	$class = 'fau-person person page';
	if ((isset($arguments['class'])) && (!empty($arguments['class']))) {
	    $class .= ' '.esc_attr($arguments['class']);
	}
	if (isset($display['border'])) {
	    if ($display['border']) {
		$class .= ' border';
	    } else {
		$class .= ' noborder';
	    }
	}
	if (isset($arguments['background']) && (!empty($arguments['background']))) {
	    $bg_array = array('grau', 'fau', 'phil', 'med', 'nat', 'tf', 'rw');
	    if (in_array($arguments['background'], $bg_array)) {
		$class .=' background-' . esc_attr($arguments['background']);
	    }
	}




	$content = '<div class="'.$class.'" itemscope itemtype="http://schema.org/Person">';
	     
	
	if ( $is_shortcode) {
	    $content .= Schema::create_Name($data,'name','','h'.$hstart,false,$viewopts);
         }
	$content .= '<div class="person-meta">';
	
	$content .= Data::create_kontakt_image($id, 'person-thumb-page', "person-image alignright", false, false,'');	    

         $content .= '<div class="person-info">';
         if (isset($data['jobTitle']) && (!empty($data['jobTitle']))) {
             $content .= '<span class="person-info-position" itemprop="jobTitle">' . $data['jobTitle'] . '</span><br>';
	}
	$orgadata = array();
	if (isset($data['worksFor']) && (!empty($data['worksFor']))) { 
	    $orgadata['name'] = $data['worksFor'];
	}
	if (isset($data['department']) && (!empty($data['department']))) { 
	    $orgadata['department'] = $data['department'];
	}
	$content .= Schema::create_Organization($orgadata,'p','worksFor','',false,false,false);
	
	
	if (isset($data['connection_only']) && $data['connection_only']==false) {
	    $adresse = Schema::create_PostalAdress($data, 'address','', 'address', true);
	    if (isset($adresse)) {
		$content .= $adresse;
	    } 

	    $content .= Schema::create_contactpointlist($data, 'ul', '', 'contactlist', 'li',$viewopts);
	      
	}
	 if (($data['connection_only']==false) && (isset($display['hoursAvailable'])) && $display['hoursAvailable']==true) {
	    $content .=   Schema::create_ContactPoint($data);
	}
           

	$content .= '</div>';
	$content .= '</div>';

        if (!empty($data['connection_text']) || !empty($data['connection_options']) || !empty($data['connections'])) {
            $content .= self::fau_person_connection($data['connection_text'], $data['connection_options'], $data['connections'], $hstart);
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

    public static function fau_person_tablerow($id = 0, $display = array(), $arguments = array()) {
	if ($id == 0) {
	    return;
	}
	$fields = self::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
	$viewopts = self::get_viewsettings();
	 
	$content = '';
	Main::enqueueForeignThemes();
	
	$fields['permalink'] = get_permalink($id);
	$fields['name'] = get_the_title($id);
	
	if (get_post_field('post_excerpt', $id)) {
	    $fields['description']  = get_post_field('post_excerpt', $id);
	} else {
	    $post = get_post($id);
	    if ($post->post_content) {
		$fields['description']  = wp_trim_excerpt($post->post_content);
	    }
	}

	$data = self::filter_fields($fields, $display);

         $content .= '<tr class="person-info" itemscope itemtype="http://schema.org/Person">';
         $content .= '<td>'.Schema::create_Name($data,'name','','a',false,$viewopts).'</td>';
	$content .=  Schema::create_contactpointlist($data, '', '', '', 'td', $viewopts,true);
	
	if ((isset($data['description'])) && (!empty($data['description'])) && isset($display['kurzbeschreibung']) && $display['kurzbeschreibung'] ) {	
		$content .= "<td>" . $data['description'].'</td>';
	}
	
	if (isset($data['connection_only']) && ($data['connection_only']==false) && isset($display['hoursAvailable']) && ($display['hoursAvailable'])) {
	    $content .= '<td>'.  Schema::create_ContactPoint($data).'</td>';
	}
        $content .= '</tr>';

        return $content;	
	
    }
    
    
    
    public static function fau_person_shortlist($id, $display = array(), $arguments = array()) {
	if ($id == 0) {
	    return;
	}
         $fields = self::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
	$viewopts = self::get_viewsettings();
	 

        $content = ''; 
	Main::enqueueForeignThemes();


	$fields['permalink'] = get_permalink($id);
	$fields['name'] = get_the_title($id);
	if (get_post_field('post_excerpt', $id)) {
	    $fields['description']  = get_post_field('post_excerpt', $id);
	} else {
	    $post = get_post($id);
	    if ($post->post_content) {
		$fields['description']  = wp_trim_excerpt($post->post_content);
	    }
	}
	$data = self::filter_fields($fields, $display);
	
	

        if ( $display['format']=='liste' ) {
            $content .= '<li itemscope itemtype="http://schema.org/Person>';
	} else {
	    $content .= '<span itemscope itemtype="http://schema.org/Person">';
	}
        $content .=  Schema::create_Name($data,'name','','a',false,$viewopts);
		
	if (isset($data['connection_only']) && $data['connection_only']==false) {
	    if ($display['format']=='liste' ) {
		$content .= Schema::create_contactpointlist($data, 'span', '', '', 'span',$viewopts);
	    }
	    if (isset($display['hoursAvailable']) && $display['hoursAvailable']) {
		$content .=   Schema::create_ContactPoint($data);
	    }
	}

	if ((isset($data['description'])) && (!empty($data['description'])) && isset($display['kurzbeschreibung']) && $display['kurzbeschreibung']) {	
		$content .= "<br>" . $data['description'];
	}
	
        if ( $display['format']=='liste' ) {
            $content .= '</li>';        
	} else {
	    $content .= '</span>';
	}
        return $content;
    }

   
    
    public static function fau_person_sidebar($id, $display, $arguments = array()) {
        	if ($id == 0) {
	    return;
	}
         $fields = self::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
	$viewopts = self::get_viewsettings();
	

        $content = ''; 
	Main::enqueueForeignThemes();


	$fields['permalink'] = get_permalink($id);
	$fields['name'] = get_the_title($id);
	if (get_post_field('post_excerpt', $id)) {
	    $fields['description']  = get_post_field('post_excerpt', $id);
	} else {
	    $post = get_post($id);
	    if ($post->post_content) {
		$fields['description']  = wp_trim_excerpt($post->post_content);
	    }
	}
	
	
	
	$data = self::filter_fields($fields, $display);
	$sitebaropts = self::map_old_keys(self::get_viewsettings('sidebar'));
	foreach ($sitebaropts as $key => $value) {
	    if (empty($sitebaropts[$key])) {
		$display[$key] = false;
	    } else {
		$display[$key] = true;
	    }
	}
	
	$data = self::filter_fields($data, $sitebaropts);

		
	if (isset($arguments['hstart'])) {
	    $hstart = intval($arguments['hstart']);
	} else {
	    $hstart = 2;
	}
	if (($hstart <1) || ($hstart > 6)) {
	    $hstart = 2;
         }

	    $fullname = Schema::create_Name($data,'name','','a',false,$viewopts);

	 

	    $class = 'fau-person person sidebar';
	    if ((isset($arguments['class'])) && (!empty($arguments['class']))) {
		$class .= ' '.esc_attr($arguments['class']);
	    }
	    if (isset($display['border'])) {
		if ($display['border']) {
		    $class .= ' border';
		} else {
		    $class .= ' noborder';
		}
	    }
	    if (isset($arguments['background']) && (!empty($arguments['background']))) {
		$bg_array = array('grau', 'fau', 'phil', 'med', 'nat', 'tf', 'rw');
		if (in_array($arguments['background'], $bg_array)) {
		    $class .=' background-' . esc_attr($arguments['background']);
		}
	    }
	
	    
		
             $content = '<div class="'.$class.'" itemscope itemtype="http://schema.org/Person">' . "\n";
             $content .= '<div class="side">';         
		   
	    $content .= '<div class="row">' . "\n";
	    
	    if ((isset($display['bild'])) && (!empty($display['bild'])) && (has_post_thumbnail($id) )) {
		
		$alttext = esc_html($data['name']);		
		$imagedata['alt'] = $alttext;
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
                $content .= Schema::create_Image($imagedata, 'figure', 'image', 'person-thumb', true);
             }            

            $content .= '<div class="person-sidebar">' . "\n";
            $content .= '<h' . $hstart . '>';
            $content .= $fullname;
            $content .= '</h' . $hstart . '>' . "\n";
            
             $content .= '<div class="person-info">';
	     if (isset($data['jobTitle']) && (!empty($data['jobTitle']))) {
		 $content .= '<span class="person-info-position" itemprop="jobTitle">' . $data['jobTitle'] . '</span><br>';
	    }
	    $orgadata = array();
	    if (isset($data['worksFor']) && (!empty($data['worksFor']))) { 
		$orgadata['name'] = $fields['worksFor'];
	    }
	    if (isset($data['department']) && (!empty($data['department']))) { 
		$orgadata['department'] = $data['department'];
	    }
	    $content .= Schema::create_Organization($orgadata,'p','worksFor','',false,false,false);


	    if (isset($data['connection_only']) && $data['connection_only']==false) {
		$adresse = Schema::create_PostalAdress($data, 'address','', 'address', true);
		if (isset($adresse)) {
		    $content .= $adresse;
		} 
		$content .= Schema::create_contactpointlist($data, 'ul', '', 'contactlist', 'li',$viewopts); 
	    
		if (isset($display['hoursAvailable']) && $display['hoursAvailable']) {
		    $sprechzeitentitletag = 'h'.($hstart+1);
		    $content .=   Schema::create_ContactPoint($data,'div','contactPoint','',$sprechzeitentitletag);
		}
	    }
	    $content .= '</div>';
	

            if (!empty($data['description']) && isset($display['kurzbeschreibung'])) {
                $content .= '<div class="person-info-description"><span class="screen-reader-text">' . __('Beschreibung', 'fau-person') . ': </span><span itemprop="description">' . $data['description'] . '</span></div>' . "\n";
	    }
	    
	    if ((!empty($data['connection_text']) || !empty($data['connection_options']) || !empty($data['connections'])) && $display['ansprechpartner']==true) {
		$content .= self::fau_person_connection($data['connection_text'], $data['connection_options'], $data['connections'], $hstart);
	    }

         

            $content .= '</div><!-- /sidebar -->';
            $content .= '</div><!-- /row -->';
            $content .= '</div><!-- /side -->';
            $content .= '</div><!-- /fau-person -->';
   
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
	    
	    $data['permalink'] = get_permalink($data['nr']);
            if ($data['link']) {
                $data['url'] = $data['link'];
            }
	    
	    $oldurl = '';
	    if (isset($data['url'])) {
		$oldurl = $data['url'];
	    }
	    if (($data['permalink']) || ($data['url'])) {
		$surroundingtag = 'a';
	    }
	    if (!empty(get_the_title($data['nr']))) {
		 $data['name'] = get_the_title($data['nr']);
	     }
	    $fullname = Schema::create_Name($data,'name','',$surroundingtag,false,$viewopts);
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
    
    public static function filter_fields($input, $filter) {
	if (!isset($input)) {
	    return;
	}
	if (!isset($filter)) {
	    return $input;
	}
	$res = array();
	foreach ($input as $key => $value) {
	    if (isset($filter[$key])) {
		if ($filter[$key] == true) {
		    $res[$key] = $input[$key];
		} else {
		    unset($res[$key]);
		}
	    } else {
		$res[$key] = $input[$key];
	    }
	}
	return $res;
    }
    
    
    
    public static function create_fau_standort($id, $showfields, $titletag = 'h2') {
	if (!isset($id)) {
	    return;
	}
	$id = sanitize_key($id);
	if (!is_array($showfields)) {
	    return;
	}
	$fields = self::get_fields( $id, get_post_meta($id, 'fau_person_univis_id', true), 0 );
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
	$fields = self::get_fields( $id, get_post_meta($id, 'fau_person_univis_id', true), 0 );
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

    
     
      //Legt die in UnivIS hinterlegten Werte in einem Array ab, Feldbezeichnungen
    public static function univis_defaults($id ) {
         $post = get_post($id);
	if( !is_null( $post ) && $post->post_type === 'person' && get_post_meta($id, 'fau_person_univis_id', true)) {
	    $univis_id = get_post_meta($id, 'fau_person_univis_id', true);
	    $univis_default = self::get_fields($id, $univis_id, 1);
	    return $univis_default;
	} else {
	$univis_default = Config::get_keys_fields('persons');
	    return $univis_default;
	}
    }
    
    
     //gibt die Werte der Person an, Inhalte abhängig von UnivIS, 
    //Übergabewerte: ID der Person, UnivIS-ID der Person, 
    //Default-Wert 1 für Ausgabe der hinterlegten Werte im Personeneingabeformular, 
    //$ignore_connection=1 wenn die verknüpften Kontakte einer Person ignoriert werden sollen (z.B. wenn die Person selbst schon eine verknüpfte Kontaktperson ist)
    public static function get_fields( $id, $univis_id, $defaults, $ignore_connection=0 ) {
        $univis_sync = 0;
        $person = array();
        if( $univis_id  ) {
            $person = UnivIS_Data::get_univisdata( $univis_id );
            $univis_sync = 1;
        } 
        $fields = array();
        // Ab hier Definition aller Feldzuordnungen, $key ist Name der Metaboxen, $value ist Name in UnivIS
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
            'workLocation' => 'office', 
        );
        $fields_univis_officehours = array(
            'hoursAvailable_group' => 'officehours',
        );
        // Die Detailfelder zu den Sprechzeiten
        $subfields_univis_officehours = array(
            /* von der UnivIS-Doku:
             * repeat mode is encoded in a string
             * syntax: <modechar><numbers><space><args>                  
             * mode  description                  
             * d     daily                  
             * w     weekly
             * m     monthly                  
             * y     yearly                 
             * b     block
             * numbers: number of skips between repeats
             * example:  "d2":      every second day
             * weekly and monthly have additional arguments:  
             * weekly: argument is comma-separated list of weekdays where event is repeated                  
             * example:  "w3 1,2":  every third week on Monday and Tuesday                  
             * also possible: „we“ and „wo"
             * e = even calender week                  
             * o = odd calender week                  
             * monthly: argument has syntax "<submodechar><numbers>"                 
             * submode description                  
             * d       monthly by date                  
             * w       monthly by week                  
             * numbers: monthly by date: number of day (1-31)                  
             * monthly by week: number of week (1-5,e,o))                  
             * special case: 5 = last week of month                
             * examples:  "m1 d23": on the 23rd day of every month
             * "m2 w5":  in the last week of every second month
             * Laut UnivIS-Live-Daten werden für die Sprechzeiten aber nur wöchentlich an verschiedenen Tagen, 2-wöchentlich und täglich verwendet. Sollte noch was anderes benötigt werden, muss nachprogrammiert werden.
             */
            'comment' => 'comment',
            'endtime' => 'endtime',
            'repeat' => 'repeat',
            //'repeat_mode' => 'repeat_mode',
            'repeat_submode' => '',
            'office' => 'office', 
            'starttime' => 'starttime',
        );
        $fields_univis_orgunits = array(
            'worksFor' => 'orgunit',            
        );
        $fields_fauperson = array(
            'contactPoint' => '',
            'typ' => '',
            'alternateName' => '',
            'addressCountry' => '',
            'link' => '',
            'hoursAvailable_text' => '',
            'hoursAvailable' => '',
            'description' => '',
            'mobilePhone' => '',
        );
        $fields_exception = array(
            'postalCode' => '',
        );            
        $fields_connection = array(             // hier alle Felder ergänzen, die für die Anzeige der verknüpften Kontakte benötigt werden
            'connection_text' => '',
            'connection_only' => '',
            'connection_options' => array(),
            'connection_honorificPrefix' => 'honorificPrefix',
            'connection_givenName' => 'givenName',
            'connection_familyName' => 'familyName',
            'connection_honorificSuffix' => 'honorificSuffix',
            'connection_alternateName' => 'alternateName',
            'connection_streetAddress' => 'streetAddress',
            'connection_postalCode' => 'postalCode',
            'connection_addressLocality' => 'addressLocality',
            'connection_addressCountry' => 'addressCountry',  
            'connection_workLocation' => 'workLocation',
            'connection_telephone' => 'telephone',
            'connection_faxNumber' => 'faxNumber',         
            'connection_email' => 'email',
            'connection_hoursAvailable' => 'hoursAvailable',
            'connection_hoursAvailable_group' => 'hoursAvailable_group',
            'connection_nr' => 'nr',
            'connection_link' => 'link',
        );
        foreach( $fields_univis as $key => $value ) {
            if( $univis_sync && array_key_exists( $value, $person ) ) {
                if( $value == 'orgname' ) {
                    $language = get_locale();
                    if( strpos( $language, 'en_' ) === 0 && array_key_exists( 'orgname_en', $person ) ) {
                        $value = 'orgname_en';
                    } else {
                        $value = 'orgname';                   
                    }
                }
                $value = UnivIS_Data::sync_univis( $id, $person, $key, $value, $defaults ); 
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', 'fau-person');     
                } else {
                    $value = get_post_meta($id, 'fau_person_'.$key, true);                          
                }
            }
            $fields[$key] = $value;
        }
        foreach( $fields_univis_location as $key => $value ) {
            if( $univis_sync && array_key_exists( 'locations', $person ) && array_key_exists( 'location', $person['locations'][0] ) ) {
                $person_location = $person['locations'][0]['location'][0];
                if(($key == 'telephone' || $key == 'faxNumber') && !$defaults) {
                    $phone_number = UnivIS_Data::sync_univis( $id, $person_location, $key, $value, $defaults );
                    switch ( get_post_meta($id, 'fau_person_telephone_select', true) ) {
                        case 'erl':
                            $value = Sanitizer::correct_phone_number($phone_number, 'erl');
                            break;
                        case 'nbg':
                            $value = Sanitizer::correct_phone_number($phone_number, 'nbg');                        
                            break;
                        default:
                            $value = Sanitizer::correct_phone_number($phone_number, 'standard');                        
                            break;
                    }                    
                } else {
                    $value = UnivIS_Data::sync_univis( $id, $person_location, $key, $value, $defaults );
                }
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', 'fau-person');
                } else {
                    if($key == 'telephone' || $key == 'faxNumber') {
                        $phone_number = get_post_meta($id, 'fau_person_'.$key, true);
                        switch ( get_post_meta($id, 'fau_person_telephone_select', true) ) {
                        case 'erl':
                            $value = Sanitizer::correct_phone_number($phone_number, 'erl');
                            break;
                        case 'nbg':
                            $value = Sanitizer::correct_phone_number($phone_number, 'nbg');                        
                            break;
                        default:
                            $value = Sanitizer::correct_phone_number($phone_number, 'standard');  
                            break;
                        }
                    } else {                    
                        $value = get_post_meta($id, 'fau_person_'.$key, true);
                    }
                }
            }
            //add_action( 'admin_notices', array( 'FAU_Person', 'admin_notice_phone_number' ) );
            $fields[$key] = $value;
        }

        foreach( $fields_univis_officehours as $key => $value ) {
            // ist eine UnivIS-ID vorhanden?      
            switch ( $univis_sync ) {
                case true:
                    if ( array_key_exists( 'officehours', $person ) && array_key_exists( 'officehour', $person['officehours'][0] ) ) { // sind in UnivIS überhaupt Sprechzeiten hinterlegt?
                        if( get_post_meta($id, 'fau_person_univis_sync', true) || $defaults ) { // ist der Haken zur Synchronisation da bzw. werden die UnivIS-Werte für das Backend abgefragt
                            $person_officehours = $person['officehours'][0]['officehour'];   
                            $officehours = array();
                            foreach ($person_officehours as $num => $num_val) {
                                $repeat = isset( $person_officehours[$num]['repeat'] ) ? $person_officehours[$num]['repeat'] : 0;
                                $repeat_submode = isset( $person_officehours[$num]['repeat_submode'] ) ? $person_officehours[$num]['repeat_submode'] : 0;
                                $starttime = isset( $person_officehours[$num]['starttime'] ) ? $person_officehours[$num]['starttime'] : 0;
                                $endtime = isset( $person_officehours[$num]['endtime'] ) ? $person_officehours[$num]['endtime'] : 0;
                                $office = isset( $person_officehours[$num]['office'] ) ? $person_officehours[$num]['office'] : 0;
                                $comment = isset( $person_officehours[$num]['comment'] ) ? $person_officehours[$num]['comment'] : 0;
                                $officehour = UnivIS_Data::officehours_repeat($repeat, $repeat_submode, $starttime, $endtime, $office, $comment);                    
                                array_push($officehours, $officehour);
                            }
                            if ( $defaults ) {
                                $officehours = implode($officehours, '</p></li><li><p class="cmb_metabox_description">');
                                $officehours = sprintf(__('<p class="cmb_metabox_description">[Aus UnivIS angezeigter Wert: </p><ul><li><p class="cmb_metabox_description">%s</p></li></ul><p class="cmb_metabox_description">]</p>', 'fau-person'), $officehours); 
                            }
                            break;
                        }  
                    } elseif ( $defaults ) { // in UnivIS stehen keine Sprechzeiten
                        $officehours = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', 'fau-person');                                                   
                        break;
                    }                                                              
                default:  // keine UnivIS-ID da bzw. kein Haken bei Datenanzeige aus UnivIS => die Feldinhalte werden ausgegeben
                    $person_officehours = get_post_meta($id, 'fau_person_hoursAvailable_group', true);
                    $officehours = array();
                    if( !empty( $person_officehours ) ) {
                        foreach ( $person_officehours as $num => $num_val ) {                            
                            $repeat = isset( $person_officehours[$num]['repeat'] ) ? $person_officehours[$num]['repeat'] : 0;
                            $repeat_submode = isset( $person_officehours[$num]['repeat_submode'] ) ? $person_officehours[$num]['repeat_submode'] : 0;
                            $starttime = isset( $person_officehours[$num]['starttime'] ) ? $person_officehours[$num]['starttime'] : 0;
                            $endtime = isset( $person_officehours[$num]['endtime'] ) ? $person_officehours[$num]['endtime'] : 0;
                            $office = isset( $person_officehours[$num]['office'] ) ? $person_officehours[$num]['office'] : 0;
                            $comment = isset( $person_officehours[$num]['comment'] ) ? $person_officehours[$num]['comment'] : 0;
                            $officehour = UnivIS_Data::officehours_repeat($repeat, $repeat_submode, $starttime, $endtime, $office, $comment);
                            array_push($officehours, $officehour);                                
                        }
                    }
            }
            $fields[$key] = $officehours;
            
        }       
        
        foreach( $fields_univis_orgunits as $key => $value ) {
            $language = get_locale();
            if( strpos( $language, 'en_' ) === 0 && array_key_exists( 'orgunit_ens', $person ) ) {
                $orgunit = 'orgunit_en';
                $orgunits = 'orgunit_ens';
            } else {
                $orgunit = 'orgunit';
                $orgunits = 'orgunits';
            }
            if( array_key_exists( $orgunits, $person ) ) {
                $person_orgunits = $person[$orgunits][0][$orgunit];
                $i = count($person_orgunits);
                if($i>1) {
                    $i = count($person_orgunits)-2;
                } 
                $value = UnivIS_Data::sync_univis( $id, $person_orgunits, $key, $i, $defaults );  
            } else {
                if( $defaults ) {
                    $value = __('<p class="cmb_metabox_description">[In UnivIS ist hierfür kein Wert hinterlegt.]</p>', 'fau-person');
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
        if( !$ignore_connection ) 
            $connections = get_post_meta($id, 'fau_person_connection_id', true);
        if( !empty( $connections ) ) {    
            $connection = array();
            foreach( $connections as $ckey => $cvalue ) {
                $connection_fields[$ckey] = self::get_fields($cvalue, get_post_meta($cvalue, 'fau_person_univis_id', true), 0, 1);
                $connection_fields[$ckey]['nr'] = $cvalue;
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
            $fields_standort = self::get_fields_standort( $id, get_post_meta($id, 'fau_person_standort_id', true), 0 );
            $fields = array_merge( $fields, $fields_standort );
        }
        return $fields;
    }
    

    
        public static function get_default_display($format = '') {
	$display = '';
	switch($format) {
	    case 'name':
		$display = 'titel, familyName, givenName, name, suffix';
		break;
	    case 'shortlist':
		$display = 'titel, familyName, givenName, name, mail, telefon, suffix, permalink';
		break;
	    case 'plain':
		$display = 'familyName, givenName, name';
		break;
	     case 'compactindex':
	     case 'kompakt':
		$display = 'titel, familyName, givenName, name, suffix, position, telefon, email, email, adresse, bild, border';		 
		break;
	    case 'full':
	    case 'page':
		$display = 'titel, familyName, givenName, name, suffix, workLocation, worksFor, department, jobTitle, telefon, mobil, email, fax, url, content, adresse, bild, permalink, ansprechpartner';  
		break;
	    case 'listentry':
	    case 'liste':
		$display = 'titel, familyName, givenName, name, suffix, telefon, email, fax, url, kurzbeschreibung, permalink';  
		break;
	     case 'sidebar':
		$display = 'titel, familyName, givenName, name, suffix, workLocation, worksFor, jobTitle, telefon, email, fax, url, adresse, bild, permalink, sprechzeiten, ansprechpartner';  
		break;
	    case 'table': 
		$display = 'titel, familyName, givenName, name, suffix, telefon, email, url, permalink';  
		break;
	    default:
		$display = 'title, familyName, givenName, name, suffix, worksFor, department, jobTitle, telefon, email, permalink';  
	}	
	return $display;
    }

    
    public static function get_display_field($format = '', $show = '',  $hide = '') {	
	$display = self::get_default_display($format);

	$showfields = self::parse_liste($display,true);
	
	if ((isset($show)) && (!empty($show))) {
	    $showfields = self::parse_liste($show, true, $showfields);
	}
	if ((isset($hide)) && (!empty($hide))) {
	    $showfields = self::parse_liste($hide, false, $showfields);
	}

	$showfields = self::map_old_keys($showfields);
	if (!empty($format)) {
	    $showfields['format'] = $format;
	}
	return $showfields;
    }
    
    
    
    public static function map_old_keys($liste) {
	$newlist = array();
	foreach ($liste as $key => $value) {
	    switch($key) {
		case 'kurzauszug':
		   $newlist['description'] = $liste[$key];
		   break;
	         case 'organisation':
	         case 'institution':
		   $newlist['worksFor'] = $liste[$key];
		   break;
		case 'abteilung':
		   $newlist['department'] = $liste[$key];
		   break;
	         case 'position':
		   $newlist['jobTitle'] = $liste[$key];
		   break;
	         case 'bild':
		   $newlist['bild'] = $liste[$key];
		   $newlist['showthumb'] = $liste[$key];
		   break;
	         case 'compactindex':
		   $newlist['kompakt'] = $liste[$key];
		   break;   
		case 'mail':
		   $newlist['email'] = $liste[$key];
		   break;   
		case 'fax':
		   $newlist['faxNumber'] = $liste[$key];
		   break;   
		case 'telefon':
		   $newlist['telephone'] = $liste[$key];
		   break;   
		case 'fax':
		   $newlist['faxNumber'] = $liste[$key];
		   break;   
		case 'mobil':
		   $newlist['mobilePhone'] = $liste[$key];
		   break;   
		case 'webseite':
		   $newlist['url'] = $liste[$key];
		   break;   
		case 'mehrlink':
		   $newlist['link'] = $liste[$key];
		   break;   
		case 'suffix':
		   $newlist['honorificSuffix'] = $liste[$key];
		   break;   
		case 'titel':
		   $newlist['honorificPrefix'] = $liste[$key];
		   break;   
		case 'sprechzeiten':
		   $newlist['hoursAvailable'] = $liste[$key];
		   break;
		case 'ansprechpartner':
		   $newlist['ansprechpartner'] = $liste[$key];
		   break;
		case 'rahmen':
		   $newlist['border'] = $liste[$key];
		   break;
	       default:
		   $newlist[$key] = $liste[$key];
	   }
	    
	}
	return $newlist;
	
    }

    public static function parse_liste($liste = '', $resbool=true, $showarray = array()) {
	if (!empty($liste)) {
	    
	    $showvals = explode(',', $liste);
	    foreach ($showvals as $value) {
		$key = esc_attr(trim($value));
		if (!empty($key)) {
		    $showarray[$key] = $resbool;
		}
	    }
	}   
	return $showarray;
    }

}