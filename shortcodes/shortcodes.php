<?php

    add_shortcode('person', 'fau_person' );
    add_shortcode('persons', 'fau_persons');
/*
function rrze_dlp_kontakt_shortcode( $atts ) {
    global $options;

	extract( shortcode_atts( array(
		'id'	=> '',
	
	), $atts ) );
	$out = '';
	if ((isset($id)) && ( strlen(trim($id))>0)) {
		$args = array(
			'post_type' => 'kontakt',
			'p' => $id
		);
		
		$person = new WP_Query( $args );
		if( $person->have_posts() ) { 
		    while ($person->have_posts() ) {
			    $person->the_post();	   
			    $post_id = $person->post->ID;
			    $out .= rrze_dlp_display_kontakt($post_id);
			 
		    }
		}  
		wp_reset_query();
	}
	return $out;
}
add_shortcode( 'kontakt', 'rrze_dlp_kontakt_shortcode' );
*/    
    
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
                    "extended" => FALSE,
                    ), $atts));

            $posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
            if ($posts) {
                $post = $posts[0];
                $id = $post->ID;		    
                return fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showmail);
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
                    $content .= fau_person_markup($post->ID, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showmail);
            }

            return $content;
    }

    function fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showmail)
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
                                            if($showtitle && get_post_meta($id, 'fau_person_titel', true)) 	$content .= get_post_meta($id, 'fau_person_titel', true) . ' ';
                                            $content .= get_the_title($id);
                                            if($showsuffix && get_post_meta($id, 'fau_person_abschluss', true)) 	$content .= ' '.get_post_meta($id, 'fau_person_abschluss', true);
                                    $content .= '</h3>';
                                    $content .= '<ul class="person-info">';
                                            if($showposition && get_post_meta($id, 'fau_person_position', true)) 				$content .= '<li class="person-info person-info-position"><strong>'.get_post_meta($id, 'fau_person_position', true).'</strong></li>';
                                            if($showinstitution && get_post_meta($id, 'fau_person_institution', true))			$content .= '<li class="person-info person-info-institution">'.get_post_meta($id, 'fau_person_institution', true).'</li>';
                                            if($showtelefon && get_post_meta($id, 'fau_person_telefon', true))					$content .= '<li class="person-info person-info-phone">'.get_post_meta($id, 'fau_person_telefon', true).'</li>';
                                            if(($extended || $showfax) && get_post_meta($id, 'fau_person_telefax', true))		$content .= '<li class="person-info person-info-fax">'.get_post_meta($id, 'fau_person_telefax', true).'</li>';
                                            if($showmail && get_post_meta($id, 'fau_person_email', true))					$content .= '<li class="person-info person-info-email"><a href="mailto:'.strtolower(get_post_meta($id, 'fau_person_email', true)).'">'.strtolower(get_post_meta($id, 'fau_person_email', true)).'</a></li>';
                                            if(($extended || $showwebsite) && get_post_meta($id, 'fau_person_url', true))	$content .= '<li class="person-info person-info-www"><a href="'.get_post_meta($id, 'fau_person_url', true).'">'.get_post_meta($id, 'fau_person_url', true).'</a></li>';
                                            if(($extended || $showaddress)) {
                                                //ACHTUNG: vorher css person-info-address (war Textarea)!!!
                                                if(get_post_meta($id, 'fau_person_strasse', true))  $content .= '<li class="person-info person-info-street">'.get_post_meta($id, 'fau_person_strasse', true).'</li>';
                                                if(get_post_meta($id, 'fau_person_plz', true) || get_post_meta($id, 'fau_person_ort', true)) {
                                                    $content .= '<li class="person-info person-info-city">';
                                                    if(get_post_meta($id, 'fau_person_plz', true))	$content .= get_post_meta($id, 'fau_person_plz', true).' ';  
                                                    if(get_post_meta($id, 'fau_person_ort', true))	$content .= get_post_meta($id, 'fau_person_ort', true);
                                                    $content .= '</li>';
                                                }
                                                if(get_post_meta($id, 'fau_person_land', true))	$content .= '<li class="person-info person-info-country">'.get_post_meta($id, 'fau_person_land', true).'</li>';
                                                
                                            }
                                            if(($extended || $showroom) && get_post_meta($id, 'fau_person_raum', true))		$content .= '<li class="person-info person-info-room">' . __('Raum', FAU_PERSON_TEXTDOMAIN) .' '.get_post_meta($id, 'fau_person_raum', true).'</li>';
                                            if($showoffice && get_post_meta($id, 'fau_person_sprechzeiten', true))		$content .= '<li class="person-info person-info-office">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) .': '.get_post_meta($id, 'fau_person_sprechzeiten', true).'</li>';
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