/*
 * Backend JS for FAU Person
 */
jQuery(document).ready(function($){

    $('#fau_person_showhint').hide();
    $(document).on('change', '#fau_person_contactselect', function(){
	 
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
    

    if($("#fau_person_standort_sync").is(":checked")) {
	$(".cmb2-id-fau-person-streetAddress").hide();
	$(".cmb2-id-fau-person-postalCode").hide();
	$(".cmb2-id-fau-person-addressLocality").hide();
	$(".cmb2-id-fau-person-addressCountry").hide();
    } 

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

	var onlineCheck = $("input[name='fau_person_bookingAvailable']");
	var onlineAvailable = onlineCheck.is(':checked');
	if (onlineAvailable === false) {
		$("div.online-only").hide();
	}
	onlineCheck.change(function(){
		if ($(this).is(':checked') === true) {
			$("div.online-only").slideDown();
		} else {
			$("div.online-only").slideUp();
		}
	});
    var repeat = $("input[name*='[repeat]']");
	repeat.change(function(){
		if ($(this).val() === 'w2') {
			$(this).parents("div[id^='cmb-group-fau_person_hoursAvailable']").find("div.w2-only").slideDown();
		} else {
			$(this).parents("div[id^='cmb-group-fau_person_hoursAvailable']").find("div.w2-only").slideUp();
		}
	});

});