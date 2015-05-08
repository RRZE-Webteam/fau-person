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
                    "showthumb" => FALSE,
                    "showpubs" => FALSE,
                    "showoffice" => FALSE,
                    "showtitle" => TRUE,
                    "showsuffix" => TRUE,
                    "showposition" => TRUE,
                    "showinstitution" => TRUE,
                    "showmail" => TRUE,
                    "showtelefon" => TRUE,
                    "extended" => FALSE,
		     "format" => '',
                    ), $atts));
                
            if( empty($id) ) {
                if( empty($slug) )  {
                    return sprintf(__('Bitte geben Sie den Titel oder die ID des Kontakteintrags an.', FAU_PERSON_TEXTDOMAIN), $slug);
                } else {
                    $posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
                    if ($posts) {
                        $post = $posts[0];
                        $id = $post->ID;		
                    } else {
                        return sprintf(__('Es konnte kein Kontakteintrag mit dem angegebenen Titel %s gefunden werden. Versuchen Sie statt dessen die Angabe der ID des Kontakteintrags.', FAU_PERSON_TEXTDOMAIN), $slug);                        
                    }
                        
                }
            } 
            if( get_post($id) ) {
		if ( ($format == 'full') || ($format=='page') ) {
		    return fau_person_page($id);
		} else { 
		    return fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showmail, $showtelefon);
                }                
            } else {
                return sprintf(__('Es konnte kein Kontakteintrag mit der angegebenen ID %s gefunden werden.', FAU_PERSON_TEXTDOMAIN), $id);                
            }

    }
 }


 if(!function_exists('fau_persons')) {
    function fau_persons( $atts, $content = null) {
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

            foreach($posts as $post)
            {
                    $content .= fau_person_markup($post->ID, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showmail, $showtelefon);
            }

            return $content;
    }
 }

 if(!function_exists('fau_person_markup')) {
    function fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showmail, $showtelefon)
    {

	
            $honorificPrefix = get_post_meta($id, 'fau_person_honorificPrefix', true);
            $givenName = get_post_meta($id, 'fau_person_givenName', true);
            $familyName = get_post_meta($id, 'fau_person_familyName', true);
            $honorificSuffix = get_post_meta($id, 'fau_person_honorificSuffix', true);
            $jobTitle = get_post_meta($id, 'fau_person_jobTitle', true);
            $worksFor = get_post_meta($id, 'fau_person_worksFor', true);
            $telephone = get_post_meta($id, 'fau_person_telephone', true);
            $faxNumber = get_post_meta($id, 'fau_person_faxNumber', true);
            $email = get_post_meta($id, 'fau_person_email', true);
            $url = get_post_meta($id, 'fau_person_url', true);
            $streetAddress = get_post_meta($id, 'fau_person_streetAddress', true);
            $postalCode = get_post_meta($id, 'fau_person_postalCode', true);
            $addressLocality = get_post_meta($id, 'fau_person_addressLocality', true);
            $addressCountry = get_post_meta($id, 'fau_person_addressCountry', true);
            $workLocation = get_post_meta($id, 'fau_person_workLocation', true);
            $hoursAvailable = get_post_meta($id, 'fau_person_hoursAvailable', true);
            $pubs = get_post_meta($id, 'fau_person_pubs', true);
            $description = get_post_meta($id, 'fau_person_description', true);
            
            $link = get_post_meta($id, 'fau_person_link', true);
	    $type = get_post_meta($id, 'fau_person_typ', true);
            
            
            $excerpt = get_post_field('post_excerpt', $id);

            
                                                            //ACHTUNG: vorher css person-info-address (war Textarea bei FAU)!!!
                                                if($streetAddress || $postalCode || $addressLocality || $addressCountry) {
                                                    $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">'.__('Adresse',FAU_PERSON_TEXTDOMAIN).': </span><br>';    
                                                
                                                    if($streetAddress)          $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">'.$streetAddress.'</span>';
                                                    if($streetAddress && ($postalCode || $addressLocality)) $contactpoint .= '<br>';
                                                    if($postalCode || $addressLocality) {
                                                        $contactpoint .= '<span class="person-info-city">';
                                                        if($postalCode)         $contactpoint .= '<span itemprop="postalCode">'.$postalCode.'</span> ';  
                                                        if($addressLocality)	$contactpoint .= '<span itemprop="addressLocality">'.$addressLocality.'</span>';
                                                        $contactpoint .= '</span>';
                                                    }
                                                    if(($streetAddress || $postalCode || $addressLocality) && $addressCountry)                    $contactpoint .= '<br>';
                                                    if($addressCountry)         $contactpoint .= '<span class="person-info-country" itemprop="addressCountry">'.$addressCountry.'</span></';
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
				    } else {
					$bild = plugin_dir_url( __FILE__ ) .'../images/platzhalter-mann.png';
				    }
				    
				    if ($bild) {
					$content .=  '<img src="'.$bild.'" width="90" height="120" alt="">';
				    }
				}

                                $content .= '</div>';
                            }

                            $content .= '<div class="span3">';
                                    $content .= '<h3>';
                                            if($showtitle && $honorificPrefix) 	$content .= $honorificPrefix . ' ';
                                            $content .= get_the_title($id);
                                            if($showsuffix && $honorificSuffix) 	$content .= ' '.$honorificSuffix;
                                    $content .= '</h3>';
                                    $content .= '<ul class="person-info">';
                                            if($showposition && $jobTitle) 				$content .= '<li class="person-info-position"><span class="screen-reader-text">'.__('Tätigkeit',FAU_PERSON_TEXTDOMAIN).': </span><strong><span itemprop="jobTitle">'.$jobTitle.'</span></strong></li>';
                                            if($showinstitution && $worksFor)			$content .= '<li class="person-info-institution"><span class="screen-reader-text">'.__('Einrichtung',FAU_PERSON_TEXTDOMAIN).': </span><span itemprop="worksFor">'.$worksFor.'</span></li>';
                                            if($showtelefon && $telephone)					$content .= '<li class="person-info-phone"><span class="screen-reader-text">'.__('Telefonnummer',FAU_PERSON_TEXTDOMAIN).': </span><span itemprop="telephone">'.$telephone.'</span></li>';
                                            if(($extended || $showfax) && $faxNumber)		$content .= '<li class="person-info-fax"><span class="screen-reader-text">'.__('Faxnummer',FAU_PERSON_TEXTDOMAIN).': </span><span itemprop="faxNumber">'.$faxNumber.'</span></li>';
                                            if($showmail && $email)					$content .= '<li class="person-info-email"><span class="screen-reader-text">'.__('E-Mail',FAU_PERSON_TEXTDOMAIN).': </span><a itemprop="email" href="mailto:'.strtolower($email).'">'.strtolower($email).'</a></li>';
                                            if(($extended || $showwebsite) && $url)	$content .= '<li class="person-info-www"><span class="screen-reader-text">'.__('Webseite',FAU_PERSON_TEXTDOMAIN).': </span><a itemprop="url" href="'.$url.'">'.$url.'</a></li>';
                                            if(($extended || $showaddress) && !empty($contactpoint)) {
                                                    $content .= $contactpoint;
                                            }
                                            if(($extended || $showroom) && $workLocation)		$content .= '<li class="person-info-room"><span class="screen-reader-text">' . __('Raum', FAU_PERSON_TEXTDOMAIN) .' </span><span itemprop="workLocation">'.$workLocation.'</span></li>';
                                            if($showoffice && $hoursAvailable)		$content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) .': </span><span itemprop="hoursAvailable">'.$hoursAvailable.'</span></li>';
                                            if($showpubs && $pubs)		$content .= '<li class="person-info-pubs"><span class="screen-reader-text">' . __('Publikationen', FAU_PERSON_TEXTDOMAIN) .': </span>'.$pubs.'</li>';                                            
                                            $content .= '</ul>';

                            $content .= '</div>';
                            $content .= '<div class="span3">';
                                    if( $showlist && $excerpt )                                  $content .= '<div class="person-info-description">'.$excerpt.'</div>';    
                                    if(($extended || $showsidebar) && $description)		$content .= '<div class="person-info-description">'.$description.'</div>';
                                    if($showlink && $link) {
                                            $content .= '<div class="person-info-more"><a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" class="person-read-more" href="'.$link.'">';
                                            $content .= __('Mehr', FAU_PERSON_TEXTDOMAIN) . ' ›</a></div>';
                                    }

                            $content .= '</div>';
                    $content .= '</div>';

            $content .= '</div>';

            return $content;
    }
 }

 if(!function_exists('fau_person_page')) {
     function fau_person_page($id) {
 
     	$res = '<div class="person" itemscope itemtype="http://schema.org/Person">';

	    $honorificPrefix = get_post_meta($id, 'fau_person_honorificPrefix', true);
	    $givenName = get_post_meta($id, 'fau_person_givenName', true);
	    $familyName = get_post_meta($id, 'fau_person_familyName', true);
	    $honorificSuffix = get_post_meta($id, 'fau_person_honorificSuffix', true);
	    $jobTitle = get_post_meta($id, 'fau_person_jobTitle', true);
	    $worksFor = get_post_meta($id, 'fau_person_worksFor', true);
	    $telephone = get_post_meta($id, 'fau_person_telephone', true);
	    $faxNumber = get_post_meta($id, 'fau_person_faxNumber', true);
	    $email = get_post_meta($id, 'fau_person_email', true);
	    $url = get_post_meta($id, 'fau_person_url', true);
	    $streetAddress = get_post_meta($id, 'fau_person_streetAddress', true);
	    $postalCode = get_post_meta($id, 'fau_person_postalCode', true);
	    $addressLocality = get_post_meta($id, 'fau_person_addressLocality', true);
	    $addressCountry = get_post_meta($id, 'fau_person_addressCountry', true);
	    $workLocation = get_post_meta($id, 'fau_person_workLocation', true);
	    $hoursAvailable = get_post_meta($id, 'fau_person_hoursAvailable', true);
	    $pubs = get_post_meta($id, 'fau_person_pubs', true);
	    $link = get_post_meta($id, 'fau_person_link', true);             


	    if($streetAddress || $postalCode || $addressLocality || $addressCountry) {
		    $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">'.__('Adresse',FAU_PERSON_TEXTDOMAIN).': </span><br>';    

		    if($streetAddress)          $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">'.$streetAddress.'</span>';
		    if($streetAddress && ($postalCode || $addressLocality)) $contactpoint .= '<br>';
		    if($postalCode || $addressLocality) {
			    $contactpoint .= '<span class="person-info-city">';
			    if($postalCode)         $contactpoint .= '<span itemprop="postalCode">'.$postalCode.'</span> ';  
			    if($addressLocality)	$contactpoint .= '<span itemprop="addressLocality">'.$addressLocality.'</span>';
			    $contactpoint .= '</span>';
			    }
		    if(($streetAddress || $postalCode || $addressLocality) && $addressCountry)                    $contactpoint .= '<br>';
		    if($addressCountry)         $contactpoint .= '<span class="person-info-country" itemprop="addressCountry">'.$addressCountry.'</span></';
		    $contactpoint .= '</li>';                                                
	    }



			if ((strlen($url)>4) && (strpos($url,"http") === false)) {
			    $url = 'http://'.$url;
			}


			$content = '';
			$fullname = '';
			if($honorificPrefix) 	$fullname .= '<span itemprop="honorificPrefix">'.$honorificPrefix.'</span> ';
			if($givenName) 	$fullname .= '<span itemprop="givenName">'.$givenName.'</span> ';
			if($familyName) 		$fullname .= '<span itemprop="familyName">'.$familyName.'</span>';
			if($honorificSuffix) 	$fullname .= ' '.$honorificSuffix;


			if ($jobTitle) {
			    $headline =  '<span itemprop="jobTitle">'.$jobTitle.'</span>';
			    $res .= '<h2>'.$headline.'</h2>';

			} else {
			    $headline = $fullname;
			    $res .=  '<h2 itemprop="name">'.$headline.'</h2>';
			}





			$post = get_post($id);



		    if(has_post_thumbnail($id)) {
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

	//	    if (($options['plugin_fau_person_headline'] != 'jobTitle') && ($position)) 
	//		$content .= '<li class="person-info-position"><span class="screen-reader-text">'.__('Tätigkeit','fau').': </span><strong><span itemprop="jobTitle">'.$jobTitle.'</span></strong></li>';

		    if($worksFor)	
			$content .= '<li class="person-info-institution"><span class="screen-reader-text">'.__('Einrichtung','fau').': </span><span itemprop="worksFor">'.$worksFor.'</span></li>';
		    if($telephone)			
			$content .= '<li class="person-info-phone"><span class="screen-reader-text">'.__('Telefonnummer','fau').': </span><span itemprop="telephone">'.$telephone.'</span></li>';
		    if($faxNumber)			
			$content .= '<li class="person-info-fax"><span class="screen-reader-text">'.__('Faxnummer','fau').': </span><span itemprop="faxNumber">'.$faxNumber.'</span></li>';
		    if($email)			
			$content .= '<li class="person-info-email"><span class="screen-reader-text">'.__('E-Mail','fau').': </span><a itemprop="email" href="mailto:'.strtolower($email).'">'.strtolower($email).'</a></li>';
		    if($url)		
			$content .= '<li class="person-info-www"><span class="screen-reader-text">'.__('Webseite','fau').': </span><a itemprop="url" href="'.$url.'">'.$url.'</a></li>';
		    if(!empty($contactpoint))		
			$content .= $contactpoint;
		    if($workLocation)			
			$content .= '<li class="person-info-room"><span class="screen-reader-text">' . __('Raum', 'fau') .' </span><span itemprop="workLocation">'.$workLocation.'</span></li>';



		    $content .= '</ul>';


		    $res .=  $content;
		    $res .= "\n";
		    $res .= "</div>\n";

	    return $res;

    } 
 }
?>