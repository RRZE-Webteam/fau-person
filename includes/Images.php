<?php

namespace FAU_Person;

defined('ABSPATH') || exit;

/**
 * Define Image Sizes
 */
class Images extends Main {

    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }

    public function onLoaded()    {
	/* Thumb for person-type; small for sidebar - Name: person-thumb */
	add_image_size( 'person-thumb', $this->settings->constants['images']['default_person_thumb_width' ], $this->settings->constants['images']['default_person_thumb_height'], $this->settings->constants['images']['default_person_thumb_crop'	]); // 60, 80, true
	
        /* Thumb for person-type; small for content - Name: person-thumb-bigger */
	add_image_size( 'person-thumb-bigger', $this->settings->constants['images']['default_person_thumb_bigger_width'], $this->settings->constants['images'][ 'default_person_thumb_bigger_height'], $this->settings->constants['images']['default_person_thumb_bigger_crop']); // 90,120,true

	 /* Thumb for person-type; big for content - Name: person-thumb-page */
	add_image_size( 'person-thumb-page', $this->settings->constants['images']['default_person_thumb_page_width'], $this->settings->constants['images'][ 'default_person_thumb_page_height'], $this->settings->constants['images']['default_person_thumb_page_crop']); // 200,300,true

    }

}