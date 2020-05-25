<?php

namespace FAU_Person\Widgets;
defined('ABSPATH') || exit;

use WP_Widget;
/**
 * Define Image Sizes
 */
class Kontakt extends \WP_Widget {
    protected $pluginFile;
    private $settings;

    public function __construct($pluginFile, $settings) {
	$this->pluginFile = $pluginFile;
        $this->settings = $settings;
	

    }


    public function onLoaded()    {
	add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	       
	  // Unterstützung vom Shortcode im Widget   
	add_filter('widget_text','do_shortcode');
    }
    
    public static function register_widgets() {
	require_once('kontakt-widget.php');
	register_widget( 'FAUPersonWidget' );
    }

}
