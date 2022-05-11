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
    add_action( 'cmb2_render_select_multiple', array( $this, 'cmb2_render_select_multiple_field_type'), 10, 5 );
    add_filter( 'cmb2_sanitize_select_multiple', array( $this, 'cmb2_sanitize_select_multiple_callback'), 10, 2 );

	
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

    /**
     * Adds a custom field type for select multiples.
     * @param  object $field             The CMB2_Field type object.
     * @param  string $value             The saved (and escaped) value.
     * @param  int    $object_id         The current post ID.
     * @param  string $object_type       The current object type.
     * @param  object $field_type_object The CMB2_Types object.
     * @return void
     */
    function cmb2_render_select_multiple_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

        $select_multiple = '<select multiple name="' . $field->args['_name'] . '[]" id="' . $field->args['_id'] . '"';
        foreach ( $field->args['attributes'] as $attribute => $value ) {
            $select_multiple .= " $attribute=\"$value\"";
        }
        $select_multiple .= ' />';

        foreach ( $field->options() as $value => $name ) {
            $selected = ( $escaped_value && in_array( $value, $escaped_value ) ) ? 'selected="selected"' : '';
            $select_multiple .= '<option class="cmb2-option" value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
        }

        $select_multiple .= '</select>';
        $select_multiple .= $field_type_object->_desc( true );

        echo $select_multiple; // WPCS: XSS ok.
    }


    /**
     * Sanitize the selected value.
     */
    function cmb2_sanitize_select_multiple_callback( $override_value, $value ) {
        if ( is_array( $value ) ) {
            foreach ( $value as $key => $saved_value ) {
                $value[$key] = sanitize_text_field( $saved_value );
            }

            return $value;
        }

        return;
    }


}