<?php

namespace FAU_Person\Metaboxes;

defined('ABSPATH') || exit;

/**
 * Define Metaboxes for Kontakt-Edit
 */
class Standort extends Metaboxes {

    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;	
    }

    public function onLoaded()    {
	add_filter('cmb2_meta_boxes', array( $this, 'cmb2_standort_metaboxes') );
	
    }
   
    

    public function cmb2_standort_metaboxes( $meta_boxes ) {
	$prefix = $this->prefix;

	
	// Meta-Box Standortinformation - fau_standort_info
	$meta_boxes['fau_standort_info'] = array(
	    'id' => 'fau_standort_info',
	    'title' => __( 'Standortinformationen', 'fau-person' ),
	    'object_types' => array('standort'), // post type
	    //'show_on' => array( 'key' => 'submenu-slug', 'value' => 'kontakt' ),        
	    'context' => 'normal',
	    'priority' => 'default',
	    'fields' => array(
		array(
		    'name' => __('Straße und Hausnummer', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'streetAddress',
		    'default'	=> 'Schloßplatz 4'
		),
		array(
		    'name' => __('Postleitzahl', 'fau-person'),
		    //'desc' => 'Wenn der Ort aus UnivIS übernommen werden soll bitte leer lassen!',
		    'desc' => __('Nur 5-stellige Zahlen erlaubt.', 'fau-person'),
		    'type' => 'text_small',
		    'id' => $prefix . 'postalCode',
		    'sanitization_cb' => 'validate_plz',
		    'default'	=> '91054'
		),
		array(
		    'name' => __('Ort', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'addressLocality',
		    'default'	=> 'Erlangen'
		),
		array(
		    'name' => __('Land', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'addressCountry',
		    'default'	=> 'Bayern'
		),
	    )
	);

	return $meta_boxes;
    }
}