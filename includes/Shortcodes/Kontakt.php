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
	extract(shortcode_atts($defaults, $atts));
	

	
        if ($atts['category']) {
	    $out = self::shortcode_kontaktListe($atts, $content);
            return $out;
        } 
	
	
            $shortlist = '';
            $sidebar = '';
            $compactindex = '';
            $page = '';
            $list = '';
            $showvia = '';
            if (!empty($format)) {
                //format-Parameter: 
                //name (Alternativ shortlist, $shortlist = 1), 
                //liste ($list = 1 und $showlist = 1), wie Name nur mit Aufzählungszeichen, 
                //sidebar ($showsidebar, $sidebar, $showabteilung, $showtitle, $showsuffix, $showtelefon, $showmail, $showwebsite, $showdescription, $showthumb = 1), 
                //index (keine Formatangabe, default-Wert), 
                //page (Alternativ full, $page = 1), 
                //plain, 
                //table,
                //accordion,
                if ($format == 'name' || $format == 'shortlist')
                    $shortlist = 1;
                if ($format == 'sidebar') {
		    
		    
                    $showsidebar = 1;
                    $sidebar = 1;
                    $showinstitution = 1;
                    $showabteilung = 1;
                    $showposition = 1;
                    $showtitle = 1;
                    $showsuffix = 1;
                    $showaddress = 1;
                    $showroom = 1;
                    $showtelefon = 1;
                    $showfax = 1;
                    $showmobile = 0;
                    $showmail = 1;
                    $showwebsite = 1;
                    $showdescription = 1;
                    $showoffice = 1;
                    $showthumb = 1;
                }
                if ($format == 'full' || $format == 'page') {
                    $page = 1;
                    $showname = 0;
                }
                if ($format == 'liste' || $format == 'listentry') {
                    $list = 1;
                    $showlist = 1;
                    $showtelefon = 0;
                    $showmail = 0;
                }
                if ($format == 'plain') {
                    $showlist = 0;
                    $showinstitution = 0;
                    $showabteilung = 0;
                    $showposition = 0;
                    $showtitle = 0;
                    $showsuffix = 0;
                    $showaddress = 0;
                    $showroom = 0;
                    $showtelefon = 0;
                    $showfax = 0;
                    $showmobile = 0;
                    $showmail = 0;
                    $showwebsite = 0;
                    $showlink = 0;
                    $showdescription = 0;
                    $showoffice = 0;
                    $showthumb = 0;
                    $showvia = 0;
                }
                if ($format == 'kompakt' || $format == 'compactindex') {
                    $compactindex = 1;
                    $showinstitution = 0;
                    $showabteilung = 0;
                    $showposition = 1;
                    $showtitle = 1;
                    $showsuffix = 1;
                    $showaddress = 1;
                    $showroom = 0;
                    $showtelefon = 1;
                    $showfax = 0;
                    $showmobile = 0;
                    $showmail = 1;
                    $showwebsite = 0;
                    $showdescription = 0;
                    $showoffice = 0;
                    $showthumb = 1;
                }
            }
            if ($extended == 1) {
                $showlist = 1;
                $showinstitution = 0;
                $showfax = 0;
                $showwebsite = 0;
                $showthumb = 1;
            }
            // Wenn neue Felder dazukommen, hier die Anzeigeoptionen auch mit einstellen
            if (!empty($show)) {
                $show = array_map('trim', explode(',', $show));                                       // schema.org-Bezeichnungen = Variablenname
                if (in_array('kurzbeschreibung', $show))
                    $showlist = 1;          //
                if (in_array('organisation', $show))
                    $showinstitution = 1;   // $worksFor
                if (in_array('abteilung', $show))
                    $showabteilung = 1;     // $department
                if (in_array('position', $show))
                    $showposition = 1;      // $jobTitle
                if (in_array('titel', $show))
                    $showtitle = 1;         // $honorificPrefix
                if (in_array('suffix', $show))
                    $showsuffix = 1;        // $honorificSuffix
                if (in_array('adresse', $show))
                    $showaddress = 1;       // $streetAddress, $postalCode, $addressLocality, $addressCountry   
                if (in_array('raum', $show))
                    $showroom = 1;          // $workLocation
                if (in_array('telefon', $show))
                    $showtelefon = 1;       // $telephone   
                if (in_array('fax', $show))
                    $showfax = 1;           // $faxNumber
                if (in_array('mobil', $show))
                    $showmobile = 1;        // $mobilePhone
                if (in_array('mail', $show))
                    $showmail = 1;          // $email
                if (in_array('webseite', $show))
                    $showwebsite = 1;       // $url  
                if (in_array('mehrlink', $show))
                    $showlink = 1;          // $link
                if (in_array('kurzauszug', $show))
                    $showdescription = 1;   // $description (erscheint bei Sidebar)
                if (in_array('sprechzeiten', $show))
                    $showoffice = 1;        // $hoursAvailable

                if (in_array('bild', $show))
                    $showthumb = 1;         //
                if (in_array('ansprechpartner', $show))
                    $showvia = 1;           //
                if (in_array('name', $show))
                    $showname = 1;           // bei format="page" Anzeige des Namens über den Daten
                if (in_array('rahmen', $show))  
                    $border = 1;            // ergänzende Klasse noborder bei false              
            }
            if (!empty($hide)) {
                $hide = array_map('trim', explode(',', $hide));
                if (in_array('kurzbeschreibung', $hide))
                    $showlist = 0;
                if (in_array('organisation', $hide))
                    $showinstitution = 0;
                if (in_array('abteilung', $hide))
                    $showabteilung = 0;
                if (in_array('position', $hide))
                    $showposition = 0;
                if (in_array('titel', $hide))
                    $showtitle = 0;
                if (in_array('suffix', $hide))
                    $showsuffix = 0;
                if (in_array('adresse', $hide))
                    $showaddress = 0;
                if (in_array('raum', $hide))
                    $showroom = 0;
                if (in_array('telefon', $hide))
                    $showtelefon = 0;
                if (in_array('fax', $hide))
                    $showfax = 0;
                if (in_array('mobil', $hide))
                    $showmobile = 0;
                if (in_array('mail', $hide))
                    $showmail = 0;
                if (in_array('webseite', $hide))
                    $showwebsite = 0;
                if (in_array('mehrlink', $hide))
                    $showlink = 0;
                if (in_array('kurzauszug', $hide))
                    $showdescription = 0;
                if (in_array('sprechzeiten', $hide))
                    $showoffice = 0;

                if (in_array('bild', $hide))
                    $showthumb = 0;
                if (in_array('ansprechpartner', $hide))
                    $showvia = 0;
                if (in_array('name', $hide))
                    $showname = 0;           // bei format="page" Anzeige des Namens über den Daten
                if (in_array('rahmen', $hide))  // ergänzende Klasse noborder bei false
                    $border = 0;
            }
            
            $bg_array = array('grau', 'fau', 'phil', 'med', 'nat', 'tf', 'rw');
            if (in_array($background, $bg_array)) {
                $bg_color = ' background-' . $background;
            } else {
                $bg_color = '';
            }

            if ($border == 0) {
                $noborder = ' noborder';
            } else {
                $noborder = '';
            }

            $hstart = absint($hstart);
            if (!$hstart) {
                $hstart = 3;
            } elseif ($hstart > 5) {
                $hstart = 5;
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
		
                if ($shortlist) {
                    $liste = '<span class="liste-person" itemscope itemtype="http://schema.org/Person">';
                } elseif ($list) {
                    $liste = '<ul class="person liste-person" itemscope itemtype="http://schema.org/Person">';
                    $liste .= "\n";
                } else {
                    $liste = '';
                }

                $list_ids = array_map('trim', explode(',', $id));
                $number = count($list_ids);
                $i = 1;
                foreach ($list_ids as $value) {
                    $post = get_post($value);
                    if ($post && $post->post_type == 'person') {
                        if ($page) {
                            $liste .= Data::fau_person_page($value, 1, $showname);
                        } elseif ($shortlist) {
                            $liste .= Data::fau_person_shortlist($value, $showlist, 0, $showmail, $showtelefon);
                            if ($i < $number)
                                $liste .= ", ";
                        } elseif ($list) {
                            $liste .= '<li class="person-info">' . "\n";
                            $liste .= Data::fau_person_shortlist($value, $showlist, 1, $showmail, $showtelefon);
                            $liste .= "</li>\n";
                        } elseif ($sidebar) {
                            $liste .= Data::fau_person_sidebar($value, 0, $showlist, $showinstitution, $showabteilung, $showposition, $showtitle, $showsuffix, $showaddress, $showroom, $showtelefon, $showfax, $showmobile, $showmail, $showwebsite, $showlink, $showdescription, $showoffice, $showthumb, $showvia, $hstart);
                        } else {
                            $liste .= Data::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex, $noborder, $hstart, $bg_color);
                        }
                    } else {
                        $liste .= sprintf(__('Es konnte kein Kontakteintrag mit der angegebenen ID %s gefunden werden.', 'fau-person'), $value);
                        if ($i < $number)
                            $liste .= ", ";
                    }
                    $i++;
                }
                if ($shortlist) {
                    $liste .= "</span>";
                } elseif ($list) {
                    $liste .= "</ul>\n";
                } else {
                    $liste .= '';           
                }
                return $liste;
            }
        
    }

    public static function shortcode_kontaktListe($atts, $content = null) {
	$defaults = getShortcodeDefaults('kontaktliste');
	$arguments = shortcode_atts($defaults, $atts);
	$arguments = self::translate_parameters($arguments);
	$displayfield = Data::get_display_field($arguments['format'],$arguments['show'],$arguments['hide']);
	
	extract($arguments);
	

        $content = '';

        $shortlist = '';
        $sidebar = '';
        $compactindex = 0;
        $page = '';
        $list = '';
        $showvia = '';

	
	
        if (!empty($format)) {
            //format-Parameter: 
            //name (Alternativ shortlist, $shortlist = 1), 
            //liste ($list = 1 und $showlist = 1), wie Name nur mit Aufzählungszeichen, 
            //sidebar ($showsidebar, $sidebar, $showabteilung, $showtitle, $showsuffix, $showtelefon, $showmail, $showwebsite, $showdescription, $showthumb = 1), 
            //index (keine Formatangabe, default-Wert), 
            //page (Alternativ full, $page = 1), 
            //plain, 
            //table,
            //accordion,
	    
	    

	    switch($format) {
		case 'name':
		case 'shortlist':
		    $shortlist = 1;
		    $display = 'title, name, honorificPrefix, honorificSuffix, permalink, telefon, email, fax, url';
		    break;
		case 'full':
		case 'page':
		    $display = 'title, telefon, email, fax, url, content, adresse, bild, permalink';  
		    $page = 1;
		    $showname = 1;
		    break;
		case 'liste':
		    $display = 'title, telefon, email, fax, url, kurzbeschreibung, permalink';  
		    $list = 1;
		    $showlist = 1;
		    $showtelefon = 0;
		    $showmail = 0;
		    break;
		case 'sidebar':
		    $display = 'title, telefon, email, fax, url, adresse, bild, permalink';  

		    $showsidebar = 1;
		    $sidebar = 1;
		    $showinstitution = 1;
		    $showabteilung = 1;
		    $showposition = 1;
		    $showtitle = 1;
		    $showsuffix = 1;
		    $showaddress = 1;
		    $showroom = 1;
		    $showtelefon = 1;
		    $showfax = 1;
		    $showmobile = 0;
		    $showmail = 1;
		    $showwebsite = 1;
		    $showdescription = 1;
		    $showoffice = 1;
		    $showthumb = 1;

		    break;

		case 'kompakt':
		    $compactindex = 1;
		    $showinstitution = 0;
		    $showabteilung = 0;
		    $showposition = 1;
		    $showtitle = 1;
		    $showsuffix = 1;
		    $showaddress = 1;
		    $showroom = 0;
		    $showtelefon = 1;
		    $showfax = 0;
		    $showmobile = 0;
		    $showmail = 1;
		    $showwebsite = 0;
		    $showdescription = 0;
		    $showoffice = 0;
		    $showthumb = 1;
		    break;
		default:
		    $display = 'title, telefon, email, fax, url, adresse, bild, permalink';  
	    }

        }
        // Wenn neue Felder dazukommen, hier die Anzeigeoptionen auch mit einstellen
        if (!empty($show)) {
            $show = array_map('trim', explode(',', $show));                                       // schema.org-Bezeichnungen = Variablenname
            if (in_array('kurzbeschreibung', $show))
                $showlist = 1;          //
            if (in_array('organisation', $show))
                $showinstitution = 1;   // $worksFor
            if (in_array('abteilung', $show))
                $showabteilung = 1;     // $department
            if (in_array('position', $show))
                $showposition = 1;      // $jobTitle
            if (in_array('titel', $show))
                $showtitle = 1;         // $honorificPrefix
            if (in_array('suffix', $show))
                $showsuffix = 1;        // $honorificSuffix
            if (in_array('adresse', $show))
                $showaddress = 1;       // $streetAddress, $postalCode, $addressLocality, $addressCountry   
            if (in_array('raum', $show))
                $showroom = 1;          // $workLocation
            if (in_array('telefon', $show))
                $showtelefon = 1;       // $telephone   
            if (in_array('fax', $show))
                $showfax = 1;           // $faxNumber
            if (in_array('mobil', $show))
                $showmobile = 1;        // $mobilePhone
            if (in_array('mail', $show))
                $showmail = 1;          // $email
            if (in_array('webseite', $show))
                $showwebsite = 1;       // $url  
            if (in_array('mehrlink', $show))
                $showlink = 1;          // $link
            if (in_array('kurzauszug', $show))
                $showdescription = 1;   // $description (erscheint bei Sidebar)
            if (in_array('sprechzeiten', $show))
                $showoffice = 1;        // $hoursAvailable

            if (in_array('bild', $show))
                $showthumb = 1;         //
            if (in_array('ansprechpartner', $show))
                $showvia = 1;           //
            if (in_array('name', $show))
                $showname = 1;           // bei format="page" Anzeige des Namens über den Daten
            if (in_array('rahmen', $show))  // ergänzende Klasse noborder bei false
                $border = 1;
        }
        if (!empty($hide)) {
            $hide = array_map('trim', explode(',', $hide));
            if (in_array('kurzbeschreibung', $hide))
                $showlist = 0;
            if (in_array('organisation', $hide))
                $showinstitution = 0;
            if (in_array('abteilung', $hide))
                $showabteilung = 0;
            if (in_array('position', $hide))
                $showposition = 0;
            if (in_array('titel', $hide))
                $showtitle = 0;
            if (in_array('suffix', $hide))
                $showsuffix = 0;
            if (in_array('adresse', $hide))
                $showaddress = 0;
            if (in_array('raum', $hide))
                $showroom = 0;
            if (in_array('telefon', $hide))
                $showtelefon = 0;
            if (in_array('fax', $hide))
                $showfax = 0;
            if (in_array('mobil', $hide))
                $showmobile = 0;
            if (in_array('mail', $hide))
                $showmail = 0;
            if (in_array('webseite', $hide))
                $showwebsite = 0;
            if (in_array('mehrlink', $hide))
                $showlink = 0;
            if (in_array('kurzauszug', $hide))
                $showdescription = 0;
            if (in_array('sprechzeiten', $hide))
                $showoffice = 0;

            if (in_array('bild', $hide))
                $showthumb = 0;
            if (in_array('ansprechpartner', $hide))
                $showvia = 0;
            if (in_array('name', $hide))
                $showname = 0;           // bei format="page" Anzeige des Namens über den Daten
            if (in_array('rahmen', $hide))  // ergänzende Klasse noborder bei false
                $border = 0;
        }
        if ($extended == 1) {
            $showlist = 1;
            $showinstitution = 0;
            $showfax = 0;
            $showwebsite = 0;
            $showthumb = 1;
        }

        $bg_array = array('grau', 'fau', 'phil', 'med', 'nat', 'tf', 'rw');
        if (in_array($background, $bg_array)) {
            $bg_color = ' background-' . $background;
        } else {
            $bg_color = '';
        }
            
        if ( $border == 0 ) {
            $noborder = ' noborder';
        } else {
            $noborder = '';
        }

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
        
        if ( isset( $posts ) ) {
	    Main::enqueueForeignThemes();

             $posts = Data::sort_person_posts( $posts, $arguments['sort'], $arguments['order']  );   
      
	    $class = 'fau-person';
	    if ($arguments['class']) {
		$class .= ' '.esc_attr($arguments['class']);
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
			$thisentry = Data::fau_person_shortlist($value, $displayfield);
			if (!empty($thisentry)) {
			    $content .= $thisentry;
			}
			break;
		    case 'name':
		    case 'shortlist':
			$thisentry = Data::fau_person_shortlist($value, $displayfield);
			if (!empty($thisentry)) {
			    $content .= $thisentry;
			    if ($i < $number) {
				$content .= ", ";
			    }
			}   
			 break;
		    
		    case 'table':
			$content .= Data::fau_person_tablerow($value, $displayfield);
			break;
		     case 'page':
			$content .= Data::fau_person_page($value, 1, $showname);
			break;
		    case 'sidebar':
			$content .= Data::fau_person_sidebar($value, 0, $showlist, $showinstitution, $showabteilung, $showposition, $showtitle, $showsuffix, $showaddress, $showroom, $showtelefon, $showfax, $showmobile, $showmail, $showwebsite, $showlink, $showdescription, $showoffice, $showthumb, $showvia, $hstart);
			break;
		    
		    default:
			$content .= Data::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex, $noborder, $hstart, $bg_color);
		
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
	$validpars = 'id, slug, category, hstart, class, sort, format, order';
	$oldargs = explode(',', $validpars);
	foreach ($oldargs as $value) {
	    $key = esc_attr(strtolower(trim($value)));
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
	}
	if (!empty($hide)) {
	    $res['hide'] = rtrim($hide, " ,");
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

