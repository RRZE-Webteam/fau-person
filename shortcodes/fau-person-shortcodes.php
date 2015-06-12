<?php


 if(!function_exists('fau_person')) {   
    function fau_person( $atts, $content = null) {
            extract(shortcode_atts(array(
            "slug" => FALSE,
            "id" => FALSE,
            "showlink" => FALSE,
            "showfax" => FALSE,
            "showwebsite" => FALSE,
            "showaddress" => FALSE,
            "showroom" => FALSE,
            "showdescription" => FALSE,
            "showlist" => FALSE,
            "showsidebar" => FALSE,
            "showthumb" => FALSE,
            "showpubs" => FALSE,
            "showoffice" => FALSE,
            "showtitle" => TRUE,
            "showsuffix" => TRUE,
            "showposition" => TRUE,
            "showinstitution" => TRUE,
            "showabteilung" => TRUE,
            "showmail" => TRUE,
            "showtelefon" => TRUE,
            "extended" => FALSE,
            "format" => '',
                        ), $atts));

        if (empty($id)) {
            if (empty($slug)) {
                return '<p>' . sprintf(__('Bitte geben Sie den Titel oder die ID des Kontakteintrags an.', FAU_PERSON_TEXTDOMAIN), $slug) . '</p>';
            } else {
                $posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
                if ($posts) {
                    $post = $posts[0];
                    $id = $post->ID;
                } else {
                    return '<p>' . sprintf(__('Es konnte kein Kontakteintrag mit dem angegebenen Titel %s gefunden werden. Versuchen Sie statt dessen die Angabe der ID des Kontakteintrags.', FAU_PERSON_TEXTDOMAIN), $slug) . '</p>';
                }
            }
        }
        if (!empty($id)) {
            $list_ids = explode(',', $id);
            if ($format == 'shortlist') {
                $liste = '<ul class="person liste-person" itemscope itemtype="http://schema.org/Person">';
                $liste .= "\n";
            } else {
                $liste = '';
            }

            foreach ($list_ids as $value) {
                $post = get_post($value);
                if ($post->post_type == 'person') {
                    if (($format == 'full') || ($format == 'page')) {
                        $liste .= fau_person_page($value);
                    } elseif ($format == 'shortlist') {
                        $liste .= fau_person_shortlist($value, $showlist);
                    } else {
                        $liste .= fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon);
                    }
                } else {
                    $liste .= '<p>' . sprintf(__('Es konnte kein Kontakteintrag mit der angegebenen ID %s gefunden werden.', FAU_PERSON_TEXTDOMAIN), $value) . '</p>';
                }
            }
            if ($format == 'shortlist')
                $liste .= "</ul>\n";
            return $liste;
        }
    }

}


 if(!function_exists('fau_persons')) {
    function fau_persons($atts, $content = null) {
        extract(shortcode_atts(array(
            "category" => 'category',
            "showlink" => FALSE,
            "showfax" => FALSE,
            "showwebsite" => FALSE,
            "showaddress" => FALSE,
            "showroom" => FALSE,
            "showdescription" => FALSE,
            "showsidebar" => FALSE,
            "showlist" => FALSE,
            "showthumb" => FALSE,
            "showpubs" => FALSE,
            "showoffice" => FALSE,
            "showtitle" => TRUE,
            "showsuffix" => TRUE,
            "showposition" => TRUE,
            "showinstitution" => TRUE,
            "showabteilung" => TRUE,            
            "showmail" => TRUE,
            "showtelefon" => TRUE,
            "extended" => FALSE,
                        ), $atts));

        $category = get_term_by('slug', $category, 'persons_category');

        $posts = get_posts(array('post_type' => 'person', 'post_status' => 'publish', 'numberposts' => 1000, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(
                array(
                    'taxonomy' => 'persons_category',
                    'field' => 'id', // can be slug or id - a CPT-onomy term's ID is the same as its post ID
                    'terms' => $category->term_id
                )
            ), 'suppress_filters' => false));

        $content = '';

        foreach ($posts as $post) {
            $content .= fau_person_markup($post->ID, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon);
        }

        return $content;
    }

}

if(!function_exists('fau_person_markup')) {

    function fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon) {
        $fields = sync_helper::get_fields( $id, get_post_meta($id, 'fau_person_univis_id', true) );
        extract($fields);
         
        
        $link = get_post_meta($id, 'fau_person_link', true);
	$type = get_post_meta($id, 'fau_person_typ', true);

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
                    
        $content = '<div class="person content-person" itemscope itemtype="http://schema.org/Person">';			
        $content .= '<div class="row">';

        if($showthumb) {
            $content .= '<div class="span1 span-small" itemprop="image">';		   				    
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
            $content .= '</div>';
        }

        $content .= '<div class="span3">';
        $content .= '<h3>';
        if($showtitle && $honorificPrefix)                      
            $content .= '<span itemprop="honorificPrefix">' . $honorificPrefix . '</span> ';
        if(get_post_meta( $id, 'fau_person_univis_sync', true)) {
            $content .= '<span itemprop="givenName">' . $givenName . '</span> <span itemprop="familyName">' . $familyName . '</span>';
        } elseif( !empty( get_the_title($id) ) ) {                                                
            $content .= get_the_title($id);
        }
        if($showsuffix && $honorificSuffix)                     
            $content .= ' <span itemprop="honorificSuffix">' . $honorificSuffix . '</span>';
        $content .= '</h3>';
        $content .= '<ul class="person-info">';
        if ($showposition && $jobTitle)
            $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', FAU_PERSON_TEXTDOMAIN) . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>';
        if ($showinstitution && $worksFor)
            $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>';
        if ($showabteilung && $department)
            $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="department">' . $department . '</span></li>';
        if ($showtelefon && $telephone)
            $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
        if (($extended || $showfax) && $faxNumber)
            $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
        if ($showmail && $email)
            $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
        if (($extended || $showwebsite) && $url)
            $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>';
        if (($extended || $showaddress) && !empty($contactpoint)) 
            $content .= $contactpoint;
        if (($extended || $showroom) && $workLocation)
            $content .= '<li class="person-info-room"><span class="screen-reader-text">' . __('Raum', FAU_PERSON_TEXTDOMAIN) . ' </span><span itemprop="workLocation">' . $workLocation . '</span></li>';
        if ($showoffice && $hoursAvailable)
            $content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="hoursAvailable">' . $hoursAvailable . '</span></li>';
        if ($showpubs && $pubs)
            $content .= '<li class="person-info-pubs"><span class="screen-reader-text">' . __('Publikationen', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $pubs . '</li>';
        $content .= '</ul>';

        $content .= '</div>';
        if (($showlist && $excerpt) || (($showsidebar || $extended) && $description) || ($showlink && $link)) {
            $content .= '<div class="span3">';
            if ($showlist && $excerpt)
                $content .= '<div class="person-info-description"><p>' . $excerpt . '</p></div>';
            if (($extended || $showsidebar) && $description)
                $content .= '<div class="person-info-description"><span class="screen-reader-text">' . __('Beschreibung', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $description . '</div>';
            if ($showlink && $link) {
                $content .= '<div class="person-info-more"><a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" class="person-read-more" href="' . $link . '">';
                $content .= __('Mehr', FAU_PERSON_TEXTDOMAIN) . ' ›</a></div>';
            }
            $content .= '</div>';
        }
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        return $content;
    }

}

 if(!function_exists('fau_person_page')) {
    function fau_person_page($id) {
 
     	$content = '<div class="person" itemscope itemtype="http://schema.org/Person">';
        
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true));
        extract($fields);

        if ($streetAddress || $postalCode || $addressLocality || $addressCountry) {
            $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">' . __('Adresse', FAU_PERSON_TEXTDOMAIN) . ': </span><br>';
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

        if ((strlen($url) > 4) && (strpos($url, "http") === false)) {
            $url = 'http://' . $url;
        }
        //$content = '';
        $fullname = '';
        if ($honorificPrefix)
            $fullname .= '<span itemprop="honorificPrefix">' . $honorificPrefix . '</span> ';
        if ($givenName)
            $fullname .= '<span itemprop="givenName">' . $givenName . '</span> ';
        if ($familyName)
            $fullname .= '<span itemprop="familyName">' . $familyName . '</span>';
        if ($honorificSuffix)
            $fullname .= ' <span itemprop="honorificSuffix">' . $honorificSuffix . '</span>';
        if ($jobTitle) {
            $headline = '<span itemprop="jobTitle">' . $jobTitle . '</span>';
            $content .= '<h2>' . $headline . '</h2>';
        } else {
            $headline = $fullname;
            $content .= '<h2 itemprop="name">' . $headline . '</h2>';
        }
        $post = get_post($id);
        if (has_post_thumbnail($id)) {
            $content .= '<div itemprop="image" class="alignright">';
            // $content .= get_the_post_thumbnail($id, 'post');	    
            $content .= get_the_post_thumbnail($id, 'person-thumb-page');
            $content .= '</div>';
        }
        if ($jobTitle) {
            $content .= '<h3 itemprop="name">';
            $content .= $fullname;
            $content .= '</h3>';
        }
        $content .= '<ul class="person-info">';
        if ($jobTitle)
            $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', FAU_PERSON_TEXTDOMAIN) . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>';
        if ($worksFor)
            $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>';
        if ($department)
            $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $department . '</span></li>';
        if ($telephone)
            $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
        if ($faxNumber)
            $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
        if ($email)
            $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
        if ($url)
            $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>';
        if (!empty($contactpoint)) {
            $content .= $contactpoint;
        }
        if ($workLocation)
            $content .= '<li class="person-info-room"><span class="screen-reader-text">' . __('Raum', FAU_PERSON_TEXTDOMAIN) . ' </span><span itemprop="workLocation">' . $workLocation . '</span></li>';
        if ($hoursAvailable)
            $content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="hoursAvailable">' . $hoursAvailable . '</span></li>';
        if ($pubs)
            $content .= '<li class="person-info-pubs"><span class="screen-reader-text">' . __('Publikationen', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $pubs . '</li>';
        $content .= '</ul>';


        //	    if (($options['plugin_fau_person_headline'] != 'jobTitle') && ($position)) 
        //		$content .= '<li class="person-info-position"><span class="screen-reader-text">'.__('Tätigkeit','fau').': </span><strong><span itemprop="jobTitle">'.$jobTitle.'</span></strong></li>';

        return $content;
    } 
 }
