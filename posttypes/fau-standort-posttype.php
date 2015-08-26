<?php

$standort_labels = array(
    'name' => _x('Standorte', 'Post Type General Name', self::textdomain),
    'singular_name' => _x('Standort', 'Post Type Singular Name', self::textdomain),
    'menu_name' => __('Standort', self::textdomain),
    'parent_item_colon' => __('Übergeordneter Standort', self::textdomain),
    'all_items' => __('Alle Standorte', self::textdomain),
    'view_item' => __('Standort ansehen', self::textdomain),
    'add_new_item' => __('Standort hinzufügen', self::textdomain),
    'add_new' => __('Neuer Standort', self::textdomain),
    'edit_item' => __('Standort bearbeiten', self::textdomain),
    'update_item' => __('Standort aktualisieren', self::textdomain),
    'search_items' => __('Standorte suchen', self::textdomain),
    'not_found' => __('Keine Standorte gefunden', self::textdomain),
    'not_found_in_trash' => __('Keine Standorte in Papierkorb gefunden', self::textdomain),
);
$standort_rewrite = array(
    'slug' => 'standort',
    'with_front' => true,
    'pages' => true,
    'feeds' => true,
);
$standort_args = array(
    'label' => __('standort', self::textdomain),
    'description' => __('Standortinformationen', self::textdomain),
    'labels' => $standort_labels,
    'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
    //'taxonomies' => array('persons_category'),
    'hierarchical' => false,
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => 'edit.php?post_type=person',
    'show_in_nav_menus' => false,
    'show_in_admin_bar' => true,
    //'menu_position' => 20,
    //'menu_icon' => 'dashicons-id-alt',
    'can_export' => true,
    'has_archive' => true,
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'query_var' => 'person',
    'rewrite' => $standort_rewrite,
    'capability_type' => 'standort',
    'capabilities' => array(
        'edit_post' => 'edit_standort',
        'read_post' => 'read_standort',
        'delete_post' => 'delete_standort',
        'edit_posts' => 'edit_standorts',
        'edit_others_posts' => 'edit_others_standorts',
        'publish_posts' => 'publish_standorts',
        'read_private_posts' => 'read_private_standorts',
        'delete_posts' => 'delete_standorts',
        'delete_private_posts' => 'delete_private_standorts',
        'delete_published_posts' => 'delete_published_standorts',
        'delete_others_posts' => 'delete_others_standorts',
        'edit_private_posts' => 'edit_private_standorts',
        'edit_published_posts' => 'edit_published_standorts'
    ),
    'map_meta_cap' => true
);




