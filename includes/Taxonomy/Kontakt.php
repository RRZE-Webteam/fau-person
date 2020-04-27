<?php

namespace FAU_Person\Taxonomy;
use FAU_Person\Data;
use FAU_Person\Schema;
use RRZE\Lib\UnivIS\Config;

defined('ABSPATH') || exit;

/**
 * Posttype Person
 */
class Kontakt extends Taxonomy {

    protected $postType = 'person';
    protected $taxonomy = 'persons_category';

    protected $pluginFile;
    private $settings = '';
    
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }
    public function onLoaded() {
	add_action('init', [$this, 'set']);
	add_action('admin_init', [$this, 'register']);
	
    }

    public function set() {
	 $labels = [
	    'name'		=> _x('Kontakte', 'Post Type General Name', 'fau-person'),
	    'singular_name'	=> _x('Kontakt', 'Post Type Singular Name', 'fau-person'),
	    'menu_name'		=> __('Kontakte', 'fau-person'),
	    'parent_item_colon'	=> __('Übergeordneter Kontakt', 'fau-person'),
	    'all_items'		=> __('Alle Kontakte', 'fau-person'),
	    'view_item'		=> __('Kontakt ansehen', 'fau-person'),
	    'add_new_item'	=> __('Kontakt hinzufügen', 'fau-person'),
	    'add_new'		=> __('Neuer Kontakt', 'fau-person'),
	    'edit_item'		=> __('Kontakt bearbeiten', 'fau-person'),
	    'update_item'		=> __('Kontakt aktualisieren', 'fau-person'),
	    'search_items'	=> __('Kontakte suchen', 'fau-person'),
	    'not_found'		=> __('Keine Kontakte gefunden', 'fau-person'),
	    'not_found_in_trash'    => __('Keine Kontakte in Papierkorb gefunden', 'fau-person'),
        ];
	
	$has_archive_page = $this->settings->getOption('constants', 'has_archive_page');
	$person_args = array(
	    'label'		=> __('Kontakt', 'fau-person'),
	    'description'		=> __('Kontaktinformationen', 'fau-person'),
	    'labels'		=> $labels,
	    'supports'		=> array('title', 'editor', 'thumbnail', 'revisions'),
	    'taxonomies'		=> array('persons_category'),
	    'hierarchical'	=> false,
	    'public'		=> true,
	    'show_ui'		=> true,
	    'show_in_menu'	=> true,
	    'show_in_nav_menus'	=> true,
	    'show_in_admin_bar'	=> true,
	    'menu_position'	=> 20,
	    'menu_icon'		=> 'dashicons-id-alt',
	    'can_export'		=> true,
	    'has_archive'		=> $has_archive_page,
	    'exclude_from_search'	=> false,
	    'publicly_queryable'	=> true,
	    'query_var'		=> 'person',
	    'rewrite'		=> [
		'slug'	    => $this->postType,
		'with_front' => true,
		'pages'	    => true,
		'feeds'	    => true,
	    ],
	    'capability_type' => $this->postType,
	    'capabilities' => [
		'edit_post'	=> 'edit_person',
		'read_post'	=> 'read_person',
		'delete_post'	=> 'delete_person',
		'edit_posts'	=> 'edit_persons',
		'edit_others_posts' => 'edit_others_persons',
		'publish_posts'	=> 'publish_persons',
		'read_private_posts' => 'read_private_persons',
		'delete_posts'	=> 'delete_persons',
		'delete_private_posts' => 'delete_private_persons',
		'delete_published_posts' => 'delete_published_persons',
		'delete_others_posts' => 'delete_others_persons',
		'edit_private_posts' => 'edit_private_persons',
		'edit_published_posts' => 'edit_published_persons'
	    ],
	    'map_meta_cap' => true
	);
	
	
       

	
	register_post_type($this->postType, $person_args);	
	
	
	
	
         register_taxonomy(
            $this->taxonomy,
            $this->postType,
            [
		'hierarchical'	=> true,
	//	'labels'		=> $labels,
		'show_ui'	=> true,
		'show_admin_column' => true,
		'query_var'	=> true,
		'rewrite'	=> [
			'slug'	    => $this->taxonomy, 
			'with_front' => false
		],
            ]
        );
    }

    public function register() {
        register_taxonomy_for_object_type($this->taxonomy, $this->postType);
        add_action( 'restrict_manage_posts', [ $this, 'person_restrict_manage_posts' ] );
        add_filter('parse_query', [$this, 'taxonomy_filter_post_type_request']);
	        // Kontakttyp als zusätzliche Spalte in Übersicht
	
        add_filter( 'manage_person_posts_columns', array( $this, 'change_columns' ));
        add_action( 'manage_person_posts_custom_column', array( $this, 'custom_columns'), 10, 2 ); 
        // Sortierung der zusätzlichen Spalte
	
	
	
        add_filter( 'manage_edit-person_sortable_columns', array( $this, 'sortable_columns' ));
        add_action( 'pre_get_posts', array( $this, 'custom_columns_orderby') );
	
	
	
    }

    
    
    public function taxonomy_filter_post_type_request( $query ) {
	global $pagenow, $typenow;
        if ( 'edit.php' == $pagenow ) {
            $filters = get_object_taxonomies( $typenow );

            foreach ( $filters as $tax_slug ) {
                $var = &$query->query_vars[$tax_slug];
                if ( isset( $var ) ) {
                    $term = get_term_by( 'id', $var, $tax_slug );
                    if ( !empty( $term ) )      $var = $term->slug;
                }
            }
        }
    }
    public function person_restrict_manage_posts() {
        global $typenow;	
	if ($typenow == 'person') {
	    $typenow = $this->postType;
	    $filters = get_object_taxonomies($typenow);
	    foreach ($filters as $tax_slug) {
		$tax_obj = get_taxonomy($tax_slug);
		wp_dropdown_categories(array(
		    'show_option_all' => sprintf(__('Alle %s anzeigen', 'fau-person'), $tax_obj->label),
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
        
    // Change the columns for the edit CPT screen
    public function change_columns( $cols ) {
	$cols = array(
	    'cb' => '<input type="checkbox" />',
	    'title' => __( 'Titel', 'fau-person' ),
	    'thumb' => __( 'Bild', 'fau-person' ),
	    'fullname'  => __( 'Angezeigter Name', 'fau-person' ),
	    'contact' => __( 'Kontakt', 'fau-person' ),
	    'source'	=> __( 'Datenquelle', 'fau-person' ),
            'date' => __( 'Datum', 'fau-person' ),
	);

	return $cols;
    }

    public function custom_columns( $column, $post_id ) {
	$univisid = get_post_meta($post_id, 'fau_person_univis_id', true);
	$data = Data::get_fields($post_id, $univisid, 0);
	$univisconfig = Config::get_Config();
	$api_url = $univisconfig['api_url'];
	
	switch ( $column ) {
	    case 'thumb':
		 $thumb = Data::create_kontakt_image($post_id, 'person-thumb-v3', '', true, false,'',false);
		echo $thumb;
		break;

	    case 'fullname':
		
		$fullname = Schema::create_Name( $data, '', '', 'span', false );		
		if (empty(trim($fullname))) {
		    $fullname = get_the_title($post_id);
		}
		echo $fullname;
		break;
	    case 'contact':
		// echo $data['email'];
		echo Schema::create_contactpointlist($data,  'ul', '',  '', 'li');
		
		break;
	    case 'source':
		if ($univisid) {
		    echo __('UnivIS', 'fau-person').' (Id: <a target="univis" href="'.$api_url.'?search=persons&id='.$univisid.'&show=info">'.$univisid.'</a>)';
		} else {
		    echo __('Lokal', 'fau-person');
		}
		break;
		
	}
    }
    
    // Make these columns sortable
    public function sortable_columns( $columns ) {
	$columns = array(
	    'title' => 'title',
	    'source' => 'source',
	    'date' => 'date',
	);
        return $columns;
    }	
    
    public function custom_columns_orderby( $query ) {
        if( ! is_admin() )
            return;
 
        $orderby = $query->get( 'orderby' );
	

	if( 'source' == $orderby ) {
	    $query->set('meta_key','fau_person_univis_id');
             $query->set('orderby','meta_value');
	    
	}
    }
  
    
    
    public function person_post_types_admin_order($wp_query) {
        if (is_admin()) {
            $post_type = $wp_query->query['post_type'];
            if ($post_type == 'person') {
                if (!isset($wp_query->query['orderby'])) {
                    $wp_query->set('orderby', 'title');
                    $wp_query->set('order', 'ASC');
                }
            }
        }
    }    
}





    
    