<?php



class FAUPersonWidget extends WP_Widget
{
	public function __construct() {
            parent::__construct(
                    'FAUPersonWidget',
                    __('Personen-Visitenkarte', FAU_PERSON_TEXTDOMAIN),
                    array('description' => __('Personen-Visitenkarte anzeigen', FAU_PERSON_TEXTDOMAIN), 'class' => 'FAUPersonWidget')
            );
	}

	public function form($instance)
	{
            
            $default = array(
                'title' => '',
                'id' => '',
            );
		$instance = wp_parse_args( (array) $instance, $default );
		$id = $instance['id'];
		$title = $instance['title'];
		
                $persons = new WP_Query(array('post_type' => 'person', 'posts_per_page' => -1));
					           		
		if(!empty($persons->post_title)) {
			$name = $persons->post_title;
		}
		else
		{
			$name = $this->get_field_id('firstname').' '.$this->get_field_id('lastname');
		}
		echo '<p>';
			echo '<label for="'.$this->get_field_id('title').'">'. __('Titel', FAU_PERSON_TEXTDOMAIN). ': ';
				echo '<input type="text" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.esc_attr($title).'" />';
			echo '</label>';
		echo '</p>';

		echo '<p>';
			echo '<label for="'.$this->get_field_id('id').'">' . __('Person', FAU_PERSON_TEXTDOMAIN). ': ';

                        echo '<select id="'.$this->get_field_id('id').'" name="'.$this->get_field_name('id').'">';
		
                                foreach($persons->posts as $item)
					{
                                      
						echo '<option value="'.$item->ID.'"';
							if($item->ID == esc_attr($id)) echo ' selected';
						echo '>'.$item->post_title.'</option>';
					}
				echo '</select>';
			echo '</label>';
		echo '</p>';     
	}

	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['id'] = $new_instance['id'];
		$instance['title'] = $new_instance['title'];
		return $instance;
	}

	public function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		$id = empty($instance['id']) ? ' ' : $instance['id'];
		$title = empty($instance['title']) ? ' ' : $instance['title'];

		if (!empty($id))
		{
			$post = get_post($id);
			
                        $honorificPrefix = get_post_meta($id, 'fau_person_honorificPrefix', true);
                        $givenName = get_post_meta($id, 'fau_person_givenName', true);
                        $familyName = get_post_meta($id, 'fau_person_familyName', true);
                        $honorificSuffix = get_post_meta($id, 'fau_person_honorificSuffix', true);
                        $jobTitle = get_post_meta($id, 'fau_person_jobTitle', true);
                        $worksFor = get_post_meta($id, 'fau_person_worksFor', true);
                        $telephone = get_post_meta($id, 'fau_person_telephone', true);
                        $faxNumber = get_post_meta($id, 'fau_person_faxNumber', true);
                        $email = get_post_meta($id, 'fau_person_email', true);
                        $url = get_post_meta($id, 'fau_person_url', true);
                        $streetAddress = get_post_meta($id, 'fau_person_streetAddress', true);
                        $postalCode = get_post_meta($id, 'fau_person_postalCode', true);
                        $addressLocality = get_post_meta($id, 'fau_person_addressLocality', true);
                        $addressCountry = get_post_meta($id, 'fau_person_addressCountry', true);
                        $workLocation = get_post_meta($id, 'fau_person_workLocation', true);
                        $hoursAvailable = get_post_meta($id, 'fau_person_hoursAvailable', true);
                        $pubs = get_post_meta($id, 'fau_person_pubs', true);
                        $freitext = get_post_meta($id, 'fau_person_freitext', true);
                        $link = get_post_meta($id, 'fau_person_link', true);             
                        
                        
                        if($streetAddress || $postalCode || $addressLocality || $addressCountry) {
                                $contactpoint = '<li class="person-info-address"><span class="screen-reader-text">'.__('Adresse',FAU_PERSON_TEXTDOMAIN).': </span><br>';    
                                                
                                if($streetAddress)          $contactpoint .= '<span class="person-info-street" itemprop="streetAddress">'.$streetAddress.'</span>';
                                if($streetAddress && ($postalCode || $addressLocality)) $contactpoint .= '<br>';
                                if($postalCode || $addressLocality) {
                                        $contactpoint .= '<span class="person-info-city">';
                                        if($postalCode)         $contactpoint .= '<span itemprop="postalCode">'.$postalCode.'</span> ';  
                                        if($addressLocality)	$contactpoint .= '<span itemprop="addressLocality">'.$addressLocality.'</span>';
                                        $contactpoint .= '</span>';
                                        }
                                if(($streetAddress || $postalCode || $addressLocality) && $addressCountry)                    $contactpoint .= '<br>';
                                if($addressCountry)         $contactpoint .= '<span class="person-info-country" itemprop="addressCountry">'.$addressCountry.'</span></';
                                $contactpoint .= '</li>';                                                
                        }
                                                

                        /*
                        if($streetAddress)  $contactPoint = '<li class="person-info-street"><span class="screen-reader-text">'.__('Straße',FAU_PERSON_TEXTDOMAIN).': </span><span itemprop="streetAddress">'.$streetAddress.'</span></li>';
                        if($postalCode || $addressLocality) {
                                if(empty($contactPoint)) $contactPoint = "";
                                $contactPoint .= '<li class="person-info-city"><span class="screen-reader-text">'.__('Wohnort',FAU_PERSON_TEXTDOMAIN).': </span>';
                                if($postalCode)     $contactPoint .= '<span itemprop="postalCode">'.$postalCode.'</span> ';  
                                if($addressLocality)	$contactPoint .= '<span itemprop="addressLocality">'.$addressLocality.'</span';
                                $contactPoint .= '</li>';
                        }
                        if($addressCountry) {	
                            if(empty($contactPoint)) $contactPoint = "";
                            $contactPoint .= '<li class="person-info-country"><span class="screen-reader-text">'.__('Land',FAU_PERSON_TEXTDOMAIN).': </span><span itemprop="addressCountry">'.$addressCountry.'</span></li>';
                        }
                            */                    
                       
			
			$content = '<div class="person" itemscope itemtype="http://schema.org/Person">';
				if(!empty($title)) 					$content .= '<h2 class="small">'.$title.'</h2>';
				
				$content .= '<div class="row">';
				
					if(has_post_thumbnail($id))
					{
						$content .= '<div class="span1" itemprop="image">';
							$content .= get_the_post_thumbnail($id, 'person-thumb');
						$content .= '</div>';
					}
					
					$content .= '<div class="span3">';
						$content .= '<h3>';
							if($honorificPrefix) 	$content .= $honorificPrefix.' ';
							/*if($givenName) 	$content .= $givenName.' ';
							if($familyName) 		$content .= $familyName;*/
                                                        $content .= get_the_title($id);
							if($honorificSuffix) 	$content .= ' '.$honorificSuffix;
						$content .= '</h3>';
						$content .= '<ul class="person-info">';
							if($jobTitle) 		$content .= '<li class="person-info-position"><span class="screen-reader-text">'.__('Tätigkeit',FAU_PERSON_TEXTDOMAIN).': </span><strong><span itemprop="jobTitle">'.$jobTitle.'</span></strong></li>';
							if($worksFor)	$content .= '<li class="person-info-institution"><span class="screen-reader-text">'.__('Einrichtung',FAU_PERSON_TEXTDOMAIN).': </span><span itemprop="worksFor">'.$worksFor.'</span></li>';
							if($telephone)			$content .= '<li class="person-info-phone"><span class="screen-reader-text">'.__('Telefonnummer',FAU_PERSON_TEXTDOMAIN).': </span><span itemprop="telephone">'.$telephone.'</span></li>';
							if($faxNumber)			$content .= '<li class="person-info-fax"><span class="screen-reader-text">'.__('Faxnummer',FAU_PERSON_TEXTDOMAIN).': </span><span itemprop="faxNumber">'.$faxNumber.'</span></li>';
							if($email)			$content .= '<li class="person-info-email"><span class="screen-reader-text">'.__('E-Mail',FAU_PERSON_TEXTDOMAIN).': </span><a itemprop="email" href="mailto:'.strtolower($email).'">'.strtolower($email).'</a></li>';
							if($url)		$content .= '<li class="person-info-www"><span class="screen-reader-text">'.__('Webseite',FAU_PERSON_TEXTDOMAIN).': </span><a itemprop="url" href="'.$url.'">'.$url.'</a></li>';
							if(!empty($contactpoint))		$content .= $contactpoint;
							if($workLocation)			$content .= '<li class="person-info-room"><span class="screen-reader-text">' . __('Raum', FAU_PERSON_TEXTDOMAIN) .' </span><span itemprop="workLocation">'.$workLocation.'</span></li>';
							//	if($description)		$content .= '<div class="person-info-description">'.$description.'</div>';
						$content .= '</ul>';
					$content .= '</div>';
				$content .= '</div>';
			
			$content .= '</div>';
		}
		
		echo $content;
		
		echo $after_widget;
	}
}
