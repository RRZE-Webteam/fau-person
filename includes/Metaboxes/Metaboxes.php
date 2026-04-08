<?php

namespace FAU_Person\Metaboxes;

defined('ABSPATH') || exit;

use FAU_Person\Metaboxes\Kontakt;
use FAU_Person\Metaboxes\Standort;
use FAU_Person\Metaboxes\Posts;
use FAU_Person\UnivIS\Data as UnivIS_Data;


class Metaboxes
{
    protected $pluginFile;
    private $settings = '';
    public $prefix = 'fau_person_';

    public function __construct($pluginFile, $settings)
    {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }

    public function onLoaded() {
        add_action('cmb2_render_text_number', array($this, 'sm_cmb_render_text_number'));


        $kontaktmetabox = new Kontakt($this->pluginFile,  $this->settings);
        $kontaktmetabox->onLoaded();
        $standortmetabox = new Standort($this->pluginFile,  $this->settings);
        $standortmetabox->onLoaded();

    }



    function sm_cmb_render_text_number($field_args, $escaped_value, $object_id, $object_type, $field_type_object) {
        echo $field_type_object->input(array('class' => 'cmb_text_small', 'type' => 'text'));
    }

    //add_filter( 'cmb_validate_text_number', 'sm_cmb_validate_text_number' );
    function sm_cmb_validate_text_number($new)
    {
        $new = filter_var($new, FILTER_SANITIZE_NUMBER_INT);
        return $new;
    }


    function validate_univis_id($str)
    {
        if (ctype_digit($str) && strlen($str) == 8)
            return $str;
    }

    function validate_plz($str)
    {
        if (ctype_digit($str) && strlen($str) == 5)
            return $str;
    }

    function validate_number($str)
    {
        if ($str) {
            $location = get_post_meta(cmb2_Meta_Box::get_object_id(), 'fau_person_telephone_select', true);
            $str = UnivIS_Data::correct_phone_number($str, $location);
            return $str;
        }
    }
}
