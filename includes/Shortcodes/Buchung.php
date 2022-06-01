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
        add_shortcode('terminbuchung', [$this, 'shortcode_buchung'], 10, 2);
        add_action( 'wp_ajax_UpdateForm', [$this, 'ajaxUpdateForm'] );
        add_action( 'wp_ajax_nopriv_UpdateForm', [$this, 'ajaxUpdateForm'] );
    }

    public static function shortcode_buchung($atts, $content = null) {
        $defaults = getShortcodeDefaults('buchung');
        $arguments = shortcode_atts($defaults, $atts);

        $id = 0;
        if (isset($arguments['id'])) {
            $id =  $arguments['id'];
        }
        $slug = '';
        if (isset($arguments['slug'])) {
            $slug =  $arguments['slug'];
        }

        if (empty($id)) {
            if (empty($slug)) {
                return '<div class="alert alert-danger">' . sprintf(__('Bitte geben Sie den Titel oder die ID des Kontakteintrags an.', 'fau-person'), $slug) . '</div>';
            } else {
                $posts = get_posts(array('name' => $slug, 'post_type' => 'person', 'post_status' => 'publish'));
                if ($posts) {
                    $post = $posts[0];
                    $id = $post->ID;
                } else {
                    return '<div class="alert alert-danger">' . sprintf(__('Es konnte kein Kontakteintrag mit dem angegebenen Titel %s gefunden werden. Versuchen Sie statt dessen die Angabe der ID des Kontakteintrags.', 'fau-person'), $slug) . '</div>';
                }
            }
        }

        $bookingAvailable = get_post_meta($id, 'fau_person_bookingAvailable', true);
        if ($bookingAvailable == '') {
            return '<div class="alert alert-warning">' . __('Dieser Kontakt hat seine Sprechstunde nicht für die Online-Buchung freigegeben.', 'fau-person') . '</div>';
        }
        $officeHoursRaw = get_post_meta($id, 'fau_person_hoursAvailable_group', true);
        if ($officeHoursRaw == '') {
            return '<div class="alert alert-warning">' . __('Keine Sprechstunden verfügbar.', 'fau-person') . '</div>';
        }

        //print "<pre>"; var_dump($officeHoursRaw); print "</pre>";
        //print "<pre>"; var_dump(Data::get_kontakt_data($id)); print "</pre>";

        $output = '';
        $output .= '<div class="fau-person-booking">';
        $output .= '<form action="' . get_permalink() . '" method="post" id="" class="">'
            . '<div id="loading"><i class="fa fa-refresh fa-spin fa-4x"></i></div>';
        $currentMonth = date('m', current_time('timestamp'));
        $currentYear = date('Y', current_time('timestamp'));
        $output .=self::buildCalendar($currentMonth, $currentYear, $id);
        $output .= '</form></div>';

        return $output;
    }

    private static function buildCalendar($month, $year, $id, $bookingdate_selected = '') {
        // Create array containing abbreviations of days of week.
        $daysOfWeek = self::daysOfWeekAry(0, 1, 2);
        // What is the first day of the month in question?
        $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
        $firstDayOfMonthDate = date('Y-m-d', $firstDayOfMonth);
        $bookingDaysStart = date('Y-m-d', current_time('timestamp'));
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
        $calendar = '<table class="booking_calendar" data-period="'.date_i18n('Y-m', $firstDayOfMonth).'">';
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
                    if ( !empty( $timeslot ) ) {
                        $active = true;
                        $class = 'available';
                        $title = __( 'Seats available', 'fau-person' );
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
                $input_open = "<input type=\"radio\" id=\"rsvp_date_$date\" value=\"$date\" name=\"rsvp_date\" $checked required><label for=\"rsvp_date_$date\">";
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

    public function ajaxUpdateForm() {
        /*check_ajax_referer( 'rsvp-ajax-nonce', 'nonce'  );
        $roomID = ((isset($_POST['room']) && $_POST['room'] > 0) ? (int)$_POST['room'] : '');
        $date = (isset($_POST['date']) ? sanitize_text_field($_POST['date']) : false);
        $time = (isset($_POST['time']) ? sanitize_text_field($_POST['time']) : false);
        $seat = (isset($_POST['seat']) ? sanitize_text_field($_POST['seat']) : false);
        $response = [];
        if ($date !== false) {
            $response['time'] = '<div class="rsvp-time-select error">'.__('Please select a date.', 'rrze-rsvp').'</div>';
        }
        if (!$date || !$time) {
            $response['seat'] = '<div class="rsvp-seat-select error">'.__('Please select a date and a time slot.', 'rrze-rsvp').'</div>';
        }
        $availability = getAvailability($roomID, $date, $date, false);
        $bookingMode = get_post_meta($roomID, 'rrze-rsvp-room-bookingmode', true);
        if ($date) {
            $response['time'] = $this->buildTimeslotSelect($roomID, $date, $time, $availability);
            if ($time && ($bookingMode != 'consultation')) {
                $seatSelect = $this->buildSeatSelect($roomID, $date, $time, $seat, $availability);
                $seatInfo = ($seat) ? $this->buildSeatInfo($seat) : '';
                $response['seat'] = $seatSelect . $seatInfo;
            }
        }
        wp_send_json($response);*/
    }


}