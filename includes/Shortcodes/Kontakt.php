<?php

namespace FAU_Person\Shortcodes;
use function FAU_Person\Config\getShortcodeSettings;
use function FAU_Person\Config\getShortcodeDefaults;
use FAU_Person\Main;
use FAU_Person\Data;
use RRZE\Lib\UnivIS\Data as UnivIS_Data;


defined('ABSPATH') || exit;

/**
 * Define Shortcodes 
 */
class Kontakt extends Shortcodes {
    public $pluginFile = '';
    private $settings = '';
    private $shortcodesettings = '';
    
    public function __construct($pluginFile, $settings) {
	$this->pluginFile = $pluginFile;
	$this->settings = $settings;	
	$this->shortcodesettings = getShortcodeSettings();
    }

    public function onLoaded() {	
	add_shortcode('kontakt', [$this, 'shortcode_kontakt'], 10, 2);
	add_shortcode('person', [$this, 'shortcode_kontakt'], 10, 2);
	add_shortcode('kontaktliste', [$this, 'shortcode_kontaktListe'], 10, 2);
	add_shortcode('persons', [$this, 'shortcode_kontaktListe'], 10, 2);
    }
    
    
  
    
    
    public static function shortcode_kontakt($atts, $content = null) {
	$defaults = getShortcodeDefaults('kontakt');
	$arguments = shortcode_atts($defaults, $atts);
	$arguments = self::translate_parameters($arguments);
	$displayfield = Data::get_display_field($arguments['format'],$arguments['show'],$arguments['hide']);
	
	// extract(shortcode_atts($defaults, $atts));
	
         if ((isset($arguments['category'])) && (!empty($arguments['category']))) {
            return self::shortcode_kontaktListe($atts, $content);
         } 
	 
         $id = 0;
	if (isset($arguments['id'])) {
	    $id =  $arguments['id'];
	}
	$slug = '';
	if (isset($arguments['slug'])) {
	    $slug =  $arguments['slug'];
	}
	
	if (empty($id)) {
	    if (empty($slug)) {
		return '<div class="alert alert-danger">' . sprintf(__('Bitte geben Sie den Titel oder die ID des Kontakteintrags an.', 'fau-person'), $slug) . '</div>';
	    } else {
		$posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
		if ($posts) {
		    $post = $posts[0];
		    $id = $post->ID;
		} else {
		    return '<div class="alert alert-danger">' . sprintf(__('Es konnte kein Kontakteintrag mit dem angegebenen Titel %s gefunden werden. Versuchen Sie statt dessen die Angabe der ID des Kontakteintrags.', 'fau-person'), $slug) . '</div>';
		}
	    }
	}

            if (!empty($id)) {
		Main::enqueueForeignThemes();
			
		$class = 'fau-person';
		if ($arguments['class']) {
		    $class .= ' '.esc_attr($arguments['class']);
		}
		if (isset($displayfield['border'])) {
		    if ($displayfield['border']) {
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
		$format = '';
		if (isset($arguments['format'])) {
		    $format = $arguments['format'];
		}
		
		switch($format) {
		    case 'table':
			   $content = '<table class="'.$class.'">';
			 break;
		    case 'name':
		    case 'shortlist':
			$class .= ' person liste-person';
			 $content = '<span class="'.$class.'">';
			 break;
		    case 'liste':
			$class .= ' person liste-person';
			  $content = '<ul class="'.$class.'">';
			 break;
		     default:
			 $content = '';
		}
			

                $list_ids = array_map('trim', explode(',', $id));
                $number = count($list_ids);
                $i = 1;
                foreach ($list_ids as $value) {
                    $post = get_post($value);
                    if ($post && $post->post_type == 'person') {
				
		    switch($format) {
			case 'liste':
			    $thisentry = Data::fau_person_shortlist($value, $displayfield, $arguments);
			    if (!empty($thisentry)) {
				$content .= $thisentry;
			    }
			    break;
			case 'name':
			case 'shortlist':
			    $thisentry = Data::fau_person_shortlist($value, $displayfield, $arguments);
			    if (!empty($thisentry)) {
				$content .= $thisentry;
				if ($i < $number) {
				    $content .= ", ";
				}
			    }   
			     break;

			case 'table':
			    $content .= Data::fau_person_tablerow($value, $displayfield, $arguments);
			    break;
			 case 'page':
			    $content .= Data::fau_person_page($value, $displayfield, $arguments, true);
			    break;
			case 'sidebar':
			    $content .= Data::fau_person_sidebar($value, $displayfield. $arguments);
			    break;

			default:
			    $content .= Data::fau_person_markup($value, $displayfield, $arguments);		    }
		    $i++;
			

                    } else {
                        $content .= sprintf(__('Es konnte kein Kontakteintrag mit der angegebenen ID %s gefunden werden.', 'fau-person'), $value);
                    }
       
                }

		switch($format) {
		    case 'table':
			   $content .= '</table>';
			 break;
		    case 'name':
		    case 'shortlist':
			 $content .= '</span>';
			 break;
		    case 'liste':
			  $content .= '</ul>';
			 break;
		     default:
		} 
		
                return $content;
            }
        
    }

    public static function shortcode_kontaktListe($atts, $content = null) {
	$defaults = getShortcodeDefaults('kontaktliste');
	$arguments = shortcode_atts($defaults, $atts);
	$arguments = self::translate_parameters($arguments);
	$displayfield = Data::get_display_field($arguments['format'],$arguments['show'],$arguments['hide']);
	
	$id = 0;
	if (isset($arguments['id'])) {
	    $id =  $arguments['id'];
	}
	$slug = '';
	if (isset($arguments['slug'])) {
	    $slug =  $arguments['slug'];
	}
	
	if (isset($arguments['category'])) {
	    $category = get_term_by('slug', $arguments['category'], 'persons_category');    
	    if( is_object( $category ) ) {
		$posts = get_posts(array('post_type' => 'person', 'post_status' => 'publish', 'numberposts' => 1000, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(
		    array(
			'taxonomy' => 'persons_category',
			'field' => 'id', // can be slug or id - a CPT-onomy term's ID is the same as its post ID
			'terms' => $category->term_id   // Notice: Trying to get property of non-object bei unbekannter Kategorie
		    )
		), 'suppress_filters' => false));
	    } 
	}
        
        if ( isset( $posts ) ) {
	    Main::enqueueForeignThemes();

             $posts = Data::sort_person_posts( $posts, $arguments['sort'], $arguments['order']  );   
      
	    $class = 'fau-person';
	    if ($arguments['class']) {
		$class .= ' '.esc_attr($arguments['class']);
	    }
	    if (isset($displayfield['border'])) {
		if ($displayfield['border']) {
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
	    $format = '';
	    if (isset($arguments['format'])) {
	        $format = $arguments['format'];
	    }
	    switch($format) {
		case 'table':
		       $content = '<table class="'.$class.'">';
		     break;
		case 'name':
		case 'shortlist':
		    $class .= ' person liste-person';
		     $content = '<span class="'.$class.'">';
		     break;
		case 'liste':
		    $class .= ' person liste-person';
		      $content = '<ul class="'.$class.'">';
		     break;
		 default:
		     $content = '';
	    }
	    $number = count($posts);
             $i = 1;

            foreach ($posts as $post) {
                // Bei Sortierung nach Name ist $posts ein Array
                 $value = $post['ID'];
		
		switch($format) {
		    case 'liste':
			$thisentry = Data::fau_person_shortlist($value, $displayfield, $arguments);
			if (!empty($thisentry)) {
			    $content .= $thisentry;
			}
			break;
		    case 'name':
		    case 'shortlist':
			$thisentry = Data::fau_person_shortlist($value, $displayfield, $arguments);
			if (!empty($thisentry)) {
			    $content .= $thisentry;
			    if ($i < $number) {
				$content .= ", ";
			    }
			}   
			 break;
		    
		    case 'table':
			$content .= Data::fau_person_tablerow($value, $displayfield, $arguments);
			break;
		     case 'page':
			$content .= Data::fau_person_page($value, $displayfield, $arguments, true);
			break;
		    case 'sidebar':
			$content .= Data::fau_person_sidebar($value, $displayfield,$arguments);
			break;		    
		    default:
			$content .= Data::fau_person_markup($value, $displayfield,$arguments);		
		}
		$i++;
            }
	    
	    switch($format) {
		case 'table':
		       $content .= '</table>';
		     break;
		case 'name':
		case 'shortlist':
		     $content .= '</span>';
		     break;
		case 'liste':
		      $content .= '</ul>';
		     break;
		 default:
	    } 

        } else {
            if( is_object( $category ) ) {
                $content = '<p>' . sprintf(__('Es konnten keine Kontakte in der Kategorie %s gefunden werden.', 'fau-person'), $category->slug) . '</p>'; 
            } else {
                $content = '<p>' . sprintf(__('Die Kategorie %s konnte leider nicht gefunden werden.', 'fau-person'), $atts['category']) . '</p>';                 
            }
        }

        return $content;
    }

    // Copies old direkt paraneters of the shortcode into show/hide-Parameter
    private static function translate_parameters($arguments) {
	if (!isset($arguments)) {
	    return;
	}
	$show = '';
	if (isset($arguments['show'])) {
	   $show = $arguments['show'];
	}
	$hide = '';
	if (isset($arguments['hide'])) {
	   $hide = $arguments['hide'];
	}
	
	// First we copy arguments, that stay as they was
	$validpars = 'id, slug, category, hstart, class, sort, format, order, background';
	
	$oldargs = explode(',', $validpars);
	foreach ($oldargs as $value) {
	    $key = esc_attr(trim($value));
	    if ((!empty($key)) && (isset($arguments[$key]))) {
		$res[$key] = $arguments[$key];
	    }
	}
	
	$oldparams = 'showlink, showfax, showwebsite, showaddress, showroom, showdescription, showthumb, showoffice, showtitle, showsuffix, showposition,showinstitution,showabteilung,showmail,showtelefon,showmobile,border';
	$oldargs = explode(',', $oldparams);
	foreach ($oldargs as $value) {
	    $key = esc_attr(strtolower(trim($value)));
	    $key = preg_replace('/^show/','',$key);
	    if ((!empty($key)) && (isset($arguments[$key]))) {
		if (($arguments[$key] == 1) 
		    || ($arguments[$key] == "ja")
		    || ($arguments[$key] == "true")
		    || ($arguments[$key] == "+")
		    || ($arguments[$key] == "x")) {
		    $show .= $key.', ';
		} elseif (($arguments[$key] == 0) 
		    || empty($arguments[$key])
		    || ($arguments[$key] == "-")
		    || ($arguments[$key] == "nein")
		    || ($arguments[$key] == "false")
		    || ($arguments[$key] == "no")) {
		    $hide .= $key.', ';
		}
	    }
	}
	if (!empty($show)) {    
	    $res['show'] = rtrim($show, " ,");
	} else {
	    $res['show'] = '';
	}
	if (!empty($hide)) {
	    $res['hide'] = rtrim($hide, " ,");
	} else {
	    $res['hide'] = '';
	}
	
	$format = '';
	if (isset($arguments['format'])) {
	   $format = $arguments['format'];
	} else {
	    if (isset($arguments['shortlist']) && ($arguments['shortlist'])) {
		$format = 'shortlist';
	    } elseif (isset($arguments['page']) && ($arguments['page'])) {
		$format = 'page';
	    } elseif (isset($arguments['list']) && ($arguments['list'])) {
		$format = 'liste';
	    } elseif (isset($arguments['sidebar']) && ($arguments['sidebar'])) {
		$format = 'sidebar';	
	    } elseif (isset($arguments['compactindex']) && ($arguments['compactindex'])) {
		$format = 'kompakt';		
	    } 
	}
	if (!empty($format)) {
	    $res['format'] = $format;
	}

	return $res;
    }
        
}
