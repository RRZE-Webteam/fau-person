<?php

namespace FAU_Person\Metaboxes;

defined('ABSPATH') || exit;

use FAU_Person\Main;
use FAU_Person\Metaboxes\Kontakt;
use FAU_Person\Metaboxes\Standort;
use FAU_Person\Metaboxes\Pages;
use FAU_Person\Metaboxes\Posts;
use RRZE\Lib\UnivIS\Data as UnivIS_Data;


class Metaboxes  {
     protected $pluginFile;
     private $settings = '';
     public $prefix = 'fau_person_';
     
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }

    public function onLoaded()     {
	

	require_once(plugin_dir_path($this->pluginFile) . 'vendor/cmb2/init.php');
   

	add_action( 'cmb2_render_text_number', array( $this, 'sm_cmb_render_text_number' ) );

	
//	add_filter( 'cmb2_show_on', array( $this, 'mb_show_on_person' ) );
	
	$kontaktmetabox = new Kontakt($this->pluginFile,  $this->settings);
	$kontaktmetabox->onLoaded();
	$standortmetabox = new Standort($this->pluginFile,  $this->settings);
	$standortmetabox->onLoaded();
	$pagesmb = new Pages($this->pluginFile,  $this->settings);
	$pagesmb->onLoaded();
	$postsmb = new Posts($this->pluginFile,  $this->settings);
	$postsmb->onLoaded();
    }
    
    
    
    function sm_cmb_render_text_number( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
	echo $field_type_object->input( array( 'class' => 'cmb_text_small', 'type' => 'text' ) );
    }

	//add_filter( 'cmb_validate_text_number', 'sm_cmb_validate_text_number' );
    function sm_cmb_validate_text_number( $new ) {
	$new = filter_var($new, FILTER_SANITIZE_NUMBER_INT);
	return $new;
    }
    
   
    function validate_univis_id( $str ) {   
	if( ctype_digit( $str ) && strlen( $str ) == 8 )
	    return $str;
    }

    function validate_plz( $str ) {   
	if( ctype_digit( $str ) && strlen( $str ) == 5 ) 
	    return $str;
    }

    function validate_number( $str ) {
	if ($str) {
	    $location = get_post_meta( cmb2_Meta_Box::get_object_id(), 'fau_person_telephone_select', true );
	    $str = UnivIS_Data::correct_phone_number( $str, $location );
	 //   add_action( 'admin_notices', array( 'FAU_Person\Helper', 'admin_notice_phone_number' ) );
	    return $str;
	}
    }



}