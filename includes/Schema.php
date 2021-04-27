<?php

namespace FAU_Person;
use function FAU_Person\Config\getSocialMediaList;

defined('ABSPATH') || exit;


class Schema {
    /*
     * Create Schema Markup for Place
     */
    
    public static function get_SocialMediaList() {
        return getSocialMediaList(); // Standard-Array für zukünftige Optionen
    } 
    
    public static function create_Place($data, $itemprop = 'location', $class = '', $surroundingtag = 'div', $widthbreak = true, $widthaddress = true, $phoneuri = true ) {

	if (!is_array($data)) {
	    return;
	}
	$filled = false;
	$res = '<'.$surroundingtag;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'" itemscope';
	}
	if (!empty($class)) {
	    $res .= ' class="'.$class.'"';
	}
	$res .= ' itemtype="http://schema.org/Place">';	
	
	if ((isset($data['name'])) && (!empty(trim($data['name'])))) {	    
	    $res .= '<span itemprop="name">'.esc_html($data['name']).'</span>';
	    $filled = true;
	    if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['telephone'])) && (!empty(trim($data['telephone'])))) {	    

	    $number = self::get_sanitized_phone($data['telephone']);
	    if ($phoneuri) {
		$res .= '<a itemprop="telephone" href="tel:'.self::get_telephone_uri($number).'">' . $number . '</a>';
	    } else {
		$res .= '<span itemprop="telephone">' . $number . '</span>';
	    }

	     $filled = true;
	     if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['faxNumber'])) && (!empty(trim($data['faxNumber'])))) {	    
	    $number = self::get_sanitized_phone($data['faxNumber']);
	    if ($phoneuri) {
		$res .= '<a itemprop="telephone" href="tel:'.self::get_telephone_uri($number).'">' . $number . '</a>';
	    } else {
		$res .= '<span itemprop="telephone">' . $number . '</span>';
	    }
	     $filled = true;
	   if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['url'])) && (!empty(trim($data['url'])))) {	    
	    $res .= '<a itemprop="url" href="'.esc_url($data['url']).'">'.$data['url'].'</a>';
	     $filled = true;
	     if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	
	
	if ($widthaddress) {
	    $postal = self::create_PostalAdress($data, 'address','', 'address', $widthbreak);
	    if (!empty($postal)) {
		$res .= $postal;
		$filled = true;
	    }
	} 

	$res .= '</'.$surroundingtag.'>';
	if ( $filled ) {
	    return $res;
	}
	return;
    }
    
    
    /*
     * Create Schema Markup for PostalAdress
     */
    public static function create_PostalAdress($data, $itemprop = 'address', $class = '', $surroundingtag = 'address', $widthbreak = true ) {
	if (!is_array($data)) {
	    return;
	}
	$filled = false;
	$res = '<'.$surroundingtag;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'" itemscope';
	}
	if (!empty($class)) {
	    $res .= ' class="'.esc_attr($class).'"';
	}
	$res .= ' itemtype="http://schema.org/PostalAddress">';
	
	if ((isset($data['workLocation'])) && (!empty(trim($data['workLocation'])))) {
	    $res .= '<span class="screen-reader-text">' . __('Raum', 'fau-person') . ': </span>';
	    $res .= '<span class="room">'.esc_html($data['workLocation']).'</span>';
	    $filled = true;
	    if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	
	if ((isset($data['streetAddress'])) && (!empty(trim($data['streetAddress'])))) {
	    $res .= '<span itemprop="streetAddress">'.esc_html($data['streetAddress']).'</span>';
	    $filled = true;
	    if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['addressLocality'])) && (isset($data['postalCode'])) && (!empty(trim($data['addressLocality'])))  && (!empty(trim($data['postalCode']))) ) {
	    $res .= '<span class="person-info-city">';
	}
	  
	if ((isset($data['postalCode'])) && (!empty(trim($data['postalCode'])))) {
	     $res .= '<span itemprop="postalCode">'.esc_html($data['postalCode']).'</span>';
	     $filled = true;
	     if ($widthbreak) {
		$res .= ' ';
	    }
	}
	if ((isset($data['addressLocality'])) && (!empty(trim($data['addressLocality'])))) {
	    $res .= '<span itemprop="addressLocality">'.esc_html($data['addressLocality']).'</span>';
	     $filled = true;
	}
	if ((isset($data['addressLocality'])) && (isset($data['postalCode'])) && (!empty(trim($data['addressLocality'])))  && (!empty(trim($data['postalCode']))) ) {
	    $res .= '</span>';
	}
	if ((isset($data['addressLocality'])) && $widthbreak && (!empty(trim($data['addressLocality'])))) {
	    $res .= '<br>';
	}
	
	if ((isset($data['addressRegion'])) && (!empty(trim($data['addressRegion'])))) {
	    $res .= '<span itemprop="addressRegion">'.esc_html($data['addressRegion']).'</span>';
	    $filled = true;
	    if (($widthbreak) && (isset($data['addressCountry'])) && (!empty(trim($data['addressCountry'])))) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['addressCountry'])) && (!empty(trim($data['addressCountry'])))) {
	    $res .= '<span itemprop="addressCountry">'.esc_html($data['addressCountry']).'</span>';
	    $filled = true;
	}
	$res .= '</'.$surroundingtag.'>';
	
	if ( $filled ) {
	    return $res;
	}
	return;
	
    }
    
    
     
    public static function create_Name( $data, $itemprop = 'name', $class = '', $surroundingtag = 'span', $suffixbracket = false, $args = array() ) {
	if (!is_array($data)) {
	    return;
	}
	
	$url = '';
	$thisurl = '';
	if (isset($data['morelink'])) {    
	   $thisurl  = $data['morelink'];
	}
	if (isset($thisurl) && (!empty(esc_url($thisurl)))) {
	    $url = $thisurl;
	    $surroundingtag = 'a';
	}
	
	if (($surroundingtag == 'a') && (empty(esc_url($url)))) {
	     $surroundingtag = 'span';
	}    
	    

	$res = '<'.$surroundingtag;
	
	if (!empty($class)) {
	    $res .= ' class="'.esc_attr($class).'"';
	}
	if ($surroundingtag == 'a') {
	    $res .= ' href="'.esc_url($url).'"';
	} elseif (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'"';
	}
	$res .= '>';
	if (($surroundingtag == 'a') && (!empty($itemprop))) {
	    $res .= '<span itemprop="'.$itemprop.'">';
	}
	    
	    
	
	$honorificPrefix = $honorificSuffix = $givenName = $familyName = $fullname = '';
	
	if ((isset($data['honorificPrefix'])) && (!empty($data['honorificPrefix']))) {
	    $honorificPrefix = '<span itemprop="honorificPrefix">' . esc_html($data['honorificPrefix']) . '</span>';
	}
	if ((isset($data['honorificSuffix'])) && (!empty($data['honorificSuffix']))) {
	    $honorificSuffix = '<span itemprop="honorificSuffix">' . esc_html($data['honorificSuffix']) . '</span>';
	}
	

	if ((isset($data['givenName'])) && (!empty($data['givenName']))) {
	    $givenName  = '<span itemprop="givenName">' . esc_html($data['givenName']) . '</span>';
	}
	if ((isset($data['familyName'])) && (!empty($data['familyName']))) {
	    $familyName  = '<span itemprop="familyName">' . esc_html($data['familyName']) . '</span>';
	}
	
	if ((!empty($givenName)) || (!empty($familyName))) {
	    $fullname = '<span class="fullname">'. $givenName.' '.$familyName.'</span>';   
	} elseif ((isset($data['name'])) && (!empty($data['name']))) {
	    $fullname = $data['name'];   
	} elseif ((isset($data['alternateName'])) && (!empty($data['alternateName']))) {
	    $fullname = '<span itemprop="alternateName">' . $data['alternateName'] . '</span>';
	}
	
	if (!empty($fullname))  {
	    
	    if (!empty($honorificPrefix)) {
		$res .= $honorificPrefix. ' ';
	    }
	    $res .= $fullname;
	    if (!empty($honorificSuffix)) {
		if ($suffixbracket) {
		    $res .= ' ('.$honorificSuffix.')';
		} else {
		    $res .= ', '.$honorificSuffix;
		}
	    }
	    
	    if (($surroundingtag == 'a') && (!empty($itemprop))) {
		$res .= '</span>';
	    }

	    $res .= '</'.$surroundingtag.'>';
	    return $res;
	} else {
	   $res .= $data['kontakt_title'];
	   if (($surroundingtag == 'a') && (!empty($itemprop))) {
		$res .= '</span>';
	    }

	    $res .= '</'.$surroundingtag.'>';
	    return $res;
	}

	return;
    }
    public static function create_NameforAttributs($data) {
	if (!is_array($data)) {
	    return;
	}
	$givenName = $familyName = '';
	
	if ((isset($data['givenName'])) && (!empty($data['givenName']))) {
	    $givenName  = esc_html($data['givenName']);
	}
	if ((isset($data['familyName'])) && (!empty($data['familyName']))) {
	    $familyName  = esc_html($data['familyName']);
	}
	
	if ((!empty($givenName)) || (!empty($familyName))) {
	    $fullname = $givenName.' '.$familyName;   
	} elseif ((isset($data['name'])) && (!empty($data['name']))) {
	    $fullname = esc_html($data['name']);   
	} elseif ((isset($data['alternateName'])) && (!empty($data['alternateName']))) {
	    $fullname = esc_html($data['alternateName']);
	}
	return esc_attr($fullname);
    }
    
    public static function create_contactpointlist($data, $blockstart = 'ul', $itemprop = '', $class = 'person-info', $liststart = 'li', $args = array(), $fillempty = false, $addcomma = false) {
	if (!is_array($data)) {
	    return;
	}
	$filled = false;
	$res  = '';
	if (!empty($blockstart)) {
	    $res .= '<'.$blockstart;
	    if (!empty($itemprop)) {
		$res .= ' itemprop="'.$itemprop.'"';
	    }
	    if (!empty($class)) {
		$res .= ' class="'.esc_attr($class).'"';
	    }
	    $res .= '>';
	}
	$phoneuri = true;
	$intformat = true;
	if (isset($args) && is_array($args)) {	    
	    if (isset($args['view_telefonlink'])) {
		 $phoneuri = $args['view_telefonlink'];
	    }
	    if (isset($args['view_telefon_intformat'])) {
		 $intformat = $args['view_telefon_intformat'];
	    }  
	}
	
	
	if ((isset($data['telephone'])) && (!empty($data['telephone']))) {
	    $res .= '<'.$liststart.' class="person-info-phone telephone">';
	    $res .= '<span class="screen-reader-text">' . __('Telefon', 'fau-person') . ': </span>';
	    $number = self::get_sanitized_phone($data['telephone']);
	    $displaynumber = $number; 
	    if ($intformat==false) {
		$displaynumber = self::get_national_telefon_format($number);
	    }
	    
	    if ($phoneuri) {
		$res .= '<a itemprop="telephone" href="tel:'.self::get_telephone_uri($number).'">' . $displaynumber . '</a>';
	    } else {
		$res .= '<span itemprop="telephone">' . $displaynumber . '</span>';
	    }
	    $res .= '</'.$liststart.'>';
	    if (($blockstart !=='ul') && ($addcomma)) {
		 $res .= ', ';
	    }
	    $filled = true;
	} elseif (($fillempty) && isset($data['telephone'])) {
	    $res .= '<'.$liststart.'>';
	    $res .= '</'.$liststart.'>';
	    $filled = true;
	}
	
	if ((isset($data['mobilePhone'])) && (!empty($data['mobilePhone']))) {
	    $res .= '<'.$liststart.' class="person-info-mobile mobilePhone">';
	    $res .= '<span class="screen-reader-text">' . __('Mobil', 'fau-person') . ': </span>';
	    $number = self::get_sanitized_phone($data['mobilePhone']);
	    $displaynumber = $number; 
	    if ($intformat==false) {
		$displaynumber = self::get_national_telefon_format($number);
	    }
	    if ($phoneuri) {
		$res .= '<a class="mobile" itemprop="telephone" href="tel:'.self::get_telephone_uri($number).'">' . $displaynumber . '</a>';
	    } else {
		$res .= '<span class="mobile" itemprop="telephone">' . $displaynumber . '</span>';
	    }
	    $res .= '</'.$liststart.'>';
	    if (($blockstart !=='ul') && ($addcomma)) {
		 $res .= ', ';
	    }
	     $filled = true;
	} elseif (($fillempty)  && isset($data['mobilePhone'])) {
	    $res .= '<'.$liststart.'>';
	    $res .= '</'.$liststart.'>';
	    $filled = true;
	}
	
	
	if ((isset($data['faxNumber'])) && (!empty($data['faxNumber']))) {
	    $res .= '<'.$liststart.' class="person-info-fax faxNumber">';
	    $res .= '<span class="screen-reader-text">' . __('Faxnummer', 'fau-person') . ': </span>';
	    $number = self::get_sanitized_phone($data['faxNumber']);
	    $displaynumber = $number; 
	    if ($intformat==false) {
		$displaynumber = self::get_national_telefon_format($number);
	    }
	    if ($phoneuri) {
		$res .= '<a itemprop="faxNumber" href="tel:'.self::get_telephone_uri($number).'">' . $displaynumber . '</a>';
	    } else {
		$res .= '<span itemprop="faxNumber">' . $displaynumber . '</span>';
	    }
	    $res .= '</'.$liststart.'>';
	    if (($blockstart !=='ul') && ($addcomma)) {
		 $res .= ', ';
	    }
	     $filled = true;
	} elseif (($fillempty)  && isset($data['faxNumber'])) {
	    $res .= '<'.$liststart.'>';
	    $res .= '</'.$liststart.'>';
	    $filled = true;
	}
	
	
	if ((isset($data['email'])) && (!empty($data['email']))) {
	    $res .= '<'.$liststart.' class="person-info-email email">';
	    $res .= '<span class="screen-reader-text">' . __('E-Mail', 'fau-person') . ': </span>';
	    $res .= '<a itemprop="email" href="mailto:'.self::get_email_uri($data['email']).'">' . self::get_email_uri($data['email']) . '</a>';
	    $res .= '</'.$liststart.'>';
	    if (($blockstart !=='ul') && ($addcomma)) {
		 $res .= ', ';
	    }
	     $filled = true;
	} elseif (($fillempty)  && isset($data['email'])) {
	    $res .= '<'.$liststart.'>';
	    $res .= '</'.$liststart.'>';
	    $filled = true;
	}
	
	
	if ((isset($data['url'])) && (!empty($data['url']))) {
	    $res .= '<'.$liststart.' class="person-info-www url">';
	    $res .= '<span class="screen-reader-text">' . __('Webseite', 'fau-person') . ': </span>';
	    $res .= '<a itemprop="url" href="'.self::get_sanitized_url($data['url']).'">' . self::get_sanitized_url($data['url']) . '</a>';
	    $res .= '</'.$liststart.'>';
	    if (($blockstart !=='ul') && ($addcomma)) {
		 $res .= ', ';
	    }
	     $filled = true;
	} elseif (($fillempty)  && isset($data['url'])) {
	    $res .= '<'.$liststart.'>';
	    $res .= '</'.$liststart.'>';
	    $filled = true;
	}
	
	if (($blockstart !=='ul')  && ($addcomma)) {
	    $res = preg_replace('/\s*,\s*$/i', '', $res);
	}
	    
	if (!empty($blockstart)) {
	    $res .= '</'.$blockstart.'>';
	}
	if ( $filled ) {
	    return $res;
	}
	return;
    }
    public static function create_SocialMedialist($data, $blockstart = 'ul', $class = 'socialmedia', $itemel = 'li', $itemprop = 'sameAs') {
	if (!is_array($data)) {
	    return;
	}
	$res  = '';
	$filled = false;
	if (!empty($blockstart)) {
	    $res .= '<'.$blockstart;
	    if (!empty($class)) {
		$res .= ' class="'.esc_attr($class).'"';
	    }
	    $res .= '>';
	}
	$screenreaderadd = self::create_NameforAttributs($data);
	
	$SocialMedia = self::get_SocialMediaList();
	foreach ($SocialMedia as $key => $value) {
	    $datakey = $key."_url";
	    $name = $SocialMedia[$key]['title'];
	    $iclass = $SocialMedia[$key]['class'];
	    if (isset($data[$datakey]) && (!empty($data[$datakey]))) {
		$res .= '<'.$itemel.' class="'.$iclass.'">'.'<a itemprop="'.$itemprop.'" href="'.$data[$datakey].'">';
		$res .= $name;
		if (!empty($screenreaderadd)) {
		    $res .= '<span class="screen-reader-text">: '.__('Seite von','fau-person').' '.$screenreaderadd.'</span>';
		}
		$res .= '</a></'.$itemel.'>';
		$filled = true;
	    }	
	}
	

	if (!empty($blockstart)) {
	    $res .= '</'.$blockstart.'>';
	}
	if ( $filled ) {
	    return $res;
	}

	return;
    }
    
    public static function create_ContactPoint( $data, $blockstart = 'div', $itemprop = 'contactPoint', $class = '', $titletagopeninghours = 'strong') {	
	if (!is_array($data)) {
	    return;
	}
	$filled = false;
	$res = '<'.$blockstart;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'"';
	}
	if (!empty($class)) {
	    $res .= ' class="'.esc_attr($class).'"';
	}
	$res .= ' itemtype="http://schema.org/ContactPoint">';	
	
	$hoursAvailable = self::create_OpeningHours($data, 'div', 'hoursAvaible', '', $titletagopeninghours);
	if (!empty($hoursAvailable)) {
	    $res .= $hoursAvailable;
	    $filled = true;
	}
	
	$res .= '</'.$blockstart.'>';
	if ( $filled ) {
	    return $res;
	}
	return;	
    }
    
    public static function create_OpeningHours( $data, $blockstart = 'p', $itemprop = 'hoursAvaible', $class = '', $titletagopeninghours  = 'strong') {
	if (!is_array($data)) {
	    return;
	}
	$filled = false;
	
	$desctag = 'span';
	$descbreak = '<br>';
	$titletagbreak = ': ';
	
	if (substr($titletagopeninghours,0,1)=='h')  {
	    $desctag = 'p';
	    $descbreak = '';
	    $blockstart = 'div';
	    $titletagbreak = '';
	    
	} elseif ($blockstart == 'p') {
	    $titletagopeninghours = 'strong';
	    $titletagbreak = '<br>';
	}
	
	$res = '<'.$blockstart;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'"';
	}
	if (!empty($class)) {
	    $res .= ' class="'.$class.'"';
	}
	$res .= ' itemtype="http://schema.org/OpeningHoursSpecification">';	
	
	$hoursAvailable = $hoursAvailable_group = $hoursAvailable_text = '';
	
	if ((isset($data['hoursAvailable'])) && (!empty($data['hoursAvailable']))) {
	    $hoursAvailable = $data['hoursAvailable'];
	}
	if ((isset($data['hoursAvailable_group'])) && (!empty($data['hoursAvailable_group']))) {
	    $hoursAvailable_group = $data['hoursAvailable_group'];
	}
	if ((isset($data['hoursAvailable_text'])) && (!empty($data['hoursAvailable_text']))) {
	    $hoursAvailable_text = $data['hoursAvailable_text'];
	}

        if(!empty($hoursAvailable) || !empty($hoursAvailable_group)) {
            
            if(!empty($hoursAvailable_text)) {
                $res  .= '<'.$titletagopeninghours.' itemprop="name">' . esc_html($hoursAvailable_text) . '</'.$titletagopeninghours.'>';
            } else {
                $res  .= '<'.$titletagopeninghours.' itemprop="name">' . __('Sprechzeiten', 'fau-person') . '</'.$titletagopeninghours.'>';   
            }
	    $res .= $titletagbreak;
	    
            if ( $hoursAvailable ) {
                $res  .= '<'.$desctag.' itemprop="description">' . $hoursAvailable. '</'.$desctag.'>'; 
		// Notice: We allow HTML here, therfor no escapting
		$res  .= $descbreak;
            }
            if ( $hoursAvailable_group ) {
		if ((is_array($hoursAvailable_group)) && (count($hoursAvailable_group)>1)){
		     $res  .= '<ul class="hoursAvailable_group" itemprop="disambiguatingDescription">';
		     foreach ($hoursAvailable_group as $val) {
			 $res .= '<li>'.$val.'</li>';
		     }
		    $res  .= '</ul>';
		} else {
		
		    $res  .= '<span itemprop="disambiguatingDescription">';
		    $res  .= implode('<br>', $hoursAvailable_group);
		    $res  .= '</span>';
		}
            }
	    $filled = true;
        }
	
	$res .= '</'.$blockstart.'>';
	if ( $filled ) {
	    return $res;
	}
	return;	
	
	
    }
  
    public static function create_Organization($data, $blockstart = 'p', $itemprop = 'affiliation', $class = '', $withaddress = true, $withcontactpoints = true, $withOpeningHours = false) {
	if (!is_array($data)) {
	    return;
	}
	$filled = false;
	$res = '<'.$blockstart;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'"';
	}
	if (!empty($class)) {
	    $res .= ' class="'.esc_attr($class).'"';
	}
	$res .= ' itemtype="http://schema.org/Organization">';	
	if (isset($data['name']) && (!empty($data['name']))) { 
            $res .= '<span itemprop="name">' . esc_html($data['name']) . '</span><br>';	
	    $filled = true;
	}
	if (isset($data['parentOrganization']) && (!empty($data['parentOrganization']))) { 
            $res .= '<span itemprop="parentOrganization">' . esc_html($data['parentOrganization']) . '</span><br>';	
	    $filled = true;
	}
	if (isset($data['department']) && (!empty($data['department']))) { 
	    $res .= '<span itemprop="department">' . esc_html($data['department']) . '</span><br>';	
	    $filled = true;
	}
	if (isset($data['subOrganization']) && (!empty($data['subOrganization']))) { 
            $res .= '<span itemprop="subOrganization">' . esc_html($data['subOrganization']) . '</span><br>';	
	    $filled = true;
	}
	

	if ($withaddress) {
	    $adresse = self::create_PostalAdress($data, 'address','', 'address', true);
	    if (!empty($adresse)) {
		$res .= $adresse;
		$filled = true;
	    }
	}
	if ($withcontactpoints) {
	    $contactpointlist = self::create_contactpointlist($data, 'ul', '', '', 'li');
	     if (!empty($contactpointlist)) {
		$res .= $contactpointlist;
		$filled = true;
	    }
	}
	if ($withOpeningHours) {
	    $openinghours = self::create_OpeningHours($data);
	     if (!empty($openinghours)) {
		$res .= $openinghours;
		$filled = true;
	    }
	}
	$res .= '</'.$blockstart.'>';
	if ( $filled ) {
	    return $res;
	}
	return;	
    }
    
    
    public static function create_Image($data, $blockstart = 'figure', $itemprop = 'image', $class = '', $ariahidden = true, $targetlink = '', $targettitle = '' ) {
	if (!is_array($data)) {
	    return;
	}
	$filled = false;
	$res = '<'.$blockstart;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.esc_attr($itemprop).'"';
	}
	if ((isset($data['caption'])) && (!empty($data['caption']))) {
	    if (!empty($class)) {
		$class .= ' ';
	    }
	    $class .= 'with-caption';
	}
	$meta = '';
	if (!empty($class)) {
	    $res .= ' class="'.esc_attr($class).'"';
	}
	if ($ariahidden) {
	    $res .= ' aria-hidden="true" role="presentation"';
	}

	$res .= ' itemtype="http://schema.org/ImageObject">';	
	
	if ((isset($targetlink)) && (!empty($targetlink))) {
	    $res .= '<a href="'.esc_url($targetlink).'"';
	    if ((isset($targettitle)) && (!empty($targettitle))) {
		$res .= ' title="'.esc_attr($targettitle).'"';	
	    }
	    if ($ariahidden) {
		$res .= ' tabindex="-1"';
	    }
	    $res .= '>';
	    $meta .= '<meta itemprop="identifier" content="'.esc_url($targetlink).'">';

	}
	if (isset($data['src'])) {

	     $res .= '<img src="'.esc_url($data['src']).'" itemprop="contentUrl"';
	     $filled = true;

	     if ((isset($data['alt'])) && (!empty($data['alt']))) {
		 $res .= ' alt="'.esc_attr($data['alt']).'"';
	     }
	     if ((isset($data['title'])) && (!empty($data['title']))) {
		 $res .= ' alt="'.esc_attr($data['title']).'"';
	     }
	     if ((isset($data['width'])) && (!empty($data['width']))) {
		 $res .= ' width="'.esc_attr($data['width']).'"';
		 $meta .= '<meta itemprop="width" content="'.esc_attr($data['width']).'">';
	     }
	     if ((isset($data['height'])) && (!empty($data['height']))) {
		 $res .= ' height="'.sanitize_key($data['height']).'"';
		 $meta .= '<meta itemprop="height" content="'.esc_attr($data['height']).'">';
	     }
	     if ((isset($data['srcset'])) && (!empty($data['srcset']))) {
		$res .= ' srcset="'.esc_attr($data['srcset']).'"';
	     }
	     if ((isset($data['sizes'])) && (!empty($data['sizes']))) {
		$res .= ' sizes="'.esc_attr($data['sizes']).'"';
	     } 
	     $res .= '>';
		 

	}
	if ((isset($targetlink)) && (!empty($targetlink))) {
	    $res .= '</a>';
	}
	if (!empty($meta)) {
	    $res .= $meta;
	}
	
	if ((isset($data['caption'])) && (!empty($data['caption']))) {
	    $res .= '<figcaption itemprop="caption">';
	    $res .= esc_html( $data['caption'] ); 
	    $res .= '</figcaption>'; 
	}
	
	
	$res .= '</'.$blockstart.'>';
	if ( $filled ) {
	    return $res;
	}
	return;	
    }
    
    
    private static function get_telephone_uri($number) {
	if (!isset($number)) {
	    return;
	}
	
	$res = preg_replace("/[\s]+/", "-", trim($number));
	$res = preg_replace("/[^0-9\-\+\.]+/", "", $res);
	return $res;
    }
    
    private static function get_national_telefon_format($number) {
	if (!isset($number)) {
	    return;
	}
	
	$res = preg_replace("/^\+\d\d\s+/", "0", trim($number));
	return $res;
    }
    
    private static function get_email_uri($email) {
	if ((!isset($email)) || (empty($email))) {
	    return;
	}
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);
	$res = strtolower($email);

	return $res;
    }
         private static function get_sanitized_phone($number) {
	if ((!isset($number)) || (empty($number))) {
	    return;
	}
	$res = preg_replace("/[^\(\)0-9\-\+\s]+/", "", $number);

	return $res;
    }
     private static function get_sanitized_url($url) {
	if ((!isset($url)) || (empty($url))) {
	    return;
	}
	$url = filter_var($url, FILTER_SANITIZE_URL);

	return $url;
    }
}
