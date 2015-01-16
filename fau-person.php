<?php

/**
 * Plugin Name: FAU Person
 * Description: Visitenkarten-Plugin für FAU Webauftritte
 * Version: 0.1
 * Author: Karin Kimpan
 * Author URI: http://blogs.fau.de/webworking/
 * License: GPLv2 or later
 */

/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

add_action('plugins_loaded', array('FAU_Person', 'instance'));

register_activation_hook(__FILE__, array('FAU_Person', 'activation'));
register_deactivation_hook(__FILE__, array('FAU_Person', 'deactivation'));

class FAU_Person {

    const version = '0.1';
    const option_name = '_fau_person';
    const version_option_name = '_fau_person_version';
    const textdomain = 'fau-person';
    const php_version = '5.3'; // Minimal erforderliche PHP-Version
    const wp_version = '4.0'; // Minimal erforderliche WordPress-Version

    protected static $post_types = 'person';
    
    public static $options;
    
    public static $person_fields;
    
    protected static $instance = null;

    public static function instance() {

        if (null == self::$instance) {
            self::$instance = new self;
            self::$instance->init();
        }

        return self::$instance;
    }

    private function init() {
        define('FAU_PERSON_ROOT', dirname(__FILE__));
        define('FAU_PERSON_FILE_PATH', FAU_PERSON_ROOT . '/' . basename(__FILE__));
        define('FAU_PERSON_URL', plugins_url('/', __FILE__));
        define('FAU_PERSON_TEXTDOMAIN', self::textdomain);
        
        load_plugin_textdomain(self::textdomain, false, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
        
        self::$options = (object) $this->get_options();
        
        add_action('init', array($this, 'update_version'));
        add_action('add_meta_boxes_person', array($this, 'adding_meta_boxes_person'));
        
        self::register_post_types();
        self::register_widgets();
        self::add_shortcodes();
        self::$person_fields = $this->person_fields();

    }

    public static function activation() {
        self::version_compare();
        update_option(self::version_option_name, self::version);
        
        self::register_post_types();        
        flush_rewrite_rules(); // Flush Rewrite-Regeln, so dass CPT und CT auf dem Front-End sofort vorhanden sind
        
        // CPT-Capabilities für die Administrator-Rolle zuweisen
        /*
        foreach(self::$post_types as $cap_type) {
            $caps = self::get_caps($cap_type);
            self::add_caps('administrator', $caps);
        }    
         * 
         */    
    }
    
    public static function deactivation() {
        // CPT-Capabilities aus der Administrator-Rolle entfernen
        /*
        foreach(self::$post_types as $cap_type) {
            $caps = self::get_caps($cap_type);
            self::remove_caps('administrator', $caps);
        }
         * 
         */
    }

    private static function version_compare() {
        $error = '';

        if (version_compare(PHP_VERSION, self::php_version, '<')) {
            $error = sprintf(__('Ihre PHP-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die PHP-Version %s.', self::textdomain), PHP_VERSION, self::php_version);
        }

        if (version_compare($GLOBALS['wp_version'], self::wp_version, '<')) {
            $error = sprintf(__('Ihre Wordpress-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die Wordpress-Version %s.', self::textdomain), $GLOBALS['wp_version'], self::wp_version);
        }

        if (!empty($error)) {
            deactivate_plugins(plugin_basename(__FILE__), false, true);
            wp_die($error);
        }
    }

    public static function update_version() {
        if (get_option(self::version_option_name, null) != self::version)
            update_option(self::version_option_name, self::version);
    }

    private function default_options() {
        return array(); // Standard-Array für zukünftige Optionen
    }

    protected function get_options() {
        $defaults = $this->default_options();
        
        $options = (array) get_option(self::option_name);
        $options = wp_parse_args($options, $defaults);
        $options = array_intersect_key($options, $defaults);

        return $options;
    }
    
    private static function get_caps($cap_type) {
        $caps = array(
            "edit_" . $cap_type,
            "read_" . $cap_type,
            "delete_" . $cap_type,
            "edit_" . $cap_type . "s",
            "edit_others_" . $cap_type . "s",
            "publish_" . $cap_type . "s",
            "read_private_" . $cap_type . "s",
            "delete_" . $cap_type . "s",
            "delete_private_" . $cap_type . "s",
            "delete_published_" . $cap_type . "s",
            "delete_others_" . $cap_type . "s",
            "edit_private_" . $cap_type . "s",
            "edit_published_" . $cap_type . "s",                
        );

        return $caps;
    }
    
    private static function add_caps($role, $caps) {
        $role = get_role($role);
        foreach($caps as $cap) {
            $role->add_cap($cap);
        }        
    }
    
    private static function remove_caps($role, $caps) {
        $role = get_role($role);
        foreach($caps as $cap) {
            $role->remove_cap($cap);
        }        
    }    
    
    private static function register_post_types() {
        require_once('posttypes/fau-person-posttype.php');
    }
 
    private static function register_widgets() {
        //require_once('widgets/fau-person-widget.php');
        //require_once('widgets/fau-event-widget.php');       
    }
    
    private static function add_shortcodes() {
    /*
        require_once('shortcodes/events-shortcode.php');
        require_once('shortcodes/shortcodes.php');    
     */    
    }

    /* Create one or more meta boxes to be displayed on the post editor screen. */
    public function adding_meta_boxes_person() {
        add_meta_box(
		'fau_person_info',			// Unique ID
		__( 'Kontaktinformationen', FAU_PERSON_TEXTDOMAIN ),		// Title
		array($this, 'new_meta_boxes_person'),		// Callback function
		'person',					// Admin page (or post type)
		'normal',					// Context
		'default'					// Priority
	);
        /*add_meta_box(
		'fau_person_social_media',			// Unique ID
		esc_html__( 'Social Media', FAU_PERSON_TEXTDOMAIN ),		// Title
		'new_meta_boxes',		// Callback function
		'person',					// Admin page (or post type)
		'normal',					// Context
		'default'					// Priority
	);*/        
    }
    
    public function new_meta_boxes_person() {
        $this->new_meta_boxes('person');
    }
    
    // Ausgabe der Custom Fields
    public function new_meta_boxes( $type ) {
        global $post;
        if($type == 'person') $new_meta_boxes = self::$person_fields; 
        wp_nonce_field('more_meta_box', 'more_meta_box_nonce');
        echo '<div class="form-wrap">';
        foreach($new_meta_boxes as $field => $value) {
          if($value['type'] == 'title') {
                echo '<p style="font-size: 18px; font-weight: bold; font-style: normal; color: #e5e5e5; text-shadow: 0 1px 0 #111; line-height: 40px; background-color: #464646; border: 1px solid #111; padding: 0 10px; -moz-border-radius: 6px;">' . $value['title'] . '</p>';
            } else {
                echo '<div class="form-field form-required">';
                echo '<label for="' . $field . '"><strong>' . $value['title'] . '</strong></label>';
                switch ($value['type']) {
                    case 'text':
                        echo '<input type="text" name="' . $field . '" id="' . $field . '" value="' . esc_attr(get_post_meta($post->ID, $field, true)) . '" size="15" />';
                        //echo '<input class="widefat" type="text" name="'.$field.'" id="'.$field.'" value="'.esc_attr( get_post_meta( $post->ID, $field, true ) ).'" size="15" />';
                        break;
                    case 'textarea':
                        echo '<textarea name="' . $field . '" id="' . $field . '" cols="60" rows="5" />' . esc_attr(get_post_meta($post->ID, $field, true)) . '</textarea>';
                        break;
                    case 'email':
                        echo '<input type="text" name="' . $field . '" id="' . $field . '" value="' . esc_attr(get_post_meta($post->ID, $field, true)) . '" size="10" />';
                        break;
                    case 'url':
                        echo '<input type="text" name="' . $field . '" id="' . $field . '" value="' . esc_attr(get_post_meta($post->ID, $field, true)) . '" size="15" />';
                        break;
                    case 'intval':
                        echo '<input type="text" name="' . $field . '" id="' . $field . '" value="' . esc_attr(get_post_meta($post->ID, $field, true)) . '" size="5" />';
                        break;
                    case 'checkbox':
                        if ($meta_box_value == '1') {
                            $checked = "checked=\"checked\"";
                        } else {
                            $checked = "";
                        }
                        echo '<label for="' . $field . '"><strong>' . $value['title'] . '</strong>&nbsp;<input style="width: 20px;" type="checkbox" id="' . $value['name'] . '" name="' . $value['name'] . '" value="1" ' . $checked . ' /></label>';
                        break;  
                    case 'select':
                        echo '<select name="' . $field . '">';
                        foreach ($value['options'] as $option) {
                            if (is_array($option)) {
                                echo '<option ' . ( $meta_box_value == $option['value'] ? 'selected="selected"' : '' ) . ' value="' . $option['value'] . '">' . $option['text'] . '</option>';
                            } else {
                                echo '<option ' . ( $meta_box_value == $option ? 'selected="selected"' : '' ) . ' value="' . $option['value'] . '">' . $option['text'] . '</option>';
                            }
                        }
                        echo '</select>';
                        break;
                    case 'image':
                        echo '<input type="text" name="' . $field . '" id="' . $value['name'] . '" value="' . htmlspecialchars($meta_box_value) . '" style="width: 400px; border-color: #ccc;" />';
                        echo '<input type="button" id="button' . $field . '" value="Browse" style="width: 60px;" class="button button-upload" rel="' . $post->ID . '" />';
                        echo '&nbsp;<a href="#" style="color: red;" class="remove-upload">remove</a>';
                        break;
                } //end switch
                echo '<p>' . $value['description'] . '</p>';
                echo '</div>';
            }
        }
        echo '</div>';

    }

    function save_postdata( $post_id ) {
        if ( ! isset( $_POST['more_meta_box_nonce'] ) ) {
                return;
        }
        if ( !wp_verify_nonce( $_POST['more_meta_box_nonce'], 'more_meta_box') ) {
            return $post_id;
        }

        if ( wp_is_post_revision( $post_id ) or wp_is_post_autosave( $post_id ) )
            return $post_id;

        global $post;
        $new_meta_boxes = self::$person_fields;

        foreach($new_meta_boxes as $meta_box) {
            if ( $meta_box['type'] != 'title' ) {

                if ( 'page' == $_POST['post_type'] ) {
                    if ( !current_user_can( 'edit_page', $post_id ))
                        return $post_id;
                } else {
                    if ( !current_user_can( 'edit_post', $post_id ))
                        return $post_id;
                }

                if ( is_array($_POST[$meta_box['name']]) ) {

                    foreach($_POST[$meta_box['name']] as $cat){
                        $cats .= $cat . ",";
                    }
                    $data = substr($cats, 0, -1);
                } else { 
                    $data = $_POST[$meta_box['name']];                     
                }        

                if(get_post_meta($post_id, $meta_box['name']) == "")
                    add_post_meta($post_id, $meta_box['name'], $data, true);
                elseif($data != get_post_meta($post_id, $meta_box['name'], true))
                    update_post_meta($post_id, $meta_box['name'], $data);
                elseif($data == "")
                    delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));   
            }
        }
    } 
    
    public function person_fields() {
        /* möglich bei type: text, textarea, checkbox, select, image, title, headline (für Zwischenüberschriften) */
        $person_fields = array(
            '_person_titel' => array(
                'default' => 'false',
                'title' => __( 'Titel (Präfix)', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),
            '_person_institution' => array(
                'default' => 'false',
                'title' => __( 'Institution/Abteilung', FAU_PERSON_TEXTDOMAIN ),
                'description' => 'Geben Sie hier die Institution ein.',
                'type' => 'text',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),
            '_person_abschluss' => array(
                'default' => 'false',
                'title' => __( 'Abschluss (Suffix)', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),
            '_person_vorname' => array(
                'default' => 'false',
                'title' => __( 'Vorname', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),
            '_person_nachname' => array(
                'default' => 'false',
                'title' => __( 'Nachname', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),
            '_person_position' => array(
                'default' => 'false',
                'title' => __( 'Position/Funktion', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),
            '_person_telefon' => array(
                'default' => 'false',
                'title' => __( 'Telefon', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),            
            '_person_telefax' => array(
                'default' => 'false',
                'title' => __( 'Telefax', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),        
            '_person_email' => array(
                'default' => 'false',
                'title' => __( 'E-Mail', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_social_media',
                'location' => 'person'),    
            '_person_url' => array(
                'default' => 'false',
                'title' => __( 'Webseite', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_social_media',
                'location' => 'person'),            
            '_person_adresse' => array(
                'default' => 'false',
                'title' => __( 'Adresse', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'textarea',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),
            '_person_raum' => array(
                'default' => 'false',
                'title' => __( 'Raum', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_info',
                'location' => 'person'),  
            '_person_link' => array(
                'default' => 'false',
                'title' => __( 'Link', FAU_PERSON_TEXTDOMAIN ),
                'description' => '',
                'type' => 'text',
                'meta-box' => 'fau_person_social_media',
                'location' => 'person')            
            );

        return apply_filters( '_fau_person_fields', $person_fields );
    }



}
