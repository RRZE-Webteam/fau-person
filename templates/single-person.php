<?php
 

get_header(); ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <?php get_template_part('hero', 'small'); ?>
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="span8">
                    <?php 
                    $id = $post->ID;

                    if (!empty($id) && !post_password_required() )
                    {           
                    $post = get_post_meta($id); 

                    $content = '<div class="person">';
                    $content .= '<div class="row">';
                    if(has_post_thumbnail($id))
                    {
                        $content .= '<div class="person-info person-info-thumb">';
                        $content .= get_the_post_thumbnail($id, 'person-thumb');
                        $content .= '</div>';
                    }

                    $content .= '<div class="person-info">';
                        $content .= '<h1>';
                            if(get_post_meta($id, 'fau_person_honorificPrefix', true)) $content .= get_post_meta($id, 'fau_person_honorificPrefix', true).' ';
                            $content .= get_the_title($id);
                            if(get_post_meta($id, 'fau_person_honorificSuffix', true)) $content .= ' '.get_post_meta($id, 'fau_person_honorificSuffix', true);
                            $content .= '</h1>';

                            if(get_post_meta($id, 'fau_person_givenName', true) || get_post_meta($id, 'fau_person_familyName', true) || get_post_meta($id, 'fau_person_alternateName', true) ) {
                                $content .= '<h2>';
                                if((get_post_meta($id, 'fau_person_typ', true)=='pseudo') || (get_post_meta($id, 'fau_person_typ', true)=='einrichtung')) {
                                    if(get_post_meta($id, 'fau_person_alternateName', true)) $content .= get_post_meta($id, 'fau_person_alternateName', true);   
                                } else {
                                    if(get_post_meta($id, 'fau_person_givenName', true)) $content .= get_post_meta($id, 'fau_person_givenName', true).' ';      
                                    if(get_post_meta($id, 'fau_person_familyName', true)) $content .= get_post_meta($id, 'fau_person_familyName', true);  
                                }
                                $content .= '</h2>';
                            }
                            $content .= '<ul class="person-info">';
                            if(get_post_meta($id, 'fau_person_jobTitle', true)) $content .= '<li class="person-info person-info-position"><strong>'.get_post_meta($id, 'fau_person_jobTitle', true).'</strong></li>';
                            if(get_post_meta($id, 'fau_person_worksFor', true)) $content .= '<li class="person-info person-info-institution">'.get_post_meta($id, 'fau_person_worksFor', true).'</li>';
                            if(get_post_meta($id, 'fau_person_telephone', true)) $content .= '<li class="person-info person-info-phone">'.get_post_meta($id, 'fau_person_telephone', true).'</li>';
                            if(get_post_meta($id, 'fau_person_faxNumber', true)) $content .= '<li class="person-info person-info-fax">'.get_post_meta($id, 'fau_person_faxNumber', true).'</li>';
                            if(get_post_meta($id, 'fau_person_email', true)) $content .= '<li class="person-info person-info-email"><a href="mailto:'.strtolower(get_post_meta($id, 'fau_person_email', true)).'">'.strtolower(get_post_meta($id, 'fau_person_email', true)).'</a></li>';
                            if(get_post_meta($id, 'fau_person_url', true)) $content .= '<li class="person-info person-info-www"><a href="'.get_post_meta($id, 'fau_person_url', true).'">'.get_post_meta($id, 'fau_person_url', true).'</a></li>';
                            if(get_post_meta($id, 'fau_person_streetAddress', true))  $content .= '<li class="person-info person-info-street">'.get_post_meta($id, 'fau_person_streetAddress', true).'</li>';
                            if(get_post_meta($id, 'fau_person_postalCode', true) || get_post_meta($id, 'fau_person_addressLocality', true)) {
                                                    $content .= '<li class="person-info person-info-city">';
                                                    if(get_post_meta($id, 'fau_person_postalCode', true))	$content .= get_post_meta($id, 'fau_person_postalCode', true).' ';  
                                                    if(get_post_meta($id, 'fau_person_addressLocality', true))	$content .= get_post_meta($id, 'fau_person_addressLocality', true);
                                                    $content .= '</li>';
                                                }
                            if(get_post_meta($id, 'fau_person_addressCountry', true))	$content .= '<li class="person-info person-info-country">'.get_post_meta($id, 'fau_person_addressCountry', true).'</li>';
                                                
                            if(get_post_meta($id, 'fau_person_workLocation', true)) $content .= '<li class="person-info person-info-room">' . __('Raum', FAU_PERSON_TEXTDOMAIN) . ' '.get_post_meta($id, 'fau_person_workLocation', true).'</li>';
                            if(get_post_meta($id, 'fau_person_hoursAvailable', true)) $content .= '<li class="person-info person-info-room">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': ' .get_post_meta($id, 'fau_person_hoursAvailable', true).'</li>';
                            if(get_post_meta($id, 'fau_person_pubs', true)) $content .= '<li class="person-info person-info-pubs">' . __('Publikationen', FAU_PERSON_TEXTDOMAIN) . ': '.get_post_meta($id, 'fau_person_pubs', true).'</li>';
                            $content .= '</ul>';
                            if(get_post_meta($id, 'fau_person_freitext', true)) $content .= '<div class="person-info person-info-description">'.get_post_meta($id, 'fau_person_freitext', true).'</div>';
                            if(get_post_meta($id, 'fau_person_link', true))  $content .= '<div class="person-info person-info-more"><a class="person-read-more" href="'.get_post_meta($id, 'fau_person_link', true).'">'. __('Mehr', FAU_PERSON_TEXTDOMAIN) . ' ›</a></div>';
                            $content .= '</div>';
                            $content .= '</div>';
                            $content .= '</div>';
                        } else {
                            $content = __('Es konnte kein Kontakteintrag gefunden werden.', FAU_PERSON_TEXTDOMAIN);
                        }
                    echo $content;
                ?>
                        <div style="margin-top: 2em;">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile;
get_footer();
