<?php

$person_labels = array(
    'name' => _x('Kontakte', 'Post Type General Name', self::textdomain),
    'singular_name' => _x('Kontakt', 'Post Type Singular Name', self::textdomain),
    'menu_name' => __('Kontakte', self::textdomain),
    'parent_item_colon' => __('Übergeordneter Kontakt', self::textdomain),
    'all_items' => __('Alle Kontakte', self::textdomain),
    'view_item' => __('Kontakt ansehen', self::textdomain),
    'add_new_item' => __('Kontakt hinzufügen', self::textdomain),
    'add_new' => __('Neuer Kontakt', self::textdomain),
    'edit_item' => __('Kontakt bearbeiten', self::textdomain),
    'update_item' => __('Kontakt aktualisieren', self::textdomain),
    'search_items' => __('Kontakte suchen', self::textdomain),
    'not_found' => __('Keine Kontakte gefunden', self::textdomain),
    'not_found_in_trash' => __('Keine Kontakte in Papierkorb gefunden', self::textdomain),
);
$person_rewrite = array(
    'slug' => 'person',
    'with_front' => true,
    'pages' => true,
    'feeds' => true,
);
$person_args = array(
    'label' => __('person', self::textdomain),
    'description' => __('Kontaktinformationen', self::textdomain),
    'labels' => $person_labels,
    'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
    'taxonomies' => array('persons_category'),
    'hierarchical' => false,
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_nav_menus' => true,
    'show_in_admin_bar' => true,
    'menu_position' => 20,
    'menu_icon' => 'dashicons-id-alt',
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




