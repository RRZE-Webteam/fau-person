/*
 * JS for shortcode [terminbuchung]
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
        calendar.remove();
        $('div.fau-person-time-select').remove();
        $('div.fau-person-seat-select').remove();
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


    function updateForm() {
        var room = $('#fau-person_room').val();
        var date = $('table.fau-person_calendar input[name="fau-person_date"]:checked').val();
        var time = $('div.fau-person-time-container input[name="fau-person_time"]:checked').val();
        var seat = $('div.fau-person-seat-container input[name="fau-person_seat"]:checked').val();
        // console.log(room);
        // console.log(date);
        // console.log(time);
        $('div.fau-person-time-select').remove();
        $('div.fau-person-seat-select').remove();
        $.post(fau_person_ajax.ajax_url, {         //POST request
            _ajax_nonce: fau_person_ajax.nonce,     //nonce
            action: "UpdateForm",            //action
            room: room,                  //data
            date: date,          //data
            time: time,          //data
            seat: seat,          //data
        }, function(result) {                 //callback
            //console.log(result);
            $('div.fau-person-time-container').append(result['time']);
            $('div.fau-person-seat-container').html(result['seat']);
        });
    }
});