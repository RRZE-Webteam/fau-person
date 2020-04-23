/*
 * Backend JS for FAU Person
 */
jQuery(document).ready(function($){

    $('#fau_person_showhint').hide();
    $('#fau_person_contactselect').live('change', function(){
	 
	var value = $('#fau_person_contactselect').val();
	var shortcode = '[kontakt id="' + value + '"]';
      
	$('#fau_person_showhint').show();
	$('#copyshortcode').text(shortcode);
    });
    
    
    $('#fau_person_cp_shortcode').bind('click', function (event) {
	 var $tempElement = $("<input>");	
	$("body").append($tempElement);
	 var copyText = $('#copyshortcode').text();
	$tempElement.val(copyText).select();
	document.execCommand("copy");
	$tempElement.remove();
    });
    

    
    $(".cmb2-id-fau-person-streetAddress").show();
    $(".cmb2-id-fau-person-postalCode").show();
    $(".cmb2-id-fau-person-addressLocality").show();
    $(".cmb2-id-fau-person-addressCountry").show();
    $("#fau_person_standort_sync").click(function() {
    if($(this).is(":checked")) {
	$(".cmb2-id-fau-person-streetAddress").hide(300);
	$(".cmb2-id-fau-person-postalCode").hide(300);
	$(".cmb2-id-fau-person-addressLocality").hide(300);
	$(".cmb2-id-fau-person-addressCountry").hide(300);
    } else {
	$(".cmb2-id-fau-person-streetAddress").show(200);
	$(".cmb2-id-fau-person-postalCode").show(200);
	$(".cmb2-id-fau-person-addressLocality").show(200);
	$(".cmb2-id-fau-person-addressCountry").show(200);
    }
});
    
});