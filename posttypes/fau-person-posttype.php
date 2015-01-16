<?php

function persons_taxonomy() {
	register_taxonomy(
		'persons_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'person',   		 //post type name
		array(
			'hierarchical' 		=> true,
			'label' 		=> __('Personen-Kategorien', FAU_PERSON_TEXTDOMAIN),  //Display name
			'query_var' 		=> true,
			'rewrite'		=> array(
					'slug' 			=> 'persons', // This controls the base slug that will display before each term
					'with_front' 	=> false // Don't display the category base before
					)
			)
		);
}
add_action( 'init', 'persons_taxonomy');


// Register Custom Post Type
function person_post_type() {	
	
	$labels = array(
		'name'                => _x( 'Personen', 'Post Type General Name', FAU_PERSON_TEXTDOMAIN ),
		'singular_name'       => _x( 'Person', 'Post Type Singular Name', FAU_PERSON_TEXTDOMAIN ),
		'menu_name'           => __( 'Personen', FAU_PERSON_TEXTDOMAIN ),
		'parent_item_colon'   => __( 'Übergeordnete Person', FAU_PERSON_TEXTDOMAIN ),
		'all_items'           => __( 'Alle Personen', FAU_PERSON_TEXTDOMAIN ),
		'view_item'           => __( 'Person ansehen', FAU_PERSON_TEXTDOMAIN ),
		'add_new_item'        => __( 'Person hinzufügen', FAU_PERSON_TEXTDOMAIN ),
		'add_new'             => __( 'Neue Person', FAU_PERSON_TEXTDOMAIN ),
		'edit_item'           => __( 'Person bearbeiten', FAU_PERSON_TEXTDOMAIN ),
		'update_item'         => __( 'Person aktualisieren', FAU_PERSON_TEXTDOMAIN ),
		'search_items'        => __( 'Personen suchen', FAU_PERSON_TEXTDOMAIN ),
		'not_found'           => __( 'Keine Personen gefunden', FAU_PERSON_TEXTDOMAIN ),
		'not_found_in_trash'  => __( 'Keine Personen in Papierkorb gefunden', FAU_PERSON_TEXTDOMAIN ),
	);
	$rewrite = array(
		'slug'                => 'person',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'person', FAU_PERSON_TEXTDOMAIN ),
		'description'         => __( 'Personeninformationen', FAU_PERSON_TEXTDOMAIN ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'thumbnail' ),
		'taxonomies'          => array( 'persons_category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'query_var'           => 'person',
		'rewrite'             => $rewrite,
		'capability_type'     => 'person',
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
	register_post_type( 'person', $args );

}

// Hook into the 'init' action
add_action( 'init', 'person_post_type', 0 );


function person_restrict_manage_posts() {
	global $typenow;

	if( $typenow == "person" ){
		$filters = get_object_taxonomies($typenow);
		
		foreach ($filters as $tax_slug) {
			$tax_obj = get_taxonomy($tax_slug);
			wp_dropdown_categories(array(
                'show_option_all' => sprintf(__('Alle %s anzeigen', FAU_PERSON_TEXTDOMAIN), $tax_obj->label),
                'taxonomy' => $tax_slug,
                'name' => $tax_obj->name,
                'orderby' => 'name',
                'selected' => isset($_GET[$tax_slug]) ? $_GET[$tax_slug] : '',
                'hierarchical' => $tax_obj->hierarchical,
                'show_count' => true,
                'hide_if_empty' => true
            ));
		}

	}
}
add_action( 'restrict_manage_posts', 'person_restrict_manage_posts' );



function person_post_types_admin_order( $wp_query ) {
	if (is_admin()) {

		$post_type = $wp_query->query['post_type'];

		if ( $post_type == 'person') {

			if( ! isset($wp_query->query['orderby']))
			{
				$wp_query->set('orderby', 'title');
				$wp_query->set('order', 'ASC');
			}

		}
	}
}
add_filter('pre_get_posts', 'person_post_types_admin_order');



