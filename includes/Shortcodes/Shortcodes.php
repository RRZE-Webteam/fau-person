<?php

namespace FAU_Person\Shortcodes;

defined('ABSPATH') || exit;

use FAU_Person\Main;
use FAU_Person\Shortcodes\Kontakt;
use FAU_Person\Shortcodes\Standort;


/**
 * Laden und definieren der Shortcodes
 */
class Shortcodes {
    protected $pluginFile;
    private $settings = '';
    
     public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }

    public function onLoaded() {
	$kontakt_shortcode = new Kontakt($this->pluginFile,  $this->settings);
	$kontakt_shortcode->onLoaded();

	$standort_shortcode = new Standort($this->pluginFile,  $this->settings);
	$standort_shortcode->onLoaded();
    }
}

