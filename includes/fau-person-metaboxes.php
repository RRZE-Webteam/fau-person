<?php
// UnivIS-Anbindung
require_once(plugin_dir_path(__FILE__) . 'univis/class_controller.php');


function get_univisdata(  ) {
    
    	$univis_url = "http://univis.uni-erlangen.de/prg";
        global $post;

        $id = get_post_meta( $post->ID, 'fau_person_univis_id', true);

        if($id) {
		//Ueberpruefe ob Vor- und Nachname gegeben sind.
/*		$noetige_felder = array("firstname", "lastname");
		foreach ($noetige_felder as $feld) {
			if(!array_key_exists($feld, $this->optionen) || $this->optionen[$feld] == "") {
				// Fehler: Bitte geben Sie Vor- und Nachname der gesuchten Person an
				echo "<div class=\"hinweis_wichtig\">Bitte geben Sie Vor- und Nachname der gesuchten Person an.</div>";
				return -1;
			}

			if(strrpos($this->optionen[$feld], "&") !== false) {
				echo "Ung&uuml;ltige Eingabe.";
				return -1;
			}
		}
*/
		// Hole Daten von Univis
		$url = $univis_url."?search=persons&id=".$id."&show=xml";



		if(!fopen($url, "r")) {
			// Univis Server ist nicht erreichbar
			return -1;
		}

		$persArray = xml2array($url);
                if(empty($persArray)) {
                    echo "Leider konnte die Person nicht gefunden werden.";
                    return -1;
                } else {
		$person = $persArray["Person"];

		if(count($persArray) == 0 ) {

			// Keine Person gefunden
			return -1;
		}

		// Falls mehrer Personen gefunden wurden, wähle die erste
		if($person) $person = $person[0];

		// Lade Publikationen und Lehrveranstaltungen falls noetig
/*              if ($this->optionen["Personenanzeige_Publikationen"]) {
			$person["publikationen"] = $this->_ladePublikationen($person["id"]);
		}

		if ($this->optionen["Personenanzeige_Lehrveranstaltungen"]) {
			$person["lehrveranstaltungen"] = $this->_ladeLehrveranstaltungenAlle($person["id"]);
		}
*/
                _rrze_debug($person);
		return $person;
                }
        } else {
            echo "Sie haben keine UnivIS-ID ausgewählt.";
        }
}



// In dieser Datei werden alle Metaboxen und Felder für den Custom Post Type person definiert
// Basis dafür Custom Metaboxes and Fields for WordPress, siehe auch fau-person/metabox/readme.md

add_action('init', function() {
    if ( !class_exists( 'cmb_Meta_Box' ) ) {
        // Das CMB-Framework wird eingebunden und initialisiert.
        require_once(plugin_dir_path(__FILE__) . 'cmb/init.php');
        // Textdomain wird festgestellt.
        cmb_Meta_Box::$textdomain = self::textdomain;
    }           
}, 9999);


// render numbers
add_action( 'cmb_render_text_number', 'sm_cmb_render_text_number', 10, 5 );
function sm_cmb_render_text_number( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
    echo $field_type_object->input( array( 'class' => 'cmb_text_small', 'type' => 'text' ) );
}

// validate the field
//add_filter( 'cmb_validate_text_number', 'sm_cmb_validate_text_number' );
function sm_cmb_validate_text_number( $new ) {
    $new = filter_var($new, FILTER_SANITIZE_NUMBER_INT);
    return $new;
}



function get_contactdata( $query_args ) {
    
    
       /* $contactselect = array(
            '' => __( 'keine Angabe', FAU_PERSON_TEXTDOMAIN ),
        );
        
         $args = array(
            'post_type' => 'person',
            'order' => 'ASC',
            'meta_key' => 'fau_person_familyName',
            'orderby' => 'meta_value',
            'posts_per_page' => 30,
        );

	$personlist = get_posts($args);
        //_rrze_debug($personlist);
        if($personlist) {
            //foreach()
        }
        /*if ($personlist) {
            while ($personlist) {
                //$personlist->the_post();
                $listid = $personlist->post->ID;
                $fullname = get_the_title($listid);
                //$out .= '<option value="' . $listid . '"';
                //if ($oldid && $oldid == $listid) {
                  //  $out .= ' selected="selected';
                //}
                //$out .= '">' . $fullname . '</option>' . "\n";
                $add = array($listid => $fullname);
                $contactselect = array_merge($contactselect, $add);
                        _rrze_debug($contactselect);
            }
        } else {
            $wpautop(__('Keine Kontaktdaten verf&uuml;gbar.', FAU_PERSON_TEXTDOMAIN));
        }
*/
        
        //return $contactselect;  

    $args = wp_parse_args( $query_args, array(
        'post_type' => 'person',
        'posts_per_page' => 5,
    ));
    
    $posts = get_posts( $args );
    if ( $posts ) {
        foreach ( $posts as $post ) {
            $post_options[] = array(
                'name' => $post->post_title,
                'value' => $post->ID,
            );
        }
    }
    
    return $post_options;
    
    
}




add_filter('cmb_meta_boxes', function(array $metaboxes) {
//function fau_person_metaboxes( $meta_boxes ) {
    $prefix = 'fau_person_'; // Prefix for all fields
    //$contactselect = $this->get_contactdata();
/*    $meta_boxes['fau_person_postdata'] = array(
        'id' => 'fau_person_postdata',
        'title' => __( 'Infos zum Personenbeitrag', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'readonly' => true,
        'fields' => array(
            array(
                'name' => __('ID des Beitrags', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zum Aufruf der Person im shortcode',
                'id' => $prefix . 'id',
                'type' => 'text',
                'value' => get_post($id),
            ),
        )        
    );*/
    // Zuordnung - fau_person_orga
    $meta_boxes['fau_person_orga'] = array(
        'id' => 'fau_person_orga',
        'title' => __( 'Zuordnung', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Position/Funktion', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'id' => $prefix . 'jobTitle',
                'type' => 'text'
            ),
            array(
                'name' => __('Institution/Abteilung', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Geben Sie hier die Institution ein.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'worksFor'
            ),
            array(
                'name' => __('"Mehr"-Link führt zur Seite ...', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Bitte vollständigen Permalink der Zielseite angeben', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text_url',
                'id' => $prefix . 'link',
            )
        )
    );
    // Kontaktinformation - fau_person_info
    $meta_boxes['fau_person_info'] = array(
        'id' => 'fau_person_info',
        'title' => __( 'Kontaktinformationen', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Typ des Eintrags', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Bei Einrichtungen und Pseudonymen wird die Bezeichnung angezeigt, ansonsten Vor- und Nachname.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'select',
                'options' => array('realperson' => __('Person (geschlechtsneutral)', FAU_PERSON_TEXTDOMAIN),
                    'realmale' => __('Männliche Person', FAU_PERSON_TEXTDOMAIN),
                    'realfemale' => __('Weibliche Person', FAU_PERSON_TEXTDOMAIN),
                    'pseudo' => __('Pseudonym', FAU_PERSON_TEXTDOMAIN),
                    'einrichtung' => __('Nicht-Person', FAU_PERSON_TEXTDOMAIN)
                ),
                'id' => $prefix . 'typ'
            ),
            array(
                'name' => __('Titel (Präfix)', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'select',
                'options' => array(
                    '' => __('Keine Angabe', FAU_PERSON_TEXTDOMAIN),
                    'Dr.' => __('Doktor', FAU_PERSON_TEXTDOMAIN),
                    'Prof.' => __('Professor', FAU_PERSON_TEXTDOMAIN),
                    'Prof. Dr.' => __('Professor Doktor', FAU_PERSON_TEXTDOMAIN)
                ),
                'id' => $prefix . 'honorificPrefix'
            ),
            array(
                'name' => __('Vorname', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'givenName'
            ),
            array(
                'name' => __('Nachname', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'familyName'
            ),
            array(
                'name' => __('Bezeichnung (oder Pseudonym)', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'alternateName'
            ),
            array(
                'name' => __('Abschluss (Suffix)', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'honorificSuffix'
            ),
            array(
                'name' => __('Straße und Hausnummer', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'streetAddress'
            ),
            array(
                'name' => __('Postleitzahl', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'postalCode'
            ),
            array(
                'name' => __('Ort', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'addressLocality'
            ),
            array(
                'name' => __('Land', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'addressCountry'
            ),
            array(
                'name' => __('Raum', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'workLocation'
            ),
            array(
                'name' => __('Standort Telefonanschluss', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'radio',
                'id' => $prefix . 'telephone_select',
                'options' => array(
                    'erl' => __('Uni-intern, Standort Erlangen', FAU_PERSON_TEXTDOMAIN),
                    'nbg' => __('Uni-intern, Standort Nürnberg', FAU_PERSON_TEXTDOMAIN),
                    'standard' => __('Außerhalb der Universität', FAU_PERSON_TEXTDOMAIN)
                )
            ),
            array(
                'name' => __('Telefon', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Bitte geben Sie uni-interne Nummern für Erlangen in der Form 09131 85-22222 und für Nürnberg in der Form 0911 5302-555 an.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'telephone'
            ),
            array(
                'name' => __('Telefax', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'faxNumber'
            )
        )
    );
    // Social Media - fau_person_social_media
    $meta_boxes['fau_person_social_media'] = array(
        'id' => 'fau_person_social_media',
        'title' => __('Verknüpfungen', FAU_PERSON_TEXTDOMAIN),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('E-Mail', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text_email',
                'id' => $prefix . 'email'
            ),
            array(
                'name' => __('Webseite', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text_url',
                'id' => $prefix . 'url'
            )
        )
    );
    // Weitere Informationen - fau_person_adds
    $meta_boxes['fau_person_adds'] = array(
        'id' => 'fau_person_adds',
        'title' => __('Weitere Informationen', FAU_PERSON_TEXTDOMAIN),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Freitext', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'textarea',
                'id' => $prefix . 'description'
            ),
            array(
                'name' => __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'textarea',
                'id' => $prefix . 'hoursAvailable'
            ),
            /*array(
                'name' => __('Publikationen', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'pubs'
            )*/
        )
    );
    // Synchronisierung mit externen Daten - fau_person_sync ab hier
    $meta_boxes['fau_person_sync'] = array(
        'id' => 'fau_person_sync',
        'title' => __('Datenimport', FAU_PERSON_TEXTDOMAIN),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('UnivIS-ID', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Die UnivIS-ID der Person, von der die Daten importiert werden sollen.',
                'type' => 'text_number',
                'id' => $prefix . 'univis_id',
                //'options' => get_univisdata(),
            ),
            /*array(
                'name' => __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'textarea',
                'id' => $prefix . 'hoursAvailable'
            ),
            array(
                'name' => __('Publikationen', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'pubs'
            )*/
        )
    );

    
    
    $meta_boxes['fau_person_post_metabox'] = array(
        'id' => 'fau_person_post_metabox',
        'title' => __( 'Kontaktinformationen', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('post', 'page'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Kontaktangabe auswählen', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'id' => $prefix . 'contactselect',
                'type' => 'select',
                'options' => get_contactdata( array( 'post_type' => 'person', 'posts_per_page' => 900 )),
            ),
        )        
    );
    
    return $meta_boxes;
});

    ///////////////////////////////////////////////////////////////
    /////		Hilfsmethoden
    ///////////////////////////////////////////////////////////////
    // XML Parser
    function xml2array($fname) {
        //$sxi = $fname;
        $sxi = new SimpleXmlIterator($fname, null, true);
        return sxiToArray($sxi);
    }

    function sxiToArray($sxi) {
        $a = array();

        for ($sxi->rewind(); $sxi->valid(); $sxi->next()) {
            if (!array_key_exists($sxi->key(), $a)) {
                $a[$sxi->key()] = array();
            }
            if ($sxi->hasChildren()) {
                $a[$sxi->key()][] = sxiToArray($sxi->current());
            } else {
                $a[$sxi->key()] = strval($sxi->current());

                //Fuege die UnivisRef Informationen ein.
                if ($sxi->UnivISRef) {
                    $attributes = (array) $sxi->UnivISRef->attributes();
                    $a[$sxi->key()][] = $attributes["@attributes"];
                }
            }

            if ($sxi->attributes()) {
                $attributes = (array) $sxi->attributes();
                $a["@attributes"] = $attributes["@attributes"];
            }
        }
        return $a;
    }

    

