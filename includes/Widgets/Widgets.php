<?php

namespace FAU_Person\Widgets;

defined('ABSPATH') || exit;

use FAU_Person\Main;
use FAU_Person\Widgets\KontaktWidget;

/**
 * Laden und definieren der Posttypes
 */
class Widgets extends Main {
    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }

    public function onLoaded() {
       $kontakt2_widget = new Kontakt( $this->pluginFile,  $this->settings);
       $kontakt2_widget->onLoaded();


    }
}
