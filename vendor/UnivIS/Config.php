<?php
namespace RRZE\Lib\UnivIS;

defined('ABSPATH') || exit;

/*
 * Fixe und nicht aenderbare Plugin-Optionen
 * @return array 
 */
class Config {
    public static function get_Config() {
	    $options = array(
		'transient_prefix'		=> 'univis_data_',
		'api_url'			=> 'http://univis.uni-erlangen.de/prg',
		'api_transient_expiration'		=> DAY_IN_SECONDS,
		'api_timeout'			=> HOUR_IN_SECONDS,
		'api_results_limit'		=> 100,
	    );               
	    // für ergänzende Optionen aus anderen Plugins
	    return apply_filters('univis_config', $options);
    }

    public static function get_fields() {
	return [
	    'persons'   => [
		'department' => [
		    'keyname'   => 'orgname',
		    'default'   => '',
		    'type'	    => 'string'
		],
		'honorificPrefix' => [
		    'keyname'   => 'title',
		    'default'   => '',
		    'type'	    => 'string'
		],
		'honorificSuffix' => [
		    'keyname'   => 'atitle',
		    'default'   => '',
		    'type'	    => 'string'
		],
		'givenName' => [
		    'keyname'   => 'firstname',
		    'default'   => '',
		    'type'	    => 'string'
		],
		'familyName' => [
		    'keyname'   => 'lastname',
		    'default'   => '',
		    'type'	    => 'string'
		],
		 'jobTitle' => [
		    'keyname'   => 'lastname',
		    'default'   => '',
		    'type'	    => 'work'
		],
		 'lehrbeauftragter' => [
		    'keyname'   => 'lehr',
		    'default'   => '',
		    'type'	    => 'boolstring'
		],
		 'visible' => [
		    'keyname'   => 'visible',
		    'default'   => '',
		    'type'	    => 'boolstring'
		],
		'public' => [
		    'keyname'   => 'pub_visible',
		    'default'   => '',
		    'type'	    => 'boolstring'
		],
		'restrict' => [
		    'keyname'   => 'restrict',
		    'default'   => '',
		    'type'	    => 'boolstring'
		],
		 'idm_id' => [
		    'keyname'   => 'idm_id',
		    'default'   => '',
		    'type'	    => 'loginstring'
		],
		 'univisid' => [
		    'keyname'   => 'id',
		    'default'   => '',
		    'type'	    => 'string'
		],
		 'univiskey' => [
		    'keyname'   => 'key',
		    'default'   => '',
		    'type'	    => 'string'
		],
		 'gender' => [
		    'keyname'   => 'gender',
		    'default'   => '',
		    'type'	    => 'genderstring'
		],
		'worksFor' => [
		     'keyname'   => 'orgunit',
		    'type'	=> 'arraystring',
		],
		 'worksFor_en' => [
		     'keyname'   => 'orgunit_en',
		    'type'	=> 'arraystring',
		],
		'location' => [
		    'type'	=> 'array',
		    'fields' => [
		       'email' => [
			    'keyname'   => 'email',
			    'default'   => '',
			    'type'	    => 'email'
			],
			'workLocation' => [
			    'keyname'   => 'office',
			    'default'   => '',
			    'type'	    => 'string'
			],
			'faxNumber' => [
			    'keyname'   => 'fax',
			    'default'   => '',
			    'type'	    => 'faxnumber'
			],
			'telephone' => [
			    'keyname'   => 'tel',
			    'default'   => '',
			    'type'	    => 'telnumber'
			],
			'streetAddress' => [
			    'keyname'   => 'street',
			    'default'   => '',
			    'type'	    => 'string'
			],
			'addressLocality' => [
			    'keyname'   => 'ort',
			    'default'   => '',
			    'type'	    => 'string'
			],
			'url' => [
			    'keyname'   => 'url',
			    'default'   => '',
			    'type'	    => 'url'
			],

		    ]

		],
		'hoursAvailable_group'  => [
		    'keyname'   => 'officehours',
		    'default'   => '',
		    'type'	    => 'string'
		]
	    ]
	];

    }
    public static function get_keys_fields($name = 'persons') {
	$fields_univis = self::get_fields(); 
	$res = array();
	
	if (isset($name)) {
	    foreach( $fields_univis[$name] as $key => $value ) {
		if ($value['type'] === 'array') {
		    foreach( $fields_univis[$name][$key]['fields'] as $subkey => $subvalue ) {
			if (isset($subvalue['default'])) {
			    $res[$subkey] = $subvalue['default'];
			 } 
		    }
		} elseif ($value['type'] == 'arraystring') {
		    $res[$key] = '';
		} else {
		    if (isset($value['default'])) {
			$res[$key] = $value['default'];
		    } 
		}
	    }
	}
	return $res;
    }
}
