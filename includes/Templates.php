<?php

namespace FAU_Person;

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
        define('FAU_PERSON_ROOT', dirname(__FILE__));
	
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
                    $template_path = FAU_PERSON_ROOT . '/templates/single-person.php';                    
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
                    $template_path = FAU_PERSON_ROOT . '/templates/single-standort.php';                    
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
                    $template_path = FAU_PERSON_ROOT . '/templates/archive-person.php';                    
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
                    $template_path = FAU_PERSON_ROOT . '/templates/archive-standort.php';                    
                }
            //}
        }
        return $template_path;
    }      
}