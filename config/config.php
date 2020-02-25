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
		'default_person_thumb_width'	=> 60,
		'default_person_thumb_height'	=> 80,
		'default_person_thumb_crop'	=> true,

		/* Thumb for person-type; small for content - Name: person-thumb-bigger */
		'default_person_thumb_bigger_width' => 90,
		'default_person_thumb_bigger_height'	=> 120,
		'default_person_thumb_bigger_crop'  => true,
		
		/* Thumb for person-type; small for content - Name: person-thumb-page */
		'default_person_thumb_page_width'   => 200,
		'default_person_thumb_page_height'  => 300,
		'default_person_thumb_page_crop'    => true,
	    ],
	    
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
            'title' => __('Anzeigeoptionen (Widget Kontakt-Visitenkarte)', 'fau-person')
        ],
	[
            'id'    => 'indexpage',
            'title' => __('Indexseiten', 'fau-person')
        ],
      
    ];
}

/**
 * Gibt die Einstellungen der Optionsfelder zurück.
 * @return array [description]
 */
function getFields() {
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
	],
	'indexpage' => [
	    [
		'name'  => 'checkbox',
		'label' => __('Kontakt-Übersichtsseite', 'fau-person'),
		'desc'  => __('Zeige die Standard-Übersichtsseite aller Kontakte an. Bevor diese Option deaktiviert wird, muss eine eigene Seite mit der Titelform (slug) "person" direkt unterhalb der Hauptebene angelegt werden.', 'fau-person'),
		'type'  => 'checkbox',
		'default' => true,
            ],
	],
	
        'basic' => [
	    /*
            [
                'name'              => 'text_input',
                'label'             => __('Text Input', 'fau-person'),
                'desc'              => __('Text input description.', 'fau-person'),
                'placeholder'       => __('Text Input placeholder', 'fau-person'),
                'type'              => 'text',
                'default'           => 'Title',
                'sanitize_callback' => 'sanitize_text_field'
            ],
            [
                'name'              => 'number_input',
                'label'             => __('Number Input', 'fau-person'),
                'desc'              => __('Number input description.', 'fau-person'),
                'placeholder'       => '5',
                'min'               => 0,
                'max'               => 100,
                'step'              => '1',
                'type'              => 'number',
                'default'           => 'Title',
                'sanitize_callback' => 'floatval'
            ],
            [
                'name'        => 'textarea',
                'label'       => __('Textarea Input', 'fau-person'),
                'desc'        => __('Textarea description', 'fau-person'),
                'placeholder' => __('Textarea placeholder', 'fau-person'),
                'type'        => 'textarea'
            ],
            [
                'name'  => 'checkbox',
                'label' => __('Checkbox', 'fau-person'),
                'desc'  => __('Checkbox description', 'fau-person'),
                'type'  => 'checkbox'
            ],
            
            [
                'name'    => 'animation-duration',
                'label'   => __('Animation Duration', 'fau-person'),
                'desc'    => __('How many snow will come.', 'fau-person'),
                'type'    => 'select',
                'default' => 'infinite',
                'options' => [
                    'infinite' => __('Infinite snowflakes', 'fau-person'),
		  '10'  => __('560 Snowflakes', 'fau-person'),
                    '20'  => __('1.160 Snowflakes', 'fau-person'),
		  '100'  => __('5.600 Snowflakes', 'fau-person'),
		  '1000'  => __('56.000 Snowflakes', 'fau-person')
                ]
            ]

	     */
        ],
       
    ];
}




/**
 * Gibt die Einstellungen der Parameter für Shortcode für den klassischen Editor und für Gutenberg zurück.
 * @return array [description]
 */

function getShortcodeSettings(){
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
    ];
}

