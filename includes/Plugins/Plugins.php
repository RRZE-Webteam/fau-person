<?php

namespace FAU_Person\Plugins;

defined('ABSPATH') || exit;

use FAU_Person\Main;
use FAU_Person\Plugins\TinyMCE;


/**
 * [Plugins description]
 */
class Plugins extends Main {
     protected $pluginFile;
     private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }

    public function onLoaded()     {
        $tinymce = new TinyMCE($this->pluginFile,  $this->settings);
        $tinymce->onLoaded();
    }
}
