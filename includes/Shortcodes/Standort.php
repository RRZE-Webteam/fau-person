<?php

namespace FAU_Person\Shortcodes;
use function FAU_Person\Config\getShortcodeSettings;
use FAU_Person\Data;
use UnivIS_Data;
use sync_helper;


defined('ABSPATH') || exit;

/**
 * Define Metaboxes for Kontakt-Edit
 */
class Standort extends Shortcodes {

    protected $pluginFile;
    private $settings = '';
    private $shortcodesettings = '';
    
    public function __construct($pluginFile, $settings) {
	$this->pluginFile = $pluginFile;
	$this->settings = $settings;	
	$this->shortcodesettings = getShortcodeSettings();
    }

      /**
     * Er wird ausgeführt, sobald die Klasse instanziiert wird.
     * @return void
     */
    public function onLoaded() {
	add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
	add_shortcode('standort', [$this, 'standortShortcode'], 10, 2);
    }

    /**
     * Enqueue der Skripte.
     */
    public function enqueueScripts()  {
        wp_register_style('fau-person-shortcode', plugins_url('css/fau-person.css', plugin_basename($this->pluginFile)));
    //    wp_register_script('fau-person-shortcode', plugins_url('js/fau-person.js', plugin_basename($this->pluginFile)));
    }


    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @param  string  $content Beiliegender Inhalt
     * @return string Gib den Inhalt zurück
     */
    public function standortShortcode( $atts )  {
        $content = '';
        $shortcode_atts = shortcode_atts([
            'display' => 'false'
        ], $atts);

        $display = $shortcode_atts['display'] == 'true' ? true : false;

        $output = '';

        if ($display) {
            $output = '<div class="fau-person" data-display="true">[shortcode display]</div>';
        } else {
            $output = '<span class="fau-person" data-display="false">[shortcode hidden]</span>';
        }

        wp_enqueue_style('fau-person-shortcode');
      //   wp_enqueue_script('fau-person-shortcode');

        return $output;
    }



    public function gutenberg_init() {
        // Skip block registration if Gutenberg is not enabled/merged.
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }
        $js = '../js/gutenberg.js';
        
        wp_register_script(
            'fau-person' . '-editor',
            plugins_url( $js, __FILE__ ),
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor'
            ),
            filemtime( dirname( __FILE__ ) . '/' . $js )
        );

        wp_localize_script( 'fau-person' . '-editor', 'phpConfig', $this->shortcodesettings );

        register_block_type( 'FAU_Person/Standort', array(
            'editor_script' => 'fau-person' . '-editor',
            'render_callback' => [$this, 'standortShortcode'],
            'attributes' => $this->shortcodesettings
            ) 
        );
    }    
    

}

