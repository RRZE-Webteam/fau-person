<?php
use FAU_Person\Main;
use FAU_Person\Data;
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
	    );
	    $instance = wp_parse_args((array) $instance, $default);   
	    $persons = new WP_Query(array('post_type' => 'person', 'nopaging' => true));

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
	   
	    <?php 
	    }

	public function update($new_instance, $old_instance) {
	    $instance = $old_instance;
	    $instance['id'] = $new_instance['id'];
	    $instance['title'] = $new_instance['title'];
	    return $instance;
	}

	public function widget($args, $instance) {
	    extract($args, EXTR_SKIP);
	    
	    Main::enqueueForeignThemes();
	    
	    echo $before_widget;
	    $id = empty($instance['id']) ? ' ' : $instance['id'];
	    $title = empty($instance['title']) ? '' : $instance['title'];

	    $hstart = 3;
	    if (isset($title) && (!empty($title))) {
		echo '<h' . ($hstart-1) . '>' . $title . '</h' . ($hstart-1) . '>' . "\n";
	    }
 

	    $displayfield = Data::get_display_field('sidebar');
	    
	    echo FAU_Person\Data::fau_person_sidebar($id, $displayfield);
	    echo $after_widget;
	}

    }
}