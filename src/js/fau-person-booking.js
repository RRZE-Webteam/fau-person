/*
 * JS for Shortcode [terminbuchung]
 */

jQuery(document).ready(function($){
    var $loading = $('#loading').hide();
    $(document)
        .ajaxStart(function () {
            $loading.show();
        })
        .ajaxStop(function () {
            $loading.hide();
        });

    $('div.fau-person-booking').on('click', 'a.cal-skip', function(e) {
        e.preventDefault();
        var calendar = $('table.booking_calendar');
        var monthCurrent = calendar.data('period');
        var direction = $(this).data('direction');
        var id = calendar.data('id');
        //calendar.remove();
        $('div.fau_person_time_select').remove();
        $.post(fau_person_ajax.ajax_url, {         //POST request
            _ajax_nonce: fau_person_ajax.nonce,     //nonce
            action: "UpdateCalendar",            //action
            month: monthCurrent ,                  //data
            direction: direction,
            id: id,
        }, function(result) {                 //callback
            $('div.fau-person-date-container').html(result);
        });
    });

    //$('.fau-person-date-container .booking_calendar').on('click', '.day-select', function() {
    $('.fau-person-date-container').on('click', ' .booking_calendar .day-select', function() {
        var calendar = $(this).closest('table.booking_calendar');
        var id = calendar.data('id');
        var date = $(this).val();
        console.log(date);
        $('div.fau_person_time_select').remove();
        $.post(fau_person_ajax.ajax_url, {          //POST request
            _ajax_nonce: fau_person_ajax.nonce,     //nonce
            action: "UpdateTimeSelect",             //action
            date: date,                             //data
            id: id,
        }, function(result) {                 //callback
            $('div.fau-person-time-container').append(result);
        });
    });

});