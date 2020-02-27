<?php

namespace FAU_Person;

defined('ABSPATH') || exit;


class Schema {
    /*
     * Create Schema Markup for Place
     */
    public function create_Place($data, $itemprop = 'location', $class = '', $surroundingtag = 'div', $widthbreak = true, $widthaddress = true ) {

	if (!is_array($data)) {
	    return;
	}
	$res = '<'.$surroundingtag;
	if (!empty($itemprop)) {
	    $res .= ' itemprop="'.$itemprop.'" itemscope';
	}
	if (!empty($class)) {
	    $res .= ' class="'.$class.'"';
	}
	$res .= ' itemtype="http://schema.org/Place">';	
	
	if (isset($data['name'])) {	    
	    $res .= '<span itemprop="name">'.$data['name'].'</span>';
	    if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	if (isset($data['telephone'])) {
	    $res .= '<span itemprop="telephone">'.$data['telephone'].'</span>';
	     $filled = true;
	}
	if (isset($data['faxNumber'])) {
	    $res .= '<span itemprop="faxNumber">'.$data['faxNumber'].'</span>';
	     $filled = true;
	}
	if (isset($data['url'])) {
	    $res .= '<a itemprop="url" href="'.$data['url'].'">'.$data['url'].'</a>';
	     $filled = true;
	}
	
	
	if ($widthaddress) {
	    $res .= self::create_PostalAdress($data, 'address','', 'address', $widthbreak);
	} 
	
	
	$res .= '</'.$surroundingtag.'>';
	return $res;
    }
    
    
    /*
     * Create Schema Markup for PostalAdress
     */
    public function create_PostalAdress($data, $itemprop = 'address', $class = '', $surroundingtag = 'address', $widthbreak = true ) {
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
	
	
	if (isset($data['streetAddress'])) {
	    $res .= '<span itemprop="streetAddress">'.$data['streetAddress'].'</span>';
	    $filled = true;
	    if ($widthbreak) {
		$res .= '<br>';
	    }
	}
	if ((isset($data['addressLocality'])) && (isset($data['postalCode']))) {
	    $res .= '<span class="person-info-city">';
	}
	  
	if (isset($data['postalCode'])) {
	     $res .= '<span itemprop="postalCode">'.$data['postalCode'].'</span>';
	     $filled = true;
	     if ($widthbreak) {
		$res .= ' ';
	    }
	}
	if (isset($data['addressLocality'])) {
	    $res .= '<span itemprop="addressLocality">'.$data['addressLocality'].'</span>';
	     $filled = true;
	}
	if ((isset($data['addressLocality'])) && (isset($data['postalCode']))) {
	    $res .= '</span>';
	}
	if ((isset($data['addressLocality'])) && $widthbreak) {
	    $res .= '<br>';
	}
	
	if (isset($data['addressRegion'])) {
	    $res .= '<span itemprop="addressRegion">'.$data['addressRegion'].'</span>';
	    $filled = true;
	    if (($widthbreak) && (isset($data['addressCountry']))) {
		$res .= '<br>';
	    }
	}
	if (isset($data['addressCountry'])) {
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
