<?php



class FAUPersonWidget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'FAUPersonWidget', __('Kontakt-Visitenkarte', FAU_PERSON_TEXTDOMAIN), array('description' => __('Kontakt-Visitenkarte anzeigen', FAU_PERSON_TEXTDOMAIN), 'class' => 'FAUPersonWidget')
        );
    }

    public function form($instance) {           
        $default = array(
            'title' => '',
            'id' => '',
        );
        $instance = wp_parse_args((array) $instance, $default);
        $id = $instance['id'];
        $title = $instance['title'];

        $persons = new WP_Query(array('post_type' => 'person', 'posts_per_page' => -1));

        if (!empty($persons->post_title)) {
            $name = $persons->post_title;
        } else {
            $name = $this->get_field_id('firstname') . ' ' . $this->get_field_id('lastname');
        }
        echo '<p>';
        echo '<label for="' . $this->get_field_id('title') . '">' . __('Titel', FAU_PERSON_TEXTDOMAIN) . ': ';
        echo '<input type="text" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" value="' . esc_attr($title) . '" />';
        echo '</label>';
        echo '</p>';
        echo '<p>';
        echo '<label for="' . $this->get_field_id('id') . '">' . __('Kontakt', FAU_PERSON_TEXTDOMAIN) . ': ';

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
	}

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['id'] = $new_instance['id'];
        $instance['title'] = $new_instance['title'];
        return $instance;
    }

    public function widget($args, $instance) {
        extract($args, EXTR_SKIP);

        echo $before_widget;
        $id = empty($instance['id']) ? ' ' : $instance['id'];
        $title = empty($instance['title']) ? ' ' : $instance['title'];

        echo fau_person_sidebar($id, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 1, 0, 0, 1);
        echo $after_widget;
    }

}
