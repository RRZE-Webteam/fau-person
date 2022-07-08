<?php

namespace FAU_Person\Metaboxes;

use FAU_Person\Data;
use RRZE\Lib\UnivIS\Data as UnivIS_Data;
use function FAU_Person\Config\getSocialMediaList;

defined('ABSPATH') || exit;

/**
 * Define Metaboxes for Kontakt-Edit
 */
class Kontakt extends Metaboxes
{

    protected $pluginFile;
    private $settings = '';

    public function __construct($pluginFile, $settings)
    {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }

    public function onLoaded()
    {
        require_once(plugin_dir_path($this->pluginFile) . 'vendor/UnivIS/UnivIS.php');
        require_once(plugin_dir_path($this->pluginFile) . 'vendor/DIP/DIP.php');
        add_filter('cmb2_meta_boxes', array($this, 'cmb2_kontakt_metaboxes'));
    }


    public function cmb2_kontakt_metaboxes($meta_boxes)
    {
        $prefix = $this->prefix;

        $contactselect_connection = Data::get_contactdata(1);
        $standortselect =  Data::get_standortdata();
        $default_fau_person_typ = Data::get_default_fau_person_typ();

        $person_id = 0;

        if (isset($_GET['post'])) {
            $person_id = intval($_GET['post']);
        } elseif (isset($_POST['post_ID'])) {
            $person_id = intval($_POST['post_ID']);
        }

        $univis_id = get_post_meta($person_id, 'fau_person_univis_id', true);
        $univisdata = Data::get_fields($person_id, $univis_id, 0, false, true);

        if ($univisdata) {
            $univis_sync = '';
        } else {
            $univis_sync = '<p class="cmb2-metabox-description">' . __('Derzeit sind keine Daten aus UnivIS syncronisiert.', 'fau-person') . '</p>';
        }
        $standort_default = Data::get_standort_defaults($person_id);
        $univis_default = Data::univis_defaults($person_id);

        $defaultkurzauszug = '';
        if (get_post_field('post_excerpt', $person_id)) {
            $defaultkurzauszug  = get_post_field('post_excerpt', $person_id);
        }
        // Meta-Box Weitere Informationen - fau_person_adds
        $meta_boxes['fau_person_textinfos'] = array(
            'id' => 'fau_person_textinfos',
            'title' => __('Kontakt Beschreibung in Kurzform', 'fau-person'),
            'object_types' => array('person'), // post type
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __('Kurzbeschreibung', 'fau-person'),
                    'desc' => __('Kurzform und Zusammenfassung der Kontaktbeschreibung bei Nutzung des Attributs <code>show="description"</code>.', 'fau-person'),
                    'type' => 'textarea_small',
                    'id' => $prefix . 'description',
                    'default'    => $defaultkurzauszug
                ),

                array(
                    'name' => __('Kurzbeschreibung (Sidebar und Kompakt)', 'fau-person'),
                    'desc' => __('Diese Kurzbeschreibung wird bei der Anzeige von <code>show="description"</code> in einer Sidebar (<code>format="sidebar"</code>) oder einer Liste (<code>format="kompakt"</code>) verwendet.', 'fau-person'),
                    'type' => 'textarea_small',
                    'id' => $prefix . 'small_description',
                    'default'    => $defaultkurzauszug
                ),

            )
        );



        // Meta-Box Kontaktinformation - fau_person_info
        $meta_boxes['fau_person_info'] = array(
            'id' => 'fau_person_info',
            'title' => __('Kontaktinformationen', 'fau-person'),
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
                        'Prof. em.' => __('Professor (Emeritus)', 'fau-person'),
                        'Prof. Dr. em.' => __('Professor Doktor (Emeritus)', 'fau-person'),
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
                    'show_on_cb' => 'callback_cmb2_show_on_person',
                    'attributes'  => array(
                        'placeholder' => $univisdata['givenName'],
                    ),
                ),
                array(
                    'name' => __('Nachname', 'fau-person'),
                    'desc' => '',
                    'type' => 'text',
                    'id' => $prefix . 'familyName',
                    'after' => $univis_default['familyName'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['familyName'],
                    ),
                    'show_on_cb' => 'callback_cmb2_show_on_person'
                ),

                array(
                    'name' => __('Abschluss (Suffix)', 'fau-person'),
                    'desc' => '',
                    'type' => 'text',
                    'id' => $prefix . 'honorificSuffix',
                    'after' => $univis_default['honorificSuffix'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['honorificSuffix'],
                    ),
                    'show_on_cb' => 'callback_cmb2_show_on_person'
                ),
                array(
                    'name' => __('Position/Funktion', 'fau-person'),
                    'desc' => '',
                    'id' => $prefix . 'jobTitle',
                    'type' => 'text',
                    'after' => $univis_default['jobTitle'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['jobTitle'],
                    ),
                ),
                array(
                    'name' => __('Organisation', 'fau-person'),
                    'desc' => __('Geben Sie hier die Organisation (Lehrstuhl oder Einrichtung) ein.', 'fau-person'),
                    'type' => 'text',
                    'id' => $prefix . 'worksFor',
                    'after' => $univis_default['worksFor'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['worksFor'],
                    ),
                ),
                array(
                    'name' => __('Abteilung', 'fau-person'),
                    'desc' => __('Geben Sie hier die Abteilung oder Arbeitsgruppe ein.', 'fau-person'),
                    'type' => 'text',
                    'id' => $prefix . 'department',
                    'after' => $univis_default['department'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['department'],
                    ),

                ),


                array(
                    'name' => __('Raum', 'fau-person'),
                    'desc' => '',
                    'type' => 'text',
                    'id' => $prefix . 'workLocation',
                    'after' => $univis_default['workLocation'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['workLocation'],
                    ),
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
                    'after' => $univis_default['telephone'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['telephone'],
                    ),
                ),
                array(
                    'name' => __('Telefax', 'fau-person'),
                    'desc' => __('Bitte geben Sie uni-interne Nummern für Erlangen in der internationalen Form +49 9131 85-22222 und für Nürnberg in der internationalen Form +49 911 5302-555 an, uni-externe Nummern in der internationalen Form +49 9131 1111111.', 'fau-person'),
                    'type' => 'text',
                    'id' => $prefix . 'faxNumber',
                    'sanitization_cb' => 'validate_number',
                    'after' => $univis_default['faxNumber'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['faxNumber'],
                    ),
                ),
                array(
                    'name' => __('Mobiltelefon', 'fau-person'),
                    'desc' => __('Bitte geben Sie die Nummer in der internationalen Form +49 176 1111111 an.', 'fau-person'),
                    'type' => 'text',
                    'sanitization_cb' => 'validate_number',
                    'id' => $prefix . 'mobilePhone',
                    'after' => $univis_default['mobilePhone'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['mobilePhone'],
                    ),
                ),
                array(
                    'name' => __('E-Mail', 'fau-person'),
                    'desc' => '',
                    'type' => 'text_email',
                    'id' => $prefix . 'email',
                    'after' => $univis_default['email'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['email'],
                    ),
                ),
                array(
                    'name' => __('Webseite', 'fau-person'),
                    'desc' => '',
                    'type' => 'text_url',
                    'id' => $prefix . 'url',
                    'after' => $univis_default['url'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['url'],
                    ),
                ),
                array(
                    'name' => __('Sortierfeld', 'fau-person'),
                    'desc' => __('Wird für eine Sortierung verwendet, die sich weder nach Name, Titel der Kontaktseite oder Vorname richten soll. Geben SIe hier Buchstaben oder Zahlen ein, nach denen sortiert werden sollen. Zur Sortierunge der Einträge geben Sie im Shortcode das Attribut <code>sort="sortierfeld"</code> ein.', 'fau-person'),
                    'type' => 'text_small',
                    'id' => $prefix . 'alternateName',
                    'attributes'  => array(
                        'placeholder' => $univisdata['alternateName'],
                    ),
                    'show_on_cb' => 'callback_cmb2_show_on_einrichtung'
                ),
                array(
                    'name' => __('Name und "Mehr"-Link verlinken auf Seite ...', 'fau-person'),
                    'desc' => __('Optionale URL-Angabe zu einer selbst gepflegten Seite für Details zum Kontakt. Wenn diese Angabe leer gelassen wird, wird zu der automatisch erstellten Kontaktseite verlinkt.', 'fau-person'),
                    'type' => 'text_url',
                    'id' => $prefix . 'link',
                    'attributes'  => array(
                        'placeholder' => get_permalink($person_id),
                    ),
                    //'after' => '<hr>' . __('Zum Anzeigen der Person verwenden Sie bitte die ID', 'fau-person') . ' ' . $helpuse,                
                ),
            )
        );

        $meta_boxes['fau_person_adressdaten'] = array(
            'id' => 'fau_person_adressdaten',
            'title' => __('Postalische Adressdaten', 'fau-person'),
            'object_types' => array('person'), // post type
            'context' => 'normal',
            'priority' => 'default',
            'fields' => array(
                array(
                    'name' => __('Zugeordneter Standort', 'fau-person'),
                    //'desc' => 'Der Standort, von dem die Daten angezeigt werden sollen.',
                    'type' => 'select',
                    'id' => $prefix . 'standort_id',
                    'options' => $standortselect,
                ),
                array(
                    'name' => __('Standort-Daten für Adressanzeige nutzen', 'fau-person'),
                    'desc' => __('Die Adressdaten werden aus dem Standort bezogen; die folgenden optionalen Felder und Adressdaten aus UnivIS werden überschrieben.', 'fau-person'),
                    'type' => 'checkbox',
                    'id' => $prefix . 'standort_sync',
                ),
                array(
                    'name' => __('Straße und Hausnummer', 'fau-person'),
                    'type' => 'text',
                    'id' => $prefix . 'streetAddress',
                    'after' =>  $standort_default['streetAddress'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['streetAddress'],
                    ),

                ),
                array(
                    'name' => __('Postleitzahl', 'fau-person'),
                    //'desc' => 'Wenn der Ort aus UnivIS übernommen werden soll bitte leer lassen!',
                    'type' => 'text_small',
                    'id' => $prefix . 'postalCode',
                    'sanitization_cb' => 'validate_plz',
                    'after' => $standort_default['postalCode'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['postalCode'],
                    ),
                ),
                array(
                    'name' => __('Ort', 'fau-person'),
                    'type' => 'text',
                    'id' => $prefix . 'addressLocality',
                    'after' => $standort_default['addressLocality'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['addressLocality'],
                    ),
                ),
                array(
                    'name' => __('Land', 'fau-person'),
                    'type' => 'text',
                    'id' => $prefix . 'addressCountry',
                    'after' => $standort_default['addressCountry'],
                    'attributes'  => array(
                        'placeholder' => $univisdata['addressCountry'],
                    ),
                ),

            )
        );


        /*  "instagram"=> [
		'title'  => 'Instagram',
		'class' => 'instagram'
	    ],
	*/

        $somes = getSocialMediaList();
        $somefields = array();



        foreach ($somes as $key => $value) {
            $name = $somes[$key]['title'];
            $desc = '';
            if (isset($somes[$key]['desc'])) {
                $desc = $somes[$key]['desc'];
            }
            $thissome['name'] = $name . ' URL';
            $thissome['desc'] = $desc;
            $thissome['type'] = 'text_url';
            $thissome['id'] =  $prefix . $key . '_url';
            $thissome['protocols'] = array('https');

            array_push($somefields, $thissome);
        }



        // Meta-Box Social Media - fau_person_social_media
        $meta_boxes['fau_person_social_media'] = array(
            'id' => 'fau_person_social_media',
            'title' => __('Social Media', 'fau-person'),
            'object_types' => array('person'), // post type
            'context' => 'normal',
            'priority' => 'default',
            'fields' => $somefields,

        );

        // Meta-Box Weitere Informationen - fau_person_adds
        $meta_boxes['fau_person_adds'] = array(
            'id' => 'fau_person_adds',
            'title' => __('Sprechzeiten', 'fau-person'),
            'object_types' => array('person'), // post type
            'context' => 'normal',
            'priority' => 'default',
            'fields' => array(

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
                                '-' => __('Keine', 'fau-person'),
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


        // Meta-Box Synchronisierung mit externen Daten - fau_person_sync ab hier
        $meta_boxes['fau_person_sync'] = array(
            'id' => 'fau_person_sync',
            'title' => __('Metadaten zum Kontakt', 'fau-person'),
            'object_types' => array('person'), // post type
            'context' => 'side',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __('Typ des Eintrags', 'fau-person'),
                    'type' => 'select',
                    'options' => array(
                        'realperson' => __('Person (allgemein)', 'fau-person'),
                        'realmale' => __('Person (männlich)', 'fau-person'),
                        'realfemale' => __('Person (weiblich)', 'fau-person'),
                        'einrichtung' => __('Einrichtung', 'fau-person'),
                        'pseudo' => __('Pseudonym', 'fau-person'),
                    ),
                    'id' => $prefix . 'typ',
                    'default' => $default_fau_person_typ
                ),
                array(
                    'name' => __('UnivIS-Id', 'fau-person'),
                    'desc' => 'UnivIS-Id des Kontakts (<a href="/wp-admin/edit.php?post_type=person&page=search-univis-id">UnivIS-Id suchen</a>)',
                    'type' => 'text_small',
                    'id' => $prefix . 'univis_id',
                    'sanitization_cb' => 'validate_univis_id',
                    'show_on_cb' => 'callback_cmb2_show_on_person'
                ),
                array(
                    'name' => __('UnivIS-Daten verwenden', 'fau-person'),
                    'desc' => __('Daten aus UnivIS überschreiben die Kontaktdaten.', 'fau-person'),
                    'type' => 'checkbox',
                    'id' => $prefix . 'univis_sync',
                    'after' => $univis_sync,
                    'show_on_cb' => 'callback_cmb2_show_on_person'
                ),

            )
        );



        // Meta-Box um eine Kontaktperson oder -Einrichtung zuzuordnen
        $meta_boxes['fau_person_connection'] = array(
            'id' => 'fau_person_connection',
            'title' => __('Ansprechpartner / verknüpfte Kontakte', 'fau-person'),
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
    function callback_cmb2_show_on_person($field)
    {
        $default_fau_person_typ = Data::default_fau_person_typ();
        $typ = get_post_meta($field->object_id, 'fau_person_typ', true);
        if ($typ == 'pseudo' || $typ == 'einrichtung' || $default_fau_person_typ == 'einrichtung') {
            $person = false;
        } else {
            $person = true;
        }
        return $person;
    }

    //Anzeigen des Feldes nur bei Einrichtungen
    function callback_cmb2_show_on_einrichtung($field)
    {
        $default_fau_person_typ = Data::default_fau_person_typ();
        $typ = get_post_meta($field->object_id, 'fau_person_typ', true);
        if ($typ == 'pseudo' || $typ == 'einrichtung' || $default_fau_person_typ == 'einrichtung') {
            $einrichtung = true;
        } else {
            $einrichtung = false;
        }
        return $einrichtung;
    }
}
