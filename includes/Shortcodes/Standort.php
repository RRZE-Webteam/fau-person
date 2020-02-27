<?php

namespace FAU_Person\Shortcodes;
use function FAU_Person\Config\getShortcodeSettings;
use function FAU_Person\Config\getShortcodeDefaults;

use FAU_Person\Data;
use FAU_Person\Schema;
use UnivIS_Data;
use sync_helper;


defined('ABSPATH') || exit;

/**
 * Define Shortcodes for Standort Custom Type
 */
class Standort extends Shortcodes {
    protected $pluginFile;
    private $settings = '';
    private $shortcodesettings = '';
    
    public function __construct($pluginFile, $settings) {
	$this->pluginFile = $pluginFile;
	$this->settings = $settings;	
	$this->shortcodesettings = getShortcodeSettings();
    }


    public function onLoaded() {	
	add_shortcode('standort', [$this, 'shortcode_standort'], 10, 2);
    }
   
/*
    public function gutenberg_init() {
        // Skip block registration if Gutenberg is not enabled/merged.
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }
        $js = '../js/gutenberg.js';
        
        wp_register_script(
            'fau-person' . '-editor',
            plugins_url( $js, __FILE__ ),
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor'
            ),
            filemtime( dirname( __FILE__ ) . '/' . $js )
        );

        wp_localize_script( 'fau-person' . '-editor', 'phpConfig', $this->shortcodesettings );

        register_block_type( 'FAU_Person/Standort', array(
            'editor_script' => 'fau-person' . '-editor',
            'render_callback' => [$this, 'shortcode_standort'],
            'attributes' => $this->shortcodesettings['standort']
            ) 
        );
    }     
    */
    public static function shortcode_standort( $atts, $content = null) {
	$defaults = getShortcodeDefaults('standort');
         extract(shortcode_atts($defaults, $atts));
          
        $sidebar = '';
        $page = '';
        $list = '';
        $showaddress = '';
        $showlist = '';
        $showthumb = '';
        $showsidebar = '';
        $shortlist = '';

	switch($format) {
	    case 'name':
	    case 'shortlist':
		$display = 'title';
		break;
	    case 'full':
	    case 'page':
		$display = 'title, content, adresse, bild';  
		break;
	    case 'liste':
		$display = 'title, kurzbeschreibung, bild';  
		break;
	     case 'sidebar':
		$display = 'title, adresse, bild';  
		break;
	    default:
		$display = 'title, content, adresse, bild';  
	}	
	$adisplay = array_map('trim', explode(',', $display));
	$showfields = array();
	foreach ($adisplay as $val) {
	    $showfields[$val] = 1;
	}
	
        //Wenn neue Felder dazukommen, hier die Anzeigeoptionen auch mit einstellen
        if (!empty($show)) {
            $show = array_map('trim', explode(',', $show));
            if( in_array( 'kurzbeschreibung', $show ) ) $showfields['kurzbeschreibung'] = true;  
            if( in_array( 'adresse', $show ) )          $showfields['adresse'] = true;  
            if( in_array( 'bild', $show ) )             $showfields['bild'] = true;  
            if( in_array( 'title', $show ) )            $showfields['title'] = true;  
            if( in_array( 'content', $show ) )          $showfields['content'] = true;  
	}    
        if ( !empty( $hide ) ) {
            $hide = array_map('trim', explode(',', $hide));
            if( in_array( 'kurzbeschreibung', $hide ) ) $showfields['kurzbeschreibung'] = false; 
            if( in_array( 'adresse', $hide ) )          $showfields['adresse'] = false;  
            if( in_array( 'bild', $hide ) )             $showfields['bild'] = false;   
	   if( in_array( 'title', $hide ) )            $showfields['title'] = false;   
	   if( in_array( 'content', $hide ) )          $showfields['content'] = false;   	   
        }
                

        if (empty($id)) {
            if (empty($slug)) {
                return '<div class="alert alert-danger">' . sprintf(__('Bitte geben Sie den Titel oder die ID des Standorteintrags an.', 'fau-person'), $slug) . '</div>';
            } else {
                $posts = get_posts(array('name' => $slug, 'post_type' => 'standort', 'post_status' => 'publish'));
                if ($posts) {
                    $post = $posts[0];
                    $id = $post->ID;
                } else {
                    return '<div class="alert alert-danger">' . sprintf(__('Es konnte kein Standorteintrag mit dem angegebenen Titel %s gefunden werden. Versuchen Sie statt dessen die Angabe der ID des Standorteintrags.', 'fau-person'), $slug) . '</div>';
                }
            }
        }

        if (!empty($id)) {
	    if (is_numeric($id)) {
		return self::create_fau_standort($id,$showfields);
	    }
	    
	    
	    
            $list_ids = array_map('trim', explode(',', $id));
            if ( $list ) {
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
			$liste .=  sprintf(__('Es konnte kein Standort mit der angegebenen ID %s gefunden werden.', 'fau-person'), $value);
			if( $i < $number )  $liste .= ", ";
		    }
		    $i++;
		}
	    
            if ( $list ) {
                $liste .= "</ul>\n";
            } else {
                $liste .= "</p>\n";                
            } 
            return $liste;
            
        }

}
    public static function create_fau_standort($id, $showfields) {
	if (!isset($id)) {
	    return;
	}
	if (!is_array($showfields)) {
	    return;
	}
	$fields = sync_helper::get_fields( $id, get_post_meta($id, 'fau_person_univis_id', true), 0 );
	$permalink = get_permalink( $id );
	
	if (isset($showfields['kurzbeschreibung']) && ($showfields['kurzbeschreibung'])) {
	    $excerpt = get_post_field( 'post_excerpt', $id );         
	    $fields['description'] = $excerpt;
	}
	$schema = Schema::create_Place($fields,'location','','div',true,$showfields['adresse']);
	
	$title = '';
	if (isset($showfields['title']) && ($showfields['title'])) {	
	    if( !empty( get_the_title($id) ) ) {                                                
		$title .= get_the_title($id);
	    }       
	}
                    
	$content = '<div class="fau-person standort" itemscope itemtype="http://schema.org/Organization">';		
	if( !empty( $title ) ) {                                                
              $content .= '<h2 itemprop="name">' . $title . '</h2>';
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
            $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">'.__('Adresse','fau-person').': <br></span>';            
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
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $personlink . '">';
            if (has_post_thumbnail($id)) {
		    $content .= get_the_post_thumbnail($id);
            } else {
                    $bild = dirname($this->pluginFile) .'/images/platzhalter-organisation.png';
                    $content .=  '<img src="'.$bild.'" width="90" height="120" alt="">';
            }
            $content .= '</a>';
            $content .= '</div>';
        }
        $content .= '<div class="span3">';
        $content .= '<h3>';        
        $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
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
 
     
        
        $fields = Data::get_fields_standort($id, get_post_meta($id, 'fau_person_standort_id', true), 0);
        // extract($fields);
	
	
        	$content = '<div class="person">';
        $fullname = '';
        if( !empty( get_the_title($id) ) ) {                                                
            $fullname .= get_the_title($id);
        }
        $content .= '<h2 itemprop="name">' . $fullname . '</h2>';

        
        
        $post = get_post($id);
        if (has_post_thumbnail($id)) {
            $content .= '<div itemprop="image" class="alignright">';
            $content .= get_the_post_thumbnail($id);
            $content .= '</div>';
        }

	if (isset($fields)) {
            $content .= '<div class="person-info-address"><span class="screen-reader-text">' . __('Adresse', 'fau-person') . ': <br></span>';
            $content .= Schema::create_PostalAdress($fields);
            $content .= '</div>';
        }

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
                $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
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
                    $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">' . __('Adresse', 'fau-person') . ': <br></span>';
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
                $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $personlink . '">';
                $content .= get_the_post_thumbnail($id);
                $content .= '</a>';
                $content .= '</div>';
            }

            $content .= '<div class="span3">';
            $content .= '<h3>';
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', 'fau-person'), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
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

