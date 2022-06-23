<?php

namespace FAU_Person\Metaboxes;

use FAU_Person\Data;

use function FAU_Person\Config\getConstants;

defined('ABSPATH') || exit;

/**
 * Define Metaboxes for Kontakt-Edit
 */
class Buchung extends Metaboxes {

    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings   = $settings;
    }

    public function onLoaded() {
        add_filter('cmb2_meta_boxes', array($this, 'cmb2_buchung_metaboxes'));
    }


    public function cmb2_buchung_metaboxes($meta_boxes) {
        $prefix = $this->prefix;


        // Meta-Box Kontaktinformationen - fau_buchung_contact
        $meta_boxes[ 'fau_buchung_contact' ] = array(
            'id'           => 'fau_buchung_contact',
            'title'        => __('Kontaktinformationen', 'fau-person'),
            'object_types' => array('buchung'), // post type
            //'show_on' => array( 'key' => 'submenu-slug', 'value' => 'kontakt' ),
            'context'      => 'normal',
            'priority'     => 'default',
            'fields'       => array(
                array(
                    'name'    => __('Last name', 'fau-person'),
                    'desc'    => '',
                    'type'    => 'text',
                    'id'      => $prefix . 'booking_lastname',
                    'default' => ''
                ),
                array(
                    'name'    => __('First name', 'fau-person'),
                    'desc'    => '',
                    'type'    => 'text',
                    'id'      => $prefix . 'booking_firstname',
                    'default' => ''
                ),
                array(
                    'name'    => __('Email', 'fau-person'),
                    'desc'    => '',
                    'type'    => 'text_email',
                    'id'      => $prefix . 'booking_email',
                    'default' => ''
                ),
                array(
                    'name'    => __('Phone number', 'fau-person'),
                    'desc'    => '',
                    'type'    => 'text',
                    'id'      => $prefix . 'booking_phone',
                    'default' => ''
                ),

            )
        );
        // Meta-Box Buchungsinformationen - fau_buchung_info
        $contactselect_connection = Data::get_contactdata(0);
        $constants  = getConstants();
        $meta_boxes[ 'fau_buchung_info' ] = array(
            'id'           => 'fau_buchung_info',
            'title'        => __('Buchung', 'fau-person'),
            'object_types' => array('buchung'), // post type
            'context'      => 'normal',
            'priority'     => 'default',
            'fields'       => array(
                array(
                    'name'    => __('Booking status', 'fau-person'),
                    'desc'    => '',
                    'id'      => $prefix . 'booking_status',
                    'type'    => 'select',
                    'options' => $constants['booking-status'],
                    'show_option_none' => __('-- Auswahl --', 'fau-person'),
                ),
                array(
                    'name'    => __('Booking with', 'fau-person'),
                    'desc'    => '',
                    'id'      => $prefix . 'booking_contact_id',
                    'type'    => 'select',
                    'options' => $contactselect_connection,
                    'show_option_none' => __('-- Auswahl --', 'fau-person'),
                ),
                array(
                    'name'        => __('Start', 'fau-person'),
                    'desc'        => '',
                    'type'        => 'text_datetime_timestamp',
                    'id'          => $prefix . 'booking_start',
                    'default'     => '',
                    'date_format' => 'd.m.Y',
                    'time_format' => 'H:i',
                ),
                array(
                    'name'        => __('End', 'fau-person'),
                    'desc'        => '',
                    'type'        => 'text_datetime_timestamp',
                    'id'          => $prefix . 'booking_end',
                    'default'     => '',
                    'date_format' => 'd.m.Y',
                    'time_format' => 'H:i',
                ),
                array(
                    'name'        => __('Comment', 'fau-person'),
                    'desc'        => '',
                    'type'        => 'textarea_small',
                    'id'          => $prefix . 'booking_comment',
                    'default'     => '',
                ),
            )
        );

        return $meta_boxes;
    }
}