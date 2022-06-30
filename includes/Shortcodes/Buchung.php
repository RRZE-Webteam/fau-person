<?php

namespace FAU_Person\Shortcodes;
use function FAU_Person\Config\getShortcodeSettings;
use function FAU_Person\Config\getShortcodeDefaults;
use FAU_Person\Main;
use FAU_Person\Data;


defined('ABSPATH') || exit;

/**
 * Define Shortcodes
 */
class Buchung extends Shortcodes
{
    public $pluginFile = '';
    private $settings = '';

    public function __construct($pluginFile, $settings) {
        $this->pluginFile = $pluginFile;
        $this->settings = getShortcodeSettings();
        $this->settings = $this->settings['kontakt'];
        //add_action('init', [$this, 'initGutenberg']);
    }

    public function onLoaded() {
        add_shortcode('terminbuchung', [$this, 'shortcodeBooking'], 10, 2);
        add_action( 'wp_ajax_UpdateCalendar', [$this, 'ajaxUpdateCalendar'] );
        add_action( 'wp_ajax_nopriv_UpdateCalendar', [$this, 'ajaxUpdateCalendar'] );
        add_action( 'wp_ajax_UpdateTimeSelect', [$this, 'ajaxUpdateTimeSelect'] );
        add_action( 'wp_ajax_nopriv_UpdateTimeSelect', [$this, 'ajaxUpdateTimeSelect'] );
        add_action( 'wp_enqueue_scripts', [$this, 'enqueueScripts'] );
    }

    public static function shortcodeBooking($atts, $content = null) {
        $output = '';
        if (!empty($_POST)) {
            $data = self::processFormData($_POST);
            if (!is_wp_error($data)) {
                $output = '<div class="alert alert-success" role="alert"><p><strong>Vielen Dank für Ihre Buchung.</strong></p><p>Eine E-Mail mit Ihren Buchungsdaten wurde an die von Ihnen angegebene E-Mail-Adresse gesendet.</p></div>';
            } else {
                $output = '<div class="alert alert-danger" role="alert">Es gab einen Fehler bei der Verarbeitung Ihrer Daten. Bitte versuchen Sie es erneut.</div>';
            }

        } else {
            /* TODO:
            - SSO-Anmeldung
            */
            $defaults  = getShortcodeDefaults('buchung');
            $arguments = shortcode_atts($defaults, $atts);

            $id = 0;
            if (isset($arguments[ 'id' ])) {
                $id = $arguments[ 'id' ];
            }
            $slug = '';
            if (isset($arguments[ 'slug' ])) {
                $slug = $arguments[ 'slug' ];
            }

            if (empty($id)) {
                if (empty($slug)) {
                    return '<div class="alert alert-danger">' . sprintf(
                            __('Bitte geben Sie den Titel oder die ID des Kontakteintrags an.', 'fau-person'),
                            $slug
                        ) . '</div>';
                } else {
                    $posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
                    if ($posts) {
                        $post = $posts[ 0 ];
                        $id   = $post->ID;
                    } else {
                        return '<div class="alert alert-danger">' . sprintf(
                                __(
                                    'Es konnte kein Kontakteintrag mit dem angegebenen Titel %s gefunden werden. Versuchen Sie statt dessen die Angabe der ID des Kontakteintrags.',
                                    'fau-person'
                                ),
                                $slug
                            ) . '</div>';
                    }
                }
            }

            $bookingAvailable = get_post_meta($id, 'fau_person_bookingAvailable', true);
            if ($bookingAvailable == '') {
                return '<div class="alert alert-warning">' . __(
                        'Dieser Kontakt hat seine Sprechstunde nicht für die Online-Buchung freigegeben.',
                        'fau-person'
                    ) . '</div>';
            }
            $officeHoursRaw = get_post_meta($id, 'fau_person_hoursAvailable_group', true);
            if ($officeHoursRaw == '') {
                return '<div class="alert alert-warning">' . __(
                        'Keine Sprechstunden verfügbar.',
                        'fau-person'
                    ) . '</div>';
            }
            $now         = current_time('timestamp');
            $bookingTime = false;
            if (isset($_GET[ 'date' ])) {
                $bookingDate   = sanitize_text_field($_GET[ 'date' ]);
                $BookingDateTS = strtotime($bookingDate);
                $currentMonth  = date('m', $BookingDateTS);
                $currentYear   = date('Y', $BookingDateTS);
                if (isset($_GET[ 'time' ])) {
                    $bookingTime = sanitize_text_field($_GET[ 'time' ]);
                    if (strtotime($bookingDate . ' ' . $bookingTime) < current_time('timestamp')) {
                        $bookingTime = false;
                    }
                }
            } else {
                $bookingDate  = date('Y-m-d', $now);
                $currentMonth = date('m', $now);
                $currentYear  = date('Y', $now);
            }

            //print "<pre>"; var_dump($officeHoursRaw); print "</pre>";
            //print "<pre>"; var_dump(Data::get_kontakt_data($id)); print "</pre>";
            $output = '';
            $output .= '<div class="fau-person-booking">';
            $output .= '<form action="' . get_permalink() . '" method="post" id="" class="">'
                       . '<div id="loading"><i class="fa fa-refresh fa-spin fa-4x"></i></div>'
                       . '<fieldset><legend>' . __('Select date and time', 'fau-person') . '</legend>'
                       . '<div class="fau-person-date-time-container">'
                       . '<div class="fau-person-date-container">';
            $output .= self::buildCalendar($currentMonth, $currentYear, $id, $bookingDate);
            $output .= '</div>';

            $output .= '<div class="fau-person-time-container">'
                       . '<p><strong>' . __('Available time slots:', 'fau-person') . '</strong></p>';
            if ($bookingDate) {
                $output .= self::buildTimeslotSelect($id, $bookingDate, $bookingTime);
            } else {
                $output .= '<div class="fau-person-time-select error">' . __(
                        'Please select a date.',
                        'fau-person'
                    ) . '</div>';
            }
            $output .= '</div>'; //.fau-person-time-container
            $output .= '</div></fieldset>'; //.fau-person-date-time-container

            $output .= '<fieldset><legend>' . __('Your data', 'fau-person') . '</legend>';
            $output .= '<div class="form-group">
                <label for="fau_person_booking_lastname">' . __('Last name', 'fau-person') . '</label>
                <input type="text" name="fau_person_booking_lastname" value="" id="fau_person_booking_lastname" required="">
                <div class="error-message"></div>
            </div>';
            $output .= '<div class="form-group">
                <label for="fau_person_booking_firstname">' . __('First name', 'fau-person') . '</label>
                <input type="text" name="fau_person_booking_firstname" value="" id="fau_person_booking_firstname" required="">
                <div class="error-message"></div>
            </div>';
            $output .= '<div class="form-group">
                <label for="fau_person_booking_email">' . __('Email', 'fau-person') . '</label>
                <input type="email" name="fau_person_booking_email" value="" id="fau_person_booking_email" required="" pattern="^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*(\.\w{2,})+$">
                <div class="error-message"></div>
            </div>';
            $output .= '<div class="form-group">
                <label for="fau_person_booking_phone">' . __('Phone Number', 'fau-person') . '</label>
                <input type="text" name="fau_person_booking_phone" value="" id="fau_person_booking_lastname" required="" pattern="^([+])?(\d{1,3})?\s?(\(\d{3,5}\)|\d{3,5})?\s?(\d{1,3}\s?|\d{1,3}[-])?(\d{3,8})$">
                <div class="error-message"></div>
            </div>';
            $output .= '</fieldset>';

            $output .= '<div class="form-group">'
                       . '<label for="fau_person_comment">' . __('Additional information', 'fau-person') . '</label>'
                       . '<textarea name="fau_person_comment" id="fau_person_comment"></textarea>'
                       . '</div>';

            $output .= '<input type="hidden" name="fau_person_booking_id" value="' . $id . '">';
            $output .= '<button type="submit" class="btn btn-primary">' . __(
                    'Submit booking',
                    'fau-person'
                ) . '</button>';

            $output .= '</form></div>';
        }
        wp_enqueue_style('fau-person');
        wp_enqueue_script('fau-person-booking');
        wp_localize_script('fau-person-booking', 'fau_person_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce( 'fau-person-ajax-nonce' ),
        ]);
        return $output;
    }

    private static function buildCalendar($month, $year, $id, $bookingdate_selected = '') {
        // Create array containing abbreviations of days of week.
        $daysOfWeek = self::daysOfWeekAry(0, 1, 2);
        // What is the first day of the month in question?
        $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
        $firstDayOfMonthDate = date('Y-m-d', $firstDayOfMonth);
        $now = current_time('timestamp');
        $bookingDaysStart = date('Y-m-d', $now);
        // How many days does this month contain?
        $numberDays = date('t', $firstDayOfMonth);
        $lastDayOfMonth = mktime(0,0,0, $month, $numberDays, $year);
        $lastDayOfMonthDate = date('Y-m-d', $lastDayOfMonth);
        // What is the name of the month in question?
        $monthName = date_i18n('w', $firstDayOfMonth);
        // What is the index value (1-7) of the first day of the month in question.
        $dayOfWeek = date('N', $firstDayOfMonth);
        $link_next = '<a href="#" class="cal-skip cal-next" data-direction="next">&gt;&gt;</a>';
        $link_prev = '<a href="#" class="cal-skip cal-prev" data-direction="prev">&lt;&lt;</a>';
        //$availability = Functions::getRoomAvailability($roomID, $bookingDaysStart, $bookingDaysEnd, false);
        $availability = self::getAvailability($id, $firstDayOfMonth, $lastDayOfMonth);
        // Create the table tag opener and day headers
        $calendar = '<table class="booking_calendar" data-period="'.date_i18n('Y-m', $firstDayOfMonth).'" data-id="'.$id.'">';
        $calendar .= "<caption>";
        if ($bookingDaysStart <= $firstDayOfMonthDate) {
            $calendar .= $link_prev;
        }
        $calendar .= date_i18n('F Y', $firstDayOfMonth);
        //if ($bookingDaysEnd >= $lastDayOfMonthDate) {
            $calendar .= $link_next;
        //}
        $calendar .= "</caption>";
        // Create the calendar headers
        $calendar .= "<tr>";
        foreach($daysOfWeek as $day) {
            $calendar .= "<th class='header'>$day</th>";
        }
        $calendar .= "</tr>";
        // Create the rest of the calendar
        // Initiate the day counter, starting with the 1st.
        $currentDay = 1;
        $calendar .= "<tr>";
        // The variable $dayOfWeek is used to ensure that the calendar display consists of exactly 7 columns.
        if ($dayOfWeek > 1) {
            $colspan = $dayOfWeek - 1;
            $calendar .= "<td colspan='$colspan'>&nbsp;</td>";
        }
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        $bookingdate_selected = strtotime($bookingdate_selected);
        while ($currentDay <= $numberDays) {
            // Seventh column (Saturday) reached. Start a new row.
            if ($dayOfWeek > 7) {
                $dayOfWeek = 1;
                $calendar .= "</tr><tr>";
            }
            $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
            $date = strtotime("$year-$month-$currentDayRel");
            $active = false;
            $class = 'soldout';
            $title = __('Not bookable (soldout or room blocked)','fau-person');
            if (isset($availability[$date]) && !empty($availability[$date])) {
                foreach ( $availability[ $date ] as $timeslot ) {
                    if ( !empty( $timeslot) && isset($timeslot['start']) && $timeslot['start'] >= $now ) {
                        $active = true;
                        $class = 'available';
                        $title = __( 'Booking available', 'fau-person' );
                    }
                }
            }

            $input_open = '<span class="inactive">';
            $input_close = '</span>';
            if ($active) {
                if ($bookingdate_selected == $date) {
                    $checked = 'checked="checked"';
                } else {
                    $checked = '';
                }
                $input_open = "<input type=\"radio\" id=\"fau_person_booking_date_$date\" class=\"day-select\" value=\"$year-$month-$currentDayRel\" name=\"fau_person_booking_date\" $checked required><label for=\"fau_person_booking_date_$date\">";
                $input_close = '</label>';
            }
            $calendar .= "<td class='day $class' rel='$date' title='$title'>" . $input_open.$currentDay.$input_close . "</td>";
            // Increment counters
            $currentDay++;
            $dayOfWeek++;
        }
        // Complete the row of the last week in month, if necessary
        if ($dayOfWeek != 8) {
            $remainingDays = 8 - $dayOfWeek;
            $calendar .= "<td colspan='$remainingDays'>&nbsp;</td>";
        }
        $calendar .= "</tr>";
        $calendar .= "</table>";
        return $calendar;
    }

    private static function buildTimeslotSelect($id, $date, $time = false) {
        $timeSelects = '';
        $startDate = strtotime($date);
        $endDate = strtotime($date) + (60 * 60 * 24) - 1;
        $availability = self::getAvailability($id, $startDate, $endDate);
        if (!empty($availability) && isset($availability[$startDate])) {
            $slots = ($availability[$startDate]);
            $start = array_column($slots, 'start');
            array_multisort($start, SORT_ASC, $slots);
            foreach ($slots as $slot) {
                $slotValue = $slot['start'].'-'.$slot['end'];
                $id = 'fau_person_time_' . sanitize_title($slotValue);
                $startTime = date(get_option('time_format'), $slot['start']);
                $endTime = date(get_option('time_format'), $slot['end']);
                $label = $startTime . ' - ' . $endTime;
                $checked = checked($time !== false && $time == $startTime, true, false);
                $timeSelects .= "<div class='form-group'><input type='radio' id='$id' value='$slotValue' name='fau_person_booking_time' " . $checked . " required><label for='$id'>$label</label></div>";
            }
        }
        if ($timeSelects == '') {
            $timeSelects .= __('No time slots available.', 'fau-person');
        }
        return '<div class="fau_person_time_select">' . $timeSelects . '</div>';
    }



    private static function daysOfWeekAry(int $startKey = 0, int $startWd = 1, int $abbr = 0): array {
        global $wp_locale;
        $weekDays = [];
        for ($wdCount = 0; $wdCount <= 6; $wdCount++) {
            $wd = $wp_locale->get_weekday(($wdCount + $startWd) % 7);
            if ($abbr == 1) {
                $wd = $wp_locale->get_weekday_initial($wd);
            } elseif ($abbr == 2) {
                $wd = $wp_locale->get_weekday_abbrev($wd);
            }
            $weekDays[$wdCount + $startKey] = $wd;
        }
        return $weekDays;
    }

    private static function getAvailability(int $id, string $start, string $end) {
        $availability = [];
        $officeHours = get_post_meta($id, 'fau_person_hoursAvailable_group', true);
        if ($officeHours == '') {
            return [];
        }
        $counter = $start;
        // Loop through days
        while ($counter <= $end) {
            $availability[$counter] = [];
            $day = date('Y-m-d', $counter);
            $i = 0;
            // Loop through timeslots
            foreach ($officeHours as $officeHour) {
                if (isset($officeHour['exceptions']) && $officeHour['exceptions'] != '') {
                    $exceptions = explode("\n", str_replace("\r", '', $officeHour['exceptions']));
                    if (in_array($day, $exceptions)) {
                        continue;
                    }
                }
                switch ($officeHour['repeat']) {
                    case 'd1':
                        if (!isset($officeHour['starttime']) || !isset($officeHour['endtime'])) {
                            continue 2;
                        }
                        $availability[$counter][$i]['start'] = strtotime($day . ' ' . $officeHour['starttime']);
                        $availability[$counter][$i]['end'] = strtotime($day . ' ' . $officeHour['endtime']);
                        break;
                    case 'w1':
                    case 'w2':
                        $weekDaysAvailable = $officeHour['repeat_submode'];
                        $weekDayCurrent = date('N', $counter);
                        if (!in_array($weekDayCurrent, $weekDaysAvailable)) {
                            // Skip if it is a wrong day of the week
                            continue 2;
                        } if (!isset($officeHour['starttime']) || !isset($officeHour['endtime'])) {
                            // Skip if start/end times are not complete
                            continue 2;
                        }
                        if ($officeHour['repeat'] == 'w1') {
                            // Weekly timeslots
                            $availability[$counter][$i]['start'] = strtotime($day . ' ' . $officeHour['starttime']);
                            $availability[$counter][$i]['end'] = strtotime($day . ' ' . $officeHour['endtime']);
                        } else {
                            // 2-weekly timeslots
                            if (!isset($officeHour['bookingWeeks'])) {
                                continue 2;
                            }
                            $bookingWeeks = $officeHour['bookingWeeks'];
                            $weekNumber = date('W', $counter);
                            if ($weekNumber % 2 == 0){
                                $weekType = 'even';
                            } else {
                                $weekType = 'odd';
                            }
                            if ($bookingWeeks == $weekType) {
                                $availability[$counter][$i]['start'] = strtotime($day . ' ' . $officeHour['starttime']);
                                $availability[$counter][$i]['end'] = strtotime($day . ' ' . $officeHour['endtime']);
                            }
                        }
                        break;
                    case '-':
                    default:
                        break;
                }
                $i++;
            }
            $counter += (60 * 60 * 24); // increment timestamp by 1 day
        }
        //print "<pre>";var_dump($availability);print "</pre>";
        return $availability;
    }

    public function ajaxUpdateCalendar() {
        check_ajax_referer( 'fau-person-ajax-nonce', 'nonce' );
        $period = explode('-', $_POST['month']);
        $month = $period[1];
        $year = $period[0];
        $personID = sanitize_text_field($_POST['id']);
        switch ($month) {
            case '1':
                $modMonth = $_POST['direction'] == 'next' ? 1 : 11;
                $modYear = $_POST['direction'] == 'next' ? 0 : -1;
                break;
            case '12':
                $modMonth = $_POST['direction'] == 'next' ? -11 : -1;
                $modYear = $_POST['direction'] == 'next' ? 1 : 0;
                break;
            default:
                $modMonth = $_POST['direction'] == 'next' ? 1 : -1;
                $modYear = 0;
                break;
        }
        $output = $this->buildCalendar($month + $modMonth, $year + $modYear, $personID);
        echo $output;
        wp_die();
    }

    public function ajaxUpdateTimeSelect() {
        check_ajax_referer( 'fau-person-ajax-nonce', 'nonce' );
        $personID = sanitize_text_field($_POST['id']);
        $date = sanitize_text_field($_POST['date']);
        $output = $this->buildTimeslotSelect($personID, $date);
        echo $output;
        wp_die();
    }

    public static function processFormData($data) {
        $dates = explode('-', $data['fau_person_booking_time']);
        $start = isset($dates[0]) ? (int)$dates['0'] : '';
        $end = isset($dates[1]) ? (int)$dates['1'] : '';
        $contactID = (int)$data['fau_person_booking_id'];
        $lastname = sanitize_text_field($data['fau_person_booking_lastname']);
        $firstname = sanitize_text_field($data['fau_person_booking_firstname']);
        $email = sanitize_email($data['fau_person_booking_email']);
        $phone = sanitize_text_field($data['fau_person_booking_phone']);
        $comment = sanitize_textarea_field($data['fau_person_comment']);
        $SSO = get_post_meta($contactID, 'fau_person_bookingSSO', true);
        $status = $SSO == true ? 'confirmed' : 'unconfirmed';
        $insert = wp_insert_post([
            'post_type' => 'buchung',
            'post_status' => 'publish',
            'meta_input' => [
                'fau_person_booking_start' => $start,
                'fau_person_booking_end' => $end,
                'fau_person_booking_contact_id' => $contactID,
                'fau_person_booking_lastname' => $lastname,
                'fau_person_booking_firstname' => $firstname,
                'fau_person_booking_email' => $email,
                'fau_person_booking_phone' => $phone,
                'fau_person_booking_comment' => $comment,
                'fau_person_booking_status' => $status,
            ],
        ]);
        if ($insert) {
            $to = $email;
            $from = get_post_meta($contactID, 'fau_person_email', true);

            //wp_mail();
            return $insert;
        }
        return false;
    }

    public static function enqueueScripts() {
        wp_register_script(
            'fau-person-booking',
            //plugins_url('js/fau-person-booking.js', plugin_basename(__FILE__)),
            WP_PLUGIN_URL . '/fau-person/src/js/fau-person-booking.js',
            ['jquery'],
            '1.0.0'
        );
    }


}