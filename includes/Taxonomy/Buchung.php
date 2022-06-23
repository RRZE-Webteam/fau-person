<?php

namespace FAU_Person\Taxonomy;
use FAU_Person\Data;
use FAU_Person\Schema;
use RRZE\Lib\UnivIS\Config;
use RRZE\Lib\UnivIS\Data as UnivIS_Data;
use function FAU_Person\Config\getConstants;

use function FAU_Person\Config\get_fau_person_capabilities;

defined('ABSPATH') || exit;

/**
 * Posttype Person
 */
class Buchung extends Taxonomy {

    protected $postType = 'buchung';
    
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
            'name'		        => _x('Buchungen', 'Post Type General Name', 'fau-person'),
            'singular_name'	    => _x('Buchung', 'Post Type Singular Name', 'fau-person'),
            'menu_name'		    => __('Buchungen', 'fau-person'),
            'all_items'		    => __('Alle Buchungen', 'fau-person'),
            'view_item'		    => __('Buchung ansehen', 'fau-person'),
            'add_new_item'	    => __('Buchung hinzufügen', 'fau-person'),
            'add_new'		    => __('Neue Buchung', 'fau-person'),
            'edit_item'		    => __('Buchung bearbeiten', 'fau-person'),
            'update_item'		=> __('Buchung aktualisieren', 'fau-person'),
            'search_items'	    => __('Buchungen suchen', 'fau-person'),
            'not_found'		    => __('Keine Buchungen gefunden', 'fau-person'),
            'not_found_in_trash'    => __('Keine Buchungen in Papierkorb gefunden', 'fau-person'),
        ];
        $has_archive_page  = true;
        if (isset($this->settings->options) && isset($this->settings->options['constants_has_archive_page'])) {
            $has_archive_page = $this->settings->options['constants_has_archive_page'];
        }
        $caps = get_fau_person_capabilities();
        $buchung_args = array(
            'label'		=> __('Buchung', 'fau-person'),
            'description'		=> __('Kontaktinformationen', 'fau-person'),
            'labels'		=> $labels,
            'supports'		=> array('author', 'revisions'),
            'hierarchical'	=> false,
            'public'		=> false,
            'show_ui'		=> true,
            'show_in_menu'	=> 'edit.php?post_type=person',
            'show_in_nav_menus'	=> false,
            'show_in_admin_bar'	=> true,
            'menu_position'	=> 20,
            'menu_icon'		=> 'dashicons-id-alt',
            'can_export'		=> true,
            'has_archive'		=> false,
            'exclude_from_search'	=> true,
            'publicly_queryable'	=> false,
            'rewrite'		=> [
                'slug'	    => $this->postType,
                'with_front' => true,
                'pages'	    => true,
                'feeds'	    => true,
            ],
            'capability_type' => $this->postType,
            'capabilities' => $caps,
            'map_meta_cap' => true
        );

        register_post_type($this->postType, $buchung_args);

    }

    public function register() {
        add_action( 'restrict_manage_posts', [ $this, 'buchung_restrict_manage_posts' ] );
        add_filter('parse_query', [$this, 'taxonomy_filter_post_type_request']);

        // Kontakttyp als zusätzliche Spalte in Übersicht
        add_filter( 'manage_buchung_posts_columns', array( $this, 'change_columns' ));
        add_action( 'manage_buchung_posts_custom_column', array( $this, 'custom_columns'), 10, 2 );

        // Sortierung der zusätzlichen Spalte
        //add_filter( 'manage_edit-buchung_sortable_columns', array( $this, 'sortable_columns' ));
        //add_action( 'pre_get_posts', array( $this, 'posttype_buchung_custom_columns_orderby') );

    }


    public function taxonomy_filter_post_type_request( $query ) {
        global $pagenow, $typenow;
        if ($typenow == 'buchung') {
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
    }
    public function buchung_restrict_manage_posts() {
        global $typenow;
        if ($typenow == 'buchung') {
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
            'name' => __( 'Name', 'fau-person' ),
            'date_booked' => __( 'Datum', 'fau-person' ),
            'time_booked'  => __( 'Uhrzeit', 'fau-person' ),
            'person' => __( 'Buchung bei', 'fau-person' ),
            'status' => __('Status', 'fau-person'),
            'date' => __( 'Gebucht am', 'fau-person' ),
        );

        return $cols;
    }

    public function custom_columns( $column, $post_id ) {
        $meta = get_post_meta($post_id);

        switch ( $column ) {
            case 'name':
                $name = (isset($meta['fau_person_booking_lastname'][0]) ? $meta['fau_person_booking_lastname'][0] . ', ' : '') . ($meta['fau_person_booking_firstname'][0] ?? '');
                echo $name;
                break;
            case 'date_booked':
                $date = isset($meta['fau_person_booking_start'][0]) ? wp_date(get_option('date_format'), $meta['fau_person_booking_start'][0]) : '';
                echo $date;
                break;
            case 'time_booked':
                $start = isset($meta['fau_person_booking_start'][0]) ? wp_date(get_option('time_format'), $meta['fau_person_booking_start'][0]) : '';
                $end = isset($meta['fau_person_booking_end'][0]) ? wp_date(get_option('time_format'), $meta['fau_person_booking_end'][0]) : '';
                echo $start . ' - ' . $end;
                break;
            case 'person':
                $contactID = (int)$meta['fau_person_booking_contact_id'][0];
                $univis_id = get_post_meta($contactID, 'fau_person_univis_id', true);
                $univisdata = Data::get_fields($contactID, $univis_id, 0, false, true);
                echo $univisdata['familyName'] . ', ' . $univisdata['givenName'];
                break;
            case 'status':
                $constants  = getConstants();
                $status = $meta['fau_person_booking_status'][0] ?? '';
                echo (isset($constants['booking-status'][$status]) ? $constants['booking-status'][$status] : __('Unbekannt', 'fau-person'));
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

    public function posttype_buchung_custom_columns_orderby( $query ) {
        if( ! is_admin() )
            return;

        $post_type = $query->query['post_type'];
        if ($post_type == 'buchung') {

            /*
           $admin_posts_per_page = 25;
           if (isset($this->settings->constants) && isset($this->settings->constants['admin_posts_per_page'])) {
           $admin_posts_per_page = $this->settings->constants['admin_posts_per_page'];
           }
           $orderby = $query->get( 'orderby' );

         //  $query->set( 'posts_per_page', $admin_posts_per_page );
             */


            if (!isset($query->query['orderby'])) {
                $query->set('orderby', 'title');
                $query->set('order', 'ASC');
                $orderby = 'title';
            } else {
                $orderby = $query->query['orderby'];
            }

            if( 'source' == $orderby ) {
                $query->set('orderby','meta_value');

                $meta_query = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'fau_person_univis_id',
                        'compare' => 'NOT EXISTS',
                        'value'   => 0,
                    ),
                    array(
                        'key' => 'fau_person_univis_id',
                        'compare' => 'EXISTS'
                    )
                );


                $query->set('meta_query',$meta_query);
            }
        }
    }
}