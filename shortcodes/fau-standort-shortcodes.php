<?php

 if(!function_exists('fau_standort_page')) { 
    function fau_standort_page( $id ) {
        return FAU_Standort_Shortcodes::fau_standort_page( $id );
    }
 }  

class FAU_Standort_Shortcodes {

    public static function fau_standort( $atts, $content = null) {
            extract(shortcode_atts(array(
            "slug" => FALSE,
            "id" => FALSE,
            "format" => '',
            "show" => '', 
            "hide" => '',
                        ), $atts));
          
        $sidebar = '';
        $page = '';
        $list = '';
        $showaddress = '';
        $showlist = '';
        $showthumb = '';
        $showsidebar = '';
        $name = '';
        $shortlist = '';
        if ( !empty( $format ) ) {      
            if( $format == 'name' || $format == 'shortlist' )   $shortlist = 1;
            if( $format == 'sidebar' ) {
                $showsidebar = 1;
                $sidebar = 1;
                $showaddress = 0;
                $showdescription = 1;
                $showthumb = 1;
            }
            if( $format == 'full' || $format == 'page' )        $page = 1;
            if( $format == 'liste' ) {
                $list = 1;
                $showlist = 1;
            }
        }     
        //Wenn neue Felder dazukommen, hier die Anzeigeoptionen auch mit einstellen
        if (!empty($show)) {
            $show = array_map('trim', explode(',', $show));
            if( in_array( 'kurzbeschreibung', $show ) ) $showlist = 1;  
            if( in_array( 'adresse', $show ) )          $showaddress = 1;            
            if( in_array( 'bild', $show ) )             $showthumb = 1;
        }    
        if ( !empty( $hide ) ) {
            $hide = array_map('trim', explode(',', $hide));
            if( in_array( 'kurzbeschreibung', $hide ) ) $showlist = 0;
            if( in_array( 'adresse', $hide ) )          $showaddress = 0;            
            if( in_array( 'bild', $hide ) )             $showthumb = 0;         
        }
                

        if (empty($id)) {
            if (empty($slug)) {
                return '<p>' . sprintf(__('Bitte geben Sie den Titel oder die ID des Standorteintrags an.', FAU_PERSON_TEXTDOMAIN), $slug) . '</p>';
            } else {
                $posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
                if ($posts) {
                    $post = $posts[0];
                    $id = $post->ID;
                } else {
                    return '<p>' . sprintf(__('Es konnte kein Standorteintrag mit dem angegebenen Titel %s gefunden werden. Versuchen Sie statt dessen die Angabe der ID des Standorteintrags.', FAU_PERSON_TEXTDOMAIN), $slug) . '</p>';
                }
            }
        }

        if (!empty($id)) {

            $list_ids = array_map('trim', explode(',', $id));
            if ( $page ) {
                $liste = '';
            } elseif ( $list ) {
                $liste = '<ul class="person liste-person" itemscope itemtype="http://schema.org/Person">';
                $liste .= "\n";              
            } else {
                $liste = '<p>';
            }

            $number = count($list_ids);   
            $i = 1;
            foreach ($list_ids as $value) {
                $post = get_post($value);
                
                if ($post && $post->post_type == 'standort') {
                    if ( $page ) {
                        $liste .= self::fau_standort_page($value);
                    } elseif ( $shortlist ) {
                        $liste .= self::fau_standort_shortlist($value, $showlist);
                        if( $i < $number )  $liste .= ", ";
                    } elseif ( $list ) {
                        $liste .= '<li class="person-info">'."\n";
                        $liste .= self::fau_standort_shortlist($value, $showlist);
                        $content .= "</li>\n";
                    } elseif ( $sidebar ) { 
                        $liste .= self::fau_standort_sidebar($value, 0, $showlist, $showaddress, $showthumb);
                    } else { 
                        $liste .= self::fau_standort_markup($value, $showaddress, $showlist, $showsidebar, $showthumb);
                    }
                } else {
                    $liste .=  sprintf(__('Es konnte kein Kontakteintrag mit der angegebenen ID %s gefunden werden.', FAU_PERSON_TEXTDOMAIN), $value);
                    if( $i < $number )  $liste .= ", ";
                }
                $i++;
            }
            if ( $list ) {
                $liste .= "</ul>\n";
            } elseif ( $page ) {
                $post = get_post( $id );
                if ( $post->post_content ) $content = wpautop($post->post_content);  
                $liste .= $content;
            } else {
                $liste .= "</p>\n";                
            } 
            return $liste;
            
        }

}

    public static function fau_standort_markup($id, $showaddress, $showlist, $showsidebar, $showthumb) {
        $fields = sync_helper::get_fields( $id, get_post_meta($id, 'fau_person_univis_id', true), 0 );
        extract($fields);
        
	$type = get_post_meta($id, 'fau_person_typ', true);

        if( $link ) {
            $personlink = $link;
        } else {
            $personlink = get_permalink( $id );
        }
        
        if( get_post_field( 'post_excerpt', $id ) ) {
            $excerpt = get_post_field( 'post_excerpt', $id );                
        } else {
            $post = get_post( $id );
            if ( $post->post_content )      
                $excerpt = wp_trim_excerpt($post->post_content);
        }         
            
        if($streetAddress || $postalCode || $addressLocality || $addressCountry) {
            $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">'.__('Adresse',FAU_PERSON_TEXTDOMAIN).': <br></span>';            
            if($streetAddress) {
                $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">'.$streetAddress.'</span>';
                if( $postalCode || $addressLocality )  {
                    $contactpoint .= '<br>';
                } elseif( $addressCountry ) {
                    $contactpoint .= '<br>';
                }                    
            }
            if($postalCode || $addressLocality) {
                $contactpoint .= '<span class="person-info-city">';
                if($postalCode)             
                    $contactpoint .= '<span itemprop="postalCode">'.$postalCode.'</span> ';  
                if($addressLocality)	
                    $contactpoint .= '<span itemprop="addressLocality">'.$addressLocality.'</span>';
                $contactpoint .= '</span>';
                if( $addressCountry )       
                    $contactpoint .= '<br>';
            }                  
            if( $addressCountry )         
                $contactpoint .= '<span class="person-info-country" itemprop="addressCountry">'.$addressCountry.'</span>';
            $contactpoint .= '</li>';                                                
        }
        
        $fullname = '';
        if( !empty( get_the_title($id) ) ) {                                                
            $fullname .= get_the_title($id);
        }        
                    
        $content = '<div class="person content-person" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';			
        $content .= '<div class="row">';

        if($showthumb) {
            $content .= '<div class="span1 span-small" itemprop="image" aria-hidden="true">';	
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">';
            if (has_post_thumbnail($id)) {
                $content .= get_the_post_thumbnail($id, 'person-thumb-bigger');
            } else {
		if ($type == 'realmale') {
                    $bild =  plugin_dir_url( __FILE__ ) .'../images/platzhalter-mann.png';   
		} elseif ($type == 'realfemale') {
                    $bild = plugin_dir_url( __FILE__ ) .'../images/platzhalter-frau.png';
                } elseif ($type == 'einrichtung') {
                    $bild = plugin_dir_url( __FILE__ ) .'../images/platzhalter-organisation.png';
                } else {
                    $bild = plugin_dir_url( __FILE__ ) .'../images/platzhalter-unisex.png';
                }				    
		if ($bild) 
                    $content .=  '<img src="'.$bild.'" width="90" height="120" alt="">';
            }
            $content .= '</a>';
            $content .= '</div>';
        }
        $content .= '<div class="span3">';
        $content .= '<h3>';        
        $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
        $content .= '</h3>';
        $content .= '<ul class="person-info">';
        if ($showaddress && !empty($contactpoint)) 
            $content .= $contactpoint;
        $content .= '</ul>';

        $content .= '</div>';
        if ( ($showlist || $showsidebar) && isset($excerpt) ) {
            $content .= '<div class="span3">';
            $content .= '<div class="person-info-description"><p>' . $excerpt . '</p></div>';
            $content .= '</div>';
        }
        $content .= '</div>';
        $content .= '</div>';
        return $content;

}

    public static function fau_standort_page($id) {
 
     	$content = '<div class="person" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
        
        $fields = standort_sync_helper::get_fields($id, get_post_meta($id, 'fau_person_standort_id', true), 0);
        extract($fields);
        
        $fullname = '';
        if( !empty( get_the_title($id) ) ) {                                                
            $fullname .= get_the_title($id);
        }
        $content .= '<h2 itemprop="name">' . $fullname . '</h2>';
        if ($streetAddress || $postalCode || $addressLocality || $addressCountry) {
            $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">' . __('Adresse', FAU_PERSON_TEXTDOMAIN) . ': <br></span>';
            if ($streetAddress) {
                $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">' . $streetAddress . '</span>';
                if ($postalCode || $addressLocality) {
                    $contactpoint .= '<br>';
                } elseif ($addressCountry) {
                    $contactpoint .= '<br>';
                }
            }
            if ($postalCode || $addressLocality) {
                $contactpoint .= '<span class="person-info-city">';
                if ($postalCode)
                    $contactpoint .= '<span itemprop="postalCode">' . $postalCode . '</span> ';
                if ($addressLocality)
                    $contactpoint .= '<span itemprop="addressLocality">' . $addressLocality . '</span>';
                $contactpoint .= '</span>';
                if ($addressCountry)
                    $contactpoint .= '<br>';
            }
            if ($addressCountry)
                $contactpoint .= '<span class="person-info-country" itemprop="addressCountry">' . $addressCountry . '</span></';
            $contactpoint .= '</li>';
        }
        //$content = '';
        
        $post = get_post($id);
        if (has_post_thumbnail($id)) {
            $content .= '<div itemprop="image" class="alignright">';
            // $content .= get_the_post_thumbnail($id, 'post');	    
            $content .= get_the_post_thumbnail($id, 'person-thumb-page');
            $content .= '</div>';
        }
        $content .= '<ul class="person-info">';
        if (!empty($contactpoint)) {
            $content .= $contactpoint;
        }
        $content .= '</ul>';
        $content .= '</div>';

        return $content;
    } 
  
  
    public static function fau_standort_shortlist($id, $showlist) {	
        
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        extract($fields);
        
            if( get_post_field( 'post_excerpt', $id ) ) {
                $excerpt = get_post_field( 'post_excerpt', $id );                
            } else {
                $post = get_post( $id );
                if ( $post->post_content )      $excerpt = wp_trim_excerpt($post->post_content);
            }
            
            if( $link ) {
                $personlink = $link;
            } else {
                $personlink = get_permalink( $id );
            }
            $content = '';			           
		$fullname = '';
                if (!empty(get_the_title($id) ) ) {
                    $fullname .= get_the_title($id);
                }
                $content .= '<span class="person-info">';
                $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
                if( $showlist && isset($excerpt) )                                  $content .= "<br>".$excerpt;    
                $content .= '</span>';
            return $content;
    }

 
    public static function fau_standort_sidebar($id, $title, $showlist=0, $showaddress=0, $showthumb=0) {
            if (!empty($id)) {
            $post = get_post($id);

            $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
            extract($fields);

            if( $link ) {
                $personlink = $link;
            } else {
                $personlink = get_permalink( $id );
            }
            
            if( $showaddress ) {
                if ($streetAddress || $postalCode || $addressLocality || $addressCountry) {
                    $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">' . __('Adresse', FAU_PERSON_TEXTDOMAIN) . ': <br></span>';
                    if ($streetAddress) {
                        $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">' . $streetAddress . '</span>';
                        if ($postalCode || $addressLocality) {
                            $contactpoint .= '<br>';
                        } elseif ($addressCountry) {
                            $contactpoint .= '<br>';
                        }
                    }
                    if ($postalCode || $addressLocality) {
                        $contactpoint .= '<span class="person-info-city">';
                        if ($postalCode)
                            $contactpoint .= '<span itemprop="postalCode">' . $postalCode . '</span> ';
                        if ($addressLocality)
                            $contactpoint .= '<span itemprop="addressLocality">' . $addressLocality . '</span>';
                        $contactpoint .= '</span>';
                        if ($addressCountry)
                            $contactpoint .= '<br>';
                    }
                    if ($addressCountry)
                        $contactpoint .= '<span class="person-info-country" itemprop="addressCountry">' . $addressCountry . '</span>';
                    $contactpoint .= '</li>';
                }
            }

            $fullname = '';
            if( !empty( get_the_title($id) ) ) {                                                
                $fullname .= get_the_title($id);
            }
            
            $content = '<div class="person" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
            
            if (!empty($title)) 
                $content .= '<h2 class="small">' . $title . '</h2>';

            $content .= '<div class="row">';

            if (has_post_thumbnail($id) && $showthumb) {
                $content .= '<div class="span1" itemprop="image" aria-hidden="true">';
                $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">';
                $content .= get_the_post_thumbnail($id, 'person-thumb');
                $content .= '</a>';
                $content .= '</div>';
            }

            $content .= '<div class="span3">';
            $content .= '<h3>';
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
            $content .= '</h3>';
            $content .= '<ul class="person-info">';
            if (!empty($contactpoint))
                $content .= $contactpoint;
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '</div>';

            $content .= '</div>';
        }
        return $content;

    }
}
