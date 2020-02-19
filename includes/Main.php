<?php

namespace FAU_Person;

defined('ABSPATH') || exit;

use FAU_Person\Settings;
use FAU_Person\Data;
use FAU_Person\Taxonomy\Taxonomy;
use FAU_Person\Plugins\Plugins;
use FAU_Person\Images;
use FAU_Person\Templates;
use FAU_Person\Shortcodes\Shortcodes;
use FAU_Person\FAUPersonWidget;
use FAU_Person\Metaboxes\Metaboxes;
use FAU_Person\Helper;
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

    private $sidebar_options_page = null;

    */
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
    }

    /**
     * Es wird ausgeführt, sobald die Klasse instanziiert wird.
     */
    public function onLoaded() {
	
	// Settings-Klasse wird instanziiert.
        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();
	// $this->options = $settings->options;
	
	add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
	
	// Posttypes 
        $taxonomies = new Taxonomy($this->pluginFile, $settings);
        $taxonomies->onLoaded();

	// Posttypes 
        $plugins = new Plugins($this->pluginFile, $settings);
        $plugins->onLoaded();

	// Posttypes 
        $imagessizes = new Images($this->pluginFile, $settings);
        $imagessizes->onLoaded();
	
	
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
		return;	
		
	// Add Widget
        $widget = new FAUPersonWidget($this->pluginFile, $settings); 
        $widget->onLoaded();
			
    }
    
    /**
     * Register des Plugin Styles.
     */
    public function enqueueScripts()  {
        wp_register_style('fau-person', plugins_url('css/fau-person.css', plugin_basename($this->pluginFile)));

    //    wp_register_script('fau-person', plugins_url('js/fau-person.js', plugin_basename($this->pluginFile)));
    }

      
    public function enqueueForeignThemes() {
	$constants = getConstants();
	$themelist = $constants['fauthemes'];
	$foreign = 1;
	$active_theme = wp_get_theme();
	$active_theme = $active_theme->get( 'Name' );
	if (in_array($active_theme, $themelist)) {
	    $foreign = 0;
	}
	if ($foreign) {	
	    wp_enqueue_style('fau-person');
	}    
    }
}


