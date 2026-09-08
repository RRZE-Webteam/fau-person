<?php

/*
Plugin Name:        FAU Person
Plugin URI:         https://github.com/RRZE-Webteam/fau-person
GitHub Plugin URI:  https://github.com/RRZE-Webteam/fau-person
Description:        Visitenkarten-Plugin für FAU Webauftritte
Version:            3.10.10
Author:             RRZE-Webteam
License:            GPLv3 or later
Text Domain:        fau-person
Domain Path:        /languages
*/



namespace FAU_Person;

defined('ABSPATH') || exit;

use FAU_Person\Main;
use function FAU_Person\Config\getConstants;

// Laden der Konfigurationsdatei
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/compatibility.php';

// CMB2 bleibt eine externe Laufzeitabhängigkeit.
require_once __DIR__ . '/vendor/cmb2/cmb2/init.php';

/**
 * SPL-Autoloader nach PSR-4 für Plugin-Klassen.
 *
 * @param string $class Vollqualifizierter Klassenname.
 * @return void
 */
function autoload($class) {
    $prefix = __NAMESPACE__ . '\\';
    $baseDir = __DIR__ . '/includes/';
    $length = strlen($prefix);

    if (strncmp($prefix, $class, $length) !== 0) {
        return;
    }

    $relativeClass = substr($class, $length);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register(__NAMESPACE__ . '\\autoload');

// Load the plugin's text domain before plugin components create translated strings.
add_action('init', __NAMESPACE__ . '\loadTextdomain', 0);
// Registriert die Plugin-Funktion, die bei Aktivierung des Plugins ausgeführt werden soll.
register_activation_hook(__FILE__, __NAMESPACE__ . '\activation');
// Registriert die Plugin-Funktion, die ausgeführt werden soll, wenn das Plugin deaktiviert wird.
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivation');
// Wird ausgeführt, sobald WordPress initialisiert ist.
add_action('init', __NAMESPACE__ . '\loaded', 0);

/**
 * Lädt die Übersetzungen des Plugins.
 */
function loadTextdomain() {
    load_plugin_textdomain('fau-person', false, dirname(plugin_basename(__FILE__)) . '/languages');
}


/**
 * Überprüft die Systemvoraussetzungen.
 */
function systemRequirements() {
    $constants = getConstants();
    $phpVersion = $constants['RRZE_PHP_VERSION'];
    $wpVersion = $constants['RRZE_WP_VERSION'];
    $error = '';
    if (version_compare(PHP_VERSION, $phpVersion, '<')) {
        /* Übersetzer: 1: aktuelle PHP-Version, 2: erforderliche PHP-Version */
        $error = sprintf(__('The server is running PHP version %1$s. The Plugin requires at least PHP version %2$s.', 'fau-person'), PHP_VERSION, $phpVersion);
    } elseif (version_compare($GLOBALS['wp_version'], $wpVersion, '<')) {
        /* Übersetzer: 1: aktuelle WP-Version, 2: erforderliche WP-Version */
        $error = sprintf(__('The server is running WordPress version %1$s. The Plugin requires at least WordPress version %2$s.', 'fau-person'), $GLOBALS['wp_version'], $wpVersion);
    }
    return $error;
}

/**
 * Wird nach der Aktivierung des Plugins ausgeführt.
 */
function activation() {

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

function fau_person_remove_caps()
{
    $roles = array('person_editor_role', 'editor', 'administrator');
    $caps_person = Config\get_fau_person_capabilities();
    foreach ($roles as $the_role) {
        $role = get_role($the_role);
        if (isset($role)) {
            foreach ($caps_person as $cap => $value) {
                $role->remove_cap($value);
            }
        }
    }
}


function fau_person_set_caps_to_roles() {
    $roles = array('person_editor_role', 'editor', 'administrator');
    $caps_person = Config\get_fau_person_capabilities();

    foreach ($roles as $the_role) {
        $role = get_role($the_role);
        if (isset($role)) {
            foreach ($caps_person as $cap => $value) {
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
    add_role(
        'person_editor_role',
        __('Kontakt-Bearbeiter', 'fau-person'),
        array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => false,
            'publish_posts' => false,
            'upload_files' => true,
        )
    );
}
