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

        if (!empty($id)) {
            $post = get_post($id);

            $fields = sync_helper::get_fields($id, get_post_meta($id, 'fau_person_univis_id', true), 0);
            extract($fields);

            if ($streetAddress || $postalCode || $addressLocality || $addressCountry) {
                $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">' . __('Adresse', FAU_PERSON_TEXTDOMAIN) . ': <br></span>';
                if ($streetAddress) {
                    $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">' . $streetAddress . '</span>';
                    if ($postalCode || $addressLocality) {
                        $contactpoint .= '<br>';
                    } elseif ($addressCountry) {
                        $contactpoint .= '<br>';
                    }
                }
                if ($postalCode || $addressLocality) {
                    $contactpoint .= '<span class="person-info-city">';
                    if ($postalCode)
                        $contactpoint .= '<span itemprop="postalCode">' . $postalCode . '</span> ';
                    if ($addressLocality)
                        $contactpoint .= '<span itemprop="addressLocality">' . $addressLocality . '</span>';
                    $contactpoint .= '</span>';
                    if ($addressCountry)
                        $contactpoint .= '<br>';
                }
                if ($addressCountry)
                    $contactpoint .= '<span class="person-info-country" itemprop="addressCountry">' . $addressCountry . '</span>';
                $contactpoint .= '</li>';
            }

            $content = '<div class="person" itemscope itemtype="http://schema.org/Person">';
            
            if (!empty($title)) 
                $content .= '<h2 class="small">' . $title . '</h2>';

            $content .= '<div class="row">';

            if (has_post_thumbnail($id)) {
                $content .= '<div class="span1" itemprop="image">';
                $content .= get_the_post_thumbnail($id, 'person-thumb');
                $content .= '</div>';
            }

            $content .= '<div class="span3">';
            $content .= '<h3>';
            if ($honorificPrefix)
                $content .= '<span itemprop="honorificPrefix">' . $honorificPrefix . '</span> ';
            if(get_post_meta( $id, 'fau_person_univis_sync', true)) {
                $content .= '<span itemprop="givenName">' . $givenName . '</span> <span itemprop="familyName">' . $familyName . '</span>';
            } elseif( !empty( get_the_title($id) ) ) {                                                
                $content .= get_the_title($id);
            }
            if ($honorificSuffix)
                $content .= ' <span itemprop="honorificSuffix">' . $honorificSuffix . '</span>';
            $content .= '</h3>';
            $content .= '<ul class="person-info">';
            if ($jobTitle)
                $content .= '<li class="person-info-position"><span class="screen-reader-text">' . __('Tätigkeit', FAU_PERSON_TEXTDOMAIN) . ': </span><strong><span itemprop="jobTitle">' . $jobTitle . '</span></strong></li>';
            if ($worksFor)
                $content .= '<li class="person-info-institution"><span class="screen-reader-text">' . __('Organisation', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="worksFor">' . $worksFor . '</span></li>';
            if ($department)
                $content .= '<li class="person-info-abteilung"><span class="screen-reader-text">' . __('Abteilung', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="department">' . $department . '</span></li>';
            if ($telephone)
                $content .= '<li class="person-info-phone"><span class="screen-reader-text">' . __('Telefonnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="telephone">' . $telephone . '</span></li>';
            if ($faxNumber)
                $content .= '<li class="person-info-fax"><span class="screen-reader-text">' . __('Faxnummer', FAU_PERSON_TEXTDOMAIN) . ': </span><span itemprop="faxNumber">' . $faxNumber . '</span></li>';
            if ($email)
                $content .= '<li class="person-info-email"><span class="screen-reader-text">' . __('E-Mail', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></li>';
            if ($url)
                $content .= '<li class="person-info-www"><span class="screen-reader-text">' . __('Webseite', FAU_PERSON_TEXTDOMAIN) . ': </span><a itemprop="url" href="' . $url . '">' . $url . '</a></li>';
            if (!empty($contactpoint))
                $content .= $contactpoint;
            if ($workLocation)
                $content .= '<li class="person-info-room"><span class="screen-reader-text">' . __('Raum', FAU_PERSON_TEXTDOMAIN) . ' </span><span itemprop="workLocation">' . $workLocation . '</span></li>';
            if ($description)
                $content .= '<div class="person-info-description">' . $description . '</div>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '</div>';

            $content .= '</div>';
        }
        echo $content;

        echo $after_widget;
    }

}
