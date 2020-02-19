<?php

namespace FAU_Person;

defined('ABSPATH') || exit;

class Helper {
    /**
     * [isPluginAvailable description]
     * @param  [string  $plugin [description]
     * @return boolean         [description]
     */
    public static function isPluginAvailable($plugin) {
        if (is_network_admin()) {
            return file_exists(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin);
        } elseif (! function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        return is_plugin_active($plugin);
    }
    
    
    public static  function array_orderby(){
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
                                    if(isset($row[$field])) {
					$tmp[$key] = $row[$field];
                                    } else {
                                        $tmp[$key] = '';
                                    }
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}
	
    public static function sonderzeichen ($string) {
        $search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´");
        $replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "");
        return str_replace($search, $replace, $string);
    }
    
       //Überprüft bei neuen Seiten ob Person oder Einrichtung eingegeben wird, abhängig vom Feldtyp fau_person_typ
    public static function default_fau_person_typ( ) {     
        if(isset($_GET["fau_person_typ"]) && $_GET["fau_person_typ"] == 'einrichtung') {
            $default_fau_person_typ = 'einrichtung';
        } else {
            $default_fau_person_typ = 'realperson';
        }
        return $default_fau_person_typ;
    }     
    
    public static function admin_notice_phone_number() {
    ?>
        <div class="notice notice-warning">
            <p><?php _e( 'Bitte korrigieren Sie das Format der Telefon- oder Faxnummer, die Anzeige ist nicht einheitlich!', 'fau-person' ); ?></p>
        </div>
        <?php
    }

  
    
}