<?php

namespace FAU_Person;

defined('ABSPATH') || exit;

use FAU_Person\Settings;
use FAU_Person\Taxonomy\Taxonomy;
use FAU_Person\Plugins\Plugins;
use FAU_Person\Templates\Templates;
use FAU_Person\Shortcodes\Shortcodes;
use FAU_Person\Shortcodes\Cache;
use FAU_Person\Widgets\Widgets;
use FAU_Person\Metaboxes\Metaboxes;

/**
 * Hauptklasse (Main)
 */

class Main {
    protected $pluginFile;
    private $settings = '';
    private static $enqueuePluginStyles = false;

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
    }

    /**
     * Es wird ausgeführt, sobald die Klasse instanziiert wird.
     */
    public function onLoaded()  {
        add_action('wp', [$this, 'detectFrontendShortcodes']);
        add_action('wp_enqueue_scripts', [$this, 'registerPluginStyles']);
        add_action('wp_footer', [$this, 'printLatePluginStyles'], 1);
        // Settings-Klasse wird instanziiert.
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);

        // Run Async Task
        add_action('fau_person_data_async_task', ['\RRZE\Lib\UnivIS\Data', 'async_task']);
        add_action('save_post_person', function ($postId) {
            $univisId = get_post_meta($postId, 'fau_person_univis_id', true);
            if (!empty($univisId) && !wp_next_scheduled('fau_person_data_async_task', [$univisId])) {
                wp_schedule_single_event(time(), 'fau_person_data_async_task', [$univisId]);
            }
        });

        // Flush Shortcodes Cache
        add_action('fau_person_shortcodes_flush_cache', ['\FAU_Person\Shortcodes\Cache', 'flush']);
        add_action('save_post_person', function () {
            if (!wp_next_scheduled('fau_person_shortcodes_flush_cache')) {
                wp_schedule_single_event(time(), 'fau_person_shortcodes_flush_cache');
            }
        });

        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();
        $this->settings = $settings;

        // $this->options = $settings->options;

        $this->define_image_sizes();

        // Posttypes 
        $taxonomies = new Taxonomy($this->pluginFile, $settings);
        $taxonomies->onLoaded();

        // Posttypes 
        $plugins = new Plugins($this->pluginFile, $settings);
        $plugins->onLoaded();


        // Templates 
        $templates = new Templates($this->pluginFile, $settings);
        $templates->onLoaded();



        // Backend Setting pages
        $backend = new BackendMenu($this->pluginFile, $settings);
        $backend->onLoaded();


        // Add Metaboxes
        $metaboxes = new Metaboxes($this->pluginFile, $settings);
        $metaboxes->onLoaded();


        // Add Shortcodes
        $shortcodes = new Shortcodes($this->pluginFile, $settings);
        $shortcodes->onLoaded();


        // Add Widget
        $widget = new Widgets($this->pluginFile, $settings);
        $widget->onLoaded();
    }




    public function registerPluginStyles() {
        wp_register_style('fau-person', plugins_url('css/fau-person.css', $this->pluginFile));
        if (self::$enqueuePluginStyles) {
            wp_enqueue_style('fau-person');
        }
    }

    public function enqueueAdminScripts() {
        wp_register_style('fau-person-adminstyle', plugins_url('css/fau-person-admin.css', $this->pluginFile));
        wp_enqueue_style('fau-person-adminstyle');
        wp_register_script('fau-person-adminscripts', plugins_url('js/fau-person-admin.js', $this->pluginFile));
        wp_enqueue_script('fau-person-adminscripts');
        wp_enqueue_script('jquery');
    }



    public static function enqueueForeignThemes() {
        self::$enqueuePluginStyles = true;
        wp_enqueue_style('fau-person');
    }

    public function detectFrontendShortcodes() {
        if (is_admin()) {
            return;
        }

        global $wp_query;

        if (empty($wp_query) || empty($wp_query->posts) || !is_array($wp_query->posts)) {
            return;
        }

        foreach ($wp_query->posts as $post) {
            if (!isset($post->post_content) || !is_string($post->post_content)) {
                continue;
            }

            if (
                has_shortcode($post->post_content, 'kontakt') ||
                has_shortcode($post->post_content, 'person') ||
                has_shortcode($post->post_content, 'kontaktliste') ||
                has_shortcode($post->post_content, 'persons') ||
                has_shortcode($post->post_content, 'standort')
            ) {
                self::$enqueuePluginStyles = true;
                return;
            }
        }
    }

    public function printLatePluginStyles() {
        if (!self::$enqueuePluginStyles) {
            return;
        }

        if (!wp_style_is('fau-person', 'registered')) {
            wp_register_style('fau-person', plugins_url('css/fau-person.css', $this->pluginFile));
        }

        if (!wp_style_is('fau-person', 'done')) {
            wp_enqueue_style('fau-person');
            wp_print_styles(['fau-person']);
        }
    }

    public function define_image_sizes() {

        /* Thumb for person-type; small for sidebar - Name: person-thumb */
        add_image_size('person-thumb-v3', $this->settings->constants['images']['default_person_thumb_width'], $this->settings->constants['images']['default_person_thumb_height'], $this->settings->constants['images']['default_person_thumb_crop']); // 60, 80, true


        /* Thumb for person-type; big for content - Name: person-thumb-page */
        add_image_size('person-thumb-page-v3', $this->settings->constants['images']['default_person_thumb_page_width'], $this->settings->constants['images']['default_person_thumb_page_height'], $this->settings->constants['images']['default_person_thumb_page_crop']); // 200,300,true

    }
}
