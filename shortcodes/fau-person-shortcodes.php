<?php

if (!function_exists('fau_person_page')) {

    function fau_person_page($id) {
        return FAU_Person_Shortcodes::fau_person_page($id);
    }

}

class FAU_Person_Shortcodes {
    public static function fau_person($atts, $content = null) {
        extract(shortcode_atts(array(
            "slug" => FALSE,
            "id" => FALSE,
            "category" => FALSE,
            "showlink" => FALSE,
            "showfax" => FALSE,
            "showwebsite" => FALSE,
            "showaddress" => FALSE,
            "showroom" => FALSE,
            "showdescription" => FALSE,
            "showlist" => FALSE,
            "showsidebar" => FALSE,
            "showthumb" => FALSE,
            "showpubs" => FALSE,
            "showoffice" => FALSE,
            "showtitle" => TRUE,
            "showsuffix" => TRUE,
            "showposition" => TRUE,
            "showinstitution" => TRUE,
            "showabteilung" => TRUE,
            "showmail" => TRUE,
            "showtelefon" => TRUE,
            "extended" => FALSE,
            "format" => '',
            "show" => '',
            "hide" => '',
            "showmobile" => FALSE,
            "background" => '',  // alternativ kann eine Farbe als Hintergrund eingestellt werden: background-grau, -fau, -phil, -med, -nat, -tf, -rw
            "border" => TRUE,   //bei false wird der Rahmen um die Kontaktdarstellung ausgeblendet über die Klasse noborder
            "hstart" => '3',    //Überschriftendefinition
                        ), $atts));

        if ($category) {
            $liste = self::fau_persons($atts, $content);
            return $liste;
        } else {
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
                    return '<p>' . sprintf(__('Bitte geben Sie den Titel oder die ID des Kontakteintrags an.', FAU_PERSON_TEXTDOMAIN), $slug) . '</p>';
                } else {
                    $posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
                    if ($posts) {
                        $post = $posts[0];
                        $id = $post->ID;
                    } else {
                        return '<p>' . sprintf(__('Es konnte kein Kontakteintrag mit dem angegebenen Titel %s gefunden werden. Versuchen Sie statt dessen die Angabe der ID des Kontakteintrags.', FAU_PERSON_TEXTDOMAIN), $slug) . '</p>';
                    }
                }
            }

            if (!empty($id)) {

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
                            $liste .= self::fau_person_page($value, 1, $showname);
                        } elseif ($shortlist) {
                            $liste .= self::fau_person_shortlist($value, $showlist, 0, $showmail, $showtelefon);
                            if ($i < $number)
                                $liste .= ", ";
                        } elseif ($list) {
                            $liste .= '<li class="person-info">' . "\n";
                            $liste .= self::fau_person_shortlist($value, $showlist, 1, $showmail, $showtelefon);
                            $liste .= "</li>\n";
                        } elseif ($sidebar) {
                            $liste .= self::fau_person_sidebar($value, 0, $showlist, $showinstitution, $showabteilung, $showposition, $showtitle, $showsuffix, $showaddress, $showroom, $showtelefon, $showfax, $showmobile, $showmail, $showwebsite, $showlink, $showdescription, $showoffice, $showpubs, $showthumb, $showvia, $hstart);
                        } elseif ($compactindex) {
                            $liste .= self::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex, $noborder, $hstart, $bg_color);
                        } else {
                            $liste .= self::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, 0, $noborder, $hstart, $bg_color);
                        }
                    } else {
                        $liste .= sprintf(__('Es konnte kein Kontakteintrag mit der angegebenen ID %s gefunden werden.', FAU_PERSON_TEXTDOMAIN), $value);
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
    }

    public static function fau_persons($atts, $content = null) {
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
            "showabteilung" => TRUE,
            "showmail" => TRUE,
            "showtelefon" => TRUE,
            "extended" => FALSE,
            "showmobile" => FALSE,
//            "showvia" => 0,
            "format" => '',
            "show" => '',
            "hide" => '',
            "sort" => FALSE,    //für die Sortierung in Katgorien nach Nachname sort="nachname" angeben. Standardsortierung nach Titel
            "background" => '',  // alternativ kann eine Farbe als Hintergrund eingestellt werden: background-grau, -fau, -phil, -med, -nat, -tf, -rw
            "border" => TRUE,   //bei false wird der Rahmen um die Kontaktdarstellung ausgeblendet über die Klasse noborder
            "hstart" => '3',    //Überschriftendefinition
                        ), $atts));

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
            if ( $sort == 'nachname' ) {
                $posts = FAU_Person::sort_person_posts( $posts );   
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
                if ( $sort == 'nachname' ) {
                    $value = $post['ID'];
                } else {
                    $value = $post->ID;
                }
                if ($page) {
                    $content .= self::fau_person_page($value, 1, $showname);
                } elseif ($shortlist) {
                    $content .= self::fau_person_shortlist($value, $showlist, 0, $showmail, $showtelefon);
                    if ($i < $number)
                        $content .= ", ";
                } elseif ($list) {
                    $content .= '<li class="person-info">' . "\n";
                    $content .= self::fau_person_shortlist($value, $showlist, 1, $showmail, $showtelefon);
                    $content .= "</li>\n";
                } elseif ($sidebar) {
                    $content .= self::fau_person_sidebar($value, 0, $showlist, $showinstitution, $showabteilung, $showposition, $showtitle, $showsuffix, $showaddress, $showroom, $showtelefon, $showfax, $showmobile, $showmail, $showwebsite, $showlink, $showdescription, $showoffice, $showpubs, $showthumb, $showvia, $hstart);
                } elseif ($compactindex) {
                    $content .= self::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex, $noborder, $hstart, $bg_color);
                } else {
                    $content .= self::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, 0, $noborder, $hstart, $bg_color);
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
                $content = '<p>' . sprintf(__('Es konnten keine Kontakte in der Kategorie %s gefunden werden.', FAU_PERSON_TEXTDOMAIN), $category->slug) . '</p>'; 
            } else {
                $content = '<p>' . sprintf(__('Die Kategorie %s konnte leider nicht gefunden werden.', FAU_PERSON_TEXTDOMAIN), $atts['category']) . '</p>';                 
            }
        }

        return $content;
    }

    public static function fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex = 0, $noborder, $hstart, $bg_color) {

        // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
        extract($fields);
        if ($showvia !== 0 && !empty($connections))
            $showvia = 1;
        if ($showvia === 0 && !empty($connection_only))
            $connection_only = '';
        
        $type = get_post_meta($id, 'fau_person_typ', true);

        if ($link) {
            $personlink = $link;
        } else {
            $personlink = get_permalink($id);
        }

        if (get_post_field('post_excerpt', $id)) {
            $excerpt = get_post_field('post_excerpt', $id);
        } else {
            $post = get_post($id);
            if ($post->post_content)
                $excerpt = wp_trim_excerpt($post->post_content);
        }
        
        $fullname = self::fullname_output($id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, $showtitle, $showsuffix, $alternateName);
        $contactpoint = self::contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, $showaddress, $showroom, 'default' );
        // hier Fehlermeldung nicht vorhanden $hoursAvailable_group
        $hoursavailable_output = self::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text );
        
        $content = '<div class="person content-person' . $noborder . $bg_color . '" itemscope itemtype="http://schema.org/Person">';
        if ($compactindex)
            $content .= '<div class="compactindex">';

        // if( !$compactindex || $showthumb )        
        $content .= '<div class="row">';

        if ($showthumb) {
            $content .= '<div class="span1 span-small person-thumb" itemprop="image" aria-hidden="true" role="presentation">';
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">';

            if (has_post_thumbnail($id)) {
                $content .= get_the_post_thumbnail($id, 'person-thumb-bigger');
            } else {
                if ($type == 'realmale') {
                    $bild = plugin_dir_url(__FILE__) . '../images/platzhalter-mann.png';
                } elseif ($type == 'realfemale') {
                    $bild = plugin_dir_url(__FILE__) . '../images/platzhalter-frau.png';
                } elseif ($type == 'einrichtung') {
                    $bild = plugin_dir_url(__FILE__) . '../images/platzhalter-organisation.png';
                } else {
                    $bild = plugin_dir_url(__FILE__) . '../images/platzhalter-unisex.png';
                }
                if ($bild)
                    $content .= '<img src="' . $bild . '" width="90" height="120" alt="">';
            }
            $content .= '</a>';
            $content .= '</div>';
        }

        if ($compactindex) {
            if ($showthumb) {
                $content .= '<div class="span6 person-compact-thumb">';
            } else {
                $content .= '<div class="span7 person-compact">';
            }
        } else {
            if ($showthumb) {
                $content .= '<div class="span3 person-default-thumb">';
            } else {
                $content .= '<div class="span4 person-default">';
            }
        }
        
        $content .= '<h' . $hstart . '>';
        $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
        $content .= '</h' . $hstart . '>';
        $content .= '<ul class="person-info">';
        if ($showposition && $jobTitle)
            $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', FAU_PERSON_TEXTDOMAIN) . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>';
        if ($showinstitution && $worksFor)
            $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>';
        if ($showabteilung && $department)
            //itemprop="department" entfernt da nicht zu Person zugehörig
            $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $department . '</li>';   
        if (($extended || $showaddress || $showroom) && !empty($contactpoint) && empty($connection_only))
            $content .= $contactpoint;
        if ($showtelefon && $telephone && empty($connection_only))
            $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
        if ($showmobile && $mobilePhone && empty($connection_only))
            $content .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobil', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $mobilePhone . '</span></li>';
        if ($showfax && $faxNumber && empty($connection_only))
            $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
        if ($showmail && $email && empty($connection_only))
            $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
        if ($showwebsite && $url)
            $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>';
        if ($showpubs && $pubs)
            $content .= '<li class="person-info-pubs"><span class="screen-reader-text">' . __('Publikationen', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $pubs . '</li>';
        $content .= '</ul>';


        if ((!empty($connection_text) || !empty($connection_options) || !empty($connections)) && $showvia === 1)
            $content .= self::fau_person_connection($connection_text, $connection_options, $connections, $hstart);

        //  if( !($compactindex && $showthumb) )      $content .= '</div>';

        if (($showoffice && $hoursavailable_output && empty($connection_only)) || ($showlist && isset($excerpt)) || (($showsidebar || $extended) && $description) || ($showlink && $personlink)) {


            if (!$compactindex)
                $content .= '</div><div class="span3 person-default-more">';
            if ($showoffice && $hoursavailable_output && empty($connection_only)) {
                $content .= '<ul class="person-info">';
                //$content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><div itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</div></li>';
                $content .= $hoursavailable_output;
                $content .= '</ul>';
            }

            if ($showlist && isset($excerpt))
                $content .= '<div class="person-info-description"><p>' . $excerpt . '</p></div>';
            if (($extended || $showsidebar) && $description)
                $content .= '<div class="person-info-description"><span class="screen-reader-text">' . __('Beschreibung', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $description . '</div>';
            if ($showlink && $personlink) {
                $content .= '<div class="person-info-more"><a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" class="person-read-more" href="' . $personlink . '">';
                $content .= __('Mehr', FAU_PERSON_TEXTDOMAIN) . ' ›</a></div>';
            }
        }


        // if( $compactindex && $showthumb )      
        $content .= '</div>'; // end div row
        // if( !$compactindex || $showthumb )      
        $content .= '</div> <!-- /row-->';

        if ($compactindex)
            $content .= '</div>';   // ende div class compactindex
        $content .= '</div>';
        return $content;
    }

    public static function fau_person_page($id, $is_shortcode=0, $showname=0) {

        $content = '<div class="person page" itemscope itemtype="http://schema.org/Person">';
        // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
        extract($fields);

        if ((strlen($url) > 4) && (strpos($url, "http") === false)) {
            $url = 'https://' . $url;
        }
        if ( !$is_shortcode || $showname ) {
            $fullname = self::fullname_output($id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, 1, 1, $alternateName);
            $content .= '<h2>' . $fullname . '</h2>';
        }

        $contactpoint = self::contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, 1, 1, 'page' );
        $hoursavailable_output = self::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text );
        
        if (has_post_thumbnail($id)) {
            $content .= '<div itemprop="image" class="person-image alignright">'; 
            $content .= get_the_post_thumbnail($id, 'person-thumb-page');
            $content .= '</div>';
        }
        $content .= '<ul class="person-info">';
        if ($jobTitle)
            $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', FAU_PERSON_TEXTDOMAIN) . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>';
        if ($worksFor)
            $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>';
        if ($department)
            $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $department . '</span></li>';
        if ($telephone && empty($connection_only))
            $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
        if ($mobilePhone && empty($connection_only))
            $content .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobil', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $mobilePhone . '</span></li>';
        if ($faxNumber && empty($connection_only))
            $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
        if ($email && empty($connection_only))
            $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
        if ($url && empty($connection_only))
            $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>';

        if (!empty($contactpoint) && empty($connection_only)) {            
            $content .= $contactpoint;
        }
        if ($hoursavailable_output && empty($connection_only))
            $content .= $hoursavailable_output;
            //$content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</span></li>';
        if ($pubs)
            $content .= '<li class="person-info-pubs"><span class="screen-reader-text">' . __('Publikationen', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $pubs . '</li>';
        $content .= '</ul>';

        if (!empty($connection_text) || !empty($connection_options) || !empty($connections))
            $content .= self::fau_person_connection($connection_text, $connection_options, $connections, 2);


        if ( is_singular( 'person' ) && in_the_loop() ) {
            $post = get_the_content();
        } else {
            $post = get_post($id)->post_content;
        }
        if ($post) {
            $content .= '<div class="desc" itemprop="description">' . PHP_EOL;
            $content .= apply_filters( 'the_content', $post );
            $content .= '</div>';
        }
        $content .= '</div>';

        return $content;
    }

    public static function fau_person_shortlist($id, $showlist, $list=0, $showmail=0, $showtelefon=0) {

        // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)        
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
        extract($fields);

        if (get_post_field('post_excerpt', $id)) {
            $excerpt = get_post_field('post_excerpt', $id);
        } else {
            $post = get_post($id);
            if ($post->post_content)
                $excerpt = wp_trim_excerpt($post->post_content);
        }

        if ($link) {
            $personlink = $link;
        } else {
            $personlink = get_permalink($id);
        }
        $content = '';
        
        $fullname = self::fullname_output($id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, 1, 1, $alternateName);
        if ( $list==1 )
            $content .= '<div class="list">';
        $content .= '<span class="person-info">';
        $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
        if ( $telephone && $showtelefon && empty( $connection_only ) && $list==1 )
                $content .= ', <span class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></span>';
        if ( $email && $showmail && empty( $connection_only ) && $list==1  )
                $content .= ', <span class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></span>';    
        if ( $showlist && isset( $excerpt ) )
            $content .= "<br>" . $excerpt;
        $content .= '</span>';
        if ( $list==1 )
            $content .= '</div>';        
        return $content;
    }

    // von Widget, also Sidebar über Fakultätsthemes - Ansprechpartner: fau_person_sidebar($id, $title, list 0, inst 1, abtielung 1, posi 1, titel 1, suffix 1, addresse 1, raum 1, tele 1, fax 1, handy 0,                                                                  mail 1, url 1, mehrlink 0, kurzauszug 1, office 0, pubs 0, bild 1, via 0, hstart 3)
    // muss noch eingebaut werden: Wenn shortcode mit sidebar zeige bild ja und wo?     if (theme(FAU-*)  && template =~    else { Bild anzeigen }   if (theme(FAU-*)  && template =~( page.php || page-subnav.php ) && (not option(zeige bild in sidebar)   ) { Zeige kein Bild }   else {       if  template =~( page.php || page-subnav.php )   { binde Bild NACH dem Namen ein} else {    Bild vor dem Namen anzeigen }   }
    
    public static function fau_person_sidebar($id, $title, $showlist = 0, $showinstitution = 0, $showabteilung = 0, $showposition = 0, $showtitle = 0, $showsuffix = 0, $showaddress = 0, $showroom = 0, $showtelefon = 0, $showfax = 0, $showmobile = 0, $showmail = 0, $showwebsite = 0, $showlink = 0, $showdescription = 0, $showoffice = 0, $showpubs = 0, $showthumb = 0, $showvia = 0, $hstart = 3) {
        //Überprüfung zur Bildplatzierung in der Sidebar, ob ein FAU-Theme gewählt wurde und welches Template gewählt ist
        $active_theme = wp_get_theme();
        $active_theme = $active_theme->get( 'Name' );
        if (in_array($active_theme, FAU_Person::$fauthemes)) {
            $fautheme = 1;
            if( !is_page_template( array('page-templates/page-portal.php', 'page-templates/page-start.php', 'page-templates/page-start-sub.php'))  ) {
                $small_sidebar = 1;
            }
        }
        
        if (!empty($id)) {
            $post = get_post($id);

            // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)            
            $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
            // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
            extract($fields);

            if ($showvia !== 0 && !empty($connections))
                $showvia = 1;
            if ($showvia === 0 && !empty($connection_only))
                $connection_only = '';

            if ($link) {
                $personlink = $link;
            } else {
                $personlink = get_permalink($id);
            }

            $fullname = self::fullname_output($id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, $showtitle, $showsuffix, $alternateName);
            $contactpoint = self::contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, $showaddress, $showroom, 'default' );
            $hoursavailable_output = self::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text );
            
            if (has_post_thumbnail($id) && $showthumb) {
		
		$alttext = get_the_title($id);
		$alttext = esc_html($alttext);
		$altattr = 'alt="'.__('Weitere Informationen zu','fau').' '.$alttext.' '.__('aufrufen','fau').'"';


		$post_thumbnail_id = get_post_thumbnail_id( $id ); 
		$sliderimage = wp_get_attachment_image_src( $post_thumbnail_id, 'person-thumb' );
		$slidersrcset =  wp_get_attachment_image_srcset($post_thumbnail_id, 'person-thumb');

		$imagehtml = '<img src="'.$sliderimage[0].'" '.$altattr.' width="'.$sliderimage[1].'" height="'.$sliderimage[2].'"';
		if ($slidersrcset) {
		    $imagehtml .= 'srcset="'.$slidersrcset.'"';
		}
		$imagehtml .= '>';
		
		
		
		
                $sidebar_thumb = '<div class="span1 person-thumb" itemprop="image" aria-hidden="true">';
                $sidebar_thumb .= '<a href="' . $personlink . '">';
                $sidebar_thumb .= $imagehtml;
                $sidebar_thumb .= '</a>';
                $sidebar_thumb .= '</div>' . "\n";
            }
            
            $content = '<div class="person" itemscope itemtype="http://schema.org/Person">' . "\n";
            $content .= '<div class="side">';
                    
            if (!empty($title))
                $content .= '<h' . ($hstart-1) . ' class="small">' . $title . '</h' . ($hstart-1) . '>' . "\n";

            $content .= '<div class="row">' . "\n";
            
            if ( isset( $sidebar_thumb ) && !isset ( $small_sidebar ) ) {
                $content .= $sidebar_thumb;
            }            

            $content .= '<div class="span3 person-sidebar">' . "\n";
            $content .= '<h' . $hstart . '>';
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
            $content .= '</h' . $hstart . '>' . "\n";
            
            if ( isset( $sidebar_thumb ) && isset ( $small_sidebar ) ) {
                $content .= '</div>';
                $content .= $sidebar_thumb;
                $content .= '<div class="span3 person-sidebar">';
            }
            
            
            $content .= '<ul class="person-info">' . "\n";
            if ($jobTitle && $showposition)
                $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', FAU_PERSON_TEXTDOMAIN) . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>' . "\n";
            if ($worksFor && $showinstitution)
                $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>' . "\n";
            //itemprop="department" entfernt da nicht zu Person zugehörig
            if ($department && $showabteilung)
                $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $department . '</li>' . "\n";
            if (!empty($contactpoint) && empty($connection_only))
                $content .= $contactpoint;
            if ($telephone && $showtelefon && empty($connection_only))
                $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>' . "\n";
            if ($mobilePhone && $showmobile && empty($connection_only))
                $content .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobil', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $mobilePhone . '</span></li>' . "\n";
            if ($faxNumber && $showfax && empty($connection_only))
                $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>' . "\n";
            if ($email && $showmail && empty($connection_only))
                $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>' . "\n";
            if ($url && $showwebsite)
                $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>' . "\n";
            if ($hoursavailable_output && $showoffice && empty($connection_only))
                $content .= $hoursavailable_output;
                //$content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><div itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</div></li>';
            $content .= '</ul>' . "\n";
            if ($description && $showdescription)
                $content .= '<div class="person-info-description"><span class="screen-reader-text">' . __('Beschreibung', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $description . '</div>' . "\n";
            if ((!empty($connection_text) || !empty($connection_options) || !empty($connections) ) && $showvia === 1)
                $content .= self::fau_person_connection($connection_text, $connection_options, $connections, $hstart);
            $content .= '</div>' . "\n";
            $content .= '</div>' . "\n";
            $content .= '</div>' . "\n";
            $content .= '</div>';
        }
        return $content;
    }

    public static function fau_person_connection($connection_text, $connection_options, $connections, $hstart) {

        $content = '';
        $contactlist = '';
        foreach ($connections as $key => $value) {
            extract($connections[$key]);
            $contactpoint = '';

            if ( $connection_options && in_array( 'contactPoint', $connection_options ) ) {
                $showaddress = 1;
                $showroom = 1;
            } else {
                $showaddress = 0;
                $showroom = 0;
            }

            $fullname = self::fullname_output($nr, $honorificPrefix, $givenName, $familyName, $honorificSuffix, 1, 1, $alternateName);
            $contactpoint = self::contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, $showaddress, $showroom, 'connection' );
            if( isset($hoursAvailable_text) ) {
                $hoursavailable_output = self::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text );
            } else {
                $hoursavailable_output = self::hoursavailable_output( $hoursAvailable, $hoursAvailable_group, '' );
            }
            
            if ($link) {
                $personlink = $link;
            } else {
                $personlink = get_permalink($nr);
            }
            $contactlist .= '<li itemscope itemtype="http://schema.org/Person">';
            $contactlist .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($nr)) . '" href="' . $personlink . '">';
            $contactlist .= $fullname;
            $contactlist .= '</a>';

            if ($connection_options) {
                $cinfo = '';

                if ($telephone && in_array('telephone', $connection_options))
                    $cinfo .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
                if (isset($mobilePhone) && in_array('telephone', $connection_options))
                    $cinfo .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobiltelefon', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $mobilePhone . '</span></li>';
                if ($faxNumber && in_array('faxNumber', $connection_options))
                    $cinfo .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
                if ($email && in_array('email', $connection_options))
                    $cinfo .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
                if (!empty($contactpoint) && in_array('contactPoint', $connection_options))
                    $cinfo .= $contactpoint;
                if ($hoursavailable_output && in_array('hoursAvailable', $connection_options))
                    //$cinfo .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</span></li>';
                    $cinfo .= $hoursavailable_output;
                if (!empty($cinfo)) {
                    $contactlist .= '<ul class="person-info">';
                    $contactlist .= $cinfo;
                    $contactlist .= '</ul>';
                }
            }
            $contactlist .= '</li>';
        }

        if (!empty($contactlist)) {
            $content = '<div class="connection">';
            if ($connection_text) {
                $content .= '<h' . ($hstart+1) . '>' . $connection_text . '</h' . ($hstart+1) . '>';
            }
            $content .= '<ul class="connection-list">';
            $content .= $contactlist;
            $content .= '</ul>';
            $content .= '</div>';
        }

        return $content;
    }
    
    public static function fullname_output( $id, $honorificPrefix, $givenName, $familyName, $honorificSuffix, $showtitle, $showsuffix, $alternateName ) {
        $fullname = '<span itemprop="name">';
        if ( $alternateName ) {
            $fullname .= '<span itemprop="alternateName">' . $alternateName . '</span>';
        } else {
            if ( $showtitle && $honorificPrefix )
                $fullname .= '<span itemprop="honorificPrefix">' . $honorificPrefix . '</span> ';
            $fullname .= '<span class="fullname">';
            if ( $givenName && $familyName ) {
                    $fullname .= '<span itemprop="givenName">' . $givenName . "</span> ".'<span itemprop="familyName">' . $familyName . "</span>";
            } elseif (!empty(get_the_title($id))) {
                $fullname .= get_the_title($id);
            }
            $fullname .= '</span>';
            if ( $showsuffix && $honorificSuffix )
                $fullname .= ', <span itemprop="honorificSuffix">' . $honorificSuffix . '</span>';
        }
        $fullname .= '</span>';
        return $fullname;
    }

    public static function hoursavailable_output( $hoursAvailable, $hoursAvailable_group, $hoursAvailable_text ) {
        if(!empty($hoursAvailable) || !empty($hoursAvailable_group)) {
            $output = '<li class="person-info-office"><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">';
            if(!empty($hoursAvailable_text)) {
                $output .= '<strong>' . $hoursAvailable_text . ':</strong><br>';
            } else {
                $output .= '<span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span>';    
            }
            if ( $hoursAvailable ) {
                $output .= $hoursAvailable;
            }
            if ( $hoursAvailable_group ) {
                /* foreach ( $hoursAvailable_group as $key => $value ) {
                    $output .= '<br>';
                    $output .= $value;                                
                } */
                if ( $hoursAvailable )  $output .= '<br>';
                $output .= implode('<br>', $hoursAvailable_group);
            }

            $output .= '</span></li>';
            return $output;
        }
    }
    
    // über $type wird die Ausgabereihenfolge definiert: "page", "connection" oder alles andere
    public static function contactpoint_output( $streetAddress, $postalCode, $addressLocality, $addressCountry, $workLocation, $showaddress, $showroom, $type ) {
        if( $showaddress ) {
            if( $streetAddress )       
                $street = '<span class="person-info-street" itemprop="streetAddress">' . $streetAddress . '</span>';
            if ( $addressLocality ) {
                $city = '';
                if( $postalCode )
                    $city .= '<span itemprop="postalCode">' . $postalCode . '</span> ';
                $city .= '<span itemprop="addressLocality">' . $addressLocality . '</span>';
            }
            if( $addressCountry )
                $country = '<span class="person-info-country" itemprop="addressCountry">' . $addressCountry . '</span>';
        }
        if( $workLocation && $showroom ) 
            $room = __('Raum', FAU_PERSON_TEXTDOMAIN) . ' ' . $workLocation;
        
        if ( !empty($street) || !empty($city) || !empty($country) || !empty($room) ) {
            $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">' . __('Adresse', FAU_PERSON_TEXTDOMAIN) . ': <br></span>';
            switch( $type ) {
                case 'page':
                    if( isset($street) || isset($city) || isset($country) )
                        $contactpoint .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
                    if ( isset($street) ) {
                        $contactpoint .= $street;
                        if ( isset($city) || isset($country) ) {       
                            $contactpoint .= '<br>';
                        } else {
                            $contactpoint .= '</div>';
                        }
                    } 
                    if ( isset($city) ) {
                        $contactpoint .= $city;
                        if ( isset($country) ) {
                            $contactpoint .= '<br>';
                        } else {
                            $contactpoint .= '</div>';
                        }
                    } 
                    if ( isset($country) ) {
                            $contactpoint .= $country . '</div>';                        
                    }
                    if ( isset($room) ) {
                        $contactpoint .= '<div class="person-info-room" itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . $room . '</div>';
                    }
                    break;
                case 'connection':
                    if( isset($street) || isset($city) || isset($country) )
                        $contactpoint .= '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';  
                    if ( isset($street) ) {
                        $contactpoint .= $street;
                        if ( isset($city) || isset($country) ) {       
                            $contactpoint .= ', ';
                        } elseif ( isset($room) ) {
                            $contactpoint .= '</span>, ';
                        } else {
                            $contactpoint .= '</span>';
                        }
                    } 
                    if ( isset($city) ) {
                        $contactpoint .= $city;
                        if ( isset($country) ) {
                            $contactpoint .= ', ';
                        } elseif ( isset($room) ) {
                            $contactpoint .= '</span>, ';
                        } else {
                            $contactpoint .= '</span>';
                        }
                    }  
                    if ( isset($country) ) {
                         $contactpoint .= $country . '</span>';
                         if ( isset($room) ) {
                             $contactpoint .= ', ';
                         }
                    }
                    if ( isset($room) ) {
                        $contactpoint .= '<span class="person-info-room" itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . $room . '</span>';
                    }
                    break;                    
                default:   
                    if ( isset($street) ) {
                        $contactpoint .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">' . $street;
                        if ( isset($room) ) {
                            $contactpoint .= '</div>';
                        } elseif ( isset($city) || isset($country) ) {
                            $contactpoint .= '<br>';
                        } else {
                            $contactpoint .= '</div>';
                        }
                    }
                    if ( isset($room) ) {
                        $contactpoint .= '<div class="person-info-room" itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . $room . '</div>';
                        if ( isset($city) || isset($country) ) 
                            $contactpoint .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';                            
                    }
                    if ( !isset($street) && !isset($room) ) {
                        if ( isset($city) || isset($country) ) {
                            $contactpoint .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
                        }                       
                    }
                    if ( isset($city) ) {
                       $contactpoint .= $city;
                        if ( isset($country) ) {
                            $contactpoint .= '<br>';
                        } else {
                            $contactpoint .= '</div>';
                        }                        
                    }
                    if ( isset($country) ) {
                        $contactpoint .= $country . '</div>';
                    }
            }                    
            $contactpoint .= '</li>' . "\n";
            return $contactpoint;
        }
        
    }
        
}

