<?php
use FAU_Person\Main;
use FAU_Person\Settings;
use FAU_Person\Shortcodes\Kontakt;

if (!class_exists('FAUPersonWidget')) {
    class FAUPersonWidget extends WP_Widget {
	public function __construct() {
	    parent::__construct(
		'FAUPersonWidget', __('Kontakt-Visitenkarte', 'fau-person'), 
		array('description' => __('Kontakt-Visitenkarte anzeigen', 'fau-person'), 'class' => 'FAUPersonWidget')
	    );
	}

	public function form($instance) {           
	    $default = array(
		'title' => '',
		'id' => '',
		'bild' => '',
		'show_ansprechpartner' => '',
	    );
	    $instance = wp_parse_args((array) $instance, $default);   
	    $persons = new WP_Query(array('post_type' => 'person', 'posts_per_page' => -1));

	    if (!empty($persons->post_title)) {
		$name = $persons->post_title;
	    } else {
		$name = $this->get_field_id('firstname') . ' ' . $this->get_field_id('lastname');
	    }
	    echo '<p>';
	    echo '<label for="' . $this->get_field_id('title') . '">' . __('Titel', 'fau-person') . ': ';
	    echo '<input class="widefat" type="text" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" value="' . esc_attr($instance['title']) . '" />';
	    echo '</label>';
	    echo '</p>';
	    echo '<p>';
	    echo '<label for="' . $this->get_field_id('id') . '">' . __('Kontakt', 'fau-person') . ': ';
	    echo '</label>';
	    echo '<select class="widefat" id="' . $this->get_field_id('id') . '" name="' . $this->get_field_name('id') . '">';
	    foreach ($persons->posts as $item) {
		echo '<option value="' . $item->ID . '"';
		if ($item->ID == esc_attr($instance['id']))
		    echo ' selected';
		echo '>' . $item->post_title . '</option>';
	    }
	    echo '</select>';
	   
	    echo '</p>';   
	    ?>
	    <p>   
	    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('bild'); ?>" name="<?php echo $this->get_field_name('bild'); ?>" <?php checked( $instance[ 'bild' ], 'on' ); ?>  />
	    <label for="<?php echo $this->get_field_id('bild'); ?>"><?php echo __('Kontaktbild anzeigen', 'fau-person'); ?>
	    </label>
	    </p>
	     <p>   
	    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_ansprechpartner'); ?>" name="<?php echo $this->get_field_name('show_ansprechpartner'); ?>" <?php checked( $instance[ 'show_ansprechpartner' ], 'on' ); ?>  />
	    <label for="<?php echo $this->get_field_id('show_ansprechpartner'); ?>"><?php echo __('Ansprechpartner anzeigen', 'fau-person'); ?>
	    </label>
	    </p>
	    <?php 
	    }

	public function update($new_instance, $old_instance) {
	    $instance = $old_instance;
	    $instance['id'] = $new_instance['id'];
	    $instance['title'] = $new_instance['title'];
	    $instance['bild'] = $new_instance['bild'];
	    $instance['show_ansprechpartner'] = $new_instance['show_ansprechpartner'];
	    return $instance;
	}

	public function widget($args, $instance) {
    
	    $settings = new Settings(PLUGIN_FILE);
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
	    
	    Main::enqueueForeignThemes();
	    
	    echo $before_widget;
	    $id = empty($instance['id']) ? ' ' : $instance['id'];
	    $title = empty($instance['title']) ? '' : $instance['title'];
	    if(array_key_exists('bild', $instance)) {
		$bild = empty($instance['bild']) ? false : true;
	    } else {
		$bild = $shortcodeopt['bild'];
	    }
	    
	    if(array_key_exists('show_ansprechpartner', $instance)) {
		$show_ansprechpartner = empty($instance['show_ansprechpartner']) ? false : true;
	    } else {
		$show_ansprechpartner = $shortcodeopt['show_ansprechpartner'];
	    }

	    echo FAU_Person\Data::fau_person_sidebar($id, $title, 0, $shortcodeopt['organisation'], $shortcodeopt['abteilung'], $shortcodeopt['position'], 1, 1, $shortcodeopt['adresse'], $shortcodeopt['workLocation'], $shortcodeopt['telefon'], $shortcodeopt['fax'], $shortcodeopt['mobil'], $shortcodeopt['mail'], $shortcodeopt['webseite'], 1, $shortcodeopt['kurzauszug'], $shortcodeopt['sprechzeiten'], $bild, $show_ansprechpartner, 3);
	    echo $after_widget;
	}

    }
}