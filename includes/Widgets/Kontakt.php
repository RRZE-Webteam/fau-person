<?php

namespace FAU_Person\Widgets;

defined('ABSPATH') || exit;

/**
 * Define Image Sizes
 */
class Kontakt extends \WP_Widget
{
    protected $pluginFile;
    protected $settings;

    public function __construct($pluginFile, $settings)
    {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }


    public function onLoaded()
    {
        add_action('widgets_init', array($this, 'register_widgets'));

        // Unterstützung vom Shortcode im Widget   
        add_filter('widget_text', 'do_shortcode');
    }

    public static function register_widgets()
    {
        register_widget('\FAU_Person\Widgets\KontaktWidget');
    }
}
