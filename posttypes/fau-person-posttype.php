<?php

$person_labels = array(
    'name' => _x('Personen', 'Post Type General Name', FAU_PERSON_TEXTDOMAIN),
    'singular_name' => _x('Person', 'Post Type Singular Name', FAU_PERSON_TEXTDOMAIN),
    'menu_name' => __('Personen', FAU_PERSON_TEXTDOMAIN),
    'parent_item_colon' => __('Übergeordnete Person', FAU_PERSON_TEXTDOMAIN),
    'all_items' => __('Alle Personen', FAU_PERSON_TEXTDOMAIN),
    'view_item' => __('Person ansehen', FAU_PERSON_TEXTDOMAIN),
    'add_new_item' => __('Person hinzufügen', FAU_PERSON_TEXTDOMAIN),
    'add_new' => __('Neue Person', FAU_PERSON_TEXTDOMAIN),
    'edit_item' => __('Person bearbeiten', FAU_PERSON_TEXTDOMAIN),
    'update_item' => __('Person aktualisieren', FAU_PERSON_TEXTDOMAIN),
    'search_items' => __('Personen suchen', FAU_PERSON_TEXTDOMAIN),
    'not_found' => __('Keine Personen gefunden', FAU_PERSON_TEXTDOMAIN),
    'not_found_in_trash' => __('Keine Personen in Papierkorb gefunden', FAU_PERSON_TEXTDOMAIN),
);
$person_rewrite = array(
    'slug' => 'person',
    'with_front' => true,
    'pages' => true,
    'feeds' => true,
);
$person_args = array(
    'label' => __('person', FAU_PERSON_TEXTDOMAIN),
    'description' => __('Personeninformationen', FAU_PERSON_TEXTDOMAIN),
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




