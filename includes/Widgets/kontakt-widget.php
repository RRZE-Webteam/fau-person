<?php
// use function FAU_Person\Config\getShortcodeSettings;
// use FAU_Person\Data;
use FAU_Person\Settings;
// use UnivIS_Data;
// use sync_helper;
// use function FAU_Person\Shortcodes\Kontakt\fau_person_sidebar;
// use function FAU_Person\Settings\getOptions;

if (!class_exists('FAUPersonWidget')) {
    class FAUPersonWidget extends WP_Widget {
	public function __construct() {
	    parent::__construct(
		'FAUPersonWidget', __('Kontakt-Visitenkarte', 'fau-person'), array('description' => __('Kontakt-Visitenkarte anzeigen', 'fau-person'), 'class' => 'FAUPersonWidget')
	    );
	}

	public function form($instance) {           
	    $default = array(
		'title' => '',
		'id' => '',
		'bild' => '',
	    );
	    $instance = wp_parse_args((array) $instance, $default);
	    $id = $instance['id'];
	    $title = $instance['title'];
	    $bild = $instance['bild'];

	    $persons = new WP_Query(array('post_type' => 'person', 'posts_per_page' => -1));

	    if (!empty($persons->post_title)) {
		$name = $persons->post_title;
	    } else {
		$name = $this->get_field_id('firstname') . ' ' . $this->get_field_id('lastname');
	    }
	    echo '<p>';
	    echo '<label for="' . $this->get_field_id('title') . '">' . __('Titel', 'fau-person') . ': ';
	    echo '<input type="text" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" value="' . esc_attr($title) . '" />';
	    echo '</label>';
	    echo '</p>';
	    echo '<p>';
	    echo '<label for="' . $this->get_field_id('id') . '">' . __('Kontakt', 'fau-person') . ': ';

	    echo '<select id="' . $this->get_field_id('id') . '" name="' . $this->get_field_name('id') . '">';
	    foreach ($persons->posts as $item) {
		echo '<option value="' . $item->ID . '"';
		if ($item->ID == esc_attr($id))
		    echo ' selected';
		echo '>' . $item->post_title . '</option>';
	    }
	    echo '</select>';
	    echo '</label>';
	    echo '</p>';   
	    ?>
	    <p>   
	    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('bild'); ?>" name="<?php echo $this->get_field_name('bild'); ?>" <?php checked( $instance[ 'bild' ], 'on' ); ?>  />
	    <label for="<?php echo $this->get_field_id('bild'); ?>"><?php echo __('Kontaktbild anzeigen', 'fau-person'); ?>
	    </label>
	    </p>
	    <?php 
	    }

	public function update($new_instance, $old_instance) {
	    $instance = $old_instance;
	    $instance['id'] = $new_instance['id'];
	    $instance['title'] = $new_instance['title'];
	    $instance['bild'] = $new_instance['bild'];
	    return $instance;
	}

	public function widget($args, $instance) {
    
	    $settings = new Settings($this->pluginFile);
	    $settings->onLoaded();
	    $options = $settings->options;
	    
	    $shortcodeopt = array();
	    
	    foreach ($options as $section => $field) {
		if (substr($section,0,7) === 'sidebar') {
		    $keyname = preg_replace('/sidebar_/i','',$section);
		    $shortcodeopt[$keyname] = $options[$section];
		}
	    } 
	    
	    extract($args, EXTR_SKIP);

	    echo $before_widget;
	    $id = empty($instance['id']) ? ' ' : $instance['id'];
	    $title = empty($instance['title']) ? '' : $instance['title'];
	    if(array_key_exists('bild', $instance)) {
		$bild = empty($instance['bild']) ? 0 : 1;
	    } else {
		$bild = $shortcodeopt['bild'];
	    }
	    echo \FAU_Person\Shortcodes\Kontakt::fau_person_sidebar($id, $title, 0, $shortcodeopt['organisation'], $shortcodeopt['abteilung'], $shortcodeopt['position'], 1, 1, $shortcodeopt['adresse'], $shortcodeopt['adresse'], $shortcodeopt['telefon'], $shortcodeopt['fax'], 0, $shortcodeopt['mail'], $shortcodeopt['webseite'], 0, $shortcodeopt['kurzauszug'], $shortcodeopt['sprechzeiten'], 0, $bild, 1, 3);
	    echo $after_widget;
	}

    }
}