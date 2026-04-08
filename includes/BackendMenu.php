<?php

namespace FAU_Person;
use RRZE\Lib\UnivIS\Data as UnivIS_Data;
defined('ABSPATH') || exit;


class BackendMenu {

    protected $pluginFile;
    private $settings = '';
    public $search_univis_id_page;
    
    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
    }


    public function onLoaded() {
        add_action('admin_menu', array($this, 'person_menu_subpages'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'add_options_pages'));

    }

    public function add_options_pages() {
        $this->search_univis_id_page = add_submenu_page('edit.php?post_type=person',
            __('Suche nach UnivIS-ID', 'fau-person'),
            __('Suche nach UnivIS-ID', 'fau-person'),
            'edit_persons', 'search-univis-id',
            array($this, 'search_univis_id'));
    }


    public function search_univis_id() {
        $searchData = $this->get_search_data();
        $firstname = $searchData['firstname'];
        $givenname = $searchData['givenname'];
        $hasSearch = $this->has_search_request();
        $person = [];

        if ($hasSearch) {
            $firstname = Helper::sonderzeichen($firstname);
            $givenname = Helper::sonderzeichen($givenname);
            $person = UnivIS_Data::get_univisdata(0, $firstname, $givenname);
        }

?>
        <div class="wrap">
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
        if ($hasSearch) {
            do_settings_sections('find_univis_id_options');
        }
        if ($hasSearch && (empty($person) || empty($person[0]))) {
            echo __('<div class="alert alert-warning">Es konnten keine Daten zur Person gefunden werden. Bitte verändern Sie Ihre Suchwerte.</div>', 'fau-person');
        }
        elseif ($hasSearch) {
            $person = Helper::array_orderby($person, "lastname", SORT_ASC, "firstname", SORT_ASC);
            $no_univis_data = __('keine Daten in UnivIS eingepflegt', 'fau-person');


            echo '<div id="results">';
            echo '<table class="wp-list-table widefat striped">';
            echo '<thead><tr><td>' . esc_html__('UnivIS Id', 'fau-person') . '</td><td>' . esc_html__('Name', 'fau-person') . '</td><td>' . esc_html__('E-Mail', 'fau-person') . '</td><td>' . esc_html__('Organisation', 'fau-person') . '</td></tr></thead>';
            echo '<tbody>';
            foreach ($person as $key => $value) {
                $location = $this->get_nested_value($person[$key], ['locations', 0, 'location', 0], null);

                if (is_array($location) && array_key_exists('email', $location)) {
                    $email = $location['email'];
                } else {
                    $email = $no_univis_data;
                }
                if (array_key_exists('id', $person[$key])) {
                    $id = $person[$key]['id'];
                } else {
                    $id = $no_univis_data;
                }
                if (array_key_exists('firstname', $person[$key])) {
                    $firstname = $person[$key]['firstname'];
                } else {
                    $firstname = __('Vorname', 'fau-person') . ": " . $no_univis_data . ", ";
                }
                if (array_key_exists('lastname', $person[$key])) {
                    $lastname = $person[$key]['lastname'];
                } else {
                    $lastname = __('Nachname', 'fau-person') . ": " . $no_univis_data;
                }
                if (array_key_exists('orgname', $person[$key])) {
                    $orgname = $person[$key]['orgname'];
                } else {
                    $orgname = $no_univis_data;
                }
                echo '<tr>';
                echo '<th>' . esc_html($id) . '</th>';
                echo '<td>' . esc_html($firstname . ' ' . $lastname) . '</td>';
                echo '<td>' . esc_html($email) . '</td>';
                echo '<td>' . esc_html($orgname) . '</td>';
                echo '</tr>';

            }

            echo '</tbody>';
            echo "</table>";
            echo "</div>";
        }
?>
        </div>
        <?php
    }

    public function admin_init() {
        add_settings_section('search_univis_id_section', __('Bitte geben Sie den Vor- und/oder Nachnamen der Person ein, von der Sie die UnivIS-ID benötigen.', 'fau-person'), '__return_false', 'search_univis_id_options');
        add_settings_field('univis_id_firstname', __('Vorname', 'fau-person'), array($this, 'univis_id_firstname'), 'search_univis_id_options', 'search_univis_id_section');
        add_settings_field('univis_id_givenname', __('Nachname', 'fau-person'), array($this, 'univis_id_givenname'), 'search_univis_id_options', 'search_univis_id_section');
        add_settings_section('find_univis_id_section', __('Folgende Daten wurden in UnivIS gefunden:', 'fau-person'), '__return_false', 'find_univis_id_options');
    }

    public function univis_id_firstname() {
        $optionname = $this->settings->optionName;
        $searchData = $this->get_search_data();
?>
        <input type='text' name="<?php printf('%s[firstname]', $optionname); ?>" value="<?php echo esc_attr($searchData['firstname']); ?>"><p class="description"><?php _e('Es können auch nur Teile des Namens eingegeben werden.', 'fau-person'); ?></p>
        <?php
    }

    public function univis_id_givenname() {
        $optionname = $this->settings->optionName;
        $searchData = $this->get_search_data();
?>
        <input type='text' name="<?php printf('%s[givenname]', $optionname); ?>" value="<?php echo esc_attr($searchData['givenname']); ?>"><p class="description"><?php _e('Es können auch nur Teile des Namens eingegeben werden.', 'fau-person'); ?></p>        
        <?php
    }

    private function has_search_request() {
        return isset($_POST['fau-person-search']);
    }

    private function get_search_data() {
        $optionname = $this->settings->optionName;

        if ($this->has_search_request()) {
            $requestData = isset($_POST[$optionname]) && is_array($_POST[$optionname]) ? $_POST[$optionname] : [];
            $searchData = [
                'firstname' => isset($requestData['firstname']) ? sanitize_text_field(wp_unslash($requestData['firstname'])) : '',
                'givenname' => isset($requestData['givenname']) ? sanitize_text_field(wp_unslash($requestData['givenname'])) : '',
            ];
            set_transient($this->settings->search_univis_id_transient, $searchData, 30);

            return $searchData;
        }

        $transient = get_transient($this->settings->search_univis_id_transient);

        return [
            'firstname' => is_array($transient) && isset($transient['firstname']) ? $transient['firstname'] : '',
            'givenname' => is_array($transient) && isset($transient['givenname']) ? $transient['givenname'] : '',
        ];
    }

    private function get_nested_value($data, array $path, $default = null) {
        $current = $data;

        foreach ($path as $segment) {
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
                continue;
            }

            return $default;
        }

        return $current;
    }




    public function person_menu_subpages() {
        add_submenu_page('edit.php?post_type=person', __('Standort hinzufügen', 'fau-person'), __('Neuer Standort', 'fau-person'), 'edit_persons', 'new_standort', array($this, 'standort_menu'));
    }


    public function einrichtung_menu() {
        wp_redirect(admin_url('post-new.php?post_type=person&fau_person_typ=einrichtung'));

    }

    public function standort_menu() {
        wp_redirect(admin_url('post-new.php?post_type=standort'));
        exit;
    }


}
