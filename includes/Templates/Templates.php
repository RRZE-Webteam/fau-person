<?php


namespace FAU_Person\Templates;
use FAU_Person\Helper;

defined('ABSPATH') || exit;

/**
 * Define Templates
 */
class Templates {

    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }


    public function onLoaded()    {	
	add_filter( 'single_template', array( $this, 'include_single_template' ) );     
	add_filter( 'archive_template', array( $this, 'include_archive_template' ) );         
    }

    
    public function include_single_template($template_path) {
        global $post;
        if ($post->post_type == 'person') {
            //if (is_single()) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ($theme_file = locate_template(array('single-person.php'))) {
                    $template_path = $theme_file;
                } else {
		    if (Helper::isFAUTheme()) {
			$template_path = dirname($this->pluginFile) . '/includes/Templates/single-person-fau-theme.php';      
		    } else {
			$template_path = dirname($this->pluginFile) . '/includes/Templates/single-person.php';         
		    }
                }
            //}
        }
        if ($post->post_type == 'standort') {
            //if (is_single()) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ($theme_file = locate_template(array('single-standort.php'))) {
                    $template_path = $theme_file;
                } else {
		    if (Helper::isFAUTheme()) {
			 $template_path = dirname($this->pluginFile) . '/includes/Templates/single-standort-fau-theme.php';      
		    } else {
			 $template_path = dirname($this->pluginFile) . '/includes/Templates/single-standort.php';      
		    }            
                }
            //}
        }
        return $template_path;
    }    
    
    public function include_archive_template($template_path) {
        global $post;
        if ($post->post_type == 'person') {
            //if (is_single()) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ($theme_file = locate_template(array('archive-person.php'))) {
                    $template_path = $theme_file;
                } else {
		    
		    if (Helper::isFAUTheme()) {
			 $template_path = dirname($this->pluginFile) . '/includes/Templates/archive-person-fau-theme.php';      
		    } else {
			 $template_path = dirname($this->pluginFile) . '/includes/Templates/archive-person.php';      
		    }  
		    
                   
                }
            //}
        }
        if ($post->post_type == 'standort') {
            //if (is_single()) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ($theme_file = locate_template(array('archive-standort.php'))) {
                    $template_path = $theme_file;
                } else {
                   if (Helper::isFAUTheme()) {
			 $template_path = dirname($this->pluginFile) . '/includes/Templates/archive-standort-fau-theme.php';      
		    } else {
			 $template_path = dirname($this->pluginFile) . '/includes/Templates/archive-standort.php';      
		    }
                }
            //}
        }
        return $template_path;
    }      
}