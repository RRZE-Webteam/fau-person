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

function validate_number( $str ) {
    $location = get_post_meta( cmb_Meta_Box::get_object_id(), 'fau_person_telephone_select', true );
    $str = sync_helper::correct_phone_number( $str, $location );
    add_action( 'admin_notices', array( 'FAU_Person', 'admin_notice_phone_number' ) );
    return $str;
}

//Anzeigen des Feldes nur bei Einrichtungen
function show_on_einrichtung( $field ) {
    $default_fau_person_typ = FAU_Person::default_fau_person_typ();
    $typ = get_post_meta($field->object_id, 'fau_person_typ', true);
    if( $typ == 'pseudo' || $typ == 'einrichtung' || $default_fau_person_typ == 'einrichtung') {
        $einrichtung = true;
    } else {
        $einrichtung = false;
    }
    return $einrichtung;
}

//Anzeigen des Feldes nur bei Personen
function show_on_person( $field ) {
    $default_fau_person_typ = FAU_Person::default_fau_person_typ();
    $typ = get_post_meta($field->object_id, 'fau_person_typ', true);
    if( $typ == 'pseudo' || $typ == 'einrichtung' || $default_fau_person_typ == 'einrichtung') {
        $person = false;
    } else {
        $person = true;
    }
    return $person;
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

function mb_show_on_person( $display, $meta_box ) {
    /*_rrze_debug($meta_box['show_on']['key']);
    if ( ! isset( $meta_box['show_on']['key'] ) || $meta_box['show_on']['key'] !== 'mb_show_on_person' )
        return $display;

    $object_id = cmb_Meta_Box::get_object_id();

    if ( ! $object_id || cmb_Meta_Box::get_object_type() !== 'post' )
        return false;

    // Get current template
    $current_template = get_post_meta( $object_id, '_wp_page_template', true );

    // See if there's a match
    if ( $current_template && in_array( $current_template, (array) $meta_box['show_on']['value'] ) )
        return false;
*/
    
    return true;

}
add_filter( 'cmb_show_on', 'mb_show_on_person', 10, 2 );

add_filter('cmb_meta_boxes', function(array $metaboxes) {
    //global $post;
//function fau_person_metaboxes( $meta_boxes ) {
    $prefix = 'fau_person_'; // Prefix for all fields
    $contactselect = $this->get_contactdata();
    $contactselect_connection = $this->get_contactdata(1);
    $standortselect = $this->get_standortdata();
    $univis_default = $this->univis_defaults();  
    if( !class_exists( 'Univis_Data' ) ) {
        $univis_sync = '<p class="cmb_metabox_description">' . __('Es können aktuell keine Daten aus UnivIS angezeigt werden. Bitte überprüfen Sie, ob Sie das Plugin univis-data installiert und aktiviert haben.', FAU_PERSON_TEXTDOMAIN) . '</p>';
    } else {
        $univis_sync = '';
    }
    $standort_default = $this->standort_defaults();  
    $default_fau_person_typ = $this->default_fau_person_typ();
    //ID der Kontaktseite
    $person_id = cmb_Meta_Box::get_object_id();
   // $helpuse = $this->get_helpuse();
    
    /*$meta_boxes['fau_person_postdata'] = array(
        'id' => 'fau_person_postdata',
        'title' => __( 'Infos zum Personenbeitrag', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('ID des Beitrags', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Zum Aufruf der Person im shortcode',
                'id' => $prefix . 'id',
                'type' => 'text',
                'default' => $person_id,
            ),
        )        
    );*/
    
    // Meta-Box Zuordnung - fau_person_orga
    $meta_boxes['fau_person_orga'] = array(
        'id' => 'fau_person_orga',
        'title' => __( 'Zuordnung', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_on' => 'mb_show_on_person',
        'show_names' => true, // Show field names on the left
        'fields' => array(        
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
        'pages' => array('person'), // post type
        //'show_on' => array( 'key' => 'submenu-slug', 'value' => 'kontakt' ),        
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
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
                'after' => $univis_default['honorificPrefix'],
                'show_on_cb' => 'show_on_person'
            ),
            array(
                'name' => __('Vorname', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'givenName',
                'after' => $univis_default['givenName'],
                'show_on_cb' => 'show_on_person'
            ),
            array(
                'name' => __('Nachname', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'familyName',
                'after' => $univis_default['familyName'],
                'show_on_cb' => 'show_on_person'
            ),
            array(
                'name' => __('Bezeichnung (oder Pseudonym)', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Wird für die Kategoriensortierung nach Nachname als Sortierkriterium verwendet.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'alternateName',
                'show_on_cb' => 'show_on_einrichtung'
            ),
            array(
                'name' => __('Abschluss (Suffix)', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'honorificSuffix',
                'after' => $univis_default['honorificSuffix'],
                'show_on_cb' => 'show_on_person'
            ),
            array(
                'name' => __('Straße und Hausnummer', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'streetAddress',
                'after' => $univis_default['streetAddress'] . $standort_default['streetAddress'] 
            ),
            array(
                'name' => __('Postleitzahl', FAU_PERSON_TEXTDOMAIN),
                //'desc' => 'Wenn der Ort aus UnivIS übernommen werden soll bitte leer lassen!',
                'desc' => __('Nur 5-stellige Zahlen erlaubt.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text_small',
                'id' => $prefix . 'postalCode',
                'sanitization_cb' => 'validate_plz',
                'after' => $standort_default['postalCode'] 
            ),
            array(
                'name' => __('Ort', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'addressLocality',
                'after' => $univis_default['addressLocality'] . $standort_default['addressLocality'] 
            ),
            array(
                'name' => __('Land', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'addressCountry',
                'after' => $standort_default['addressCountry'] 
            ),
            array(
                'name' => __('Raum', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'workLocation',
                'after' => $univis_default['workLocation'] 
            ),
            array(
                'name' => __('Standort Telefon- und Faxanschluss', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'radio',
                'id' => $prefix . 'telephone_select',
                'options' => array(
                    'erl' => __('Uni-intern, Standort Erlangen', FAU_PERSON_TEXTDOMAIN),
                    'nbg' => __('Uni-intern, Standort Nürnberg', FAU_PERSON_TEXTDOMAIN),
                    'standard' => __('Allgemeine Rufnummer', FAU_PERSON_TEXTDOMAIN)
                ),
                'default' => 'standard'
            ),
            array(
                'name' => __('Telefon', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Bitte geben Sie uni-interne Nummern für Erlangen in der internationalen Form +49 9131 85-22222 und für Nürnberg in der internationalen Form +49 911 5302-555 an.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'telephone',
                'sanitization_cb' => 'validate_number',
                'after' => $univis_default['telephone'] 
            ),
            array(
                'name' => __('Telefax', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Bitte geben Sie uni-interne Nummern für Erlangen in der internationalen Form +49 9131 85-22222 und für Nürnberg in der internationalen Form +49 911 5302-555 an, uni-externe Nummern in der internationalen Form +49 9131 1111111.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'faxNumber',
                'sanitization_cb' => 'validate_number',
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
                'desc' => __('Bitte vollständigen Permalink der ausführlichen Kontaktseite angeben.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text_url',
                'id' => $prefix . 'link',
                'after' => sprintf(__('<p class="cmb_metabox_description">[Standardwert wenn leer: %s]</p>', FAU_PERSON_TEXTDOMAIN), get_permalink( $person_id )),
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
                'name' => __('Sprechzeiten: Überschrift', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Wird in Fettdruck über den Sprechzeiten ausgegeben.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'hoursAvailable_text'                
            ),
            array(
                'name' => __('Sprechzeiten: Allgemeines oder Anmerkungen', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Zur Formatierung können HTML-Befehle verwendet werden (z.B. &lt;br&gt; für Zeilenumbruch). Wird vor den Sprechzeiten ausgegeben.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'textarea_small',
                'id' => $prefix . 'hoursAvailable'
            ),
            array(
                'id' => $prefix . 'hoursAvailable_group',
                'type' => 'group',
                'desc' => $univis_default['hoursAvailable_group'],
                //'desc' => __('Bitte geben Sie die Sprechzeiten an.', FAU_PERSON_TEXTDOMAIN),
                'options' => array(
                    'group_title' => __('Sprechzeit {#}', FAU_PERSON_TEXTDOMAIN),
                    'add_button' => __('Weitere Sprechzeit einfügen', FAU_PERSON_TEXTDOMAIN),
                    'remove_button' => __('Sprechzeit löschen', FAU_PERSON_TEXTDOMAIN),
                    //'sortable' => true,
                ),
                'fields' => array(
                    array(
                        'name' =>  __('Wiederholung', FAU_PERSON_TEXTDOMAIN),
                        'id' => 'repeat',
                        'type' => 'radio_inline',
                        'options' => array(
                            'd1' => __('täglich', FAU_PERSON_TEXTDOMAIN), 
                            'w1' => __('wöchentlich', FAU_PERSON_TEXTDOMAIN),
                            'w2' => __('alle 2 Wochen', FAU_PERSON_TEXTDOMAIN),
                        )
                    ),
                    array(
                        'name' =>  __('am', FAU_PERSON_TEXTDOMAIN),
                        'id' => 'repeat_submode',
                        'type' => 'multicheck',
                        'options' => array(
                            '1' => __('Montag', FAU_PERSON_TEXTDOMAIN),
                            '2' => __('Dienstag', FAU_PERSON_TEXTDOMAIN),
                            '3' => __('Mittwoch', FAU_PERSON_TEXTDOMAIN),
                            '4' => __('Donnerstag', FAU_PERSON_TEXTDOMAIN),
                            '5' => __('Freitag', FAU_PERSON_TEXTDOMAIN),
                            '6' => __('Samstag', FAU_PERSON_TEXTDOMAIN),
                            '7' => __('Sonntag', FAU_PERSON_TEXTDOMAIN),
                        )
                    ),
                    array(
                        'name' =>  __('von', FAU_PERSON_TEXTDOMAIN),
                        'id' => 'starttime',
                        'type' => 'text_time',
                        'time_format' => 'HH:ii',
                    ),
                    array(
                        'name' =>  __('bis', FAU_PERSON_TEXTDOMAIN),
                        'id' => 'endtime',
                        'type' => 'text_time',
                        'time_format' => 'HH:ii',
                    ),
                    array(
                        'name' =>  __('Raum', FAU_PERSON_TEXTDOMAIN),
                        'id' => 'office',
                        'type' => 'text_small',
                    ),
                    array(
                        'name' =>  __('Bemerkung', FAU_PERSON_TEXTDOMAIN),
                        'id' => 'comment',
                        'type' => 'text',
                    ),
                ),

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
        'title' => __('Daten aus UnivIS', FAU_PERSON_TEXTDOMAIN),
        'pages' => array('person'), // post type
        'context' => 'side',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('UnivIS-ID', FAU_PERSON_TEXTDOMAIN),
                //'desc' => 'UnivIS-ID der Person (8-stellige Zahl)',
                'type' => 'text',
                'id' => $prefix . 'univis_id',
                'sanitization_cb' => 'validate_univis_id',
                'show_on_cb' => 'show_on_person'
            ),
            array(
                'name' => __('UnivIS-Daten in Ausgabe anzeigen', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Titel (Präfix), Vorname, Nachname, Abschluss (Suffix), Organisation bzw. Abteilung, Position/Funktion, Adresse, Telefon- und Telefaxnummer, E-Mail, Webseite. Die hier in diesen Feldern eingegebenen Werte werden in der Ausgabe nicht angezeigt.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'checkbox',
                'id' => $prefix . 'univis_sync',
                'before' => $univis_sync,
                'show_on_cb' => 'show_on_person'
            ),
            array(
                'name' => __('UnivIS-OrgNr', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Aktuell können noch keine Einrichtungsdaten aus UnivIS übernommen werden.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text',
                'id' => $prefix . 'univis_org_nr',
                'show_on_cb' => 'show_on_einrichtung'
                //'sanitization_cb' => 'validate_univis_id',
            ),
            /* array(
                'name' => __('UnivIS-Daten in Ausgabe anzeigen', FAU_PERSON_TEXTDOMAIN),
                'desc' => 'Adresse, Telefon- und Telefaxnummer, E-Mail, Webseite. Die hier in diesen Feldern eingegebenen Werte werden in der Ausgabe nicht angezeigt.',
                'type' => 'checkbox',
                'id' => $prefix . 'univis_org_sync',
                'before' => $univis_sync,
                'show_on_cb' => 'show_on_einrichtung'
            ), */
        )
    );

    $meta_boxes['fau_person_gmail_sync'] = apply_filters('fau_person_gmail_metabox', array());
    
    // Meta-Box Synchronisierung mit externen Daten - fau_person_sync ab hier
    $meta_boxes['fau_person_options'] = array(
        'id' => 'fau_person_options',
        'title' => __('Zusatzoptionen', FAU_PERSON_TEXTDOMAIN),
        'pages' => array('person'), // post type
        'context' => 'side',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Typ des Eintrags', FAU_PERSON_TEXTDOMAIN),
                //'desc' => __('Bei Einrichtungen und Pseudonymen wird die Bezeichnung angezeigt, ansonsten Vor- und Nachname.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'select',
                'options' => array('realperson' => __('Person (allgemein)', FAU_PERSON_TEXTDOMAIN),
                    'realmale' => __('Person (männlich)', FAU_PERSON_TEXTDOMAIN),
                    'realfemale' => __('Person (weiblich)', FAU_PERSON_TEXTDOMAIN),
                    'einrichtung' => __('Einrichtung', FAU_PERSON_TEXTDOMAIN),
                    'pseudo' => __('Pseudonym', FAU_PERSON_TEXTDOMAIN),
                ),
                'id' => $prefix . 'typ',
                'default' => $default_fau_person_typ
            ),
            array(
                'name' => __('Zugeordneter Standort', FAU_PERSON_TEXTDOMAIN),
                //'desc' => 'Der Standort, von dem die Daten angezeigt werden sollen.',
                'type' => 'select',
                'id' => $prefix . 'standort_id',
                'options' => $standortselect,
            ),
            array(
                'name' => __('Standort-Daten in Ausgabe anzeigen', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Straße, Postleitzahl, Ort, Land. Die hier in diesen Feldern eingegebenen Werte werden in der Ausgabe nicht angezeigt.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'checkbox',
                'id' => $prefix . 'standort_sync',
                //'before' => $standort_sync,
            ),
        )
    );    
    
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
    
    $meta_boxes['fau_person_gmail_contacts'] = apply_filters('fau_person_gmail_contacts', array());
    
    // Meta-Box um eine Kontaktperson oder -Einrichtung zuzuordnen
    $meta_boxes['fau_person_connection'] = array(
        'id' => 'fau_person_connection',
        'title' => __( 'Ansprechpartner / verknüpfte Kontakte', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('person'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Art der Verknüpfung', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Der hier eingegebene Text wird vor der Ausgabe des verknüpften Kontaktes angezeigt (z.B. Vorzimmer, Kontakt über).', FAU_PERSON_TEXTDOMAIN),
                'id' => $prefix . 'connection_text',
                'type' => 'text',
            ),
            array(
                'name' => __('Verknüpfte Kontakte auswählen', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'id' => $prefix . 'connection_id',
                'type' => 'select',
                'options' => $contactselect_connection,
                'repeatable' => true,
            ),    
            array(
                'name' => __('Angezeigte Daten der verknüpften Kontakte', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'id' => $prefix . 'connection_options',
                'type' => 'multicheck',
                'options' => array(
                    'contactPoint' => __('Adresse', FAU_PERSON_TEXTDOMAIN),
                    'telephone' => __('Telefon', FAU_PERSON_TEXTDOMAIN),
                    'faxNumber' => __('Telefax', FAU_PERSON_TEXTDOMAIN),
                    'email' => __('E-Mail', FAU_PERSON_TEXTDOMAIN),
                    'hoursAvailable' => __('Sprechzeiten', FAU_PERSON_TEXTDOMAIN),
                )
            ),
            array(
                'name' => __('Eigene Daten ausblenden', FAU_PERSON_TEXTDOMAIN),
                'desc' => __('Ausschließlich die verknüpften Kontakte werden in der Ausgabe angezeigt.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'checkbox',
                'id' => $prefix . 'connection_only',
                //'before' => $standort_sync,
            ),
        )        
    );
    
    
    // Meta-Box Standortinformation - fau_standort_info
    $meta_boxes['fau_standort_info'] = array(
        'id' => 'fau_standort_info',
        'title' => __( 'Standortinformationen', FAU_PERSON_TEXTDOMAIN ),
        'pages' => array('standort'), // post type
        //'show_on' => array( 'key' => 'submenu-slug', 'value' => 'kontakt' ),        
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Straße und Hausnummer', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'streetAddress',
                //'after' => $univis_default['streetAddress']  
            ),
            array(
                'name' => __('Postleitzahl', FAU_PERSON_TEXTDOMAIN),
                //'desc' => 'Wenn der Ort aus UnivIS übernommen werden soll bitte leer lassen!',
                'desc' => __('Nur 5-stellige Zahlen erlaubt.', FAU_PERSON_TEXTDOMAIN),
                'type' => 'text_small',
                'id' => $prefix . 'postalCode',
                'sanitization_cb' => 'validate_plz',
            ),
            array(
                'name' => __('Ort', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'addressLocality',
                //'after' => _rrze_debug($prefix . 'addressLocality')  
            ),
            array(
                'name' => __('Land', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'addressCountry'
            ),
        )
    );
    
    
    
    
    return $meta_boxes;
});
