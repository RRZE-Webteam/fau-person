<?php

namespace FAU_Person;

defined('ABSPATH') || exit;


class Schema {
    /*
     * Create Schema Markup for Place
     */
    public static function create_Place($data, $itemprop = 'location', $class = '', $surroundingtag = 'div', $widthbreak = true, $widthaddress = true ) {

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
	    $res .= '<span itemprop="telephone">'.$data['telephone'].'</span>';
	     $filled = true;
	     if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['faxNumber'])) && (!empty(trim($data['faxNumber'])))) {	    
	    $res .= '<span itemprop="faxNumber">'.$data['faxNumber'].'</span>';
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
}
