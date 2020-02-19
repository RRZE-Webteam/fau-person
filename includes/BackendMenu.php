<?php

namespace FAU_Person;
use FAU_Person\Data;
use UnivIS_Data;
use sync_helper;
defined('ABSPATH') || exit;

/**
 * Define Image Sizes
 */
class BackendMenu {

    protected $pluginFile;
    private $settings = '';
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }


    public function onLoaded()    {
        add_action( 'admin_menu' , array( $this, 'person_menu_subpages' )); 
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'add_options_pages' ) );
   
    }

    public function add_options_pages() {
        //Umgehen von register_setting für die Suche-Seite, da register_setting nur für Standard-Settings-Seiten funktioniert!!!        
        $options = $this->settings->options;
	$optionname= $this->settings->optionName;
	
        $input = isset($_POST[$optionname]) ? $_POST[$optionname] : null;
        set_transient($this->settings->search_univis_id_transient, $input, 30);
	
        if( isset( $_POST['fau-person-options'] ) ) {

            set_transient('fau-person-options', 1, 30);
            $options['has_archive_page'] = $input;
            $options = apply_filters('gmail_apikey_options', $options);
            update_option($optionname, $options);        
        }

        $this->search_univis_id_page = add_submenu_page('edit.php?post_type=person',
	    __('Suche nach UnivIS-ID', 'fau-person'), 
	    __('Suche nach UnivIS-ID', 'fau-person'), 
	    'edit_persons', 'search-univis-id',
	    array( $this, 'search_univis_id' ));
        add_action('load-' . $this->search_univis_id_page, array($this, 'help_menu_search_univis_id'));
        
    
    }


    public function search_univis_id() {
        $transient = get_transient($this->settings->search_univis_id_transient);
        $firstname = isset($transient['firstname']) ? $transient['firstname'] : '';
        $givenname = isset($transient['givenname']) ? $transient['givenname'] : '';
        if(class_exists( 'Univis_Data' ) ) {
            $firstname = Helper::sonderzeichen($firstname);
            $givenname = Helper::sonderzeichen($givenname);
            $person = sync_helper::get_univisdata(0, $firstname, $givenname);       
        } else {
            $person = array();
        }
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php echo esc_html(__('Suche nach UnivIS-ID', 'fau-person')); ?></h2>

            <form method="post">
                <?php
                settings_fields('search_univis_id_options');
                do_settings_sections('search_univis_id_options');
                submit_button(esc_html(__('Person suchen', 'fau-person')), 'primary', 'fau-person-search');
                ?>
            </form>            
        </div>
        <div class="wrap">
            <?php
                settings_fields('find_univis_id_options');
                do_settings_sections('find_univis_id_options');
                if(empty($person) || empty($person[0])) {
                    echo __('<div class="alert alert-warning">Es konnten keine Daten zur Person gefunden werden. Bitte verändern Sie Ihre Suchwerte.</div>', 'fau-person');
                } else {
                    $person = Helper::array_orderby($person,"lastname", SORT_ASC, "firstname", SORT_ASC );
                    $no_univis_data = __('keine Daten in UnivIS eingepflegt', 'fau-person');
		    
		   echo "<ul>"; 
		    
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
                            $firstname = __('Vorname', 'fau-person') . ": " . $no_univis_data . ", ";
                        }
                        if(array_key_exists('lastname', $person[$key])) {
                            $lastname = $person[$key]['lastname'];
                        } else {
                            $lastname = __('Nachname', 'fau-person') . ": " . $no_univis_data;
                        }
                        if(array_key_exists('orgname', $person[$key])) {
                            $orgname = $person[$key]['orgname'];
                        } else {
                            $orgname = $no_univis_data;
                        }
                        echo '<li>UnivIS-ID: <code>'. $id . '</code>,  '. $firstname . ' ' . $lastname . ', E-Mail: <em>' . $email. '</em>, Organisation: <em>' . $orgname. '</em></li>';
                    }
		     echo "</ul>";
                }
            ?>
        </div>
        <?php
            delete_transient($this->settings->search_univis_id_transient);
    }

    public function admin_init() {       
        add_settings_section('search_univis_id_section', __('Bitte geben Sie den Vor- und/oder Nachnamen der Person ein, von der Sie die UnivIS-ID benötigen.', 'fau-person'), '__return_false', 'search_univis_id_options');
        add_settings_field('univis_id_firstname', __('Vorname', 'fau-person'), array($this, 'univis_id_firstname'), 'search_univis_id_options', 'search_univis_id_section');
        add_settings_field('univis_id_givenname', __('Nachname', 'fau-person'), array($this, 'univis_id_givenname'), 'search_univis_id_options', 'search_univis_id_section');      
        add_settings_section('find_univis_id_section', __('Folgende Daten wurden in UnivIS gefunden:', 'fau-person'), '__return_false', 'find_univis_id_options');
    }

    public function univis_id_firstname() {
        $transient = get_transient($this->settings->search_univis_id_transient);
	$optionname = $this->settings->optionName;
        ?>
        <input type='text' name="<?php printf('%s[firstname]', $optionname); ?>" value="<?php echo (isset($transient['firstname'])) ? $transient['firstname'] : NULL; ?>"><p class="description"><?php _e('Es können auch nur Teile des Namens eingegeben werden.', 'fau-person'); ?></p>
        <?php
    }

    public function univis_id_givenname() {
        $transient = get_transient($this->settings->search_univis_id_transient);   
	$optionname = $this->settings->optionName;
        ?>
        <input type='text' name="<?php printf('%s[givenname]', $optionname); ?>" value="<?php echo (isset($transient['givenname'])) ? $transient['givenname'] : NULL; ?>"><p class="description"><?php _e('Es können auch nur Teile des Namens eingegeben werden.', 'fau-person'); ?></p>        
        <?php
    }       
    
   
    

    public function person_menu_subpages() {
        //remove_submenu_page('edit.php?post_type=person', 'load-post-new.php');
        // Personen mit oder ohne bestimmte Funktionen. Andere Ansprechpartner (aus der Rubrik Kontakt) und Standorte können diesen zugeordnet werden
    //    add_submenu_page('edit.php?post_type=person', __('Person hinzufügen', 'fau-person'), __('Neue Person', 'fau-person'), 'edit_persons', 'new_person', array( $this, 'add_person_types' ));
        // Kontakte, z.B. Vorzimmer, Sekretariat, Abteilungen. Hier sind Ansprechpartner aus den Personen zuordenbar, wird direkt über CPT angezeigt
  //      add_submenu_page('edit.php?post_type=person', __('Einrichtung hinzufügen', 'fau-person'), __('Neue Einrichtung', 'fau-person'), 'edit_persons', 'new_einrichtung', array( $this, 'add_person_types' ));
        // Zentrale Adressen, können in Personen und Kontakte übernommen werden
        add_submenu_page('edit.php?post_type=person', __('Standort hinzufügen', 'fau-person'), __('Neuer Standort', 'fau-person'), 'edit_persons', 'new_standort', array( $this, 'add_person_types' ));
    //    add_action('load-person_page_new_person', array( $this, 'person_menu' ));
  //      add_action('load-person_page_new_einrichtung', array( $this, 'einrichtung_menu' ));
        add_action('load-person_page_new_standort', array( $this, 'standort_menu' ));
    }
    
    public function add_person_types() {
        //wp_redirect( admin_url( 'post-new.php?post_type=standort' ) );
            //add_action( 'load-person_page_konakt', array( $this, 'adding_custom_meta_boxes' ));  
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

}