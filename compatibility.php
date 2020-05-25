<?php

/* 
 * Abwärtskompatibilität zu vorherigen direkten Class-Aufrüfen
 */

namespace {
    class FAU_Person_Shortcodes {
	public static function fau_person_page($id) {
	    return FAU_Person\Data::fau_person_page($id);
	}
	public static function fau_person($args) {
	    return FAU_Person\Shortcodes\Kontakt::shortcode_kontakt($args);
	}
    }
    class FAU_Person  {
	// for downwards compatibility
    }
}					
