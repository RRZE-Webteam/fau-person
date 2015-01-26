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
                    "extended" => FALSE,
                    ), $atts));

            $posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
            if ($posts) {
                $post = $posts[0];
                $id = $post->ID;		    
                return fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription);
            } else {
                return sprintf(__('Es konnte kein Kontakteintrag mit der angegebenen ID %s gefunden werden.','fau'), $slug);
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
                    $content .= fau_person_markup($post->ID, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription);
            }

            return $content;
    }

    function fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription)
    {

        $person_title = get_post_meta($id);
        foreach($person_title as $key => $value) {
            $content2 = $key . ' hat den Wert ' . $value[0];
            _rrze_debug($content2);
        }
        //global $person_fields;
        //_rrze_debug($person_fields);
            $content = '<div class="person content-person">';			
                    $content .= '<div class="row">';

                            /*if(has_post_thumbnail($id))
                            {
                                    $content .= '<div class="span1 span-small">';
                                            $content .= get_the_post_thumbnail($id, 'person-thumb-bigger');
                                    $content .= '</div>';
                            }*/

                            $content .= '<div class="span3">';
                                    $content .= '<h3>';
                                            if(get_post_meta($id, '_person_titel', true)) 	$content .= get_post_meta($id, '_person_titel', true) . ' ';
                                            if(get_post_meta($id, '_person_pseudo', true)) {
                                                $content .= get_post_meta($id, '_person_pseudo', true) . ' ';                                      
                                            } else { 
                                                if(get_post_meta($id, '_person_vorname', true)) 	$content .= get_post_meta($id, '_person_vorname', true).' ';
                                                if(get_post_meta($id, '_person_nachname', true)) 		$content .= get_post_meta($id, '_person_nachname', true);
                                            }
                                            if(get_post_meta($id, '_person_abschluss', true)) 	$content .= ' '.get_post_meta($id, '_person_abschluss', true);
                                    $content .= '</h3>';
                                    $content .= '<ul class="person-info">';
                                            if(get_post_meta($id, '_person_position', true)) 				$content .= '<li class="person-info person-info-position"><strong>'.get_post_meta($id, '_person_position', true).'</strong></li>';
                                            if(get_post_meta($id, '_person_institution', true))			$content .= '<li class="person-info person-info-institution">'.get_post_meta($id, '_person_institution', true).'</li>';
                                            if(get_post_meta($id, '_person_telefon', true))					$content .= '<li class="person-info person-info-phone">'.get_post_meta($id, '_person_telefon', true).'</li>';
                                            if(($extended || $showfax) && get_post_meta($id, '_person_telefax', true))		$content .= '<li class="person-info person-info-fax">'.get_post_meta($id, '_person_telefax', true).'</li>';
                                            if(get_post_meta($id, '_person_email', true))					$content .= '<li class="person-info person-info-email"><a href="mailto:'.get_post_meta($id, '_person_email', true).'">'.get_post_meta($id, '_person_email', true).'</a></li>';
                                            if(($extended || $showwebsite) && get_post_meta($id, '_person_url', true))	$content .= '<li class="person-info person-info-www"><a href="'.get_post_meta($id, '_person_url', true).'">'.get_post_meta($id, '_person_url', true).'</a></li>';
                                            if(($extended || $showaddress)) {
                                                $content .= '<li class="person-info person-info-address">';
                                                if(get_post_meta($id, '_person_strasse', true))	$content .= get_post_meta($id, '_person_strasse', true);
                                                if(get_post_meta($id, '_person_plz', true))	$content .= get_post_meta($id, '_person_plz', true).' ';  
                                                if(get_post_meta($id, '_person_ort', true))	$content .= get_post_meta($id, '_person_ort', true);
                                                if(get_post_meta($id, '_person_land', true))	$content .= get_post_meta($id, '_person_land', true);
                                                $content .= '</li>';
                                            }
                                            if(($extended || $showroom) && get_post_meta($id, '_person_raum', true))		$content .= '<li class="person-info person-info-room">' . __('Raum', 'fau') .' '.get_post_meta($id, '_person_raum', true).'</li>';
                                    $content .= '</ul>';

                            $content .= '</div>';
                            $content .= '<div class="span3">';
                                    if(($extended || $showdescription) && get_post_meta($id, '_person_freitext', true))		$content .= '<div class="person-info person-info-description">'.get_post_meta($id, '_person_freitext', true).'</div>';

                                    if($showlink && get_post_meta($id, '_person_abschluss', true)) {
                                            $content .= '<div class="person-info person-info-more"><a class="person-read-more" href="'.get_post_meta('link', $id).'">';
                                            $content .= __('Mehr', 'fau') . ' ›</a></div>';
                                    }

                            $content .= '</div>';
                    $content .= '</div>';

            $content .= '</div>';

            return $content;
    }
   
?>