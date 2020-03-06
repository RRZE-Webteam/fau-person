<?php

namespace FAU_Person;

defined('ABSPATH') || exit;


class Schema {
    /*
     * Create Schema Markup for Place
     */
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
	    $res .= '<span itemprop="name">'.$data['name'].'</span>';
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
		$res .= '<span itemprop="telephone">' . $data['telephone'] . '</span>';
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
		$res .= '<span itemprop="telephone">' . $data['telephone'] . '</span>';
	    }
	     $filled = true;
	   if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['url'])) && (!empty(trim($data['url'])))) {	    
	    $res .= '<a itemprop="url" href="'.$data['url'].'">'.$data['url'].'</a>';
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
	    $res .= ' class="'.$class.'"';
	}
	$res .= ' itemtype="http://schema.org/PostalAddress">';
	
	
	if ((isset($data['streetAddress'])) && (!empty(trim($data['streetAddress'])))) {
	    $res .= '<span itemprop="streetAddress">'.$data['streetAddress'].'</span>';
	    $filled = true;
	    if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['addressLocality'])) && (isset($data['postalCode'])) && (!empty(trim($data['addressLocality'])))  && (!empty(trim($data['postalCode']))) ) {
	    $res .= '<span class="person-info-city">';
	}
	  
	if ((isset($data['postalCode'])) && (!empty(trim($data['postalCode'])))) {
	     $res .= '<span itemprop="postalCode">'.$data['postalCode'].'</span>';
	     $filled = true;
	     if ($widthbreak) {
		$res .= ' ';
	    }
	}
	if ((isset($data['addressLocality'])) && (!empty(trim($data['addressLocality'])))) {
	    $res .= '<span itemprop="addressLocality">'.$data['addressLocality'].'</span>';
	     $filled = true;
	}
	if ((isset($data['addressLocality'])) && (isset($data['postalCode'])) && (!empty(trim($data['addressLocality'])))  && (!empty(trim($data['postalCode']))) ) {
	    $res .= '</span>';
	}
	if ((isset($data['addressLocality'])) && $widthbreak && (!empty(trim($data['addressLocality'])))) {
	    $res .= '<br>';
	}
	
	if ((isset($data['addressRegion'])) && (!empty(trim($data['addressRegion'])))) {
	    $res .= '<span itemprop="addressRegion">'.$data['addressRegion'].'</span>';
	    $filled = true;
	    if (($widthbreak) && (isset($data['addressCountry'])) && (!empty(trim($data['addressCountry'])))) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['addressCountry'])) && (!empty(trim($data['addressCountry'])))) {
	    $res .= '<span itemprop="addressCountry">'.$data['addressCountry'].'</span>';
	    $filled = true;
	}
	$res .= '</'.$surroundingtag.'>';
	
	if ( $filled ) {
	    return $res;
	}
	return;
	
    }
    
    
     
    public static function create_Name( $data, $itemprop = 'name', $class = '', $surroundingtag = 'span', $suffixbracket = false ) {
	if (!is_array($data)) {
	    return;
	}
	$res = '<'.$surroundingtag;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'"';
	}
	if (!empty($class)) {
	    $res .= ' class="'.$class.'"';
	}
	if ($surroundingtag === 'a') {
	    if ((isset($data['url'])) && (!empty($data['url']))) {
		$res .= 'href="'.$data['url'].'"';
	    }
	}
	$res .= '>';
	
	$honorificPrefix = $honorificSuffix = $givenName = $familyName = $fullname = '';
	
	if ((isset($data['honorificPrefix'])) && (!empty($data['honorificPrefix']))) {
	    $honorificPrefix = '<span itemprop="honorificPrefix">' . $data['honorificPrefix'] . '</span>';
	}
	if ((isset($data['honorificSuffix'])) && (!empty($data['honorificSuffix']))) {
	    $honorificSuffix = '<span itemprop="honorificSuffix">' . $data['honorificSuffix'] . '</span>';
	}
	

	if ((isset($data['givenName'])) && (!empty($data['givenName']))) {
	    $givenName  = '<span itemprop="givenName">' . $data['givenName'] . '</span>';
	}
	if ((isset($data['familyName'])) && (!empty($data['familyName']))) {
	    $familyName  = '<span itemprop="familyName">' . $data['familyName'] . '</span>';
	}
	
	if ((!empty($givenName)) && (!empty($familyName))) {
	    $fullname = $givenName.' '.$familyName;
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
	    $res .= '</'.$surroundingtag.'>';

	    return $res;
	}

	return;
    }
    
    public static function create_contactpointlist($data, $blockstart = 'ul', $itemprop = '', $class = 'person-info', $liststart = 'li', $phoneuri = true) {
	if (!is_array($data)) {
	    return;
	}
	$filled = false;
	$res = '<'.$blockstart;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'"';
	}
	if (!empty($class)) {
	    $res .= ' class="'.$class.'"';
	}
	$res .= '>';
	
	if ((isset($data['telephone'])) && (!empty($data['telephone']))) {
	    $res .= '<'.$liststart.' class="person-info-phone telephone">';
	    $res .= '<span class="screen-reader-text">' . __('Telefon', 'fau-person') . ': </span>';
	    $number = self::get_sanitized_phone($data['telephone']);
	    if ($phoneuri) {
		$res .= '<a itemprop="telephone" href="tel:'.self::get_telephone_uri($number).'">' . $number . '</a>';
	    } else {
		$res .= '<span itemprop="telephone">' . $data['telephone'] . '</span>';
	    }
	    $res .= '</'.$liststart.'>';
	    $filled = true;
	}
	
	if ((isset($data['mobilePhone'])) && (!empty($data['mobilePhone']))) {
	    $res .= '<'.$liststart.' class="person-info-mobile mobilePhone">';
	    $res .= '<span class="screen-reader-text">' . __('Mobil', 'fau-person') . ': </span>';
	    $number = self::get_sanitized_phone($data['mobilePhone']);
	    if ($phoneuri) {
		$res .= '<a itemprop="telephone" href="tel:'.self::get_telephone_uri($number).'">' . $number . '</a>';
	    } else {
		$res .= '<span itemprop="telephone">' . $number . '</span>';
	    }
	    $res .= '</'.$liststart.'>';
	     $filled = true;
	}
	
	if ((isset($data['faxNumber'])) && (!empty($data['faxNumber']))) {
	    $res .= '<'.$liststart.' class="person-info-fax faxNumber">';
	    $res .= '<span class="screen-reader-text">' . __('Faxnummer', 'fau-person') . ': </span>';
	    $number = self::get_sanitized_phone($data['faxNumber']);
	    if ($phoneuri) {
		$res .= '<a itemprop="faxNumber" href="tel:'.self::get_telephone_uri($number).'">' . $number . '</a>';
	    } else {
		$res .= '<span itemprop="faxNumber">' . $number . '</span>';
	    }
	    $res .= '</'.$liststart.'>';
	     $filled = true;
	}
	
	if ((isset($data['email'])) && (!empty($data['email']))) {
	    $res .= '<'.$liststart.' class="person-info-email email">';
	    $res .= '<span class="screen-reader-text">' . __('E-Mail', 'fau-person') . ': </span>';
	    $res .= '<a itemprop="email" href="mailto:'.self::get_email_uri($data['email']).'">' . self::get_email_uri($data['email']) . '</a>';
	    $res .= '</'.$liststart.'>';
	     $filled = true;
	}
	
	if ((isset($data['url'])) && (!empty($data['url']))) {
	    $res .= '<'.$liststart.' class="person-info-www url">';
	    $res .= '<span class="screen-reader-text">' . __('Webseite', 'fau-person') . ': </span>';
	    $res .= '<a itemprop="url" href="'.self::get_sanitized_url($data['url']).'">' . self::get_sanitized_url($data['url']) . '</a>';
	    $res .= '</'.$liststart.'>';
	     $filled = true;
	}
	
	$res .= '</'.$blockstart.'>';
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
	    $res .= ' class="'.$class.'"';
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
	$res = '<'.$blockstart;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'"';
	}
	if (!empty($class)) {
	    $res .= ' class="'.$class.'"';
	}
	$res .= ' itemtype="http://schema.org/OpeningHoursSpecification">';	
	$hoursAvailable = $data['hoursAvailable'];
	$hoursAvailable_group = $data['hoursAvailable_group'];
	$hoursAvailable_text = $data['hoursAvailable_text'];
	
        if(!empty($hoursAvailable) || !empty($hoursAvailable_group)) {
            
            if(!empty($hoursAvailable_text)) {
                $res  .= '<'.$titletagopeninghours.' itemprop="name">' . $hoursAvailable_text . ':</'.$titletagopeninghours.'>';
            } else {
                $res  .= '<'.$titletagopeninghours.' itemprop="name">' . __('Sprechzeiten', 'fau-person') . '</'.$titletagopeninghours.'>';   
            }
	    $desctag = 'span';
	    if ((substr($titletagopeninghours,0,1)!=='h') && (substr($titletagopeninghours,0,3)!== 'div')) {
		  $res  .= '<br>';
	    } else {
		 $desctag = 'p';
	    }
            if ( $hoursAvailable ) {
                $res  .= '<'.$desctag.' itemprop="description">' . $hoursAvailable. '</'.$desctag.'>';  
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
	    $res .= ' class="'.$class.'"';
	}
	$res .= ' itemtype="http://schema.org/Organization">';	
	if (isset($data['name']) && (!empty($data['name']))) { 
            $res .= '<span itemprop="name">' . $data['name'] . '</span><br>';	
	    $filled = true;
	}
	if (isset($data['parentOrganization']) && (!empty($data['parentOrganization']))) { 
            $res .= '<span itemprop="parentOrganization">' . $data['parentOrganization'] . '</span><br>';	
	    $filled = true;
	}
	if (isset($data['department']) && (!empty($data['department']))) { 
	    $res .= '<span itemprop="department">' . $data['department'] . '</span><br>';	
	    $filled = true;
	}
	if (isset($data['subOrganization']) && (!empty($data['subOrganization']))) { 
            $res .= '<span itemprop="subOrganization">' . $data['subOrganization'] . '</span><br>';	
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
    
    private static function get_telephone_uri($number) {
	if (!isset($number)) {
	    return;
	}
	
	$res = preg_replace("/[\s]+/", "-", trim($number));
	$res = preg_replace("/[^0-9\-\+\.]+/", "", $res);
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
	$res = strtolower($url);

	return $res;
    }
}
