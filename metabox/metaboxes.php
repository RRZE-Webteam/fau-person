<?php


add_filter( 'cmb_meta_boxes', 'fau_person_metaboxes' );

function fau_person_metaboxes( $meta_boxes ) {
    $prefix = 'fau_person_'; // Prefix for all fields
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
                    'einrichtung' => __('Einrichtung', FAU_PERSON_TEXTDOMAIN)
                ),
                'id' => $prefix . 'typ'
            ),
            array(
                'name' => __('Titel (Präfix)', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'select',
                'options' => array(
                    '' => 'keine Auswahl',
                    'Dr.' => 'Doktor',
                    'Prof.' => 'Professor',
                    'Prof. Dr.' => 'Professor Doktor'
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
                'name' => __('Telefon', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
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
                'type' => 'text',
                'id' => $prefix . 'hoursAvailable'
            ),
            array(
                'name' => __('Publikationen', FAU_PERSON_TEXTDOMAIN),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'pubs'
            )
        )
    );
    // Synchronisierung mit externen Daten - fau_person_sync ab hier

    return $meta_boxes;
}
