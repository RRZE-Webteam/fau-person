<?php

namespace FAU_Person\Metaboxes;
use FAU_Person\Data;


defined('ABSPATH') || exit;

/**
 * Define Metaboxes for Kontakt-Edit
 */
class Posts extends Metaboxes {

    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;	
    }

    public function onLoaded()    {
	add_filter('cmb2_meta_boxes', array( $this, 'cmb2_posts_metaboxes') );
	
    }
   
    

    public function cmb2_posts_metaboxes( $meta_boxes ) {
	$prefix = $this->prefix;
	
	if (isset($this->settings->options) && isset($this->settings->options['constants_backend_view_metabox_kontaktlist']) && ($this->settings->options['constants_backend_view_metabox_kontaktlist'])) {
	    // Meta-Box zur Anzeige der verfügbaren Kontakte auf post und page, um die Personen-ID schneller herauszufinden
	    $contactselect = Data::get_contactdata();
	    $meta_boxes['fau_person_post_metabox'] = array(
		'id'		=> 'fau_person_post_metabox',
		'title'		=> __( 'Kontaktinformationen', 'fau-person' ),
		'object_types'    => array('post'), // post type
		'context'	=> 'side',
		'priority'	=> 'default',
		'show_names'	=> true, // Show field names on the left
		'fields'		=> array(
		    array(
			'name' => __('Verfügbare Kontakte anzeigen', 'fau-person'),
			'desc' => '<p id="fau_person_showhint">Shortcode:<br> <code id="copyshortcode"></code> <button class="button-link" type="button" aria-expanded="false" id="fau_person_cp_shortcode">'.__('Kopieren','fau-person').'</button></p>',
			'id' => $prefix . 'contactselect',
			'type' => 'select',
			'options' => $contactselect,
		    ),
		)        
	    );
	}
	return $meta_boxes;

    }
}