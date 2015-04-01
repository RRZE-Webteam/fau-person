<?php

class CMB_Helper {
    
    /**
     * Return an array of built in available fields
     *
     * Key is field name, Value is class used by field.
     * Available fields can be modified using the 'cmb_field_types' filter.
     *
     * @return array
     */
    private static function available_fields() {

        return apply_filters('cmb_field_types', array(
            'text' => 'CMB_Text_Field',
            'text_small' => 'CMB_Text_Small_Field',
            'text_url' => 'CMB_URL_Field',
            'url' => 'CMB_URL_Field',
            'radio' => 'CMB_Radio_Field',
            'checkbox' => 'CMB_Checkbox',
            'file' => 'CMB_File_Field',
            'image' => 'CMB_Image_Field',
            'wysiwyg' => 'CMB_wysiwyg',
            'textarea' => 'CMB_Textarea_Field',
            'textarea_code' => 'CMB_Textarea_Field_Code',
            'select' => 'CMB_Select',
            'taxonomy_select' => 'CMB_Taxonomy',
            'post_select' => 'CMB_Post_Select',
            'date' => 'CMB_Date_Field',
            'date_unix' => 'CMB_Date_Timestamp_Field',
            'datetime_unix' => 'CMB_Datetime_Timestamp_Field',
            'time' => 'CMB_Time_Field',
            'colorpicker' => 'CMB_Color_Picker',
            'title' => 'CMB_Title',
            'group' => 'CMB_Group_Field',
            'gmap' => 'CMB_Gmap_Field',
            'number' => 'CMB_Number_Field'
                ));
    }

    /**
     * Get a field class by type
     *
     * @param  string $type
     * @return string $class, or false if not found.
     */
    public static function field_class_for_type($type) {

        $map = self::available_fields();

        if (isset($map[$type]))
            return $map[$type];

        return false;
    }

}
