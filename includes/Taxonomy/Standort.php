<?php

namespace FAU_Person\Taxonomy;

defined('ABSPATH') || exit;

/**
 * Posttype STandort
 */
class Standort {

    protected $postType = 'standort';
    protected $pluginFile;
    protected $settings;
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }
    
    public function onLoaded() {
        add_action('init', [$this, 'set']);
        // add_action('admin_init', [$this, 'register']);
	
    }

    public function set() {
        $standort_labels = array(
            'name'		=> _x('Standorte', 'Post Type General Name', 'fau-person'),
            'singular_name'	=> _x('Standort', 'Post Type Singular Name', 'fau-person'),
            'menu_name'		=> __('Standort', 'fau-person'),
            'parent_item_colon'	=> __('Übergeordneter Standort', 'fau-person'),
            'all_items'		=> __('Alle Standorte', 'fau-person'),
            'view_item'		=> __('Standort ansehen', 'fau-person'),
            'add_new_item'	=> __('Standort hinzufügen', 'fau-person'),
            'add_new'		=> __('Neuer Standort', 'fau-person'),
            'edit_item'		=> __('Standort bearbeiten', 'fau-person'),
            'update_item'		=> __('Standort aktualisieren', 'fau-person'),
            'search_items'	=> __('Standorte suchen', 'fau-person'),
            'not_found'		=> __('Keine Standorte gefunden', 'fau-person'),
            'not_found_in_trash'	=> __('Keine Standorte in Papierkorb gefunden', 'fau-person'),
        );
        $standort_rewrite = array(
            'slug'	=> 'standort',
            'with_front'	=> true,
            'pages'	=> true,
            'feeds'	=> true,
        );
        $standort_args = array(
            'label'	    => __('standort', 'fau-person'),
            'description'	    => __('Standortinformationen', 'fau-person'),
            'labels'	    => $standort_labels,
            'supports'	    => array('title', 'editor', 'excerpt', 'thumbnail'),
            'hierarchical'	=> false,
            'public'	    => true,
            'show_ui'	    => true,
            'show_in_menu'	=> 'edit.php?post_type=person',
            'show_in_nav_menus'	=> false,
            'show_in_admin_bar'	=> true,
            'can_export'		=> true,
            'has_archive'		=> true,
            'exclude_from_search'	=> false,
            'publicly_queryable'	=> true,
            'query_var'		=> 'standort',
            'rewrite'		=> $standort_rewrite,
            'capability_type'	=> 'standort',
            'capabilities'	=> array(
                'edit_post'	    => 'edit_standort',
                'read_post'	    => 'read_standort',
                'delete_post'	    => 'delete_standort',
                'edit_posts'	    => 'edit_standorts',
                'edit_others_posts' => 'edit_others_standorts',
                'publish_posts'	=> 'publish_standorts',
                'read_private_posts'	=> 'read_private_standorts',
                'delete_posts'	=> 'delete_standorts',
                'delete_private_posts' => 'delete_private_standorts',
                'delete_published_posts' => 'delete_published_standorts',
                'delete_others_posts' => 'delete_others_standorts',
                'edit_private_posts' => 'edit_private_standorts',
                'edit_published_posts' => 'edit_published_standorts'
            ),
            'map_meta_cap'	=> true
        );


        register_post_type($this->postType, $standort_args);	
	
	
    }

    public function register() {

    }

}





    
    