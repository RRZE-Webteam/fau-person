<?php
/**
 * UnivIS Helper Functions
 */
defined('ABSPATH') || exit;



/**
 * Autoloads files with UnivIS classes when needed
 *
 * @since  1.0.0
 * @param  string $class_name Name of the class being requested.
 */
function univis_autoload_classes( $class_name ) {
    $prefix = 'UnivIS';
    $base_dir = UnivIS_DIR; //  . '/includes/';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class_name, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class_name, $len);
    $file = $base_dir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }

}

