<?php

namespace RRZE\Lib\UnivIS;

/* 
 * Convert and sanitize data from UnivIS
 */


defined('ABSPATH') || exit;
class Sanitizer
{
    /*
     * Normalize Phone Numbers 
     */
    public static function correct_phone_number($phone_number, $location)
    {
        $phone_number = filter_var($phone_number, FILTER_SANITIZE_NUMBER_INT);

        if ((strpos($phone_number, '+49 9131 85-') !== 0) && (strpos($phone_number, '+49 911 5302-') !== 0)) {

            if (!preg_match('/\+49 [1-9][0-9]{1,4} [1-9][0-9]+/', $phone_number)) {

                $phone_data = preg_replace('/\D/', '', $phone_number);
                $vorwahl_erl = '+49 9131 85-';
                $vorwahl_erl_p1_p6 = '+49 9131 81146-'; // see: https://github.com/RRZE-Webteam/fau-person/issues/353
                $vorwahl_nbg = '+49 911 5302-';
                switch ($location) {
                    case 'erl':
                        if (strlen($phone_data) == 5) {
                            $phone_number = $vorwahl_erl . $phone_data;
                        } elseif (strlen($phone_data) == 7 && strpos($phone_data, '85') === 0) {
                            $phone_number = $vorwahl_erl . substr($phone_data, -5);
                        } elseif (strlen($phone_data) == 12 && strpos($phone_data, '913185') !== FALSE) {
                            $phone_number = $vorwahl_erl . substr($phone_data, -5);
                        }
                        break;
                    case 'nbg':
                        if (strlen($phone_data) == 3) {
                            $phone_number = $vorwahl_nbg . $phone_data;
                        } elseif (strlen($phone_data) == 7 && strpos($phone_data, '5302') === 0) {
                            $phone_number = $vorwahl_nbg . substr($phone_data, -3);
                        } elseif (strlen($phone_data) == 11 && strpos($phone_data, '9115302') !== FALSE) {
                            $phone_number = $vorwahl_nbg . substr($phone_data, -3);
                        } elseif (strlen($phone_data) == 12 && strpos($phone_data, '9115302') !== FALSE) {
                            $phone_number = $vorwahl_nbg . substr($phone_data, -4);
                        } elseif (strlen($phone_data) == 14 && strpos($phone_data, '9115302') !== FALSE) {
                            $phone_number = $vorwahl_nbg . substr($phone_data, -5);
                        } elseif (strlen($phone_data) == 15 && strpos($phone_data, '4909115302') !== FALSE) {
                            $phone_number = $vorwahl_nbg . substr($phone_data, 10);
                        }
                        break;
                    case 'standard':
                        switch (strlen($phone_data)) {
                            case '3':
                                $phone_number = $vorwahl_nbg . $phone_data;
                                break;
                            case '5':
                                if (strpos($phone_data, '06') === 0) {
                                    $phone_number = $vorwahl_nbg . substr($phone_data, -3);
                                    break;
                                }
                                $phone_number = $vorwahl_erl . $phone_data;
                                break;
                            case '7':
                                if (strpos($phone_data, '85') === 0 || strpos($phone_data, '06') === 0) {
                                    $phone_number = $vorwahl_erl . substr($phone_data, -5);
                                    break;
                                }
                                if (strpos($phone_data, '5302') === 0) {
                                    $phone_number = $vorwahl_nbg . substr($phone_data, -3);
                                    break;
                                }
                            default:
                                if (strpos($phone_data, '9115302') !== FALSE) {
                                    $durchwahl = explode('9115302', $phone_data);
                                    if (strlen($durchwahl[1]) ===  3) {
                                        $phone_number = $vorwahl_nbg . substr($phone_data, -3);
                                    }
                                    break;
                                }
                                if (strpos($phone_data, '913185') !== FALSE) {
                                    $durchwahl = explode('913185', $phone_data);
                                    if (strlen($durchwahl[1]) ===  5) {
                                        $phone_number = $vorwahl_erl . substr($phone_data, -5);
                                    }
                                    break;
                                }
                                // see: https://github.com/RRZE-Webteam/fau-person/issues/353
                                if (strpos($phone_data, '913181146') !== FALSE) {
                                    $durchwahl = explode('913181146', $phone_data);
                                    $phone_number = $vorwahl_erl_p1_p6 . $durchwahl[1];
                                    break;
                                }
                                if (strpos($phone_data, '09131') === 0 || strpos($phone_data, '499131') === 0) {
                                    $durchwahl = explode('9131', $phone_data);
                                    $phone_number = "+49 9131 " . $durchwahl[1];
                                    break;
                                }
                                if (strpos($phone_data, '0911') === 0 || strpos($phone_data, '49911') === 0) {
                                    $durchwahl = explode('911', $phone_data);
                                    $phone_number = "+49 911 " . $durchwahl[1];
                                    break;
                                }
                                //           add_action( 'admin_notices', array( 'FAU_Person\Helper', 'admin_notice_phone_number' ) );

                        }
                }
            }
        }

        return $phone_number;
    }


    /*
     * Correct Time Format of UnivIS
     */
    public static function convert_time($time)
    {
        if (strpos($time, 'PM')) {
            $modtime = explode(':', rtrim($time, ' PM'));
            if ($modtime[0] != 12) {
                $modtime[0] = $modtime[0] + 12;
            }
            $time = implode(':', $modtime);
        } elseif (strpos($time, 'AM')) {
            $time = str_replace('12:', '00:', $time);
            $time = rtrim($time, ' AM');
        }
        return $time;
    }

    public static  function is_valid_id($id)
    {
        $return = ((string)$id === (string)(int)$id);
        if ($return && intval($id) < 1) {
            $return = false;
        }
        return $return;
    }
}
