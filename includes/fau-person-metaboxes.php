<?php
// UnivIS-Anbindung
require_once(plugin_dir_path(__FILE__) . 'univis/class_controller.php');


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


function validate_univis_id( $str ) {   
    if( ctype_digit( $str ) && strlen( $str ) == 8 ) 
        return $str;
}

function validate_plz( $str ) {   
    if( ctype_digit( $str ) && strlen( $str ) == 5 ) 
        return $str;
}

/*    
function univis_id_notice() {
        ?><div id="message" class="updated"><p><?php _e('Bitte geben Sie eine gültige UnivIS-ID (8-stellige Zahl) ein.', self::textdomain) ?></p></div><?php
}*/

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

add_filter('cmb_meta_boxes', function(array $metaboxes) {
    //global $post;
//function fau_person_metaboxes( $meta_boxes ) {
    $prefix = 'fau_person_'; // Prefix for all fields
    $contactselect = (array) $this->get_contactdata();
    $univis_default = $this->univis_defaults();  
    if( !class_exists( 'Univis_Data' ) ) {
        $univis_sync = __('<p class="cmb_metabox_description">Es können aktuell keine Daten aus UnivIS angezeigt werden. Bitte überprüfen Sie, ob Sie das Plugin univis-data installiert und aktiviert haben.</p>', FAU_PERSON_TEXTDOMAIN);
    } else {
        $univis_sync = '';
    }
    //ID der Kontaktseite
    $id = cmb_Meta_Box::get_object_id();
   // $helpuse = $this->get_helpuse();
    
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
    // Meta-Box Zuordnung - fau_person_orga
    $meta_boxes['fau_person_orga'] = array(
        'id' => 'fau_person_orga',
        'title' => __( 'Zuordnung', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('person', 'kontakt'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Typ des Eintrags', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Kontakt, Person oder Standort', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'type'
            ),            
            array(
                'name' => __('Organisation', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Geben Sie hier die Organisation (Lehrstuhl oder Einrichtung) ein.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'worksFor',
                'after' => $univis_default['worksFor']
            ),
            array(
                'name' => __('Abteilung', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Geben Sie hier die Abteilung oder Arbeitsgruppe ein.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'department',
                'after' => $univis_default['department']                
            ),
            array(
                'name' => __('Position/Funktion', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'id' => $prefix . 'jobTitle',
                'type' => 'text',
                'after' => $univis_default['jobTitle']    
            ),
        )
    );
    // Meta-Box Kontaktinformation - fau_person_info
    $meta_boxes['fau_person_info'] = array(
        'id' => 'fau_person_info',
        'title' => __( 'Kontaktinformationen', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('person', 'kontakt'), // post type
        'show_on' => array( 'key' => 'submenu-slug', 'value' => 'kontakt' ),        
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
                    'Prof. Dr.' => __('Professor Doktor', FAU_PERSON_TEXTDOMAIN),
                    'PD' => __('Privatdozent', FAU_PERSON_TEXTDOMAIN),
                    'PD Dr.' => __('Privatdozent Doktor', FAU_PERSON_TEXTDOMAIN)
                ),
                'id' => $prefix . 'honorificPrefix',
                'after' => $univis_default['honorificPrefix']    
            ),
            array(
                'name' => __('Vorname', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'givenName',
                'after' => $univis_default['givenName']    
            ),
            array(
                'name' => __('Nachname', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'familyName',
                'after' => $univis_default['familyName']  
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
                'id' => $prefix . 'honorificSuffix',
                'after' => $univis_default['honorificSuffix']  
            ),
            array(
                'name' => __('Straße und Hausnummer', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'streetAddress',
                'after' => $univis_default['streetAddress']  
            ),
            array(
                'name' => __('Postleitzahl', FAU_PERSON_TEXTDOMAIN),
                //'desc' => 'Wenn der Ort aus UnivIS übernommen werden soll bitte leer lassen!',
                'desc' => 'Nur 5-stellige Zahlen erlaubt.',
                'type' => 'text_small',
                'id' => $prefix . 'postalCode',
                'sanitization_cb' => 'validate_plz',
            ),
            array(
                'name' => __('Ort', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'addressLocality',
                'after' => $univis_default['addressLocality']  
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
                'id' => $prefix . 'workLocation',
                'after' => $univis_default['workLocation'] 
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
                'desc' => __('Bitte geben Sie uni-interne Nummern für Erlangen in der internationalen Form +49 9131 85-22222 und für Nürnberg in der internationalen Form +49 911 5302-555 an.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'telephone',
                'after' => $univis_default['telephone'] 
            ),
            array(
                'name' => __('Telefax', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Bitte geben Sie uni-interne Nummern für Erlangen in der internationalen Form +49 9131 85-22222 und für Nürnberg in der internationalen Form +49 911 5302-555 an, uni-externe Nummern in der internationalen Form +49 9131 1111111.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'faxNumber',
                'after' => $univis_default['faxNumber'] 
            ),
            array(
                'name' => __('Mobiltelefon', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Bitte geben Sie die Nummer in der internationalen Form +49 176 1111111 an.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'mobilePhone'
            ),
        )
    );
    // Meta-Box Social Media - fau_person_social_media
    $meta_boxes['fau_person_social_media'] = array(
        'id' => 'fau_person_social_media',
        'title' => __('Social Media', FAU_PERSON_TEXTDOMAIN),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('E-Mail', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text_email',
                'id' => $prefix . 'email',
                'after' => $univis_default['email'] 
            ),
            array(
                'name' => __('Webseite', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text_url',
                'id' => $prefix . 'url',
                'after' => $univis_default['url'] 
            ),
            array(
                'name' => __('Name und "Mehr"-Link verlinken auf Seite ...', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Bitte vollständigen Permalink der ausführlichen Personenseite angeben.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text_url',
                'id' => $prefix . 'link',
                'after' => sprintf(__('<p class="cmb_metabox_description">[Standardwert wenn leer: %s]</p>', FAU_PERSON_TEXTDOMAIN), get_permalink( $id )),
                //'after' => '<hr>' . __('Zum Anzeigen der Person verwenden Sie bitte die ID', FAU_PERSON_TEXTDOMAIN) . ' ' . $helpuse,                
            )            
        )
    );
    // Meta-Box Weitere Informationen - fau_person_adds
    $meta_boxes['fau_person_adds'] = array(
        'id' => 'fau_person_adds',
        'title' => __('Weitere Informationen', FAU_PERSON_TEXTDOMAIN),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Kurzauszug', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Wird bei der Anzeige in einer Sidebar verwendet, bis zu 160 Zeichen.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'textarea_small',
                'id' => $prefix . 'description'
            ),
            array(
                'name' => __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'textarea_small',
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
    // Meta-Box Synchronisierung mit externen Daten - fau_person_sync ab hier
    $meta_boxes['fau_person_sync'] = array(
        'id' => 'fau_person_sync',
        'title' => __('Datenimport', FAU_PERSON_TEXTDOMAIN),
        'pages' => array('person'), // post type
        'context' => 'side',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('UnivIS-ID', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Die UnivIS-ID der Person, von der die Daten angezeigt werden sollen (8-stellige Zahl).',
                'type' => 'text',
                'id' => $prefix . 'univis_id',
                'sanitization_cb' => 'validate_univis_id',
            ),
            array(
                'name' => __('Daten aus UnivIS anzeigen', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige in der Ausgabe die Daten, die in UnivIS hinterlegt sind: Titel (Präfix), Vorname, Nachname, Titel (Suffix), Organisation bzw. Abteilung, Position/Funktion, Adresse, Telefon- und Telefaxnummer, E-Mail, Webseite). Die in diesen Feldern hier eingegebenen Werte werden in der Ausgabe nicht angezeigt.',
                'type' => 'checkbox',
                'id' => $prefix . 'univis_sync',
                'before' => $univis_sync,
            ),
        )
    );
    // Ausgeblendete Metabox, Daten werden für UnivIS-Einbindung verwendet - fau_person_univis ab hier
    /*$meta_boxes['fau_person_univis'] = array(
        'id' => 'fau_person_univis',
        'title' => __('UnivIS-Felder', FAU_PERSON_TEXTDOMAIN),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'low',
        'show_names' => false, // Show field names on the left
        'fields' => array(
 
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige die Position/Funktion aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'jobTitle_sync',
            ),       
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige die Organisation (Lehrstuhl oder Einrichtung) aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'worksFor_sync'
            ),     
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige den Titel (Präfix) aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'honorificPrefix_sync'
            ),       
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige den Vornamen aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'givenName_sync'
            ),  
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige den Nachnamen aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'familyName_sync'
            ),   
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige den Abschluss (Suffix) aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'honorificSuffix_sync'
            ),   
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige die Straße und Hausnummer aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'streetAddress_sync'
            ),  
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige Postleitzahl und Ort aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'addressLocality_sync'
            ),        
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige den Raum aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'workLocation_sync'
            ),     
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige die Telefonnummer aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'telephone_sync'
            ),    
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige die Telefax-Nummer aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'faxNumber_sync'
            ), 
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige die E-Mail-Adresse aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'email_sync'
            ),    
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige die Webseite aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'url_sync'
            ),   
            array(
                'name' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zeige die Sprechzeiten aus UnivIS',
                'type' => 'checkbox',
                'id' => $prefix . 'hoursAvailable_sync'
            ),            
        )
    );*/

    // Meta-Box zur Anzeige der verfügbaren Kontakte auf post und page, um die Personen-ID schneller herauszufinden
    $meta_boxes['fau_person_post_metabox'] = array(
        'id' => 'fau_person_post_metabox',
        'title' => __( 'Kontaktinformationen', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('post', 'page'), // post type
        'context' => 'side',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Verfügbare Kontakte anzeigen', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'id' => $prefix . 'contactselect',
                'type' => 'select',
                'options' => $contactselect,
            ),
        )        
    );
    
    return $meta_boxes;
});
