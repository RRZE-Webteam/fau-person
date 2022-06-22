<?php

namespace FAU_Person\Metaboxes;

defined('ABSPATH') || exit;

/**
 * Define Metaboxes for Kontakt-Edit
 */
class Buchung extends Metaboxes {

    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;	
    }

    public function onLoaded()    {
	add_filter('cmb2_meta_boxes', array( $this, 'cmb2_buchung_metaboxes') );
	
    }
   
    

    public function cmb2_buchung_metaboxes( $meta_boxes ) {
	$prefix = $this->prefix;

	
	// Meta-Box Buchunginformation - fau_buchung_info
	$meta_boxes['fau_buchung_contact'] = array(
	    'id' => 'fau_buchung_contact',
	    'title' => __( 'Kontaktinformationen', 'fau-person' ),
	    'object_types' => array('buchung'), // post type
	    //'show_on' => array( 'key' => 'submenu-slug', 'value' => 'kontakt' ),        
	    'context' => 'normal',
	    'priority' => 'default',
	    'fields' => array(
            array(
                'name' => __('Last name', 'fau-person'),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'lastname',
                'default'	=> ''
            ),
            array(
                'name' => __('First name', 'fau-person'),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'firstname',
                'default'	=> ''
            ),
            array(
                'name' => __('Email', 'fau-person'),
                'desc' => '',
                'type' => 'text_email',
                'id' => $prefix . 'email',
                'default'	=> ''
            ),
            array(
                'name' => __('Phone number', 'fau-person'),
                'desc' => '',
                'type' => 'text',
                'id' => $prefix . 'phone',
                'default'	=> ''
            ),

        )
	);
        $meta_boxes['fau_buchung_info'] = array(
            'id' => 'fau_buchung_info',
            'title' => __( 'Buchung', 'fau-person' ),
            'object_types' => array('buchung'), // post type
            //'show_on' => array( 'key' => 'submenu-slug', 'value' => 'kontakt' ),
            'context' => 'normal',
            'priority' => 'default',
            'fields' => array(
                array(
                    'name' => __('Start', 'fau-person'),
                    'desc' => '',
                    'type' => 'text_datetime_timestamp',
                    'id' => $prefix . 'booking_start',
                    'default'	=> '',
                    'date_format' => 'd.m.Y',
                    'time_format' => 'H:i',
                ),
                array(
                    'name' => __('End', 'fau-person'),
                    'desc' => '',
                    'type' => 'text_time',
                    'id' => $prefix . 'booking_end',
                    'default'	=> '',
                    'time_format' => 'H:i',
                ),
            )
        );
	return $meta_boxes;
    }
}