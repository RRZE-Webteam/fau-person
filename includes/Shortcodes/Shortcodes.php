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
        add_action( 'admin_enqueue_scripts', [$this, 'enqueueGutenberg'] );
    }

    public function onLoaded() {
    	$kontakt_shortcode = new Kontakt($this->pluginFile,  $this->settings);
    	$kontakt_shortcode->onLoaded();

    	$standort_shortcode = new Standort($this->pluginFile,  $this->settings);
    	$standort_shortcode->onLoaded();
    }


    public function isGutenberg(){
        if ( ! function_exists( 'register_block_type' ) ) {
            return false;        
        }

        // check if RRZE-Settings if classic editor is enabled
        $rrze_settings = (array) get_option( 'rrze_settings' );
        if ( isset( $rrze_settings['writing'] ) ) {
            $rrze_settings = (array) $rrze_settings['writing'];
            if ( isset( $rrze_settings['enable_classic_editor'] ) && $rrze_settings['enable_classic_editor'] ) {
                return false;        
            }
        }

        return true;        
    }

    public function enqueueGutenberg(){
        if ( ! $this->isGutenberg() ) {
            return;        
        }

        // include gutenberg lib
        wp_enqueue_script(
            'RRZE-Gutenberg',
            plugins_url( '../../js/gutenberg.js', __FILE__ ),
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor'
            ),
            NULL
        );
    }
}

