<?php

/**
 Plugin Name: FAU Person
 Plugin URI: https://github.com/RRZE-Webteam/fau-person
 * Description: Visitenkarten-Plugin für FAU Webauftritte
 * Version: 2.0
 * Author: RRZE-Webteam
 * Author URI: http://blogs.fau.de/webworking/
 * License: GPLv2 or later
 */

/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

add_action('plugins_loaded', array('FAU_Person', 'instance'));

register_activation_hook(__FILE__, array('FAU_Person', 'activation'));
register_deactivation_hook(__FILE__, array('FAU_Person', 'deactivation'));

require_once('includes/fau-person-sync-helper.php'); 
require_once('shortcodes/fau-person-shortcodes.php');     
require_once('includes/fau-standort-sync-helper.php'); 
require_once('shortcodes/fau-standort-shortcodes.php');  
//require_once('metaboxes/fau-person-metaboxes.php');
require_once('widgets/fau-person-widget.php');





class FAU_Person {

    const option_name = '_fau_person';
    const textdomain = 'fau-person';
    const php_version = '5.3'; // Minimal erforderliche PHP-Version
    const wp_version = '4.0'; // Minimal erforderliche WordPress-Version
    const search_univis_id_transient = 'sui_1k4fu7056Kl12a5';
    
    
    protected static $options;
    
    public $contactselect;
    public $univis_default;

    protected static $instance = null;

    private $search_univis_id_page = null;
    
    public static function instance() {

        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct() {
        define('FAU_PERSON_ROOT', dirname(__FILE__));
        define('FAU_PERSON_FILE_PATH', FAU_PERSON_ROOT . '/' . basename(__FILE__));
        define('FAU_PERSON_URL', plugins_url('/', __FILE__));
        define('FAU_PERSON_TEXTDOMAIN', self::textdomain);

        
        load_plugin_textdomain(self::textdomain, false, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
        
        self::$options = self::get_options();       

        include_once( plugin_dir_path(__FILE__) . 'includes/fau-person-metaboxes.php' );

        add_action( 'init', array (__CLASS__, 'register_person_post_type' ) );
        add_action( 'init', array( $this, 'register_persons_taxonomy' ) );
        add_action( 'restrict_manage_posts', array( $this, 'person_restrict_manage_posts' ) );
        
        add_action( 'init', array (__CLASS__, 'register_standort_post_type' ) );
        //add_action( 'restrict_manage_posts', array( $this, 'standort_restrict_manage_posts' ) );

        add_action('admin_menu' , array( $this, 'person_menu_subpages' )); 
        add_action( 'admin_menu', array( $this, 'add_help_tabs' ) );
        add_action( 'admin_menu', array( $this, 'add_options_pages' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'widgets_init', array( __CLASS__, 'register_widgets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_script' ) );
	add_action( 'admin_init', array($this, 'person_shortcodes_rte_button' ) );     
        
        add_filter( 'single_template', array( $this, 'include_template_function' ) );
        
        self::add_shortcodes();        
        
        // Kontakttyp als zusätzliche Spalte in Übersicht
        add_filter( 'manage_person_posts_columns', array( $this, 'change_columns' ));
        add_action( 'manage_person_posts_custom_column', array( $this, 'custom_columns'), 10, 2 ); 
        // Sortierung der zusätzlichen Spalte
        add_filter( 'manage_edit-person_sortable_columns', array( $this, 'sortable_columns' ));
        add_action( 'pre_get_posts', array( $this, 'custom_columns_orderby') );
        
        //Excerpt-Meta-Box umbenennen
        add_action( 'do_meta_boxes', array( $this, 'modified_excerpt_metabox' ));
              
          
    }

    
public function adding_custom_meta_boxes( $post ) {
    add_meta_box( 
        'my-meta-box',
        __( 'My Meta Box' ),
        'render_my_meta_box',
        'post',
        'normal',
        'default'
    );
}
 
    
    public static function activation() {

        self::version_compare();
        
        self::register_person_post_type();
        self::register_standort_post_type();
        flush_rewrite_rules(); // Flush Rewrite-Regeln, so dass CPT und CT auf dem Front-End sofort vorhanden sind

        self::$options = self::get_options();  
        
        // CPT-Capabilities für die Administrator-Rolle zuweisen
        // 
        $caps_person = self::get_caps('person');
        self::add_caps('administrator', $caps_person);
        $caps_standort = self::get_caps('standort');
        self::add_caps('administrator', $caps_standort);
        //self::add_caps('editor', $caps);       
    }
    
    public static function deactivation() {       
        // CPT-Capabilities aus der Administrator-Rolle entfernen
            $caps_person = self::get_caps('person');
            self::remove_caps('administrator', $caps_person);
            $caps_standort = self::get_caps('standort');
            self::remove_caps('administrator', $caps_standort);
            //self::remove_caps('editor', $caps);
            flush_rewrite_rules(); // Flush Rewrite-Regeln, so dass CPT und CT auf dem Front-End sofort vorhanden sind
    
    }

    private static function version_compare() {
        $error = '';

        if (version_compare(PHP_VERSION, self::php_version, '<')) {
            $error = sprintf(__('Ihre PHP-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die PHP-Version %s.', FAU_PERSON_TEXTDOMAIN), PHP_VERSION, self::php_version);
        }

        if (version_compare($GLOBALS['wp_version'], self::wp_version, '<')) {
            $error = sprintf(__('Ihre Wordpress-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die Wordpress-Version %s.', FAU_PERSON_TEXTDOMAIN), $GLOBALS['wp_version'], self::wp_version);
        }

        if (!empty($error)) {
            deactivate_plugins(plugin_basename(__FILE__), false, true);
            wp_die($error);
        }
    }

    private static function default_options() {
        $options = array();
                
        return $options; // Standard-Array für zukünftige Optionen
    }

    private static function get_options() {
        $defaults = self::default_options();
        
        $options = (array) get_option(self::option_name);
        $options = wp_parse_args($options, $defaults);
        $options = array_intersect_key($options, $defaults);

        return $options;
    }
    
   public function get_contactdata( $connection=0 ) {      
        $args = array(
            'post_type' => 'person',
            'numberposts' => -1,
            'meta_key' => 'fau_person_typ'
        );

	$personlist = get_posts($args);

        if( $personlist ) {  
            foreach( $personlist as $key => $value) {
                $personlist[$key] = (array) $personlist[$key];
                if( get_post_meta( $personlist[$key]['ID'], 'fau_person_typ', true ) == 'realperson' ||  get_post_meta( $personlist[$key]['ID'], 'fau_person_typ', true ) == 'realmale' ||  get_post_meta( $personlist[$key]['ID'], 'fau_person_typ', true ) == 'realfemale' ) {
                    $name = $personlist[$key]['post_title'];
                    if( ltrim( strpos( $name, ' ' ) ) ) {
                        $lastname = ltrim( strrchr( $name, ' ' ) );
                        $firstname = ltrim( str_replace( $lastname, '', $name ) );
                    } else {
                        $lastname = $name;
                        $firstname = '';
                    }
                } else {
                    $lastname = $personlist[$key]['post_title'];
                    $firstname = '';
                }
                $personlist[$key]['lastname'] = $lastname;
                $personlist[$key]['firstname'] = $firstname;
            }
 
            $personlist = $this->array_orderby( $personlist, "lastname", SORT_ASC );
        
            foreach( $personlist as $key => $value) {
                $fullname = $personlist[$key]['ID'] . ': ' . $personlist[$key]['lastname'];
                if( !empty( $personlist[$key]['firstname'] ) )  $fullname .= ', ' . $personlist[$key]['firstname'];          
                $contactselect[ $personlist[$key]['ID'] ] = $fullname;
            }
            if ( $connection ) {
                $contactselect = array( '0' => __('Kein Kontakt ausgewählt.', FAU_PERSON_TEXTDOMAIN) ) + $contactselect;
            }
        } else {
            $contactselect[0] = __('Noch keine Kontakte eingepflegt.', FAU_PERSON_TEXTDOMAIN);
        }
        return $contactselect;  
    }
    
    public function get_standortdata() {      
         $args = array(
            'post_type' => 'standort',
            'numberposts' => -1
        );

	$standortlist = get_posts($args);
        if( $standortlist ) {  
            foreach( $standortlist as $key => $value) {
                $standortlist[$key] = (array) $standortlist[$key];   
                $standortselect[ $standortlist[$key]['ID'] ] = $standortlist[$key]['post_title'];
            }                                                
            asort($standortselect);
            $standortselect = array( '0' => __('Kein Standort ausgewählt.', FAU_PERSON_TEXTDOMAIN) ) + $standortselect;

        } else {
            $standortselect[0] = __('Noch kein Standort eingepflegt.', FAU_PERSON_TEXTDOMAIN);
        }
        return $standortselect;  
    }
    
    private static function get_caps($cap_type) {
        $caps = array(
            "edit_" . $cap_type,
            "read_" . $cap_type,
            "delete_" . $cap_type,
            "edit_" . $cap_type . "s",
            "edit_others_" . $cap_type . "s",
            "publish_" . $cap_type . "s",
            "read_private_" . $cap_type . "s",
            "delete_" . $cap_type . "s",
            "delete_private_" . $cap_type . "s",
            "delete_published_" . $cap_type . "s",
            "delete_others_" . $cap_type . "s",
            "edit_private_" . $cap_type . "s",
            "edit_published_" . $cap_type . "s",                
        );

        return $caps;
    }
    
    private static function add_caps($role, $caps) {
        $role = get_role($role);
        foreach($caps as $cap) {
            $role->add_cap($cap);
        }        
    }
    
    private static function remove_caps($role, $caps) {
        $role = get_role($role);
        foreach($caps as $cap) {
            $role->remove_cap($cap);
        }        
    }    
    
    public function add_help_tabs() {
        add_action('load-post-new.php', array($this, 'help_menu_new_person'));
        add_action('load-post.php', array($this, 'help_menu_new_person'));
        add_action('load-edit.php', array($this, 'help_menu_person'));
        add_action('load-edit-tags.php', array($this, 'help_menu_persons_category'));
    }
    
    public function help_menu_new_person() {

        $content_overview = array(
            '<p>' . __('Geben Sie auf dieser Seite alle gewünschten Daten zu einem Kontakt ein. Die Einbindung der Kontaktdaten erfolgt dann in den Beiträgen oder Seiten über einen Shortcode oder ein Widget.', FAU_PERSON_TEXTDOMAIN) . '</p>'
        );

        $help_tab_overview = array(
            'id' => 'overview',
            'title' => __('Kontakte eingeben', FAU_PERSON_TEXTDOMAIN),
            'content' => implode(PHP_EOL, $content_overview),
        );

        $help_sidebar = sprintf('<p><strong>%1$s:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">%2$s</a></p>', __('Für mehr Information', FAU_PERSON_TEXTDOMAIN), __('RRZE-Webteam in Github', FAU_PERSON_TEXTDOMAIN));

        $screen = get_current_screen();

        if ($screen->id != 'person') {
            return;
        }

        $screen->add_help_tab($help_tab_overview);

        $screen->set_help_sidebar($help_sidebar);
    }
    
    public function help_menu_person() {

        $content_overview = array(
            '<p><strong>' . __('Einbindung der Kontakt-Visitenkarte über Shortcode', FAU_PERSON_TEXTDOMAIN) . '</strong></p>',
            '<p>' . __('Binden Sie die gewünschten Kontaktdaten mit dem Shortcode [kontakt] mit folgenden Parametern auf Ihren Seiten oder Beiträgen ein:', FAU_PERSON_TEXTDOMAIN) . '</p>',
            '<ol>',
            '<li>' . __('zwingend:', FAU_PERSON_TEXTDOMAIN),
            '<ul>',
            '<li>id: ' . __('ID des Kontakteintrags (erkennbar in der Metabox "Kontaktinformationen" auf den Seiten)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '</ul>', 
            '</li>',
            '<li>' . __('format="..." (optional), je nach Wert unterscheiden sich die Ausgabedarstellung und die angezeigten Standardparameter:', FAU_PERSON_TEXTDOMAIN),        
            '<ul>',
            '<li>name: ' . __('Ausgabe von Titel, Vorname, Nachname und Suffix (sofern vorhanden) im Fließtext mit Link auf die Kontaktseite der Person', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>page: ' . __('vollständige Ausgabe des ganzen Kontaktes wie bei der Kontakt-Einzelseite, die Parameter show und hide haben hierauf keinen Einfluss', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>sidebar: ' . __('Ausgabe wie bei der Anzeige in der Sidebar im Theme', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>liste: ' . __('Ausgabe der Namen mit Listenpunkten, unten drunter Kurzbeschreibung', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '</ul>',
            '</li>',
            '<li>' . __('show="..." bzw. hide="..." (optional), wenn ein zusätzliches Feld zu den Standardfeldern angezeigt werden soll bzw. die Anzeige eines Standardfeldes nicht gewünscht ist:', FAU_PERSON_TEXTDOMAIN),    
            '<ul>',
            '<li>kurzbeschreibung: ' . __('Standardanzeige bei format="liste" (wennn das Feld leer ist wird dann der Anfang des Inhaltsbereiches angezeigt)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>organisation: ' . __('Standardanzeige ohne format-Angabe, bei format="page" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>abteilung: ' . __('Standardanzeige ohne format-Angabe, bei format="page" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>postition: ' . __('Standardanzeige ohne format-Angabe, bei format="page" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>titel: ' . __('Standardanzeige ohne format-Angabe, bei format="name", "page", "sidebar", "liste" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',      
            '<li>suffix: ' . __('Standardanzeige ohne format-Angabe, bei format="name", "page", "sidebar", "liste" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>adresse: ' . __('Standardanzeige bei format="page" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>raum: ' . __('Standardanzeige bei format="page" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>telefon: ' . __('Standardanzeige ohne format-Angabe, bei format="page", "sidebar" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>fax: ' . __('Standardanzeige bei format="page" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>mobil: ' . __('keine Standardanzeige', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>mail: ' . __('Standardanzeige ohne format-Angabe, bei format="page", "sidebar" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',  
            '<li>webseite: ' . __('Standardanzeige bei format="page", "sidebar" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>',            
            '<li>mehrlink: ' . __('keine Standardanzeige', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>kurzauszug: ' . __('Standardanzeige bei format="sidebar" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>', 
            '<li>sprechzeiten: ' . __('Standardanzeige bei format="page"', FAU_PERSON_TEXTDOMAIN) . '</li>',            
            '<li>publikationen: ' . __('Standardanzeige bei format="page"', FAU_PERSON_TEXTDOMAIN) . '</li>',
            '<li>bild: ' . __('Standardanzeige bei format="page", "sidebar" (und bei Widget)', FAU_PERSON_TEXTDOMAIN) . '</li>', 
            '</ul>',
            '</li>',            
            '</ol>',
        );

        $help_tab_overview = array(
            'id' => 'overview',
            'title' => __('Übersicht', FAU_PERSON_TEXTDOMAIN),
            'content' => implode(PHP_EOL, $content_overview),
        );

        $help_sidebar = sprintf('<p><strong>%1$s:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">%2$s</a></p>', __('Für mehr Information', FAU_PERSON_TEXTDOMAIN), __('RRZE-Webteam in Github', FAU_PERSON_TEXTDOMAIN));

        $screen = get_current_screen();

        if ($screen->id != 'edit-person') {
            return;
        }

        $screen->add_help_tab($help_tab_overview);

        $screen->set_help_sidebar($help_sidebar);
    }    
    
    public function help_menu_persons_category() {

        $content_overview = array(
            '<p><strong>' . __('Zuordnung von Personen und Kontakten zu verschiedenen Kategorien', FAU_PERSON_TEXTDOMAIN) . '</strong></p>',
        );

        $help_tab_overview = array(
            'id' => 'overview',
            'title' => __('Übersicht', FAU_PERSON_TEXTDOMAIN),
            'content' => implode(PHP_EOL, $content_overview),
        );

        $help_sidebar = sprintf('<p><strong>%1$s:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">%2$s</a></p>', __('Für mehr Information', FAU_PERSON_TEXTDOMAIN), __('RRZE-Webteam in Github', FAU_PERSON_TEXTDOMAIN));
        
        $screen = get_current_screen();

        if ($screen->id != 'edit-persons_category') {
            return;
        }

        $screen->add_help_tab($help_tab_overview);

        $screen->set_help_sidebar($help_sidebar);
    }   
    
    public function add_options_pages() {
        //Umgehen von register_setting für die Suche-Seite
        $input = isset($_POST[self::option_name]) ? $_POST[self::option_name] : null;
        set_transient(self::search_univis_id_transient, $input, 30);
                
        $this->search_univis_id_page = add_submenu_page('edit.php?post_type=person', __('Suche nach UnivIS-ID', FAU_PERSON_TEXTDOMAIN), __('Suche nach UnivIS-ID', FAU_PERSON_TEXTDOMAIN), 'edit_posts', 'search-univis-id', array( $this, 'search_univis_id' ));
        add_action('load-' . $this->search_univis_id_page, array($this, 'help_menu_search_univis_id'));
    }

    public function search_univis_id() {
        $transient = get_transient(self::search_univis_id_transient);
        $firstname = isset($transient['firstname']) ? $transient['firstname'] : '';
        $givenname = isset($transient['givenname']) ? $transient['givenname'] : '';
        if(class_exists( 'Univis_Data' ) ) {
            $person = sync_helper::get_univisdata(0, $firstname, $givenname);           
        } else {
            $person = array();
        }
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php echo esc_html(__('Suche nach UnivIS-ID', FAU_PERSON_TEXTDOMAIN)); ?></h2>

            <form method="post">
                <?php
                settings_fields('search_univis_id_options');
                do_settings_sections('search_univis_id_options');
                submit_button(esc_html(__('Person suchen', FAU_PERSON_TEXTDOMAIN)));
                ?>
            </form>            
        </div>
        <div class="wrap">
            <?php
                settings_fields('find_univis_id_options');
                do_settings_sections('find_univis_id_options');
                if(empty($person)) {
                    echo __('Es konnten keine Daten zur Person gefunden werden. Bitte verändern Sie Ihre Suchwerte und stellen Sie sicher, dass das Plugin Univis-Data aktiviert ist.', FAU_PERSON_TEXTDOMAIN);
                } else {
                    $person = $this->array_orderby($person,"lastname", SORT_ASC, "firstname", SORT_ASC );
                    $no_univis_data = __('keine Daten in UnivIS eingepflegt', FAU_PERSON_TEXTDOMAIN);
                    foreach($person as $key=>$value) {
                        if(array_key_exists('locations', $person[$key]) && array_key_exists('location', $person[$key]['locations'][0]) && array_key_exists('email', $person[$key]['locations'][0]['location'][0])) {
                            $email = $person[$key]['locations'][0]['location'][0]['email'];
                        } else {
                            $email = $no_univis_data;
                        }
                        if(array_key_exists('id', $person[$key])) {
                            $id = $person[$key]['id'];
                        } else {
                            $id = $no_univis_data;
                        }
                        if(array_key_exists('firstname', $person[$key])) {
                            $firstname = $person[$key]['firstname'];
                        } else {
                            $firstname = __('Vorname', FAU_PERSON_TEXTDOMAIN) . ": " . $no_univis_data . ", ";
                        }
                        if(array_key_exists('lastname', $person[$key])) {
                            $lastname = $person[$key]['lastname'];
                        } else {
                            $lastname = __('Nachname', FAU_PERSON_TEXTDOMAIN) . ": " . $no_univis_data;
                        }
                        if(array_key_exists('orgname', $person[$key])) {
                            $orgname = $person[$key]['orgname'];
                        } else {
                            $orgname = $no_univis_data;
                        }
                        //echo sprintf(__('UnivIS-ID %1$s: %2$s %3$s, E-Mail: %4$s, Organisation: %5$s', FAU_PERSON_TEXTDOMAIN), $id, $firstname, $lastname, $email, $orgname);
                        echo 'UnivIS-ID '. $id . ': '. $firstname . ' ' . $lastname . ', E-Mail: ' . $email. ', Organisation: ' . $orgname;
                        echo "<br>";
                    }
                }
            ?>
        </div>
        <?php
            delete_transient(self::search_univis_id_transient);
    }

    public function admin_init() {

        add_settings_section('search_univis_id_section', __('Bitte geben Sie den Vor- und Nachnamen der Person ein, von der Sie die UnivIS-ID benötigen.', FAU_PERSON_TEXTDOMAIN), '__return_false', 'search_univis_id_options');

        add_settings_field('univis_id_firstname', __('Vorname', FAU_PERSON_TEXTDOMAIN), array($this, 'univis_id_firstname'), 'search_univis_id_options', 'search_univis_id_section');
        add_settings_field('univis_id_givenname', __('Nachname', FAU_PERSON_TEXTDOMAIN), array($this, 'univis_id_givenname'), 'search_univis_id_options', 'search_univis_id_section');
      
        add_settings_section('find_univis_id_section', __('Folgende Daten wurden in UnivIS gefunden:', FAU_PERSON_TEXTDOMAIN), '__return_false', 'find_univis_id_options');
    }
    
    public function univis_id_firstname() {
        $transient = get_transient(self::search_univis_id_transient);
        ?>
        <input type='text' name="<?php printf('%s[firstname]', self::option_name); ?>" value="<?php echo (isset($transient['firstname'])) ? $transient['firstname'] : NULL; ?>"><p class="description"><?php _e('Bitte keine Umlaute, sondern statt dessen ae, oe, ue, ss verwenden.', FAU_PERSON_TEXTDOMAIN); ?></p>
        <?php
    }

    public function univis_id_givenname() {
        $transient = get_transient(self::search_univis_id_transient);
        ?>
        <input type='text' name="<?php printf('%s[givenname]', self::option_name); ?>" value="<?php echo (isset($transient['givenname'])) ? $transient['givenname'] : NULL; ?>"><p class="description"><?php _e('Bitte keine Umlaute, sondern statt dessen ae, oe, ue, ss verwenden.', FAU_PERSON_TEXTDOMAIN); ?></p>
        
        <?php
    }       
    
    public function help_menu_search_univis_id() {

        $content_overview = array(
            '<p>' . __('Geben Sie hier den Vor- oder den Nachnamen der Person ein. Es kann auch beides oder nur Namensteile eingegeben werden. Bitte beachten Sie, dass Umlaute bei der Eingabe aufgelöst werden müssen.', FAU_PERSON_TEXTDOMAIN) . '</p>',
            '<p>' . __('Mit <i>Person suchen</i> erhalten Sie eine Auflistung aller möglichen Personen. Suchen Sie die richtige Person aus der Liste heraus, markieren Sie die UnivIS-ID, kopieren Sie diese mit Strg+C und fügen Sie dann beim entsprechenden Kontakt im Feld <i>UnivIS-ID</i> ein.', FAU_PERSON_TEXTDOMAIN) . '</p>',
        );

        $help_tab_overview = array(
            'id' => 'overview',
            'title' => __('Übersicht', FAU_PERSON_TEXTDOMAIN),
            'content' => implode(PHP_EOL, $content_overview),
        );

        $help_sidebar = sprintf('<p><strong>%1$s:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">%2$s</a></p>', __('Für mehr Information', FAU_PERSON_TEXTDOMAIN), __('RRZE-Webteam in Github', FAU_PERSON_TEXTDOMAIN));
        
        $screen = get_current_screen();

        if ($screen->id != 'person_page_search-univis-id') {
            return;
        }

        $screen->add_help_tab($help_tab_overview);

        $screen->set_help_sidebar($help_sidebar);
    }    
    
    public function person_menu_subpages() {
        //remove_submenu_page('edit.php?post_type=person', 'load-post-new.php');
        // Personen mit oder ohne bestimmte Funktionen. Andere Ansprechpartner (aus der Rubrik Kontakt) und Standorte können diesen zugeordnet werden
        add_submenu_page('edit.php?post_type=person', __('Person hinzufügen', FAU_PERSON_TEXTDOMAIN), __('Neue Person', FAU_PERSON_TEXTDOMAIN), 'edit_posts', 'new_person', array( $this, 'add_person_types' ));
        // Kontakte, z.B. Vorzimmer, Sekretariat, Abteilungen. Hier sind Ansprechpartner aus den Personen zuordenbar, wird direkt über CPT angezeigt
        add_submenu_page('edit.php?post_type=person', __('Einrichtung hinzufügen', FAU_PERSON_TEXTDOMAIN), __('Neue Einrichtung', FAU_PERSON_TEXTDOMAIN), 'edit_posts', 'new_einrichtung', array( $this, 'add_person_types' ));
        // Zentrale Adressen, können in Personen und Kontakte übernommen werden
        add_submenu_page('edit.php?post_type=person', __('Standort hinzufügen', FAU_PERSON_TEXTDOMAIN), __('Neuer Standort', FAU_PERSON_TEXTDOMAIN), 'edit_posts', 'new_standort', array( $this, 'add_person_types' ));
        add_action('load-person_page_new_person', array( $this, 'person_menu' ));
        add_action('load-person_page_new_einrichtung', array( $this, 'einrichtung_menu' ));
        add_action('load-person_page_new_standort', array( $this, 'standort_menu' ));
    }
    
    public function add_person_types() {
        //wp_redirect( admin_url( 'post-new.php?post_type=standort' ) );
            //add_action( 'load-person_page_konakt', array( $this, 'adding_custom_meta_boxes' ));  
    }
    
    public function person_menu() {
        wp_redirect( admin_url( 'post-new.php?post_type=person' ) );
        //$metaboxes = array();
        //do_action('cmb_meta_boxes', $metaboxes);
    }

    public function einrichtung_menu() {
        wp_redirect( admin_url( 'post-new.php?post_type=person&fau_person_typ=einrichtung' ) );
        //$metaboxes = array();
        //do_action('cmb_meta_boxes', $metaboxes);
    }
    
    public function standort_menu() {
        wp_redirect( admin_url( 'post-new.php?post_type=standort' ) );
        //$metaboxes = array();
        //do_action('cmb_meta_boxes', $metaboxes);
    }    

    
    public static function register_widgets() {
        register_widget( 'FAUPersonWidget' );
    }
    
    private static function add_shortcodes() {     
        add_shortcode( 'person', 'fau_person' );
        add_shortcode( 'kontakt', 'fau_person' );
        add_shortcode( 'persons', 'fau_persons' );
        add_shortcode( 'standort', 'fau_person' );
    }

    public static function register_person_post_type() {
        require_once('posttypes/fau-person-posttype.php');
        register_post_type('person', $person_args);
    }

    public static function register_standort_post_type() {
        require_once('posttypes/fau-standort-posttype.php');
        register_post_type('standort', $standort_args);
    }
    
    public function register_persons_taxonomy() {
        register_taxonomy(
                'persons_category', //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
                'person', //post type name
                array(
            'hierarchical' => true,
            'label' => __('Kontakt-Kategorien', FAU_PERSON_TEXTDOMAIN), //Display name
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'persons', // This controls the base slug that will display before each term
                'with_front' => false // Don't display the category base before
            )
                )
        );
    }
    
    public function add_admin_script($hook) {
        global $post_type;
        if('person' != $post_type) {
            return;
        }
        wp_register_script('admin', plugin_dir_url( __FILE__ ) . '/js/admin.js', array('jquery'), false, true);

        if ( 'post-new.php' == $hook) {
            wp_enqueue_script('admin');
            return;
        } 
        if ('post.php' == $hook) {       
            wp_enqueue_script('admin');
            return;
        }
    } 
    
    public function person_restrict_manage_posts() {
        global $typenow;
        if ($typenow == "person") {
            $filters = get_object_taxonomies($typenow);
            foreach ($filters as $tax_slug) {
                $tax_obj = get_taxonomy($tax_slug);
                wp_dropdown_categories(array(
                    'show_option_all' => sprintf(__('Alle %s anzeigen', FAU_PERSON_TEXTDOMAIN), $tax_obj->label),
                    'taxonomy' => $tax_slug,
                    'name' => $tax_obj->name,
                    'orderby' => 'name',
                    'selected' => isset($_GET[$tax_slug]) ? $_GET[$tax_slug] : '',
                    'hierarchical' => $tax_obj->hierarchical,
                    'show_count' => true,
                    'hide_if_empty' => true
                ));
            }
        }
    }
    
    // Change the columns for the edit CPT screen
    public function change_columns( $cols ) {
	$cols = array(
	    'cb' => '<input type="checkbox" />',
	    'title' => __( 'Neuer Titel', FAU_PERSON_TEXTDOMAIN ),
            'typ' => __( 'Typ', FAU_PERSON_TEXTDOMAIN ),
            'date' => __( 'Datum', FAU_PERSON_TEXTDOMAIN )

	);

	return $cols;
    }

    public function custom_columns( $column, $post_id ) {
	switch ( $column ) {
	    case "typ":
                $typ = get_post_meta( $post_id, 'fau_person_typ', true);
                switch ( $typ ) {
                    case 'realperson':
                        $typ = __('Person (allgemein)', FAU_PERSON_TEXTDOMAIN);
                        break;
                    case 'realmale':
                        $typ = __('Person (männlich)', FAU_PERSON_TEXTDOMAIN);
                        break;
                    case 'realfemale':
                        $typ = __('Person (weiblich)', FAU_PERSON_TEXTDOMAIN);
                        break;
                    case 'pseudo':
                        $typ = __('Einrichtung (Pseudonym)', FAU_PERSON_TEXTDOMAIN);
                        break;
                    case 'einrichtung':
                        $typ = __('Einrichtung', FAU_PERSON_TEXTDOMAIN);
                        break;
                }
                echo $typ;
                break;
	}
    }
    
    // Make these columns sortable
    public function sortable_columns( $columns ) {
	$columns = array(
	    'title' => 'title',
	    'typ' => 'typ',
	    'date' => 'date'
	);
        return $columns;
    }	
    
    public function custom_columns_orderby( $query ) {
        if( ! is_admin() )
            return;
 
        $orderby = $query->get( 'orderby' );
 
        if( 'typ' == $orderby ) {
            $query->set('meta_key','fau_person_typ');
            $query->set('orderby','meta_value');
        }        
    }
    
    public function include_template_function($template_path) {
        global $post;
        if ($post->post_type == 'person') {
            //if (is_single()) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ($theme_file = locate_template(array('single-person.php'))) {
                    $template_path = $theme_file;
                } else {
                    $template_path = FAU_PERSON_ROOT . '/templates/single-person.php';                    
                }
            //}
        }
        if ($post->post_type == 'standort') {
            //if (is_single()) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ($theme_file = locate_template(array('single-standort.php'))) {
                    $template_path = $theme_file;
                } else {
                    $template_path = FAU_PERSON_ROOT . '/templates/single-standort.php';                    
                }
            //}
        }
        return $template_path;
    }    

    public function person_post_types_admin_order($wp_query) {
        if (is_admin()) {
            $post_type = $wp_query->query['post_type'];
            if ($post_type == 'person') {
                if (!isset($wp_query->query['orderby'])) {
                    $wp_query->set('orderby', 'title');
                    $wp_query->set('order', 'ASC');
                }
            }
        }
    }    
    
    //Überprüft bei neuen Seiten ob Person oder Einrichtung eingegeben wird, abhängig vom Feldtyp fau_person_typ
    public static function default_fau_person_typ( ) {     
        if(isset($_GET["fau_person_typ"]) && $_GET["fau_person_typ"] == 'einrichtung') {
            $default_fau_person_typ = 'einrichtung';
        } else {
            $default_fau_person_typ = 'realperson';
        }
        return $default_fau_person_typ;
    }
    
    //Legt die in UnivIS hinterlegten Werte in einem Array ab, Feldbezeichnungen
    public function univis_defaults( ) {
            $id = cmb_Meta_Box::get_object_id();
            $post = get_post($id);
            if( !is_null( $post ) && $post->post_type === 'person' && get_post_meta($id, 'fau_person_univis_id', true)) {
                $univis_id = get_post_meta($id, 'fau_person_univis_id', true);
                $univis_default = sync_helper::get_fields($id, $univis_id, 1);
                return $univis_default;
            }
    }
    
    public function standort_defaults( ) {
            $id = cmb_Meta_Box::get_object_id();
            $post = get_post($id);
            if( !is_null( $post ) && $post->post_type === 'person' && get_post_meta($id, 'fau_person_standort_id', true)) {
                $standort_id = get_post_meta($id, 'fau_person_standort_id', true);
                $standort_default = standort_sync_helper::get_fields($id, $standort_id, 1);
                return $standort_default;        
            }
    }
    
    //Excerpt Metabox entfernen um Titel zu ändern und Länge zu modifizieren
    public function modified_excerpt_metabox() {
            remove_meta_box( 'postexcerpt', 'person', 'normal' ); 
            add_meta_box( 
                    'postexcerpt'
                    , __( 'Kurzbeschreibung in Listenansichten (bis zu 400 Zeichen)', FAU_PERSON_TEXTDOMAIN )
                    , 'post_excerpt_meta_box'
                    , 'person'
                    , 'normal'
                    , 'high' 
            );
    }
    
    public function person_shortcodes_rte_button() {
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {
            add_filter( 'mce_external_plugins', array($this, 'person_rte_add_buttons' ));
        }
    }

    public function person_rte_add_buttons( $plugin_array ) {
        $plugin_array['personrteshortcodes'] = plugin_dir_url(__FILE__) . 'js/tinymce-shortcodes.js';
        return $plugin_array;
    }
    
/*    public function get_helpuse() {
        global $post;
        if ($post->ID >0) {
            $helpuse = __('<p>Einbindung in Seiten und Beiträge via: </p>', FAU_PERSON_TEXTDOMAIN);
            $helpuse .= '<pre> [person id="'.$post->ID.'"] </pre>';
            if ($post->post_name) {
                $helpuse .= ' oder <br> <pre> [person slug="'.$post->post_name.'"] </pre>';
            }

        }
        	return $helpuse;
    }*/
        
        private function array_orderby(){
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
                                    if(isset($row[$field])) {
					$tmp[$key] = $row[$field];
                                    } else {
                                        $tmp[$key] = '';
                                    }
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}
    
}
