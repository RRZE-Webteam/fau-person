<?php

namespace FAU_Person\Shortcodes;
use function FAU_Person\Config\getShortcodeSettings;
use function FAU_Person\Config\getShortcodeDefaults;
use FAU_Person\Data;
use UnivIS_Data;
use sync_helper;


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

        register_block_type( 'FAU_Person/Shortcode/Kontakt', array(
            'editor_script' => 'fau-person' . '-editor',
            'render_callback' => [$this, 'shortcode_kontakt'],
            'attributes' => $this->shortcodesettings['kontakt']
            ) 
        );
    }    
    */
    public function shortcode_kontakt($atts, $content = null) {
	$defaults = getShortcodeDefaults('kontakt');
	extract(shortcode_atts($defaults, $atts));
	
        if ($category) {
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
                    $showpubs = 0;
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
                    $showpubs = 0;
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
                    $showpubs = 0;
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
                if (in_array('publikationen', $show))
                    $showpubs = 1;          //
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
                if (in_array('publikationen', $hide))
                    $showpubs = 0;
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
		\FAU_Person\Main::enqueueForeignThemes();
		
                if ($shortlist) {
                    $liste = '<span class="person liste-person" itemscope itemtype="http://schema.org/Person">';
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
                            $liste .= Data::fau_person_sidebar($value, 0, $showlist, $showinstitution, $showabteilung, $showposition, $showtitle, $showsuffix, $showaddress, $showroom, $showtelefon, $showfax, $showmobile, $showmail, $showwebsite, $showlink, $showdescription, $showoffice, $showpubs, $showthumb, $showvia, $hstart);
                        } elseif ($compactindex) {
                            $liste .= Data::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex, $noborder, $hstart, $bg_color);
                        } else {
                            $liste .= Data::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, 0, $noborder, $hstart, $bg_color);
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

    public function shortcode_kontaktListe($atts, $content = null) {
	$defaults = getShortcodeDefaults('kontaktliste');
	extract(shortcode_atts($defaults, $atts));
	

        $content = '';

        $shortlist = '';
        $sidebar = '';
        $compactindex = '';
        $page = '';
        $list = '';
        $showvia = '';
        $inhalt = '';
        //$border = 1;
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
                $showpubs = 0;
                $showthumb = 1;
            }
            if ($format == 'full' || $format == 'page')
                $page = 1;
                $showname = 1;
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
                $showpubs = 0;
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
                $showpubs = 0;
                $showthumb = 1;
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
            if (in_array('publikationen', $show))
                $showpubs = 1;          //
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
            if (in_array('publikationen', $hide))
                $showpubs = 0;
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

        $category = get_term_by('slug', $category, 'persons_category');
        
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
	   \FAU_Person\Main::enqueueForeignThemes();
	    
            if (( $sort == 'nachname' ) || ( $sort == 'name' )) {
                $posts = Data::sort_person_posts( $posts );   
                //_rrze_debug($posts);
            } 
            $number = count($posts);
            $i = 1;
            if ($shortlist) {
                $content = '<span class="person liste-person" itemscope itemtype="http://schema.org/Person">';
                //} elseif ( $page ) {
                //    $liste = '';
            } elseif ($list) {
                $content = '<ul class="person liste-person" itemscope itemtype="http://schema.org/Person">';
                $content .= "\n";
            } else {
                $content = '';
                // Herausgenommen da vermutlich nicht nötig
                //$liste = '<p>';
            }
            foreach ($posts as $post) {
                // Bei Sortierung nach Name ist $posts ein Array
                if (( $sort == 'nachname' ) || ( $sort == 'name' )) {
                    $value = $post['ID'];
                } else {
                    $value = $post->ID;
                }
                if ($page) {
                    $content .= Data::fau_person_page($value, 1, $showname);
                } elseif ($shortlist) {
                    $content .= Data::fau_person_shortlist($value, $showlist, 0, $showmail, $showtelefon);
                    if ($i < $number)
                        $content .= ", ";
                } elseif ($list) {
                    $content .= '<li class="person-info">' . "\n";
                    $content .= Data::fau_person_shortlist($value, $showlist, 1, $showmail, $showtelefon);
                    $content .= "</li>\n";
                } elseif ($sidebar) {
                    $content .= Data::fau_person_sidebar($value, 0, $showlist, $showinstitution, $showabteilung, $showposition, $showtitle, $showsuffix, $showaddress, $showroom, $showtelefon, $showfax, $showmobile, $showmail, $showwebsite, $showlink, $showdescription, $showoffice, $showpubs, $showthumb, $showvia, $hstart);
                } elseif ($compactindex) {
                    $content .= Data::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex, $noborder, $hstart, $bg_color);
                } else {
                    $content .= Data::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, 0, $noborder, $hstart, $bg_color);
                }
                $i++;
            }
            if ($shortlist) {
                $content .= "</span>";
            } elseif ($list) {
                $content .= "</ul>\n";
            } else {
                $content .= '';              
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

        


}

