<?php

 if(!function_exists('fau_person_page')) { 
    function fau_person_page( $id ) {
        return FAU_Person_Shortcodes::fau_person_page( $id );
    }
 }  

class FAU_Person_Shortcodes {


    public static function fau_person( $atts, $content = null) {
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
                        ), $atts));
        
        if( $category ) {
            $liste = self::fau_persons( $atts, $content );
            return $liste;
        } else {
        $shortlist = '';    
        $sidebar = '';
        $compactindex = '';
        $page = '';
        $list = '';
        $showvia = '';
        if ( !empty( $format ) ) {         
            //format-Parameter: 
            //name (Alternativ shortlist, $shortlist = 1), 
            //liste ($list = 1 und $showlist = 1), wie Name nur mit Aufzählungszeichen, 
            //sidebar ($showsidebar, $sidebar, $showabteilung, $showtitle, $showsuffix, $showtelefon, $showmail, $showwebsite, $showdescription, $showthumb = 1), 
            //index (keine Formatangabe, default-Wert), 
            //page (Alternativ full, $page = 1), 
            //plain, 
            //table,
            //accordion,
            if( $format == 'name' || $format == 'shortlist' )   $shortlist = 1;
            if( $format == 'sidebar' ) {
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
            if( $format == 'full' || $format == 'page' )        $page = 1;
            if( $format == 'liste'  || $format == 'listentry' ) {
                $list = 1;
                $showlist = 1;
            }
            if( $format == 'plain' ) {
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
            if( $format == 'kompakt' || $format == 'compactindex' )  {
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
        if ( $extended == 1 ) {
            $showlist = 1;
            $showinstitution = 0;
            $showfax = 0;
            $showwebsite = 0;
            $showthumb = 1;
        }
        // Wenn neue Felder dazukommen, hier die Anzeigeoptionen auch mit einstellen
        if (!empty($show)) {
            $show = array_map('trim', explode(',', $show));                                       // schema.org-Bezeichnungen = Variablenname
            if( in_array( 'kurzbeschreibung', $show ) ) $showlist = 1;          //
            if( in_array( 'organisation', $show ) )     $showinstitution = 1;   // $worksFor
            if( in_array( 'abteilung', $show ) )        $showabteilung = 1;     // $department
            if( in_array( 'position', $show ) )         $showposition = 1;      // $jobTitle
            if( in_array( 'titel', $show ) )            $showtitle = 1;         // $honorificPrefix
            if( in_array( 'suffix', $show ) )           $showsuffix = 1;        // $honorificSuffix
            if( in_array( 'adresse', $show ) )          $showaddress = 1;       // $streetAddress, $postalCode, $addressLocality, $addressCountry   
            if( in_array( 'raum', $show ) )             $showroom = 1;          // $workLocation
            if( in_array( 'telefon', $show ) )          $showtelefon = 1;       // $telephone   
            if( in_array( 'fax', $show ) )              $showfax = 1;           // $faxNumber
            if( in_array( 'mobil', $show ) )            $showmobile = 1;        // $mobilePhone
            if( in_array( 'mail', $show ) )             $showmail = 1;          // $email
            if( in_array( 'webseite', $show ) )         $showwebsite = 1;       // $url  
            if( in_array( 'mehrlink', $show ) )         $showlink = 1;          // $link
            if( in_array( 'kurzauszug', $show ) )       $showdescription = 1;   // $description (erscheint bei Sidebar)
            if( in_array( 'sprechzeiten', $show ) )     $showoffice = 1;        // $hoursAvailable
            if( in_array( 'publikationen', $show ) )    $showpubs = 1;          //
            if( in_array( 'bild', $show ) )             $showthumb = 1;         //
            if( in_array( 'ansprechpartner', $show ) )  $showvia = 1;           //
        }    
        if ( !empty( $hide ) ) {
            $hide = array_map('trim', explode(',', $hide));
            if( in_array( 'kurzbeschreibung', $hide ) ) $showlist = 0;
            if( in_array( 'organisation', $hide ) )     $showinstitution = 0;   
            if( in_array( 'abteilung', $hide ) )        $showabteilung = 0;  
            if( in_array( 'position', $hide ) )         $showposition = 0;
            if( in_array( 'titel', $hide ) )            $showtitle = 0;    
            if( in_array( 'suffix', $hide ) )           $showsuffix = 0;  
            if( in_array( 'adresse', $hide ) )          $showaddress = 0;            
            if( in_array( 'raum', $hide ) )             $showroom = 0;  
            if( in_array( 'telefon', $hide ) )          $showtelefon = 0;             
            if( in_array( 'fax', $hide ) )              $showfax = 0;
            if( in_array( 'mobil', $hide ) )            $showmobile = 0;
            if( in_array( 'mail', $hide ) )             $showmail = 0; 
            if( in_array( 'webseite', $hide ) )         $showwebsite = 0;            
            if( in_array( 'mehrlink', $hide ) )         $showlink = 0;
            if( in_array( 'kurzauszug', $hide ) )       $showdescription = 0;
            if( in_array( 'sprechzeiten', $hide ) )     $showoffice = 0;
            if( in_array( 'publikationen', $hide ) )    $showpubs = 0;
            if( in_array( 'bild', $hide ) )             $showthumb = 0;         
            if( in_array( 'ansprechpartner', $hide ) )  $showvia = 0;
        }
                
        if ( empty( $id ) ) {
            if ( empty( $slug ) ) {
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

        if ( !empty( $id ) ) {

            if ( $shortlist ) {
                $liste = '<span class="person liste-person" itemscope itemtype="http://schema.org/Person">';
            //} elseif ( $page ) {
            //    $liste = '';
            } elseif ( $list ) {
                $liste = '<ul class="person liste-person" itemscope itemtype="http://schema.org/Person">';
                $liste .= "\n";    
            } else {
                $liste = '';
                // Herausgenommen da vermutlich nicht nötig
                //$liste = '<p>';
            }

            $list_ids = array_map('trim', explode(',', $id));
            $number = count($list_ids);   
            $i = 1;
            foreach ($list_ids as $value) {
                $post = get_post($value);
                if ($post && $post->post_type == 'person') {
                    if ( $page ) {
                        $liste .= self::fau_person_page($value);
                    } elseif ( $shortlist ) {
                        $liste .= self::fau_person_shortlist($value, $showlist);
                        if( $i < $number )  $liste .= ", ";
                    } elseif ( $list ) {
                        $liste .= '<li class="person-info">'."\n";
                        $liste .= self::fau_person_shortlist($value, $showlist);
                        $liste .= "</li>\n";
                    } elseif ( $sidebar ) {
                        $liste .= self::fau_person_sidebar($value, 0, $showlist, $showinstitution, $showabteilung, $showposition, $showtitle, $showsuffix, $showaddress, $showroom, $showtelefon, $showfax, $showmobile, $showmail, $showwebsite, $showlink, $showdescription, $showoffice, $showpubs, $showthumb, $showvia);
                    } elseif ( $compactindex ) {
                        $liste .= self::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex);  
                    } else {
                        $liste .= self::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia);
                    }
                } else {
                    $liste .=  sprintf(__('Es konnte kein Kontakteintrag mit der angegebenen ID %s gefunden werden.', FAU_PERSON_TEXTDOMAIN), $value);
                    if( $i < $number )  $liste .= ", ";
                }
                $i++;
            }
            if ( $shortlist  ) {
                $liste .= "</span>";
            } elseif ( $list ) {
                $liste .= "</ul>\n";
            } elseif ( $page ) {
                $post = get_post( $id );
                if ( $post->post_content ) $content = wpautop( $post->post_content );  
                $liste .= $content;
            } else {
                $liste .= '';
                //herausgenommen da vermutlich nicht nötig
                //$liste .= "</p>\n";                
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
                        ), $atts));        

        $content = '';

        $shortlist = '';    
        $sidebar = '';
        $compactindex = '';
        $page = '';
        $list = '';
        $showvia = '';
        $inhalt = '';
        if ( !empty( $format ) ) {         
            //format-Parameter: 
            //name (Alternativ shortlist, $shortlist = 1), 
            //liste ($list = 1 und $showlist = 1), wie Name nur mit Aufzählungszeichen, 
            //sidebar ($showsidebar, $sidebar, $showabteilung, $showtitle, $showsuffix, $showtelefon, $showmail, $showwebsite, $showdescription, $showthumb = 1), 
            //index (keine Formatangabe, default-Wert), 
            //page (Alternativ full, $page = 1), 
            //plain, 
            //table,
            //accordion,
            if( $format == 'name' || $format == 'shortlist' )   $shortlist = 1;
            if( $format == 'sidebar' ) {
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
            if( $format == 'full' || $format == 'page' )        $page = 1;
            if( $format == 'liste'  || $format == 'listentry' ) {
                $list = 1;
                $showlist = 1;
            }
            if( $format == 'plain' ) {
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
            if( $format == 'kompakt' || $format == 'compactindex' )  {
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
            if( in_array( 'kurzbeschreibung', $show ) ) $showlist = 1;          //
            if( in_array( 'organisation', $show ) )     $showinstitution = 1;   // $worksFor
            if( in_array( 'abteilung', $show ) )        $showabteilung = 1;     // $department
            if( in_array( 'position', $show ) )         $showposition = 1;      // $jobTitle
            if( in_array( 'titel', $show ) )            $showtitle = 1;         // $honorificPrefix
            if( in_array( 'suffix', $show ) )           $showsuffix = 1;        // $honorificSuffix
            if( in_array( 'adresse', $show ) )          $showaddress = 1;       // $streetAddress, $postalCode, $addressLocality, $addressCountry   
            if( in_array( 'raum', $show ) )             $showroom = 1;          // $workLocation
            if( in_array( 'telefon', $show ) )          $showtelefon = 1;       // $telephone   
            if( in_array( 'fax', $show ) )              $showfax = 1;           // $faxNumber
            if( in_array( 'mobil', $show ) )            $showmobile = 1;        // $mobilePhone
            if( in_array( 'mail', $show ) )             $showmail = 1;          // $email
            if( in_array( 'webseite', $show ) )         $showwebsite = 1;       // $url  
            if( in_array( 'mehrlink', $show ) )         $showlink = 1;          // $link
            if( in_array( 'kurzauszug', $show ) )       $showdescription = 1;   // $description (erscheint bei Sidebar)
            if( in_array( 'sprechzeiten', $show ) )     $showoffice = 1;        // $hoursAvailable
            if( in_array( 'publikationen', $show ) )    $showpubs = 1;          //
            if( in_array( 'bild', $show ) )             $showthumb = 1;         //
            if( in_array( 'ansprechpartner', $show ) )  $showvia = 1;           //
        }    
        if ( !empty( $hide ) ) {
            $hide = array_map('trim', explode(',', $hide));
            if( in_array( 'kurzbeschreibung', $hide ) ) $showlist = 0;
            if( in_array( 'organisation', $hide ) )     $showinstitution = 0;   
            if( in_array( 'abteilung', $hide ) )        $showabteilung = 0;  
            if( in_array( 'position', $hide ) )         $showposition = 0;
            if( in_array( 'titel', $hide ) )            $showtitle = 0;    
            if( in_array( 'suffix', $hide ) )           $showsuffix = 0;  
            if( in_array( 'adresse', $hide ) )          $showaddress = 0;            
            if( in_array( 'raum', $hide ) )             $showroom = 0;  
            if( in_array( 'telefon', $hide ) )          $showtelefon = 0;             
            if( in_array( 'fax', $hide ) )              $showfax = 0;
            if( in_array( 'mobil', $hide ) )            $showmobile = 0;
            if( in_array( 'mail', $hide ) )             $showmail = 0; 
            if( in_array( 'webseite', $hide ) )         $showwebsite = 0;            
            if( in_array( 'mehrlink', $hide ) )         $showlink = 0;
            if( in_array( 'kurzauszug', $hide ) )       $showdescription = 0;
            if( in_array( 'sprechzeiten', $hide ) )     $showoffice = 0;
            if( in_array( 'publikationen', $hide ) )    $showpubs = 0;
            if( in_array( 'bild', $hide ) )             $showthumb = 0;         
            if( in_array( 'ansprechpartner', $hide ) )  $showvia = 0;
        }
        if ( $extended == 1 ) {
            $showlist = 1;
            $showinstitution = 0;
            $showfax = 0;
            $showwebsite = 0;
            $showthumb = 1;
        }                
        
        $category = get_term_by('slug', $category, 'persons_category');

        $posts = get_posts(array('post_type' => 'person', 'post_status' => 'publish', 'numberposts' => 1000, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(
                array(
                    'taxonomy' => 'persons_category',
                    'field' => 'id', // can be slug or id - a CPT-onomy term's ID is the same as its post ID
                    'terms' => $category->term_id
                )
            ), 'suppress_filters' => false));

        if ($posts) {
            $number = count($posts);
            $i = 1;
                if ( $shortlist ) {
                    $content = '<span class="person liste-person" itemscope itemtype="http://schema.org/Person">';
                //} elseif ( $page ) {
                //    $liste = '';
                } elseif ( $list ) {
                    $content = '<ul class="person liste-person" itemscope itemtype="http://schema.org/Person">';
                    $content .= "\n";    
                } else {
                    $content = '';
                    // Herausgenommen da vermutlich nicht nötig
                    //$liste = '<p>';
                }            
            foreach ($posts as $post) {            

                $value = $post->ID;
                if ( $page ) {
                        $content .= self::fau_person_page($value);
                    } elseif ( $shortlist ) {
                        $content .= self::fau_person_shortlist($value, $showlist);
                        if( $i < $number )  $content .= ", ";
                    } elseif ( $list ) {
                        $content .= '<li class="person-info">'."\n";
                        $content .= self::fau_person_shortlist($value, $showlist);
                        $content .= "</li>\n";
                    } elseif ( $sidebar ) {
                        $content .= self::fau_person_sidebar($value, 0, $showlist, $showinstitution, $showabteilung, $showposition, $showtitle, $showsuffix, $showaddress, $showroom, $showtelefon, $showfax, $showmobile, $showmail, $showwebsite, $showlink, $showdescription, $showoffice, $showpubs, $showthumb, $showvia);
                    } elseif ( $compactindex ) {
                        $content .= self::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex);  
                    } else {
                        $content .= self::fau_person_markup($value, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia);
                    }
                    $i++;
                    
            }        
            if ( $shortlist  ) {
                $content .= "</span>";
            } elseif ( $list ) {
                $content .= "</ul>\n";
            } elseif ( $page ) {
                $post = get_post( $post->ID );
                if ( $post->post_content ) $inhalt = wpautop( $post->post_content );  
                $content .= $inhalt;
            } else {
                $content .= '';
                //herausgenommen da vermutlich nicht nötig
                //$liste .= "</p>\n";                
            }             

        } else {
            $content = '<p>' . sprintf(__('Es konnten keine Kontakte in der Kategorie %s gefunden werden.', FAU_PERSON_TEXTDOMAIN), $category->slug) . '</p>';            
        }
        
        return $content;
    }



    public static function fau_person_markup($id, $extended, $showlink, $showfax, $showwebsite, $showaddress, $showroom, $showdescription, $showlist, $showsidebar, $showthumb, $showpubs, $showoffice, $showtitle, $showsuffix, $showposition, $showinstitution, $showabteilung, $showmail, $showtelefon, $showmobile, $showvia, $compactindex=0) {
        
        // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)
        $fields = sync_helper::get_fields( $id, get_post_meta($id, 'fau_person_univis_id', true), 0 );
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
        extract($fields);
        if( $showvia !== 0 && !empty($connections) )                    $showvia = 1;
        if( $showvia === 0 && !empty( $connection_only ) )      $connection_only = '';
 
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
            $contactpoint = '<li class="person-info-address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span class="screen-reader-text">' . __('Adresse', FAU_PERSON_TEXTDOMAIN) . ': <br></span>';            
            if($streetAddress) {
                $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">'.$streetAddress.'</span>';
                if ( $workLocation ) {
                    $contactpoint .= '<br>';
                } elseif ( $postalCode || $addressLocality )  {
                    $contactpoint .= '<br>';
                } elseif ( $addressCountry ) {
                    $contactpoint .= '<br>';
                }                    
            }
            if ( $workLocation && ( $extended || $showroom ) ) {
                $contactpoint .= '<span class="person-info-room" itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . __('Raum', FAU_PERSON_TEXTDOMAIN) . ' ' . $workLocation . '</span>'; 
                if ( $postalCode || $addressLocality || $addressCountry )
                    $contactpoint .= '<br>';                            
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
             
        $fullname = '<span itemprop="name">';
        if($showtitle && $honorificPrefix)                      
            $fullname .= '<span itemprop="honorificPrefix">' . $honorificPrefix . '</span> ';
        if($givenName || $familyName) {
                    if($givenName)          $fullname .= '<span itemprop="givenName">'.$givenName."</span> ";
                    if($familyName)         $fullname .= '<span itemprop="familyName">'.$familyName."</span>";
        } elseif( !empty( get_the_title($id) ) ) {                                                
            $fullname .= get_the_title($id);
        }
        if($showsuffix && $honorificSuffix)                     
            $fullname .= ', <span itemprop="honorificSuffix">' . $honorificSuffix . '</span>';
        $fullname .= '</span>';
        
                    
        $content = '<div class="person content-person" itemscope itemtype="http://schema.org/Person">';	
        if( $compactindex )     $content .= '<div class="compactindex">';
        
        // if( !$compactindex || $showthumb )        
	$content .= '<div class="row">';

        if($showthumb) {
            $content .= '<div class="span1 span-small" itemprop="image">';	
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">';
            
            if (has_post_thumbnail($id)) {
                $content .= get_the_post_thumbnail($id, 'person-thumb-bigger');
            } else {
		if ($type == 'realmale') {
                    $bild =  plugin_dir_url( __FILE__ ) .'../images/platzhalter-mann.png';   
		} elseif ($type == 'realfemale') {
                    $bild = plugin_dir_url( __FILE__ ) .'../images/platzhalter-frau.png';
                } elseif ($type == 'einrichtung') {
                    $bild = plugin_dir_url( __FILE__ ) .'../images/platzhalter-organisation.png';
                } else {
                    $bild = plugin_dir_url( __FILE__ ) .'../images/platzhalter-unisex.png';
                }		
                       // _rrze_debug ($bild);
		if ($bild) 
                    $content .=  '<img src="'.$bild.'" width="90" height="120" alt="">';
            }
            $content .= '</a>';
            $content .= '</div>';
        }
  
        if( $compactindex ) {
            if( $showthumb ) {
		$content .= '<div class="span6">';
	    } else {
		$content .= '<div class="span7">';
	    }
        } else {
            if( $showthumb ) {
                $content .= '<div class="span3">';
            } else {
                $content .= '<div class="span4">';
            }
        }  
        $content .= '<h3>';        
        $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
        $content .= '</h3>';
        $content .= '<ul class="person-info">';
	    if ($showposition && $jobTitle)
		$content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', FAU_PERSON_TEXTDOMAIN) . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>';
	    if ($showinstitution && $worksFor)
		$content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>';
	    if ($showabteilung && $department)
		$content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="department">' . $department . '</span></li>';
	    if (($extended || $showaddress) && !empty($contactpoint)  && empty( $connection_only ) ) 
		$content .= $contactpoint;
	    if ($showtelefon && $telephone  && empty( $connection_only ) )
		$content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
	    if ($showmobile && $mobilePhone  && empty( $connection_only ) )
		$content .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobil', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="mobilePhone">' . $mobilePhone . '</span></li>';
	    if ($showfax && $faxNumber  && empty( $connection_only ) )
		$content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
	    if ($showmail && $email  && empty( $connection_only ) )
		$content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
	    if ($showwebsite && $url)
		$content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>';
	    if ($showpubs && $pubs)
		$content .= '<li class="person-info-pubs"><span class="screen-reader-text">' . __('Publikationen', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $pubs . '</li>';
        $content .= '</ul>';
	
	
        if ( (!empty($connection_text) || !empty($connection_options) || !empty($connections))  && $showvia===1 )
            $content .= self::fau_person_connection( $connection_text, $connection_options, $connections );

      //  if( !($compactindex && $showthumb) )      $content .= '</div>';
	
        if (($showoffice && $hoursAvailable  && empty( $connection_only )) 
		|| ($showlist && isset($excerpt))
	        || (($showsidebar || $extended) && $description) 
		|| ($showlink && $personlink)) {
	    
	    
            if( !$compactindex )    $content .= '</div><div class="span3">';
            if ($showoffice && $hoursAvailable  && empty( $connection_only ) ) {
                $content .= '<ul class="person-info">';
                $content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><div itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</div></li>';
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

        if( $compactindex )     $content .= '</div>';   // ende div class compactindex
        $content .= '</div>';
        return $content;
    }


    public static function fau_person_page($id) {
 
     	$content = '<div class="person" itemscope itemtype="http://schema.org/Person">';        
        // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);        
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
        extract($fields);
        
        if ($streetAddress || $postalCode || $addressLocality || $addressCountry) {
            $contactpoint = '<li class="person-info-address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span class="screen-reader-text">' . __('Adresse', FAU_PERSON_TEXTDOMAIN) . ': <br></span>';
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

        if ((strlen($url) > 4) && (strpos($url, "http") === false)) {
            $url = 'http://' . $url;
        }
        //$content = '';
        
        $fullname = '';
        if ($honorificPrefix)
            $fullname .= '<span itemprop="honorificPrefix">' . $honorificPrefix . '</span> ';
        if ($givenName)
            $fullname .= '<span itemprop="givenName">' . $givenName . '</span> ';
        if ($familyName)
            $fullname .= '<span itemprop="familyName">' . $familyName . '</span>';
        if ($honorificSuffix)
            $fullname .= ', <span itemprop="honorificSuffix">' . $honorificSuffix . '</span>';
	
	
        $content .= '<h2 itemprop="name">' . $fullname . '</h2>';
        $post = get_post($id);
        if (has_post_thumbnail($id)) {
            $content .= '<div itemprop="image" class="alignright">';
            // $content .= get_the_post_thumbnail($id, 'post');	    
            $content .= get_the_post_thumbnail($id, 'person-thumb-page');
            $content .= '</div>';
        }
        $content .= '<ul class="person-info">';
        if ( $jobTitle )
            $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', FAU_PERSON_TEXTDOMAIN) . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>';
        if ( $worksFor )
            $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>';
        if ( $department )
            $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $department . '</span></li>';
        if ( $telephone && empty( $connection_only ) )
            $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
        if ( $mobilePhone && empty( $connection_only ) )
            $content .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobil', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="mobilePhone">' . $mobilePhone . '</span></li>';
        if ( $faxNumber && empty( $connection_only ) )
            $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
        if ( $email && empty( $connection_only ) )
            $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
        if ( $url && empty( $connection_only ) )
            $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>';
        if ( !empty( $contactpoint ) && empty( $connection_only ) ) {
            $content .= $contactpoint;
        }
        if ( $workLocation && empty( $connection_only ) )
            $content .= '<li class="person-info-room"><span itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . __('Raum', FAU_PERSON_TEXTDOMAIN) . ' ' . $workLocation . '</span></li>';
        if ( $hoursAvailable && empty( $connection_only ) )
            $content .= '<li class="person-info-office"><span itemprop="contactPoint" itemscope itemtype="http://schema.org/ContactPoint"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</span></span></li>';
        if ( $pubs )
            $content .= '<li class="person-info-pubs"><span class="screen-reader-text">' . __('Publikationen', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $pubs . '</li>';
        $content .= '</ul>';
	
        if ( !empty($connection_text) || !empty($connection_options) || !empty($connections) )
            $content .= self::fau_person_connection( $connection_text, $connection_options, $connections );
	
	
	
	$post = get_post( $id );
        if ( $post->post_content ) {
	   $content .= '<div class="desc" itemprop="description">';
	   $content .= do_shortcode($post->post_content); 
	   $content .= '</div>';
	}
        $content .= '</div>';

        //	    if (($options['plugin_fau_person_headline'] != 'jobTitle') && ($position)) 
        //		$content .= '<li class="person-info-position"><span class="screen-reader-text">'.__('Tätigkeit','fau').': </span><strong><span itemprop="jobTitle">'.$jobTitle.'</span></strong></li>';

        return $content;
    } 
 
  

    public static function fau_person_shortlist($id, $showlist) {	
        
        // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)        
        $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
        // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
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
		$fullname = '<span itemprop="name">';
		if($honorificPrefix)            $fullname .= '<span itemprop="honorificPrefix">'.$honorificPrefix."</span> ";
                if($givenName || $familyName) {
                    if($givenName)          $fullname .= '<span itemprop="givenName">'.$givenName."</span> ";
                    if($familyName)         $fullname .= '<span itemprop="familyName">'.$familyName."</span>";
                } elseif (!empty(get_the_title($id) ) ) {
                    $fullname .= get_the_title($id);
                }
                if($honorificSuffix) 	$fullname .= ', '.$honorificSuffix;
                $fullname .= '</span>';
                $content .= '<span class="person-info">';
                $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
                if( $showlist && isset($excerpt) )                                  $content .= "<br>".$excerpt;    
                $content .= '</span>';
            return $content;
    }

 
    // von Widget, also Sidebar über Fakultätsthemes - Ansprechpartner: fau_person_sidebar($id, $title, list 0, inst 1, abtielung 1, posi 1, titel 1, suffix 1, addresse 1, raum 1, tele 1, fax 1, handy 0,                                                                  mail 1, url 1, mehrlink 0, kurzauszug 1, office 0, pubs 0, bild 1, via 0)
    public static function fau_person_sidebar($id, $title, $showlist=0, $showinstitution=0, $showabteilung=0, $showposition=0, $showtitle=0, $showsuffix=0, $showaddress=0, $showroom=0, $showtelefon=0, $showfax=0, $showmobile=0, $showmail=0, $showwebsite=0, $showlink=0, $showdescription=0, $showoffice=0, $showpubs=0, $showthumb=0, $showvia=0) {
        if (!empty($id)) {
            $post = get_post($id);

            // Hole die Feldinhalte (in der Klasse sync_helper wird gesteuert, was aus UnivIS angezeigt werden soll und was nicht)            
            $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
            // Jede Feldbezeichnung wird als Variable ansprechbar gemacht
            extract($fields);           
            
            if( $showvia !== 0 && !empty( $connections ) )                    $showvia = 1;
            if( $showvia === 0 && !empty( $connection_only ) )      $connection_only = '';
            
            if( $link ) {
                $personlink = $link;
            } else {
                $personlink = get_permalink( $id );
            }
            
            if( $showaddress ) {
                if ( $streetAddress || $postalCode || $addressLocality || $addressCountry ) {
                    $contactpoint = '<li class="person-info-address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span class="screen-reader-text">' . __('Adresse', FAU_PERSON_TEXTDOMAIN) . ': <br></span>';
                    if ( $streetAddress ) {
                        $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">' . $streetAddress . '</span>';
                        if ( $workLocation ) {
                            $contactpoint .= '<br>';                            
                        } elseif ( $postalCode || $addressLocality ) {
                            $contactpoint .= '<br>';
                        } elseif ( $addressCountry ) {
                            $contactpoint .= '<br>';
                        }
                    }
                    if ( $workLocation && $showroom ) {
                        $contactpoint .= '<span class="person-info-room" itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . __('Raum', FAU_PERSON_TEXTDOMAIN) . ' ' . $workLocation . '</span>'; 
                        if ( $postalCode || $addressLocality || $addressCountry )
                            $contactpoint .= '<br>';                            
                    }
                    if ( $postalCode || $addressLocality ) {
                        $contactpoint .= '<span class="person-info-city">';
                        if ( $postalCode )
                            $contactpoint .= '<span itemprop="postalCode">' . $postalCode . '</span> ';
                        if ( $addressLocality )
                            $contactpoint .= '<span itemprop="addressLocality">' . $addressLocality . '</span>';
                        $contactpoint .= '</span>';
                        if ( $addressCountry )
                            $contactpoint .= '<br>';
                    }
                    if ( $addressCountry )
                        $contactpoint .= '<span class="person-info-country" itemprop="addressCountry">' . $addressCountry . '</span>';
                    $contactpoint .= '</li>' . "\n";
                }
            }

            $fullname = '<span itemprop="name">';
            if ($honorificPrefix && $showtitle)           $fullname .= '<span itemprop="honorificPrefix">' . $honorificPrefix . '</span> ';
            if($givenName || $familyName) {
                if($givenName)              $fullname .= '<span itemprop="givenName">'.$givenName."</span> ";
                if($familyName)             $fullname .= '<span itemprop="familyName">'.$familyName."</span>";
            } elseif( !empty( get_the_title($id) ) ) {                                                
                $fullname .= get_the_title($id);
            }
            if ($honorificSuffix && $showsuffix)           $fullname .= ', <span itemprop="honorificSuffix">' . $honorificSuffix . '</span>';
            $fullname .= '</span>';
            
            $content = '<div class="person" itemscope itemtype="http://schema.org/Person">' . "\n";
            
            if (!empty($title)) 
                $content .= '<h2 class="small">' . $title . '</h2>' . "\n";

            $content .= '<div class="row">' . "\n";

            if ( has_post_thumbnail($id) && $showthumb ) {
                $content .= '<div class="span1" itemprop="image">';
                $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">';
                $content .= get_the_post_thumbnail($id, 'person-thumb');
                $content .= '</a>';
                $content .= '</div>' . "\n";
            }

            $content .= '<div class="span3">' . "\n";
            $content .= '<h3>';
            $content .= '<a title="' . sprintf(__('Weitere Informationen zu %s aufrufen', FAU_PERSON_TEXTDOMAIN), get_the_title($id)) . '" href="' . $personlink . '">' . $fullname . '</a>';
            $content .= '</h3>' . "\n";
            $content .= '<ul class="person-info">' . "\n";
            if ( $jobTitle && $showposition )
                $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', FAU_PERSON_TEXTDOMAIN) . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>' . "\n";
            if ( $worksFor && $showinstitution )
                $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>' . "\n";
            if ( $department && $showabteilung )
                $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="department">' . $department . '</span></li>' . "\n";
            if ( !empty($contactpoint) && empty( $connection_only ) )
                $content .= $contactpoint;            
            if ( $telephone && $showtelefon && empty( $connection_only ) )
                $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>' . "\n";
            if ( $mobilePhone && $showmobile && empty( $connection_only ) )
                $content .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobil', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="mobilePhone">' . $mobilePhone . '</span></li>' . "\n";
            if ( $faxNumber && $showfax && empty( $connection_only ) )
                $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>' . "\n";
            if ( $email && $showmail && empty( $connection_only ) )
                $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>' . "\n";
            if ( $url && $showwebsite )
                $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>' . "\n";
            if ( $hoursAvailable && $showoffice  && empty( $connection_only ) )
                $content .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><div itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</div></li>';
            $content .= '</ul>' . "\n";
            if ( ( !empty($connection_text) || !empty($connection_options) || !empty($connections) ) && $showvia===1  )
                $content .= self::fau_person_connection( $connection_text, $connection_options, $connections );
            if ( $description && $showdescription )
                $content .= '<div class="person-info-description"><span class="screen-reader-text">' . __('Beschreibung', FAU_PERSON_TEXTDOMAIN) . ': </span>' . $description . '</div>' . "\n";
            $content .= '</div>' . "\n";
            $content .= '</div>' . "\n";

            $content .= '</div>';
        }
        return $content;

    }
    
    public static function fau_person_connection( $connection_text, $connection_options, $connections ) {
       
        $content = '';
	$contactlist = '';
        foreach ( $connections as $key => $value ) {
            extract ( $connections[$key] );
            $fullname = '';
            $contactpoint = '';      
	       
            if ( $honorificPrefix )            $fullname .= '<span itemprop="honorificPrefix">'.$honorificPrefix."</span> ";
                if( $givenName || $familyName ) {
                    if ( $givenName )          $fullname .= '<span itemprop="givenName">'.$givenName."</span> ";
                    if ( $familyName )         $fullname .= '<span itemprop="familyName">'.$familyName."</span>";
                } elseif ( !empty( get_the_title( $nr ) ) ) {
                    $fullname .= get_the_title($nr);
                }
                if ( $honorificSuffix ) 	$fullname .= ', '.$honorificSuffix;
  
            if ( $streetAddress || $postalCode || $addressLocality || $addressCountry || $workLocation ) {
                $contactpoint .= '<li class="person-info-address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span class="screen-reader-text">' . __('Adresse', FAU_PERSON_TEXTDOMAIN) . ': </span>';
            if ( $streetAddress ) {
                $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">' . $streetAddress . '</span>';
                if( $postalCode || $addressLocality ) {
                    $contactpoint .= ', ';
                } elseif( $addressCountry ) {
                    $contactpoint .= ', ';
                }
            }
            if ( $postalCode || $addressLocality ) {
                $contactpoint .= '<span class="person-info-city">';
                if ( $postalCode )
                    $contactpoint .= '<span itemprop="postalCode">' . $postalCode . '</span> ';
                if ( $addressLocality )
                    $contactpoint .= '<span itemprop="addressLocality">' . $addressLocality . '</span>';
                $contactpoint .= '</span>';
                if ( $addressCountry )
                    $contactpoint .= ', ';
            }
            if ( $addressCountry )
                $contactpoint .= '<span class="person-info-country" itemprop="addressCountry">' . $addressCountry . '</span>';
            if ( $streetAddress || $postalCode || $addressLocality || $addressCountry || $workLocation ) {
                $contactpoint .= ', ';
            }
            if ( $workLocation )
                $contactpoint .= '<span class="person-info-room" itemprop="workLocation" itemscope itemtype="http://schema.org/Person">' . __('Raum', FAU_PERSON_TEXTDOMAIN) . ' ' . $workLocation . '</span>';
            }    
            $contactpoint .= '</li>';
            
	    $contactlist .= '<li itemprop="name" itemscope itemtype="http://schema.org/Person">' . $fullname;
	   
		if ( $connection_options ) {
		    $cinfo = '';

		    if ( $telephone && in_array( 'telephone', $connection_options ) )
			$cinfo .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
		    if ( isset($mobilePhone) && in_array( 'telephone', $connection_options ) ) 
			$cinfo .= '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobiltelefon', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="mobilePhone">' . $mobilePhone . '</span></li>';
		    if ( $faxNumber && in_array( 'faxNumber', $connection_options ) )
			$cinfo .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
		    if ( $email && in_array( 'email', $connection_options ) )
			$cinfo .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
		    if ( !empty( $contactpoint ) && in_array( 'contactPoint', $connection_options ) )
			$cinfo .= $contactpoint;
		    if ( $hoursAvailable && in_array( 'hoursAvailable', $connection_options ) )
			$cinfo .= '<li class="person-info-office"><span class="screen-reader-text">' . __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint">' . $hoursAvailable . '</span></li>';
		    
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
	    if( $connection_text ) {
		 $content .= '<h3>' . $connection_text . '</h3>';		 
	    }
	    $content .= '<ul class="connection-list">';
	    $content .= $contactlist;
	    $content .= '</ul>';
	    $content .= '</div>';    
	}
	
        return $content;
    }
}
