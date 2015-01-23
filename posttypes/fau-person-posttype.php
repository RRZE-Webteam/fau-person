<?php

$person_labels = array(
    'name' => _x('Personen', 'Post Type General Name', self::textdomain),
    'singular_name' => _x('Person', 'Post Type Singular Name', self::textdomain),
    'menu_name' => __('Personen', self::textdomain),
    'parent_item_colon' => __('Übergeordnete Person', self::textdomain),
    'all_items' => __('Alle Personen', self::textdomain),
    'view_item' => __('Person ansehen', self::textdomain),
    'add_new_item' => __('Person hinzufügen', self::textdomain),
    'add_new' => __('Neue Person', self::textdomain),
    'edit_item' => __('Person bearbeiten', self::textdomain),
    'update_item' => __('Person aktualisieren', self::textdomain),
    'search_items' => __('Personen suchen', self::textdomain),
    'not_found' => __('Keine Personen gefunden', self::textdomain),
    'not_found_in_trash' => __('Keine Personen in Papierkorb gefunden', self::textdomain),
);
$person_rewrite = array(
    'slug' => 'person',
    'with_front' => true,
    'pages' => true,
    'feeds' => true,
);
$person_args = array(
    'label' => __('person', self::textdomain),
    'description' => __('Personeninformationen', self::textdomain),
    'labels' => $person_labels,
    'supports' => array('title', 'thumbnail'),
    'taxonomies' => array('persons_category'),
    'hierarchical' => false,
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_nav_menus' => false,
    'show_in_admin_bar' => true,
    'menu_position' => 5,
    'menu_icon' => 'dashicons-businessman',
    'can_export' => true,
    'has_archive' => true,
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'query_var' => 'person',
    'rewrite' => $person_rewrite,
    'capability_type' => 'person',
    'capabilities' => array(
        'edit_post' => 'edit_person',
        'read_post' => 'read_person',
        'delete_post' => 'delete_person',
        'edit_posts' => 'edit_persons',
        'edit_others_posts' => 'edit_others_persons',
        'publish_posts' => 'publish_persons',
        'read_private_posts' => 'read_private_persons',
        'delete_posts' => 'delete_persons',
        'delete_private_posts' => 'delete_private_persons',
        'delete_published_posts' => 'delete_published_persons',
        'delete_others_posts' => 'delete_others_persons',
        'edit_private_posts' => 'edit_private_persons',
        'edit_published_posts' => 'edit_published_persons'
    ),
    'map_meta_cap' => true
);

/* möglich bei type: text, textarea, checkbox, select, image, title, headline (für Zwischenüberschriften) */
$person_fields = array(
    // Typ des Eintrags - fau_person_typ
    '_person_typ' => array(
        'default' => 'false',
        'title' => __('Typ des Eintrags', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_typ',
        'location' => 'person'),
    // Zuordnung - fau_person_orga
    '_person_position' => array(
        'default' => 'false',
        'title' => __('Position/Funktion', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_orga',
        'location' => 'person'),
    '_person_institution' => array(
        'default' => 'false',
        'title' => __('Institution/Abteilung', self::textdomain),
        'description' => 'Geben Sie hier die Institution ein.',
        'type' => 'text',
        'meta_box' => 'fau_person_orga',
        'location' => 'person'),
    // Kontaktinformation - fau_person_info
    '_person_abschluss' => array(
        'default' => 'false',
        'title' => __('Abschluss (Suffix)', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_info',
        'location' => 'person'),
    '_person_vorname' => array(
        'default' => 'false',
        'title' => __('Vorname', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_info',
        'location' => 'person'),
    '_person_nachname' => array(
        'default' => 'false',
        'title' => __('Nachname', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_info',
        'location' => 'person'),
    '_person_pseudo' => array(
        'default' => 'false',
        'title' => __('Bezeichnung (oder Pseudonym)', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_info',
        'location' => 'person'),
    '_person_titel' => array(
        'default' => 'false',
        'title' => __('Titel (Präfix)', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_info',
        'location' => 'person'),
    '_person_telefon' => array(
        'default' => 'false',
        'title' => __('Telefon', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_info',
        'location' => 'person'),
    '_person_telefax' => array(
        'default' => 'false',
        'title' => __('Telefax', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_info',
        'location' => 'person'),
    '_person_adresse' => array(
        'default' => 'false',
        'title' => __('Adresse', self::textdomain),
        'description' => '',
        'type' => 'textarea',
        'meta_box' => 'fau_person_info',
        'location' => 'person'),
    '_person_raum' => array(
        'default' => 'false',
        'title' => __('Raum', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_info',
        'location' => 'person'),
    // Social Media - fau_person_social_media
    '_person_email' => array(
        'default' => 'false',
        'title' => __('E-Mail', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_social_media',
        'location' => 'person'),
    '_person_url' => array(
        'default' => 'false',
        'title' => __('Webseite', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_social_media',
        'location' => 'person'),
    // Weitere Informationen - fau_person_adds
    '_person_freitext' => array(
        'default' => 'false',
        'title' => __('Freitext', self::textdomain),
        'description' => '',
        'type' => 'textarea',
        'meta_box' => 'fau_person_adds',
        'location' => 'person'),
    '_person_sprechzeiten' => array(
        'default' => 'false',
        'title' => __('Sprechzeiten', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_adds',
        'location' => 'person'),
    '_person_pubs' => array(
        'default' => 'false',
        'title' => __('Publikationen', self::textdomain),
        'description' => '',
        'type' => 'text',
        'meta_box' => 'fau_person_adds',
        'location' => 'person')
    // Synchronisierung mit externen Daten - fau_person_sync ab hier

);



