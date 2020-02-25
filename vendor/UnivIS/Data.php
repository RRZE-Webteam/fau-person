<?php
/*
 * UnivIS-Data API- und Cache-Funktionen
 */
use Config;
use Sanitizer;

add_action('univis_data_async_task', array('UnivIS_Data', 'async_task'));

add_action('save_post', function() {
    wp_schedule_single_event(time(), 'univis_data_async_task');
} );

class UnivIS_Data {

    const transient_prefix = 'univis_data_';    
    protected static $univis_api_url = 'http://univis.uni-erlangen.de/prg';
    protected static $transient_expiration = DAY_IN_SECONDS;    
    protected static $timeout = HOUR_IN_SECONDS; 
    protected static $results_limit = 100;
    
    public static function get_person($id) {

        if (!Sanitizer::is_valid_id($id)) {
            return false;
        }

        if (($data = self::get_data_cache($id)) !== false) {
            return $data;
        }

        if (($data = self::get_remote_data_by('id', $id)) === false) {
            return false;
        }

        self::set_data_cache($data[0]);

        return $data[0];
    }
    
    private static function get_data_cache($id) {
        if (!Sanitizer::is_valid_id($id)) {
            return false;
        }
        
        return get_transient(self::transient_prefix . $id);
    }
    
    private static function set_data_cache($data) {
        if (!isset($data['id'])) {
            return false;
        }
        
        $id = $data['id'];
        if (!Sanitizer::is_valid_id($id)) {
            return false;
        }
        
        return set_transient(self::transient_prefix . $id, $data, self::$transient_expiration);
    }
    
    public static function async_task() {
        $timestamp = time() + self::$transient_expiration - self::$timeout;
        
        if($results = self::get_results_by_timeout($timestamp, self::$results_limit)) {
            
            foreach($results as $row) {                

                $id = ltrim($row->option_name, '_transient_timeout_' . self::transient_prefix);

                if (($data = self::get_remote_data_by('id', $id)) !== false) {
                    self::set_data_cache($data[0]);
                } else {
                    self::delete_data_cache($id);
                }
                
            }
            
        }
        
    }
        
    private static function get_results_by_timeout($timestamp, $limit) {
        global $wpdb;

        $sql = "SELECT * FROM $wpdb->options WHERE option_name LIKE %s AND option_value < %d ORDER BY option_value ASC LIMIT %d";

        return $wpdb->get_results($wpdb->prepare($sql, $wpdb->esc_like('_transient_timeout_' . self::transient_prefix) . '%', $timestamp, $limit));        
    }
    
    public static function delete_data_cache($id) {
        if (!Sanitizer::is_valid_id($id)) {
            return false;
        }

        return delete_transient(self::transient_prefix . $id);
    }
    
    public static function search_by($field = '', $value = '') {
        if ('id' == $field && !Sanitizer::is_valid_id($value)) {
            return false;
        } else {
            $value = trim($value);
        }

        if (!$value) {
            return false;
        }

        if (!$data = self::get_remote_data_by($field, $value)) {
            return false;
        }

        return $data;
    }
    
    public static function search_by_fullname($firstname = '', $lastname = '') {
        $firstname = trim($firstname);
        $lastname = trim($lastname);
        
        if (!$lastname || !$firstname) {
            return false;
        }

        if (!$data = self::get_remote_data_by_fullname($firstname, $lastname)) {
            return false;
        }

        return $data;
    }
            
    private static function get_remote_data_by($field, $value) {
        
        switch ($field) {
            case 'id':
                $url = sprintf('%1$s?search=persons&id=%2$d&show=xml', self::$univis_api_url, $value);
                break;
            case 'firstname':
                $url = sprintf('%1$s?search=persons&firstname=%2$s&show=xml', self::$univis_api_url, urlencode($value));
                break;
            case 'lastname':
                $url = sprintf('%1$s?search=persons&name=%2$s&show=xml', self::$univis_api_url, urlencode($value));
                break;
            default:
                return false;
        }

        if (!fopen($url, "r")) {
            return false;
        }

        if(!$persArray = self::xml2array($url)) {
            return false;
        }
        
        $data = $persArray['Person'];
        
        if (count($data) == 0) {
            return false;
        }
        
        return $data;        
    }
    
    private static function get_remote_data_by_fullname($firstname, $lastname) {
        
        $url = sprintf('%1$s?search=persons&firstname=%2$s&name=%3$s&show=xml', self::$univis_api_url, urlencode($firstname), urlencode($lastname));
        
        if (!fopen($url, "r")) {
            return false;
        }

        if(!$persArray = self::xml2array($url)) {
            return null;
        }
        
        $data = $persArray['Person'];
        
        if (count($data) == 0) {
            return null;
        }
        
        return $data;        
    }
    
    private static function xml2array($url) {
        $sxi = new SimpleXMLIterator($url, null, true);
        return self::sxi2array($sxi);
    }

    private static function sxi2array($sxi) {
        $a = array();

        for ($sxi->rewind(); $sxi->valid(); $sxi->next()) {

            if (!array_key_exists($sxi->key(), $a)) {
                $a[$sxi->key()] = array();
            }
            
            if ($sxi->hasChildren()) {
                $a[$sxi->key()][] = self::sxi2array($sxi->current());
            } elseif($sxi->key() === 'orgunit') {
                $a[$sxi->key()][] = strval($sxi->current());
            } elseif($sxi->key() === 'orgunit_en') {
                $a[$sxi->key()][] = strval($sxi->current());
            } else {
                $a[$sxi->key()] = strval($sxi->current());

                if ($sxi->UnivISRef) {
                    $attributes = (array) $sxi->UnivISRef->attributes();
                    $a[$sxi->key()][] = $attributes["@attributes"];
                }
            }

            if ($sxi->attributes()) {
                $attributes = (array) $sxi->attributes();
                $a["@attributes"] = $attributes["@attributes"];
            }
        }
        return $a;
    }
    
   
      //Legt die in UnivIS hinterlegten Werte in einem Array ab, Feldbezeichnungen
    public static function univis_defaults($id ) {
         $post = get_post($id);
	if( !is_null( $post ) && $post->post_type === 'person' && get_post_meta($id, 'fau_person_univis_id', true)) {
	    $univis_id = get_post_meta($id, 'fau_person_univis_id', true);
	    $univis_default = sync_helper::get_fields($id, $univis_id, 1);
	    return $univis_default;
	} else {
	$univis_default = Config::get_keys_fields('persons');
	    return $univis_default;
	}
    }
}
