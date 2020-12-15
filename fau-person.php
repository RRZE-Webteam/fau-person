<?php
/**
 Plugin Name: FAU Person
 Plugin URI: https://github.com/RRZE-Webteam/fau-person
 GitHub Plugin URI: https://github.com/RRZE-Webteam/fau-person
 Description: Visitenkarten-Plugin für FAU Webauftritte
 Version: 3.1.5
 Author: RRZE-Webteam
 Author URI: http://blogs.fau.de/webworking/
 License: GPLv3 or later
 */

	

namespace FAU_Person;

defined('ABSPATH') || exit;

use FAU_Person\Main;

// Laden der Konfigurationsdatei
require_once __DIR__ . '/vendor/UnivIS/UnivIS.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/compatibility.php';


// Automatische Laden von Klassen.
spl_autoload_register(function ($class) {
    $prefix = __NAMESPACE__;
    $base_dir = __DIR__ . '/includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

const RRZE_PHP_VERSION = '7.2';
const RRZE_WP_VERSION = '5.3';

// Registriert die Plugin-Funktion, die bei Aktivierung des Plugins ausgeführt werden soll.
register_activation_hook(__FILE__, __NAMESPACE__ . '\activation');
// Registriert die Plugin-Funktion, die ausgeführt werden soll, wenn das Plugin deaktiviert wird.
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivation');
// Wird aufgerufen, sobald alle aktivierten Plugins geladen wurden.
add_action('plugins_loaded', __NAMESPACE__ . '\loaded');


/**
 * Einbindung der Sprachdateien.
 */
function loadTextDomain() {
    load_plugin_textdomain('fau-person', false, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
}

/**
 * Überprüft die Systemvoraussetzungen.
 */
function systemRequirements() {
    $error = '';
    if (version_compare(PHP_VERSION, RRZE_PHP_VERSION, '<')) {
        /* Übersetzer: 1: aktuelle PHP-Version, 2: erforderliche PHP-Version */
        $error = sprintf(__('The server is running PHP version %1$s. The Plugin requires at least PHP version %2$s.', 'fau-person'), PHP_VERSION, RRZE_PHP_VERSION);
    } elseif (version_compare($GLOBALS['wp_version'], RRZE_WP_VERSION, '<')) {
        /* Übersetzer: 1: aktuelle WP-Version, 2: erforderliche WP-Version */
        $error = sprintf(__('The server is running WordPress version %1$s. The Plugin requires at least WordPress version %2$s.', 'fau-person'), $GLOBALS['wp_version'], RRZE_WP_VERSION);
    }
    return $error;
}

/**
 * Wird nach der Aktivierung des Plugins ausgeführt.
 */
function activation() {
    // Sprachdateien werden eingebunden.
    loadTextDomain();

    // Überprüft die minimal erforderliche PHP- u. WP-Version.
    // Wenn die Überprüfung fehlschlägt, dann wird das Plugin automatisch deaktiviert.
    if ($error = systemRequirements()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die($error);
    }
    
    
   // CPT-Capabilities für die Administrator-Rolle zuweisen
    fau_person_add_kontakteditor_role();
    fau_person_set_caps_to_roles();		    
	

}

/**
 * Wird durchgeführt, nachdem das Plugin deaktiviert wurde.
 */
function deactivation() {  
    fau_person_remove_caps();
    remove_role('person_editor_role');
    flush_rewrite_rules();    
}

/**
 * Wird durchgeführt, nachdem das WP-Grundsystem hochgefahren
 * und alle Plugins eingebunden wurden.
 */
function loaded() {
    // Sprachdateien werden eingebunden.
    loadTextDomain();

    // Überprüft die Systemvoraussetzungen.
    if ($error = systemRequirements()) {
        add_action('admin_init', function () use ($error) {
            $pluginData = get_plugin_data(__FILE__);
            $pluginName = $pluginData['Name'];
            $tag = is_plugin_active_for_network(plugin_basename(__FILE__)) ? 'network_admin_notices' : 'admin_notices';
            add_action($tag, function () use ($pluginName, $error) {
                printf(
                    '<div class="notice notice-error"><p>' . __('Plugins: %1$s: %2$s', 'fau-person') . '</p></div>',
                    esc_html($pluginName),
                    esc_html($error)
                );
            });
        });
        // Das Plugin wird nicht mehr ausgeführt.
        return;
    }

    // Hauptklasse (Main) wird instanziiert.	
    $main = new Main(__FILE__);
    $main->onLoaded();
    
    // Check if Editor role was already defined or if this is an updated plugin, where 
    // the old activation did not had this 
    
    $role = get_role('person_editor_role');
    if (!isset($role)) {
	fau_person_add_kontakteditor_role();
	fau_person_set_caps_to_roles();	
    }
    
}

 function fau_person_remove_caps() {    
	$roles = array('person_editor_role', 'editor','administrator');   
	$caps_person = Config\get_fau_person_capabilities();
	foreach($roles as $the_role) {
	    $role = get_role($the_role);
	    if (isset($role)) {
		foreach($caps_person as $cap => $value) {
		    $role->remove_cap($value);
		}  
	    }
	}    
    }

    
    function fau_person_set_caps_to_roles() {    
	$roles = array('person_editor_role', 'editor','administrator');   
	$caps_person = Config\get_fau_person_capabilities();

	foreach($roles as $the_role) {
	    $role = get_role($the_role);
	    if (isset($role)) {
		foreach($caps_person as $cap => $value) {
		    if ($the_role == 'person_editor_role') {
			switch ($value) {
			    case 'delete_persons':
			    case 'delete_private_persons':
			    case 'delete_published_persons':
			    case 'delete_others_persons':
			    case 'publish_persons':	
			       break;

			    default:
			       $role->add_cap($value);
		       }

		    } else {
			$role->add_cap($value);
		    }
		}  
	   }
	}    
	return;
    }



    function fau_person_add_kontakteditor_role() {
	add_role('person_editor_role',
            __( 'Kontakt-Bearbeiter', 'fau-person' ),
            array(
                'read' => true,
                'edit_posts' => true,
                'delete_posts' => false,
                'publish_posts' => false,
                'upload_files' => true,
            )
        );
    }
    