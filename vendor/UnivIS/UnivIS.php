<?php

namespace RRZE\Lib;
/**
 * The initation loader for UnivIS, and the main plugin file.
 *
 * @category     WordPress Plugin and Library
 * @package      UnivIS
 * @author       RRZE Webteam
 * @license      GPL-2.0+
 * @link         https://blogs.fau.de/webworking
 *
 * Version:      1.0.0
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

defined('ABSPATH') || exit;
require_once 'Config.php';
require_once 'Sanitizer.php';
require_once 'Data.php';


class UnivIS {


	const VERSION = '1.0.0';

	public static $single_instance = null;


	public static function initiate() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}
		return self::$single_instance;
	}


	public function __construct() {
		if ( ! function_exists( 'add_action' ) ) {
			// We are running outside of the context of WordPress.
			return;
		}

		add_action( 'init', array( $this, 'include_univis' ));

	}


	public function include_univis() {
		if ( class_exists( 'UnivIS', false ) ) {
			return;
		}

		if ( ! defined( 'UnivIS_VERSION' ) ) {
			define( 'UnivIS_VERSION', self::VERSION );
		}

		if ( ! defined( 'UnivIS_DIR' ) ) {
			define( 'UnivIS_DIR', trailingslashit( dirname( __FILE__ ) ) );
		}

		// Include helper functions.
		//require_once UnivIS_DIR . 'includes/Config.php';

		// Now kick off the class autoloader.
		spl_autoload_register( 'RRZE\Lib\univis_autoload_classes' );


	}



}

// Make it so...
UnivIS::initiate();

function univis_autoload_classes( $class_name ) {
    $prefix = 'RREZE\Lib\UnivIS';
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
