<?php

    add_shortcode('person', 'fau_person' );
    add_shortcode('persons', 'fau_persons');
    
    function fau_person( $atts, $content = null) {
            extract(shortcode_atts(array(
                    "slug" => 'slug',
                    "showlink" => FALSE,
                    "showfax" => FALSE,
                    "showwebsite" => FALSE,
                    "showaddress" => FALSE,
                    "showroom" => FALSE,
                    "showdescription" => FALSE,
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

            $posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
            if ($posts) {
                $post = $posts[0];
                $id = $post->ID;		    
                return fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showmail, $showtelefon);
            } else {
                return sprintf(__('Es konnte kein Kontakteintrag mit der angegebenen ID %s gefunden werden.', FAU_PERSON_TEXTDOMAIN), $slug);
            }
    }

    function fau_persons( $atts, $content = null) {
            extract(shortcode_atts(array(
                    "category" => 'category',
                    "showlink" => FALSE,
                    "showfax" => FALSE,
                    "showwebsite" => FALSE,
                    "showaddress" => FALSE,
                    "showroom" => FALSE,
                    "showdescription" => FALSE,
                    "showthumb" => FALSE,
                    "showpubs" => FALSE,
                    "showoffice" => FALSE,
                    "showtitle" => TRUE,
                    "showsuffix" => TRUE,
                    "showposition" => TRUE,
                    "showinstitution" => TRUE,
                    "showmail" => TRUE,
                    "showtelefon" => TRUE,
                    "extended" => FALSE
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
                    $content .= fau_person_markup($post->ID, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showmail, $showtelefon);
            }

            return $content;
    }

    function fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showmail, $showtelefon)
    {

            $content = '<div class="person content-person">';			
                    $content .= '<div class="row">';

                            if(has_post_thumbnail($id) && $showthumb)
                            {
                                    $content .= '<div class="span1 span-small">';
                                            $content .= get_the_post_thumbnail($id, 'person-thumb-bigger');
                                    $content .= '</div>';
                            }

                            $content .= '<div class="span3">';
                                    $content .= '<h3>';
                                            if($showtitle && get_post_meta($id, 'fau_person_honorificPrefix', true)) 	$content .= get_post_meta($id, 'fau_person_honorificPrefix', true) . ' ';
                                            $content .= get_the_title($id);
                                            if($showsuffix && get_post_meta($id, 'fau_person_honorificSuffix', true)) 	$content .= ' '.get_post_meta($id, 'fau_person_honorificSuffix', true);
                                    $content .= '</h3>';
                                    $content .= '<ul class="person-info">';
                                            if($showposition && get_post_meta($id, 'fau_person_jobTitle', true)) 				$content .= '<li class="person-info person-info-position"><strong>'.get_post_meta($id, 'fau_person_jobTitle', true).'</strong></li>';
                                            if($showinstitution && get_post_meta($id, 'fau_person_worksFor', true))			$content .= '<li class="person-info person-info-institution">'.get_post_meta($id, 'fau_person_worksFor', true).'</li>';
                                            if($showtelefon && get_post_meta($id, 'fau_person_telephone', true))					$content .= '<li class="person-info person-info-phone">'.get_post_meta($id, 'fau_person_telephone', true).'</li>';
                                            if(($extended || $showfax) && get_post_meta($id, 'fau_person_faxNumber', true))		$content .= '<li class="person-info person-info-fax">'.get_post_meta($id, 'fau_person_faxNumber', true).'</li>';
                                            if($showmail && get_post_meta($id, 'fau_person_email', true))					$content .= '<li class="person-info person-info-email"><a href="mailto:'.strtolower(get_post_meta($id, 'fau_person_email', true)).'">'.strtolower(get_post_meta($id, 'fau_person_email', true)).'</a></li>';
                                            if(($extended || $showwebsite) && get_post_meta($id, 'fau_person_url', true))	$content .= '<li class="person-info person-info-www"><a href="'.get_post_meta($id, 'fau_person_url', true).'">'.get_post_meta($id, 'fau_person_url', true).'</a></li>';
                                            if(($extended || $showaddress)) {
                                                //ACHTUNG: vorher css person-info-address (war Textarea)!!!
                                                if(get_post_meta($id, 'fau_person_streetAddress', true))  $content .= '<li class="person-info person-info-street">'.get_post_meta($id, 'fau_person_streetAddress', true).'</li>';
                                                if(get_post_meta($id, 'fau_person_postalCode', true) || get_post_meta($id, 'fau_person_addressLocality', true)) {
                                                    $content .= '<li class="person-info person-info-city">';
                                                    if(get_post_meta($id, 'fau_person_postalCode', true))	$content .= get_post_meta($id, 'fau_person_postalCode', true).' ';  
                                                    if(get_post_meta($id, 'fau_person_addressLocality', true))	$content .= get_post_meta($id, 'fau_person_addressLocality', true);
                                                    $content .= '</li>';
                                                }
                                                if(get_post_meta($id, 'fau_person_addressCountry', true))	$content .= '<li class="person-info person-info-country">'.get_post_meta($id, 'fau_person_addressCountry', true).'</li>';
                                                
                                            }
                                            if(($extended || $showroom) && get_post_meta($id, 'fau_person_workLocation', true))		$content .= '<li class="person-info person-info-room">' . __('Raum', FAU_PERSON_TEXTDOMAIN) .' '.get_post_meta($id, 'fau_person_workLocation', true).'</li>';
                                            if($showoffice && get_post_meta($id, 'fau_person_hoursAvailable', true))		$content .= '<li class="person-info person-info-office">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) .': '.get_post_meta($id, 'fau_person_hoursAvailable', true).'</li>';
                                            if($showpubs && get_post_meta($id, 'fau_person_pubs', true))		$content .= '<li class="person-info person-info-pubs">' . __('Publikationen', FAU_PERSON_TEXTDOMAIN) .': '.get_post_meta($id, 'fau_person_Publikationen', true).'</li>';                                            
                                            $content .= '</ul>';

                            $content .= '</div>';
                            $content .= '<div class="span3">';
                                    if(($extended || $showdescription) && get_post_meta($id, 'fau_person_freitext', true))		$content .= '<div class="person-info person-info-description">'.get_post_meta($id, 'fau_person_freitext', true).'</div>';
                                    if($showlink && get_post_meta($id, 'fau_person_link', true)) {
                                            $content .= '<div class="person-info person-info-more"><a class="person-read-more" href="'.get_post_meta($id, 'fau_person_link', true).'">';
                                            $content .= __('Mehr', FAU_PERSON_TEXTDOMAIN) . ' ›</a></div>';
                                    }

                            $content .= '</div>';
                    $content .= '</div>';

            $content .= '</div>';

            return $content;
    }
   
?>