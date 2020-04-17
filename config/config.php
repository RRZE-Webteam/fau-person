<?php

namespace FAU_Person\Config;

defined('ABSPATH') || exit;

/**
 * Gibt der Name der Option zurück.
 * @return array [description]
 */
function getOptionName() {
    return '_fau_person';
}

/**
 * Fixe und nicht aenderbare Plugin-Optionen
 * @return array 
 */
function getConstants() {
        $options = array(
	    'UnivIS_Transient' => 'sui_1k4fu7056Kl12a5',
	    'images' => [
		/* Thumb for person-type; small for sidebar - Name: person-thumb */
		'default_person_thumb_width'	=> 120,
		'default_person_thumb_height'	=> 160,
		'default_person_thumb_crop'	=> true,

		/* Thumb for person-type; small for content - Name: person-thumb-page */
		'default_person_thumb_page_width'   => 240,
		'default_person_thumb_page_height'  => 320,
		'default_person_thumb_page_crop'    => true,
	    ],
	    'has_archive_page'	=> true,
	    'fauthemes' => [
		'FAU-Einrichtungen', 
		'FAU-Philfak',
		'FAU-Natfak', 
		'FAU-RWFak', 
		'FAU-Medfak', 
		'FAU-Techfak',
		'FAU-Jobs'
		],

        );               
        // für ergänzende Optionen aus anderen Plugins
        $options = apply_filters('fau_person_constants', $options);
        return $options; // Standard-Array für zukünftige Optionen
    }

/**
 * Gibt die Einstellungen des Menus zurück.
 * @return array [description]
 */
function getMenuSettings() {
    return [
        'page_title'    => __('Kontakt', 'fau-person'),
        'menu_title'    => __('Kontakt', 'fau-person'),
        'capability'    => 'manage_options',
        'menu_slug'     => 'fau-person',
        'title'         => __('Kontakt Einstellungen', 'fau-person'),
    ];
}
function getSocialMediaList() {
	$SocialMedia = array(
	    "twitter" => [
		'title'  => 'Twitter',
		'class' => 'twitter'
	    ],
	    "facebook"=> [
		'title'  => 'Facebook',
		'class' => 'facebook'
	    ],
	    "linkedin"=> [
		'title'  => 'LinkedIn',
		'class' => 'linkedin'
	    ],
	    "instagram"=> [
		'title'  => 'Instagram',
		'class' => 'instagram'
	    ],
	    "xing"=> [
		'title'  => 'Xing',
		'class' => 'xing'
	    ],
	    "youtube"=> [
		'title'  => 'YouTube',
		'class' => 'youtube'
	    ],
	     "github"=> [
		'title'  => 'GitHub',
		'class' => 'github'
	    ],
	    "tiktok"=> [
		'title'  => 'TikTok',
		'class' => 'tiktok'
	    ]
	);

           // für ergänzende Optionen aus anderen Plugins
        $SocialMedia = apply_filters('fau_person_socialmedialist', $SocialMedia);
        return $SocialMedia; // Standard-Array für zukünftige Optionen
    } 
/**
 * Gibt die Einstellungen der Inhaltshilfe zurück.
 * @return array [description]
 */
function getHelpTab() {
    return [
        [
            'id'        => 'edit-person',
            'content'   => [
		'<p><strong>' . __('Einbindung der Kontakt-Visitenkarte über Shortcode', 'fau-person') . '</strong></p>',
		'<p>' . __('Binden Sie die gewünschten Kontaktdaten mit dem Shortcode [kontakt] mit folgenden Parametern auf Ihren Seiten oder Beiträgen ein:', 'fau-person') . '</p>',
		'<ol>',
		'<li>' . __('zwingend:', 'fau-person'),
		'<ul>',
		'<li>id: ' . __('ID des Kontakteintrags (erkennbar in der Metabox "Kontaktinformationen" auf den Seiten)', 'fau-person') . '</li>',
		'</ul>', 
		'</li>',
		'<li>' . __('format="..." (optional), je nach Wert unterscheiden sich die Ausgabedarstellung und die angezeigten Standardparameter:', 'fau-person'),        
		'<ul>',
		'<li>name: ' . __('Ausgabe von Titel, Vorname, Nachname und Suffix (sofern vorhanden) im Fließtext mit Link auf die Kontaktseite der Person', 'fau-person') . '</li>',
		'<li>page: ' . __('vollständige Ausgabe des ganzen Kontaktes wie bei der Kontakt-Einzelseite, die Parameter show und hide haben hierauf keinen Einfluss', 'fau-person') . '</li>',
		'<li>sidebar: ' . __('Ausgabe wie bei der Anzeige in der Sidebar im Theme', 'fau-person') . '</li>',
		'<li>liste: ' . __('Ausgabe der Namen mit Listenpunkten, unten drunter Kurzbeschreibung', 'fau-person') . '</li>',
		'</ul>',
		'</li>',
		'<li>' . __('show="..." bzw. hide="..." (optional), wenn ein zusätzliches Feld zu den Standardfeldern angezeigt werden soll bzw. die Anzeige eines Standardfeldes nicht gewünscht ist:', 'fau-person'),    
		'<ul>',
		'<li>kurzbeschreibung: ' . __('Standardanzeige bei format="liste" (wennn das Feld leer ist wird dann der Anfang des Inhaltsbereiches angezeigt)', 'fau-person') . '</li>',
		'<li>organisation: ' . __('Standardanzeige ohne format-Angabe, bei format="page" (und bei Widget)', 'fau-person') . '</li>',
		'<li>abteilung: ' . __('Standardanzeige ohne format-Angabe, bei format="page" (und bei Widget)', 'fau-person') . '</li>',
		'<li>postition: ' . __('Standardanzeige ohne format-Angabe, bei format="page" (und bei Widget)', 'fau-person') . '</li>',
		'<li>titel: ' . __('Standardanzeige ohne format-Angabe, bei format="name", "page", "sidebar", "liste" (und bei Widget)', 'fau-person') . '</li>',      
		'<li>suffix: ' . __('Standardanzeige ohne format-Angabe, bei format="name", "page", "sidebar", "liste" (und bei Widget)', 'fau-person') . '</li>',
		'<li>adresse: ' . __('Standardanzeige bei format="page" (und bei Widget)', 'fau-person') . '</li>',
		'<li>raum: ' . __('Standardanzeige bei format="page" (und bei Widget)', 'fau-person') . '</li>',
		'<li>telefon: ' . __('Standardanzeige ohne format-Angabe, bei format="page", "sidebar" (und bei Widget)', 'fau-person') . '</li>',
		'<li>fax: ' . __('Standardanzeige bei format="page" (und bei Widget)', 'fau-person') . '</li>',
		'<li>mobil: ' . __('keine Standardanzeige', 'fau-person') . '</li>',
		'<li>mail: ' . __('Standardanzeige ohne format-Angabe, bei format="page", "sidebar" (und bei Widget)', 'fau-person') . '</li>',  
		'<li>webseite: ' . __('Standardanzeige bei format="page", "sidebar" (und bei Widget)', 'fau-person') . '</li>',            
		'<li>mehrlink: ' . __('keine Standardanzeige', 'fau-person') . '</li>',
		'<li>kurzauszug: ' . __('Standardanzeige bei format="sidebar" (und bei Widget)', 'fau-person') . '</li>', 
		'<li>sprechzeiten: ' . __('Standardanzeige bei format="page"', 'fau-person') . '</li>',            
		'<li>publikationen: ' . __('Standardanzeige bei format="page"', 'fau-person') . '</li>',
		'<li>bild: ' . __('Standardanzeige bei format="page", "sidebar" (und bei Widget)', 'fau-person') . '</li>', 
		'</ul>',
		'</li>',            
		'</ol>',
            ],
            'title'     => __('Übersicht', 'fau-person'),
	],
	[
	    'id' => 'edit-persons_category',
	    'title' => __('Übersicht', 'fau-person'),
	    'content' => ['<p><strong>' . __('Zuordnung von Personen und Kontakten zu verschiedenen Kategorien', 'fau-person') . '</strong></p>'],
	  
	],
	[
	    'id' => 'person',
	    'title' => __('Kontakt eingeben', 'fau-person'),
	    'content' => ['<p>' . __('Geben Sie auf dieser Seite alle gewünschten Daten zu einem Kontakt ein. Die Einbindung der Kontaktdaten erfolgt dann in den Beiträgen oder Seiten über einen Shortcode oder ein Widget.', 'fau-person') . '</p>'],
	  
	],
	[
	    'id' => 'person_page_search-univis-id',
	    'title' => __('UnivIS Id suchen', 'fau-person'),
	    'content' => [
		'<p>' . __('Geben Sie hier den Vor- oder den Nachnamen der Person ein. Es kann auch beides oder nur Namensteile eingegeben werden. Bitte beachten Sie, dass Umlaute bei der Eingabe aufgelöst werden müssen.', 'fau-person') . '</p>',
		'<p>' . __('Mit <i>Person suchen</i> erhalten Sie eine Auflistung aller möglichen Personen. Suchen Sie die richtige Person aus der Liste heraus, markieren Sie die UnivIS-ID, kopieren Sie diese mit Strg+C und fügen Sie dann beim entsprechenden Kontakt im Feld <i>UnivIS-ID</i> ein.', 'fau-person') . '</p>'],
	  
	],
	[
	    'id' => 'fau-person-sidebar',
	    'title' => __('Anzeigeoptionen', 'fau-person'),
	    'content' => [
		 '<p>' . __('In den Anzeigeoptionen kann die standardmäßige Ausgabe bei Nutzung des [kontakt] Shortcodes definiert werden. Diese kann durch Attribute wieder überschrieben werden.', 'fau-person') . '</p>'

	    ]	  
	]
    ];
}
function getHelpTabSidebar() {
    return sprintf('<p><strong>%1$s:</strong></p><ul>'
		. '<li><a href="https://www.wordpress.rrze.fau.de/plugins/fau-und-rrze-plugins/fau-person/">%2$s</a></li>'
		. '<li><a href="https://blogs.fau.de/webworking">RRZE Webworking</a></li>'
		. '</ul>', __('Weitere Informationen', 'fau-person'), __('Plugin Dokumentation', 'fau-person'));
}
/**
 * Gibt die Einstellungen der Optionsbereiche zurück.
 * @return array [description]
 */
function getSections() {
    return [

	[
            'id'    => 'sidebar',
            'title' => __('Sidebar Kontakte', 'fau-person')
        ],

	[
            'id'    => 'constants',
            'title' => __('Erweiterte Einstellungen', 'fau-person')
        ],
      
    ];
}

/**
 * Gibt die Einstellungen der Optionsfelder zurück.
 * @return array [description]
 */
function getFields() {
    $imagesizes = array();
    $isizes = get_all_image_sizes();
	
    foreach ($isizes as $key => $value) {
	if (($value['width'] > 0) && ($value['height'] > 0)) {
	    $name = ucfirst($key);
	    $imagesizes[$key] = $name. ' ('.$value['width'].' x '.$value['height'].')';
	}
    }
    
    
    return [
	'sidebar' => [
	    [
		'name'  => 'position',
		'label' => __('Position', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
            ],
	    [
		'name'  => 'organisation',
		'label' => __('Organisation', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
            ],
	    [
		'name'  => 'abteilung',
		'label' => __('Abteilung', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
            ],
	    [
		'name'  => 'adresse',
		'label' => __('Adresse', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
            ],
	    [
		'name'  => 'telefon',
		'label' => __('Telefonnummer', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
            ],
	     [
		'name'  => 'mobil',
		'label' => __('Handynummer anzeigen', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
            ],
	     [
		'name'  => 'fax',
		'label' => __('Faxnummer', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => false,
		 'default' => false,
             ],
	    [
		'name'  => 'mail',
		'label' => __('E-Mail-Adresse', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
             ],
	    [
		'name'  => 'webseite',
		'label' => __('Website', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
             ],
	     [
		'name'  => 'sprechzeiten',
		'label' => __('Sprechzeiten', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		 'default' => true,
             ],
	     [
		'name'  => 'kurzauszug',
		'label' => __('Kurzbeschreibung', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		 'default' => true,
             ],
	    [
		'name'  => 'bild',
		'label' => __('Bild', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => false,
            ],
	    [
		'name'  => 'socialmedia',
		'label' => __('Social Media Links', 'fau-person'),
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
            ],
	     [
		'name'  => 'ansprechpartner',
		'label' => __('Ansprechpartner', 'fau-person'),
		 'desc'=> __('Im Sidebar-Widget werden nur dann Ansprechpartner gezeigt, wenn dieser Wert aktiviert ist oder alternativ bei dem Personeneintrag eingestellt ist, daß der Kontakt ausschließlich über angegebene Ansprechpartner erfolgt.','fau-person'),
		'type'  => 'checkbox',
		'checked'   => false,
		'default' => false,
            ],
	],
	
	'constants' => [
	     [
		'name'  => 'view_telefonlink',
		'label' => __('Telefonnummer als Link', 'fau-person'),
		'desc'  => __('Setzt die Telefonnummer als Link, so dass mobile Endgeräte und darauf vorbereitet Software bei einem Klick die Telefonwahlfunktion aufrufen.', 'fau-person'),
		'type'  => 'checkbox',
		'default' => true,
            ],
	     [
		'name'  => 'view_telefon_intformat',
		'label' => __('Internationales Nummernformat', 'fau-person'),
		'desc'  => __('Die Telefonnnummer wird in dem internationalen Format angezeigt.', 'fau-person'),
		'type'  => 'checkbox',
		'default' => true,
            ],
	     [
		'name'  => 'view_raum_prefix',
		'default' => __('Raum', 'fau-person'),
		'placeholder' => __('Raum', 'fau-person'),
		'label' => __('Anzuzeigender Text vor der Raumangabe', 'fau-person'),
		'field_type' => 'text',
		'type' => 'text' 
	    ],
	    [
		'name'  => 'view_kontakt_linktext',
		'default' => __('Mehr', 'fau-person') . ' ›',
		'placeholder' => __('Mehr', 'fau-person') . ' ›',
		'label' => __('Linktext für Kontaktseite', 'fau-person'),
		'field_type' => 'text',
		'type' => 'text' 
	    ],
	    [
		'name'  => 'view_kontakt_linkname',
		'label' => __('Link auf Kontaktname', 'fau-person'),		
		'default' => 'permalink',
		'type' => 'Select', 
		'options' => [
			'permalink' => __( 'Kontaktseite', 'fau-person' ),
			'url' => __( 'URL aus Profil', 'fau-person' ),
			''  => __( 'Nicht verlinken', 'fau-person' ),
		],
	    ],
	    [
		'name'  => 'view_kontakt_page_imagecaption',
		'label' => __('Bildbeschrift Kontaktseite', 'fau-person'),	
		'desc'  => __('Zeigt auf der Kontaktvisitenkarte und bei Nutzung des Shortcodes mit dem Attribut <code>format="page"</code> die Bildunterschriften eines Kontaktbildes an.', 'fau-person'),
		'default' => 'permalink',
		'type'  => 'checkbox',
		'checked'   => true,
		'default' => true,
	    ],
	     [
		'name'  => 'view_kontakt_page_imagesize',
		'label' => __('Bildformat Kontaktseite', 'fau-person'),	
		'desc'  => __('Setzt auf der Kontaktseite oder bei Nutzung des Shortcodes mit dem Attribut <code>format="page"</code> das zu verwendete Bildformat.', 'fau-person'),
		'default' => 'person-thumb-page',
		'type' => 'Selectimagesizes', 
		'options' => $imagesizes,
	    ],

	    [
		'name'  => 'has_archive_page',
		'label' => __('Kontakt-Übersichtsseite', 'fau-person'),
		'desc'  => __('Zeige die Standard-Übersichtsseite aller Kontakte an. Bevor diese Option deaktiviert wird, muss eine eigene Seite mit der Titelform (slug) "person" direkt unterhalb der Hauptebene angelegt werden.', 'fau-person'),
		'type'  => 'checkbox',
		'default' => true,
            ],
	],
	
       
    ];
}

function get_all_image_sizes() {
   
   $image_sizes = array();

   
    $ownsizes = getConstants();
    if (isset($ownsizes['images']['default_person_thumb_width' ])) {
	$image_sizes['person-thumb']['width'] = $ownsizes['images']['default_person_thumb_width' ];
	$image_sizes['person-thumb']['height'] = $ownsizes['images']['default_person_thumb_height'];

    }
    if (isset($ownsizes['images']['default_person_thumb_page_width' ])) {
	$image_sizes['person-thumb-page']['width'] = $ownsizes['images']['default_person_thumb_page_width' ];
	$image_sizes['person-thumb-page']['height'] = $ownsizes['images']['default_person_thumb_page_height'];
    }
    
   
    
    
    return $image_sizes;
}


/**
 * Gibt die Default-Werte eines gegebenen Feldes aus den Shortcodesettings zurück
 * @return array [description]
 */
function getShortcodeDefaults($field = ''){
    if (empty($field)) {
	return;
    }
    $settings = getShortcodeSettings();
    if (!isset($settings[$field])) {
	return;
    }
    $res = array();
    foreach ($settings[$field] as $fieldname => $value ) {
	$res[$fieldname] = $value['default'];
    }
    return $res;
}

/**
 * Gibt die Einstellungen der Parameter für Shortcode für den klassischen Editor und für Gutenberg zurück.
 * @return array [description]
 */

function getShortcodeSettings(){
    return [
	    'kontakt' => [
	       'id' => [
		    'default' => 0,
		    'label' => __( 'Id-Number des Kontakteintrags', 'fau-person' ),
		    'message' => __( 'Nummer der Eintrags der Kontaktliste im Backend. Nicht identisch mit einer optionalen UnivIS-Nummer.', 'fau-person' ), 
		   'field_type' => 'number',
		    'type' => 'key'
	       ],
		'slug' => [
			'default' => '',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Slug (URI) des Kontakteintrags', 'fau-person' ),
			'type' => 'text' // Variablentyp der Eingabe
		],
		'category' => [
			'default' => '',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Kategorie', 'fau-person' ),
			'type' => 'text' // Variablentyp der Eingabe
		],
		
		'format' => [
			'default' => '',
			'field_type' => 'select',
			'label' => __( 'Format', 'fau-person' ),
			'type' => 'array',
			'values' => [
				'name' => __( 'Name', 'fau-person' ),
				'shortlist' => __( 'Kurzliste', 'fau-person' ),
				'full' => __( 'Komplett', 'fau-person' ),
				'sidebar' => __( 'Sidebar', 'fau-person' ),
				'page' => __( 'Seite', 'fau-person' ),
				'liste' => __( 'Liste', 'fau-person' ),
				'listentry' => __( 'Listeneintrag', 'fau-person' ),
				'plain' => __( 'Unformatiert', 'fau-person' ),
				'kompakt' => __( 'Kompakt', 'fau-person' ),
				'compactindex' => __( 'Kompakter Index', 'fau-person' ),

			],
		],
		'show' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Anzuzeigende Felder, obige Checkboxen überschreibend', 'fau-person' ),
			'type' => 'string' 
		],
		'hide' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Zu versteckende Felder, obige Checkboxen überschreibend', 'fau-person' ),
			'type' => 'string' 
		],
		'sort' => [
			'default' => 'title',
			'field_type' => 'select',
			'label' => __( 'Bei der Ausgabe mehrerer Kontakten kann nach diesem Feld sortiert werden.', 'fau-person' ),
			'type' => 'array',
			'values' => [
				'title' => __( 'Titel', 'fau-person' ),
				'nachname' => __( 'Nachname', 'fau-person' ),
			    'name' => __( 'Vorname und Nachname', 'fau-person' ),

			],
		],
		'order' => [
			'default' => 'asc',
			'field_type' => 'select',
			'label' => __( 'Bei der Ausgabe mehrerer Kontakten kann nach diesem Feld sortiert werden.', 'fau-person' ),
			'type' => 'array',
			'values' => [
				'asc' => __( 'Von A bis Z', 'fau-person' ),
				'desc' => __( 'Von Z bis A', 'fau-person' ),
			],
		],
		'border' => [
			'default' => true,
			'field_type' => 'checkbox',
			'label' => __( 'Rahmen anzeigen', 'fau-person' ),
			'type' => 'boolean' 
		],
		'hstart' => [
			'default' => 3,
			'field_type' => 'number',
			'label' => __( 'Überschriftenebene der ersten Überschrift', 'fau-person' ),
			'type' => 'integer' 
		],
		'class' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'CSS Klassen, die der Shordcode erhalten soll.', 'fau-person' ),
			'type' => 'string' 
		],
		'background' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Farbcode für den Hintergrund.', 'fau-person' ),
			'type' => 'array',
			'values' => [
				'' => __( 'Kein', 'fau-person' ),
				'med' => __( 'Med: Blau', 'fau-person' ),
				'phil' => __( 'Phil: Oker', 'fau-person' ),
				'tf' => __( 'TF: Silbern', 'fau-person' ),
				'nat' => __( 'Nat: Meeresgrün', 'fau-person' ),
				'rw' => __( 'RW: Bordeaurot', 'fau-person' ),
				'fau' => __( 'FAU: Dunkelblau', 'fau-person' ),

			],
		],
	    ],
	    'kontaktliste' => [
	       'category' => [
			'default' => '',
			'field_type' => 'text', 
			'label' => __( 'Kategorie', 'fau-person' ),
			'type' => 'text' // Variablentyp der Eingabe
		],
		
		'format' => [
			'default' => '',
			'field_type' => 'select',
			'label' => __( 'Format', 'fau-person' ),
			'type' => 'array',
			'values' => [
				'name' => __( 'Name', 'fau-person' ),
				'shortlist' => __( 'Kurzliste', 'fau-person' ),
				'full' => __( 'Komplett', 'fau-person' ),
				'sidebar' => __( 'Sidebar', 'fau-person' ),
				'liste' => __( 'Liste', 'fau-person' ),
				'listentry' => __( 'Listeneintrag', 'fau-person' ),
				'plain' => __( 'Unformatiert', 'fau-person' ),
				'kompakt' => __( 'Kompakt', 'fau-person' ),

			],
		],
		'show' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Anzuzeigende Felder, obige Checkboxen überschreibend', 'fau-person' ),
			'type' => 'string' 
		],
		'hide' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Zu versteckende Felder, obige Checkboxen überschreibend', 'fau-person' ),
			'type' => 'string' 
		],
		'sort' => [
			'default' => 'title',
			'field_type' => 'select',
			'label' => __( 'Bei der Ausgabe mehrerer Kontakten kann nach diesem Feld sortiert werden.', 'fau-person' ),
			'type' => 'array',
			'values' => [
				'title' => __( 'Titel', 'fau-person' ),
				'nachname' => __( 'Nachname', 'fau-person' ),
				'name' => __( 'Vorname und Nachname', 'fau-person' ),

			],
		],
		'order' => [
			'default' => 'asc',
			'field_type' => 'select',
			'label' => __( 'Bei der Ausgabe mehrerer Kontakten kann nach diesem Feld sortiert werden.', 'fau-person' ),
			'type' => 'array',
			'values' => [
				'asc' => __( 'Von A bis Z', 'fau-person' ),
				'desc' => __( 'Von Z bis A', 'fau-person' ),
			],
		],
		'hstart' => [
			'default' => 3,
			'field_type' => 'number',
			'label' => __( 'Überschriftenebene der ersten Überschrift', 'fau-person' ),
			'type' => 'integer' 
		],
		'class' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'CSS Klassen, die der Shordcode erhalten soll.', 'fau-person' ),
			'type' => 'string' 
		],
		'background' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Farbcode für den Hintergrund.', 'fau-person' ),
			'type' => 'array',
			'values' => [
				'' => __( 'Kein', 'fau-person' ),
				'med' => __( 'Med: Blau', 'fau-person' ),
				'phil' => __( 'Phil: Oker', 'fau-person' ),
				'tf' => __( 'TF: Silbern', 'fau-person' ),
				'nat' => __( 'Nat: Meeresgrün', 'fau-person' ),
				'rw' => __( 'RW: Bordeaurot', 'fau-person' ),
				'fau' => __( 'FAU: Dunkelblau', 'fau-person' ),

			],
		],

	    ],
	    'standort' => [
		'id' => [
		    'default' => 0,
		    'label' => __( 'Id-Number des Standorteintrags', 'fau-person' ),
		    'message' => __( 'Nummer der Eintrags im Backend.', 'fau-person' ), 
		   'field_type' => 'number',
		    'type' => 'key'
	       ],
		'slug' => [
			'default' => '',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Slug (URI) des Kontakteintrags', 'fau-person' ),
			'type' => 'text' // Variablentyp der Eingabe
		],
		'titletag' => [
			'default' => 'h2',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'HTML-Element zur Darstellung des Standortnamens', 'fau-person' ),
			'type' => 'text' // Variablentyp der Eingabe
		],
		'adresse' => [
			'default' => true,
			'field_type' => 'checkbox',
			'label' => __( 'Telefonnummer anzeigen', 'fau-person' ),
			'type' => 'boolean' 
		],
		'format' => [
			'default' => '',
			'field_type' => 'select',
			'label' => __( 'Format', 'fau-person' ),
			'type' => 'array',
			'values' => [
				'name' => __( 'Name', 'fau-person' ),
				'shortlist' => __( 'Kurzliste', 'fau-person' ),
				'full' => __( 'Komplett', 'fau-person' ),
				'sidebar' => __( 'Sidebar', 'fau-person' ),
				'liste' => __( 'Liste', 'fau-person' ),

			],
		],
		'show' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Anzuzeigende Felder, obige Checkboxen überschreibend', 'fau-person' ),
			'type' => 'string' 
		],
		'hide' => [
			'default' => '',
			'field_type' => 'text',
			'label' => __( 'Zu versteckende Felder, obige Checkboxen überschreibend', 'fau-person' ),
			'type' => 'string' 
		],

	    ]

    ];
    

    /*
    
	return [
		'block' => [
			'name' => 'FAU_Person/kontakt', // dieser Wert muss angepasst werden
			'title' => 'Kontakt Shortcode', // Der Titel, der in der Blockauswahl im Gutenberg Editor angezeigt wird
			'category' => 'widgets', // Die Kategorie, in der der Block im Gutenberg Editor angezeigt wird
			'icon' => 'admin-users',  // Das Icon des Blocks
			'message' => __( 'Find the settings on the right side', 'fau-person' ) // erscheint bei Auswahl des Blocks
		],
		'Beispiel-Textfeld-Text' => [
			'default' => 'ein Beispiel-Wert',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Beschriftung', 'fau-person' ),
			'type' => 'text' // Variablentyp der Eingabe
		],
		'Beispiel-Textfeld-Number' => [
			'default' => 0,
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Beschriftung', 'fau-person' ),
			'type' => 'number' // Variablentyp der Eingabe
		],
		'Beispiel-Textarea-String' => [
			'default' => 'ein Beispiel-Wert',
			'field_type' => 'textarea',
			'label' => __( 'Beschriftung', 'fau-person' ),
			'type' => 'string',
			'rows' => 5 // Anzahl der Zeilen 
		],
		'Beispiel-Radiobutton' => [
			'values' => [
				'wert1' => __( 'Wert 1', 'fau-person' ), // wert1 mit Beschriftung
				'wert2' => __( 'Wert 2', 'fau-person' )
			],
			'wert2' => 'DESC', // vorausgewählter Wert
			'field_type' => 'radio',
			'label' => __( 'Order', 'fau-person' ), // Beschriftung der Radiobutton-Gruppe
			'type' => 'string' // Variablentyp des auswählbaren Werts
		],
		'Beispiel-Checkbox' => [
			'field_type' => 'checkbox',
			'label' => __( 'Beschriftung', 'fau-person' ),
			'type' => 'boolean',
			'checked'   => true // Vorauswahl: Haken gesetzt
        ],
        'Beispiel-Toggle' => [
            'field_type' => 'toggle',
            'label' => __( 'Beschriftung', 'fau-person' ),
            'type' => 'boolean',
            'checked'   => true // Vorauswahl: ausgewählt
        ],
        'Beispiel-Select' => [
			'values' => [
				'wert1' => __( 'Wert 1', 'fau-person' ),
				'wert2' => __( 'Wert 2', 'fau-person' )
			],
			'default' => 'wert1', // vorausgewählter Wert: Achtung: string, kein array!
			'field_type' => 'select',
			'label' => __( 'Beschrifung', 'fau-person' ),
			'type' => 'string' // Variablentyp des auswählbaren Werts
		],
        'Beispiel-Multi-Select' => [
			'values' => [
				'wert1' => __( 'Wert 1', 'fau-person' ),
				'wert2' => __( 'Wert 2', 'fau-person' ),
				'wert3' => __( 'Wert 2', 'fau-person' )
			],
			'default' => ['wert1','wert3'], // vorausgewählte(r) Wert(e): Achtung: array, kein string!
			'field_type' => 'multi_select',
			'label' => __( 'Beschrifung', 'fau-person' ),
			'type' => 'array',
			'items'   => [
				'type' => 'string' // Variablentyp der auswählbaren Werte
			]
        ]
    ];*/
}

