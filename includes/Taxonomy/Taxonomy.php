<?php

namespace FAU_Person\Taxonomy;

defined('ABSPATH') || exit;

use FAU_Person\Main;
use FAU_Person\Taxonomy\Kontakt;
use FAU_Person\Taxonomy\Standort;

/**
 * Laden und definieren der Posttypes
 */
class Taxonomy extends Main {
    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }

    public function onLoaded() {
        $kontakt_posttype = new Kontakt( $this->pluginFile,  $this->settings);
        $kontakt_posttype->onLoaded();

        $standort_posttype = new Standort( $this->pluginFile,  $this->settings);
        $standort_posttype->onLoaded();

        // if( get_transient('fau-person-options') ) {
        //     flush_rewrite_rules();
        //     delete_transient('fau-person-options');
	    // }
    }
}
