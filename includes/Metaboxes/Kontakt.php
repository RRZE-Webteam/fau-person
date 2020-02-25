<?php

namespace FAU_Person\Metaboxes;
use FAU_Person\Data;
use UnivIS_Data;

defined('ABSPATH') || exit;

/**
 * Define Metaboxes for Kontakt-Edit
 */
class Kontakt extends Metaboxes {

    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;	
    }

    public function onLoaded()    {
	require_once(plugin_dir_path($this->pluginFile) . 'vendor/UnivIS/UnivIS.php');
	add_filter('cmb2_meta_boxes', array( $this, 'cmb2_kontakt_metaboxes') );	
    }

    public function cmb2_kontakt_metaboxes( $meta_boxes ) {
	$prefix = $this->prefix;


	$contactselect_connection = Data::get_contactdata(1);
 	$standortselect =  Data::get_standortdata();
	$default_fau_person_typ = Data::get_default_fau_person_typ();
	
	$person_id = 0;
	
	if ( isset( $_GET['post'] ) ) {
	    $person_id = intval( $_GET['post'] );
	} elseif ( isset( $_POST['post_ID'] ) ) {
	    $person_id = intval( $_POST['post_ID'] );
	}
	
	

	
	if(UnivIS_Data::get_person($person_id) ) {
	    $univis_sync = '';
//	    $univis_sync = '<p class="cmb2_metabox_description">' . __('Es können aktuell keine Daten aus UnivIS angezeigt werden. Bitte überprüfen Sie, ob Sie das Plugin univis-data installiert und aktiviert haben.', 'fau-person') . '</p>';
	} else {
	    $univis_sync = '<p class="cmb2_metabox_description">' . __('Derzeit sind keine Daten aus UnivIS syncronisiert.', 'fau-person') . '</p>';
	}
	$standort_default = Data::get_standort_defaults($person_id);  
	$univis_default = UnivIS_Data::univis_defaults($person_id);  

	// Meta-Box Zuordnung - fau_person_orga
	$meta_boxes['fau_person_orga'] = array(
	    'id' => 'fau_person_orga',
	    'title' => __( 'Zuordnung', 'fau-person' ),
	    'object_types' => array('person'), // post type
	    'context' => 'normal',
	    'priority' => 'default',
	//    'show_on_cb' => 'callback_cmb2_show_on_person',
	    'fields' => array(        
		array(
		    'name' => __('Organisation', 'fau-person'),
		    'desc' => __('Geben Sie hier die Organisation (Lehrstuhl oder Einrichtung) ein.', 'fau-person'),
		    'type' => 'text',
		    'id' => $prefix . 'worksFor',
		    'after' => $univis_default['worksFor']
		),
		array(
		    'name' => __('Abteilung', 'fau-person'),
		    'desc' => __('Geben Sie hier die Abteilung oder Arbeitsgruppe ein.', 'fau-person'),
		    'type' => 'text',
		    'id' => $prefix . 'department',
		    'after' => $univis_default['department'],
		   
		),
		array(
		    'name' => __('Position/Funktion', 'fau-person'),
		    'desc' => '',
		    'id' => $prefix . 'jobTitle',
		    'type' => 'text',
		    'after' => $univis_default['jobTitle'],
		   
		),
	    )
	);

	// Meta-Box Kontaktinformation - fau_person_info
	$meta_boxes['fau_person_info'] = array(
	    'id' => 'fau_person_info',
	    'title' => __( 'Kontaktinformationen', 'fau-person' ),
	    'object_types' => array('person'), // post type
	    //'show_on_cb' => array( 'key' => 'submenu-slug', 'value' => 'kontakt' ),        
	    'context' => 'normal',
	    'priority' => 'default',
	    'fields' => array(
		array(
		    'name' => __('Titel (Präfix)', 'fau-person'),
		    'desc' => '',
		    'type' => 'select',
		    'options' => array(
			'' => __('Keine Angabe', 'fau-person'),
			'Dr.' => __('Doktor', 'fau-person'),
			'Prof.' => __('Professor', 'fau-person'),
			'Prof. Dr.' => __('Professor Doktor', 'fau-person'),
			'PD' => __('Privatdozent', 'fau-person'),
			'PD Dr.' => __('Privatdozent Doktor', 'fau-person')
		    ),
		    'id' => $prefix . 'honorificPrefix',
		    'after' => $univis_default['honorificPrefix'],
		    'show_on_cb' => 'callback_cmb2_show_on_person'
		),
		array(
		    'name' => __('Vorname', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'givenName',
		    'after' => $univis_default['givenName'],
		    'show_on_cb' => 'callback_cmb2_show_on_person'
		),
		array(
		    'name' => __('Nachname', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'familyName',
		    'after' => $univis_default['familyName'],
		    'show_on_cb' => 'callback_cmb2_show_on_person'
		),
		array(
		    'name' => __('Bezeichnung (oder Pseudonym)', 'fau-person'),
		    'desc' => __('Wird für die Kategoriensortierung nach Nachname als Sortierkriterium verwendet.', 'fau-person'),
		    'type' => 'text',
		    'id' => $prefix . 'alternateName',
		    'show_on_cb' => 'callback_cmb2_show_on_einrichtung'
		),
		array(
		    'name' => __('Abschluss (Suffix)', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'honorificSuffix',
		    'after' => $univis_default['honorificSuffix'],
		    'show_on_cb' => 'callback_cmb2_show_on_person'
		),
		array(
		    'name' => __('Straße und Hausnummer', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'streetAddress',
		    'after' => $univis_default['streetAddress'] . $standort_default['streetAddress'] 
		),
		array(
		    'name' => __('Postleitzahl', 'fau-person'),
		    //'desc' => 'Wenn der Ort aus UnivIS übernommen werden soll bitte leer lassen!',
		    'desc' => __('Nur 5-stellige Zahlen erlaubt.', 'fau-person'),
		    'type' => 'text_small',
		    'id' => $prefix . 'postalCode',
		    'sanitization_cb' => 'validate_plz',
		    'after' => $standort_default['postalCode'] 
		),
		array(
		    'name' => __('Ort', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'addressLocality',
		    'after' => $univis_default['addressLocality'] . $standort_default['addressLocality'] 
		),
		array(
		    'name' => __('Land', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'addressCountry',
		    'after' => $standort_default['addressCountry'] 
		),
		array(
		    'name' => __('Raum', 'fau-person'),
		    'desc' => '',
		    'type' => 'text',
		    'id' => $prefix . 'workLocation',
		    'after' => $univis_default['workLocation'] 
		),
		array(
		    'name' => __('Standort Telefon- und Faxanschluss', 'fau-person'),
		    'desc' => '',
		    'type' => 'radio',
		    'id' => $prefix . 'telephone_select',
		    'options' => array(
			'erl' => __('Uni-intern, Standort Erlangen', 'fau-person'),
			'nbg' => __('Uni-intern, Standort Nürnberg', 'fau-person'),
			'standard' => __('Allgemeine Rufnummer', 'fau-person')
		    ),
		    'default' => 'standard'
		),
		array(
		    'name' => __('Telefon', 'fau-person'),
		    'desc' => __('Bitte geben Sie uni-interne Nummern für Erlangen in der internationalen Form +49 9131 85-22222 und für Nürnberg in der internationalen Form +49 911 5302-555 an.', 'fau-person'),
		    'type' => 'text',
		    'id' => $prefix . 'telephone',
		    'sanitization_cb' => 'validate_number',
		    'after' => $univis_default['telephone'] 
		),
		array(
		    'name' => __('Telefax', 'fau-person'),
		    'desc' => __('Bitte geben Sie uni-interne Nummern für Erlangen in der internationalen Form +49 9131 85-22222 und für Nürnberg in der internationalen Form +49 911 5302-555 an, uni-externe Nummern in der internationalen Form +49 9131 1111111.', 'fau-person'),
		    'type' => 'text',
		    'id' => $prefix . 'faxNumber',
		    'sanitization_cb' => 'validate_number',
		    'after' => $univis_default['faxNumber'] 
		),
		array(
		    'name' => __('Mobiltelefon', 'fau-person'),
		    'desc' => __('Bitte geben Sie die Nummer in der internationalen Form +49 176 1111111 an.', 'fau-person'),
		    'type' => 'text',
		    'id' => $prefix . 'mobilePhone'
		),
	    )
	);

	// Meta-Box Social Media - fau_person_social_media
	$meta_boxes['fau_person_social_media'] = array(
	    'id' => 'fau_person_social_media',
	    'title' => __('Social Media', 'fau-person'),
	    'object_types' => array('person'), // post type
	    'context' => 'normal',
	    'priority' => 'default',
	    'fields' => array(
		array(
		    'name' => __('E-Mail', 'fau-person'),
		    'desc' => '',
		    'type' => 'text_email',
		    'id' => $prefix . 'email',
		    'after' => $univis_default['email'] 
		),
		array(
		    'name' => __('Webseite', 'fau-person'),
		    'desc' => '',
		    'type' => 'text_url',
		    'id' => $prefix . 'url',
		    'after' => $univis_default['url'] 
		),
		array(
		    'name' => __('Name und "Mehr"-Link verlinken auf Seite ...', 'fau-person'),
		    'desc' => __('Bitte vollständigen Permalink der ausführlichen Kontaktseite angeben.', 'fau-person'),
		    'type' => 'text_url',
		    'id' => $prefix . 'link',
		    'after' => sprintf(__('<p class="cmb_metabox_description">[Standardwert wenn leer: %s]</p>', 'fau-person'), get_permalink( $person_id )),
		    //'after' => '<hr>' . __('Zum Anzeigen der Person verwenden Sie bitte die ID', 'fau-person') . ' ' . $helpuse,                
		)            
	    )
	);

	// Meta-Box Weitere Informationen - fau_person_adds
	$meta_boxes['fau_person_adds'] = array(
	    'id' => 'fau_person_adds',
	    'title' => __('Weitere Informationen', 'fau-person'),
	    'object_types' => array('person'), // post type
	    'context' => 'normal',
	    'priority' => 'default',
	    'fields' => array(
		array(
		    'name' => __('Kurzauszug', 'fau-person'),
		    'desc' => __('Wird bei der Anzeige in einer Sidebar verwendet, bis zu 160 Zeichen.', 'fau-person'),
		    'type' => 'textarea_small',
		    'id' => $prefix . 'description'
		),
		array(
		    'name' => __('Sprechzeiten: Überschrift', 'fau-person'),
		    'desc' => __('Wird in Fettdruck über den Sprechzeiten ausgegeben.', 'fau-person'),
		    'type' => 'text',
		    'id' => $prefix . 'hoursAvailable_text'                
		),
		array(
		    'name' => __('Sprechzeiten: Allgemeines oder Anmerkungen', 'fau-person'),
		    'desc' => __('Zur Formatierung können HTML-Befehle verwendet werden (z.B. &lt;br&gt; für Zeilenumbruch). Wird vor den Sprechzeiten ausgegeben.', 'fau-person'),
		    'type' => 'textarea_small',
		    'id' => $prefix . 'hoursAvailable'
		),
		array(
		    'id' => $prefix . 'hoursAvailable_group',
		    'type' => 'group',
		    'desc' => $univis_default['hoursAvailable_group'],
		    //'desc' => __('Bitte geben Sie die Sprechzeiten an.', 'fau-person'),
		    'options' => array(
			'group_title' => __('Sprechzeit {#}', 'fau-person'),
			'add_button' => __('Weitere Sprechzeit einfügen', 'fau-person'),
			'remove_button' => __('Sprechzeit löschen', 'fau-person'),
			//'sortable' => true,
		    ),
		    'fields' => array(
			array(
			    'name' =>  __('Wiederholung', 'fau-person'),
			    'id' => 'repeat',
			    'type' => 'radio_inline',
			    'options' => array(
				'd1' => __('täglich', 'fau-person'), 
				'w1' => __('wöchentlich', 'fau-person'),
				'w2' => __('alle 2 Wochen', 'fau-person'),
			    )
			),
			array(
			    'name' =>  __('am', 'fau-person'),
			    'id' => 'repeat_submode',
			    'type' => 'multicheck',
			    'options' => array(
				'1' => __('Montag', 'fau-person'),
				'2' => __('Dienstag', 'fau-person'),
				'3' => __('Mittwoch', 'fau-person'),
				'4' => __('Donnerstag', 'fau-person'),
				'5' => __('Freitag', 'fau-person'),
				'6' => __('Samstag', 'fau-person'),
				'7' => __('Sonntag', 'fau-person'),
			    )
			),
			array(
			    'name' =>  __('von', 'fau-person'),
			    'id' => 'starttime',
			    'type' => 'text_time',
			    'time_format' => 'H:i',
			),
			array(
			    'name' =>  __('bis', 'fau-person'),
			    'id' => 'endtime',
			    'type' => 'text_time',
			    'time_format' => 'H:i',
			),
			array(
			    'name' =>  __('Raum', 'fau-person'),
			    'id' => 'office',
			    'type' => 'text_small',
			),
			array(
			    'name' =>  __('Bemerkung', 'fau-person'),
			    'id' => 'comment',
			    'type' => 'text',
			),
		    ),

		), 

	    )   
	);
	
	
//  $meta_boxes['fau_person_gmail_sync'] = apply_filters('fau_person_gmail_metabox', array());

	// Meta-Box Synchronisierung mit externen Daten - fau_person_sync ab hier
	$meta_boxes['fau_person_sync'] = array(
	    'id' => 'fau_person_sync',
	    'title' => __('Daten aus UnivIS', 'fau-person'),
	    'object_types' => array('person'), // post type
	    'context' => 'side',
	    'priority' => 'high',
	    'fields' => array(
		array(
		    'name' => __('UnivIS-ID', 'fau-person'),
		    'desc' => 'UnivIS-ID der Person (8-stellige Zahl)',
		    'type' => 'text',
		    'id' => $prefix . 'univis_id',
		    'sanitization_cb' => 'validate_univis_id',
		    'show_on_cb' => 'callback_cmb2_show_on_person'
		),
		array(
		    'name' => __('UnivIS-Daten in Ausgabe anzeigen', 'fau-person'),
		    'desc' => __('Titel (Präfix), Vorname, Nachname, Abschluss (Suffix), Organisation bzw. Abteilung, Position/Funktion, Adresse, Telefon- und Telefaxnummer, E-Mail, Webseite. Die hier in diesen Feldern eingegebenen Werte werden in der Ausgabe nicht angezeigt.', 'fau-person'),
		    'type' => 'checkbox',
		    'id' => $prefix . 'univis_sync',
		    'after' => $univis_sync,
		    'show_on_cb' => 'callback_cmb2_show_on_person'
		),
	//	array(
	//	    'name' => __('UnivIS-OrgNr', 'fau-person'),
	//	    'desc' => __('Aktuell können noch keine Einrichtungsdaten aus UnivIS übernommen werden.', 'fau-person'),
	//	    'type' => 'text',
	//	    'id' => $prefix . 'univis_org_nr',
	//	    'show_on_cb' => 'callback_cmb2_show_on_einrichtung'
		    //'sanitization_cb' => 'validate_univis_id',
	//	),

	    )
	);


	// Meta-Box Synchronisierung mit externen Daten - fau_person_sync ab hier
	$meta_boxes['fau_person_options'] = array(
	    'id' => 'fau_person_options',
	    'title' => __('Zusatzoptionen', 'fau-person'),
	    'object_types' => array('person'), // post type
	    'context' => 'side',
	    'priority' => 'default',
	    'show_names' => true, // Show field names on the left
	    'fields' => array(
		array(
		    'name' => __('Typ des Eintrags', 'fau-person'),
		    //'desc' => __('Bei Einrichtungen und Pseudonymen wird die Bezeichnung angezeigt, ansonsten Vor- und Nachname.', 'fau-person'),
		    'type' => 'select',
		    'options' => array('realperson' => __('Person (allgemein)', 'fau-person'),
			'realmale' => __('Person (männlich)', 'fau-person'),
			'realfemale' => __('Person (weiblich)', 'fau-person'),
			'einrichtung' => __('Einrichtung', 'fau-person'),
			'pseudo' => __('Pseudonym', 'fau-person'),
		    ),
		    'id' => $prefix . 'typ',
		    'default' => $default_fau_person_typ
		),
		array(
		    'name' => __('Zugeordneter Standort', 'fau-person'),
		    //'desc' => 'Der Standort, von dem die Daten angezeigt werden sollen.',
		    'type' => 'select',
		    'id' => $prefix . 'standort_id',
		    'options' => $standortselect,
		),
		array(
		    'name' => __('Standort-Daten in Ausgabe anzeigen', 'fau-person'),
		    'desc' => __('Straße, Postleitzahl, Ort, Land. Die hier in diesen Feldern eingegebenen Werte werden in der Ausgabe nicht angezeigt.', 'fau-person'),
		    'type' => 'checkbox',
		    'id' => $prefix . 'standort_sync',
		    //'before' => $standort_sync,
		),
	    )
	);    

	
//	$meta_boxes['fau_person_gmail_contacts'] = apply_filters('fau_person_gmail_contacts', array());

	// Meta-Box um eine Kontaktperson oder -Einrichtung zuzuordnen
	$meta_boxes['fau_person_connection'] = array(
	    'id' => 'fau_person_connection',
	    'title' => __( 'Ansprechpartner / verknüpfte Kontakte', 'fau-person' ),
	    'object_types' => array('person'), // post type
	    'context' => 'normal',
	    'priority' => 'default',
	    'fields' => array(
		array(
		    'name' => __('Art der Verknüpfung', 'fau-person'),
		    'desc' => __('Der hier eingegebene Text wird vor der Ausgabe des verknüpften Kontaktes angezeigt (z.B. Vorzimmer, Kontakt über).', 'fau-person'),
		    'id' => $prefix . 'connection_text',
		    'type' => 'text',
		),
		array(
		    'name' => __('Verknüpfte Kontakte auswählen', 'fau-person'),
		    'desc' => '',
		    'id' => $prefix . 'connection_id',
		    'type' => 'select',
		    'options' => $contactselect_connection,
		    'repeatable' => true,
		),    
		array(
		    'name' => __('Angezeigte Daten der verknüpften Kontakte', 'fau-person'),
		    'desc' => '',
		    'id' => $prefix . 'connection_options',
		    'type' => 'multicheck',
		    'options' => array(
			'contactPoint' => __('Adresse', 'fau-person'),
			'telephone' => __('Telefon', 'fau-person'),
			'faxNumber' => __('Telefax', 'fau-person'),
			'email' => __('E-Mail', 'fau-person'),
			'hoursAvailable' => __('Sprechzeiten', 'fau-person'),
		    )
		),
		array(
		    'name' => __('Eigene Daten ausblenden', 'fau-person'),
		    'desc' => __('Ausschließlich die verknüpften Kontakte werden in der Ausgabe angezeigt.', 'fau-person'),
		    'type' => 'checkbox',
		    'id' => $prefix . 'connection_only',
		    //'before' => $standort_sync,
		),
	    )        
	);





	return $meta_boxes;
    }

        //Anzeigen des Feldes nur bei Personen
    function callback_cmb2_show_on_person( $field ) {
	$default_fau_person_typ = Data::default_fau_person_typ();
	$typ = get_post_meta($field->object_id, 'fau_person_typ', true);
	if( $typ == 'pseudo' || $typ == 'einrichtung' || $default_fau_person_typ == 'einrichtung') {
	    $person = false;
	} else {
	    $person = true;
	}
	return $person;
    }

    //Anzeigen des Feldes nur bei Einrichtungen
    function callback_cmb2_show_on_einrichtung( $field ) {
	$default_fau_person_typ = Data::default_fau_person_typ();
	$typ = get_post_meta($field->object_id, 'fau_person_typ', true);
	if( $typ == 'pseudo' || $typ == 'einrichtung' || $default_fau_person_typ == 'einrichtung') {
	    $einrichtung = true;
	} else {
	    $einrichtung = false;
	}
	return $einrichtung;
    }




}