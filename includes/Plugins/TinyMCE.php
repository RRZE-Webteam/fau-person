<?php

namespace FAU_Person\Plugins;

defined('ABSPATH') || exit;

use FAU_Person\Helper;

/**
 * TinyMCE
 * @link https://de.wordpress.org/plugins/tinymce-advanced/
 */
class TinyMCE {
    protected $pluginFile;
    private $settings = '';
    
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }

    public function onLoaded()    {
        if (! $this->isPluginAvailable('tinymce-advanced/tinymce-advanced.php')) {
            return;
        }

	add_action( 'admin_init', array( $this, 'person_shortcodes_rte_button' ) );    

    }

    /**
     * [isPluginAvailable description]
     * @param  string  $plugin [description]
     * @return boolean         [description]
     */
    protected function isPluginAvailable($plugin) {
        return Helper::isPluginAvailable($plugin);
    }
    
     public function person_shortcodes_rte_button() {
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {
            add_filter( 'mce_external_plugins', array($this, 'person_rte_add_buttons' ));
        }
    }

    public function person_rte_add_buttons( $plugin_array ) {
        $plugin_array['personrteshortcodes'] = plugin_dir_url(__FILE__) . 'js/tinymce-shortcodes.js';
        return $plugin_array;
    }
    
}
