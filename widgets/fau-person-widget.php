<?php



class FAUPersonWidget extends WP_Widget
{
	function FAUPersonWidget()
	{
		$widget_ops = array('classname' => 'FAUPersonWidget', 'description' => __('Personen-Visitenkarte anzeigen', 'fau') );
		$this->WP_Widget('FAUPersonWidget', 'Personen-Visitenkarte', $widget_ops);
	}

	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'id' => -1, 'title' => '' ) );
		$id = $instance['id'];
		$title = $instance['title'];
		
		//$persons = query_posts('post_type=person');
		$persons = get_posts(array('post_type' => 'person', 'posts_per_page' => 9999));
                
                
		
		if(!empty($persons->post_title)) {
			$name = $persons->post_title;
		}
		else
		{
			$name = $this->get_field_id('firstname').' '.$this->get_field_id('lastname');
		}
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('title').'">'. __('Titel', 'fau'). ': ';
				echo '<input type="text" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.esc_attr($title).'" />';
			echo '</label>';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="'.$this->get_field_id('id').'">' . __('Person', 'fau'). ': ';
				echo '<select id="'.$this->get_field_id('id').'" name="'.$this->get_field_name('id').'">';
					foreach($persons as $item)
					{
						echo '<option value="'.$item->ID.'"';
							if($item->ID == esc_attr($id)) echo ' selected';
						echo '>'.$item->post_title.'</option>';
					}
				echo '</select>';
			echo '</label>';
		echo '</p>';     
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['id'] = $new_instance['id'];
		$instance['title'] = $new_instance['title'];
		return $instance;
	}

	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		$id = empty($instance['id']) ? ' ' : $instance['id'];
		$title = empty($instance['title']) ? ' ' : $instance['title'];

		if (!empty($id))
		{
			$post = get_post($id);
			
			
			$content = '<div class="person">';
				if(!empty($title)) 					$content .= '<h2 class="small">'.$title.'</h2>';
				
				$content .= '<div class="row">';
				
					if(has_post_thumbnail($id))
					{
						$content .= '<div class="span1">';
							$content .= get_the_post_thumbnail($id, 'person-thumb');
						$content .= '</div>';
					}
					
					$content .= '<div class="span3">';
						$content .= '<h3>';
							if(get_post_meta($id, 'fau_person_honorificPrefix', true)) 	$content .= get_post_meta($id, 'fau_person_honorificPrefix', true).' ';
							if(get_post_meta($id, 'fau_person_givenName', true)) 	$content .= get_post_meta($id, 'fau_person_givenName', true).' ';
							if(get_post_meta($id, 'fau_person_familyName', true)) 		$content .= get_post_meta($id, 'fau_person_familyName', true);
							if(get_post_meta($id, 'fau_person_honorificSuffix', true)) 	$content .= ' '.get_post_meta($id, 'fau_person_honorificSuffix', true);
						$content .= '</h3>';
						$content .= '<ul class="person-info">';
							if(get_post_meta($id, 'fau_person_jobTitle', true)) 		$content .= '<li class="person-info person-info-position"><strong>'.get_post_meta($id, 'fau_person_jobTitle', true).'</strong></li>';
							if(get_post_meta($id, 'fau_person_worksFor', true))	$content .= '<li class="person-info person-info-institution">'.get_post_meta($id, 'fau_person_worksFor', true).'</li>';
							if(get_post_meta($id, 'fau_person_telephone', true))			$content .= '<li class="person-info person-info-phone">'.get_post_meta($id, 'fau_person_telephone', true).'</li>';
							if(get_post_meta($id, 'fau_person_faxNumber', true))			$content .= '<li class="person-info person-info-fax">'.get_post_meta($id, 'fau_person_faxNumber', true).'</li>';
							if(get_post_meta($id, 'fau_person_email', true))			$content .= '<li class="person-info person-info-email"><a href="mailto:'.get_post_meta($id, 'fau_person_email', true).'">'.get_post_meta($id, 'email', true).'</a></li>';
							if(get_post_meta($id, 'fau_person_url', true))		$content .= '<li class="person-info person-info-www"><a href="http://'.get_post_meta($id, 'fau_person_url', true).'">'.get_post_meta($id, 'fau_person_url', true).'</a></li>';
							if(get_post_meta($id, 'fau_person_contactPoint', true))		$content .= '<li class="person-info person-info-address">'.get_post_meta($id, 'fau_person_contactPoint', true).'</li>';
							if(get_post_meta($id, 'fau_person_workLocation', true))			$content .= '<li class="person-info person-info-room">' . __('Raum', 'fau') . ' '.get_post_meta($id, 'fau_person_workLocation', true).'</li>';
							//	if(get_post_meta($id, 'fau_person_description', true))		$content .= '<div class="person-info person-info-description">'.get_post_meta($id, 'fau_person_description', true).'</div>';
						$content .= '</ul>';
					$content .= '</div>';
				$content .= '</div>';
			
			$content .= '</div>';
		}
		
		echo $content;
		
		echo $after_widget;
	}
}
