<?php

namespace FAU_Person;

defined('ABSPATH') || exit;

use FAU_Person\Settings;
use FAU_Person\Taxonomy\Taxonomy;
use FAU_Person\Plugins\Plugins;
use FAU_Person\Templates\Templates;
use FAU_Person\Shortcodes\Shortcodes;
use FAU_Person\Widgets\Widgets;
use FAU_Person\Metaboxes\Metaboxes;
use function FAU_Person\Config\getConstants;
	
	

/**
 * Hauptklasse (Main)
 */

class Main {
    protected $pluginFile;
    private $settings = '';
      /*    
    public static $options;
    
    protected static $instance = null;
    */
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
	define( 'PLUGIN_FILE', $pluginFile);

    }

    /**
     * Es wird ausgeführt, sobald die Klasse instanziiert wird.
     */
    public function onLoaded() {
	 add_action('wp_enqueue_scripts', [$this, 'registerPluginStyles']);
	// Settings-Klasse wird instanziiert.
        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();
	$this->settings = $settings;
	// $this->options = $settings->options;

	$this->define_image_sizes();
	
	// Posttypes 
        $taxonomies = new Taxonomy($this->pluginFile, $settings);
        $taxonomies->onLoaded();

	// Posttypes 
        $plugins = new Plugins($this->pluginFile, $settings);
        $plugins->onLoaded();

	
	// Templates 
        $templates = new Templates($this->pluginFile, $settings);
        $templates->onLoaded();
		
	
	// Backend Setting pages
        $backend = new BackendMenu($this->pluginFile, $settings);
        $backend->onLoaded();
	

		// Add Metaboxes
        $metaboxes = new Metaboxes($this->pluginFile, $settings); 
        $metaboxes->onLoaded();	
	

		// Add Shortcodes
        $shortcodes = new Shortcodes($this->pluginFile, $settings); 
        $shortcodes->onLoaded();

		
	// Add Widget
        $widget = new Widgets($this->pluginFile, $settings); 
        $widget->onLoaded();
	
	
	return;			
    }
    
    
    
    
    public function registerPluginStyles() {
	 wp_register_style('fau-person', plugins_url('css/fau-person.css', plugin_basename($this->pluginFile)));
    }


      
    public static function enqueueForeignThemes() {
	/* $constants = getConstants();
	$themelist = $constants['fauthemes'];
	$foreign = 1;
	$active_theme = wp_get_theme();
	$active_theme = $active_theme->get( 'Name' );
	if (in_array($active_theme, $themelist)) {
	    $foreign = 0;
	}
	if ($foreign) {	*/
	    wp_enqueue_style('fau-person');
	/* } */   
    }
    
    public function define_image_sizes() {
	
	/* Thumb for person-type; small for sidebar - Name: person-thumb */
	add_image_size( 'person-thumb-v3', $this->settings->constants['images']['default_person_thumb_width' ], $this->settings->constants['images']['default_person_thumb_height'], $this->settings->constants['images']['default_person_thumb_crop'	]); // 60, 80, true
	
    
	 /* Thumb for person-type; big for content - Name: person-thumb-page */
	add_image_size( 'person-thumb-page-v3', $this->settings->constants['images']['default_person_thumb_page_width'], $this->settings->constants['images'][ 'default_person_thumb_page_height'], $this->settings->constants['images']['default_person_thumb_page_crop']); // 200,300,true

    }
	
}


