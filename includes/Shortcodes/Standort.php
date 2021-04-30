<?php

namespace FAU_Person\Shortcodes;
use function FAU_Person\Config\getShortcodeSettings;
use function FAU_Person\Config\getShortcodeDefaults;

use FAU_Person\Data;

defined('ABSPATH') || exit;

/**
 * Define Shortcodes for Standort Custom Type
 */
class Standort extends Shortcodes {
    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
    	$this->pluginFile = $pluginFile;
        $this->settings = getShortcodeSettings();
        $this->settings = $this->settings['standort'];
        add_action( 'init', [$this, 'initGutenberg'] );
    }


    public function onLoaded() {	
    	add_shortcode('standort', [$this, 'shortcode_standort'], 10, 2);
    }
   

    public static function shortcode_standort( $atts, $content = null) {
    	$defaults = getShortcodeDefaults('standort');
         extract(shortcode_atts($defaults, $atts));
          

	switch($format) {
	    case 'name':
	    case 'shortlist':
		$display = 'title, permalink';
		break;
	    case 'full':
	    case 'page':
		$display = 'title, telefon, email, fax, url, content, adresse, bild, permalink';  
		break;
	    case 'liste':
		$display = 'title, telefon, email, fax, url, kurzbeschreibung, permalink';  
		break;
	     case 'sidebar':
		$display = 'title, telefon, email, fax, url, adresse, bild, permalink';  
		break;
	    default:
		$display = 'title, telefon, email, fax, url, adresse, bild, permalink';  
	}	
	$adisplay = array_map('trim', explode(',', $display));
	$showfields = array();
	foreach ($adisplay as $val) {
	    $showfields[$val] = 1;
	}
	if (isset($titletag)) {
	    $titletag = sanitize_html_class($titletag);
	}
	if (isset($hstart)) { 
	    // hstart überschreibt titletag, wenn gesetzt
	    $hstart = intval($hstart);
	
	    if (($hstart <1) || ($hstart > 6)) {
		$hstart = 2;
	    }
	    $titletag = 'h'.$hstart;
	}
	
	


        //Wenn neue Felder dazukommen, hier die Anzeigeoptionen auch mit einstellen
        if (!empty($show)) {
            $show = array_map('trim', explode(',', $show));
	    if( in_array( 'kurzbeschreibung', $show ) ) $showfields['kurzbeschreibung'] = true;  
	    if( in_array( 'adresse', $show ) )          $showfields['adresse'] = true;  
	    if( in_array( 'bild', $show ) )             $showfields['bild'] = true;  
	    if( in_array( 'title', $show ) )            $showfields['title'] = true;  
	    if( in_array( 'email', $show ) )	    $showfields['email'] = true;  
	    if( in_array( 'telephone', $show ) )	    $showfields['telephone'] = true;  
	    if( in_array( 'faxNumber', $show ) )	    $showfields['faxNumber'] = true;  
	    if( in_array( 'url', $show ) )		    $showfields['url'] = true;  
	    if( in_array( 'content', $show ) )          $showfields['content'] = true;  
	    if( in_array( 'permalink', $show ) )        $showfields['permalink'] = true;  
	}    
        if ( !empty( $hide ) ) {
            $hide = array_map('trim', explode(',', $hide));
	    if( in_array( 'kurzbeschreibung', $hide ) ) $showfields['kurzbeschreibung'] = false; 
	    if( in_array( 'adresse', $hide ) )          $showfields['adresse'] = false;  
	    if( in_array( 'bild', $hide ) )             $showfields['bild'] = false;   
	    if( in_array( 'title', $hide ) )            $showfields['title'] = false;   
	    if( in_array( 'email', $hide ) )            $showfields['email'] = false;   
	    if( in_array( 'telephone', $hide ) )            $showfields['telephone'] = false;   
	    if( in_array( 'faxNumber', $hide ) )            $showfields['faxNumber'] = false;   
	    if( in_array( 'url', $hide ) )            $showfields['url'] = false;   
	    if( in_array( 'content', $hide ) )          $showfields['content'] = false;   	
	    if( in_array( 'permalink', $hide ) )          $showfields['permalink'] = false;   	
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
		return Data::create_fau_standort($id,$showfields,$titletag);
	    }
	    
             $list_ids = array_map('trim', explode(',', $id));
             $number = count($list_ids); 
	    $output = '';
	    
	    $i = 1;
	    foreach ($list_ids as $value) {
		if (is_numeric($value)) {
		     $post = get_post($value);
		     if ($post && $post->post_type == 'standort') {
			
			 switch($format) {
			    case 'name':
				 $thisout = Data::create_fau_standort_plain($value,$showfields);
				if (!empty($thisout)) {
				    $output .= $thisout;
				    if( $i < $number )  $output .= ", ";
				    $i++;
				}
				break;
			    case 'shortlist':
				 $thisout = Data::create_fau_standort_plain($value,$showfields);
				if (!empty($thisout)) {
				    $output .= "<li>".$thisout."</li>";
				    $i++;
				}
				break;
			    case 'liste':
				 $thisout = Data::create_fau_standort($value,$showfields,$titletag);
				if (!empty($thisout)) {
				    $output .= "<li>".$thisout."</li>";
				    $i++;
				}
				break;
			    default:
				 $thisout = Data::create_fau_standort($value,$showfields,$titletag);
				if (!empty($thisout)) {
				    $output .= $thisout;
				    $i++;
				}
			 }
		     }
		}
	    }
	    if (($format == 'liste') || ($format == 'shortlist'))  {
		$content = '<div class="fau-person standort">';		
		$content .= '<ul>'.$output.'</ul>';
		$content .= '</div>';
		return $content;
	    } elseif ($format == 'name') {
		$content = '<div class="fau-person standort">';		
		$content .= '<p>'.$output.'</p>';
		$content .= '</div>';
		return $content;
	    }

            return $output;
            
        }
    }


    public function fillGutenbergOptions() {
        // we don't need slug because we have id
        unset($this->settings['slug']);

        // fill select "id"
        $this->settings['id']['field_type'] = 'select';
        $this->settings['id']['default'] = 0;
        $this->settings['id']['type'] = 'string';
        $this->settings['id']['items'] = array( 'type' => 'text' );
        $this->settings['id']['values'] = array();
        $this->settings['id']['values'][] = ['id' => 0, 'val' => __( '-- Alle --', 'fau-person' )];

        $aPerson = get_posts( array('posts_per_page'  => -1, 'post_type' => 'person', 'orderby' => 'title', 'order' => 'ASC'));
        foreach ($aPerson as $person){
            $this->settings['id']['values'][] = [
                'id' => $person->ID,
                'val' => str_replace( "'", "", str_replace( '"', "", $person->post_title ) )
            ];
        }

        return $this->settings;
    }

    public function initGutenberg() {
        if (! $this->isGutenberg()){
            return;
        }

        // get prefills for dropdowns
        $this->settings = $this->fillGutenbergOptions();

        // register js-script to inject php config to call gutenberg lib
        $editor_script = $this->settings['block']['blockname'] . '-block'; 
        $js = '../../js/' . $editor_script . '.js';

        wp_register_script(
            $editor_script,
            plugins_url( $js, __FILE__ ),
            array(
                'RRZE-Gutenberg',
            ),
            NULL
        );
        wp_localize_script( $editor_script, $this->settings['block']['blockname'] . 'Config', $this->settings );

        // register block
        register_block_type( $this->settings['block']['blocktype'], array(
            'editor_script' => $editor_script,
            'render_callback' => [$this, 'shortcode_standort'],
            'attributes' => $this->settings
            ) 
        );
    }
}
