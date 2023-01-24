<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

class Date {
    public static $date;
    public static $timezone = '';
    public static $default_timezone = 'UTC';

    public static function validate($date, $format = 'Y-m-d') {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    /* Helper to easily and fast output dates to the screen */
    public static function get($date = '', $format_type = -1, $timezone = '') {

        $timezone = !$timezone ? self::$timezone : $timezone;

        if(is_null($date)) {
            $date = '';
        }

        if(is_string($date)) {
            $datetime = (new \DateTime($date))->setTimezone(new \DateTimeZone($timezone));
        } else {
            $datetime = $date->setTimezone(new \DateTimeZone($timezone));
        }

        /* No format at all */
        if(is_null($format_type)) {
            return $datetime;
        }

        switch($format_type) {

            case $format_type === -1:
                return $datetime->format('Y-m-d H:i:s');

                break;

            case $format_type === 1:

                return sprintf(
                    l('global.date.datetime_ymd_his_format'),
                    $datetime->format('Y'),
                    $datetime->format('m'),
                    $datetime->format('d'),
                    $datetime->format('H'),
                    $datetime->format('i'),
                    $datetime->format('s')
                );

                break;

            case $format_type === 2:

                return sprintf(
                    l('global.date.datetime_readable_format'),
                    $datetime->format('j'),
                    l('global.date.long_months.' . $datetime->format('n')),
                    $datetime->format('Y')
                );

                break;

            case $format_type === 3:

                return sprintf(
                    l('global.date.datetime_his_format'),
                    $datetime->format('H'),
                    $datetime->format('i'),
                    $datetime->format('s')
                );

                break;

            case $format_type === 4:
                return sprintf(
                    l('global.date.datetime_ymd_format'),
                    $datetime->format('Y'),
                    $datetime->format('m'),
                    $datetime->format('d')
                );

                break;

            case $format_type === 5:

                return sprintf(
                    l('global.date.datetime_small_readable_format'),
                    $datetime->format('j'),
                    l('global.date.short_months.' . $datetime->format('n'))
                );

                break;


            /* No specific format type */
            default:

                return $datetime->format($format_type);

                break;
        }

    }

    /* Helper to generate start_date and end_date for datepicker */
    public static function get_start_end_dates($start_date, $end_date, $current_timezone = '', $wanted_timezone = '') {

        $current_timezone = !$current_timezone ? self::$timezone : $current_timezone;
        $wanted_timezone = !$wanted_timezone ? self::$default_timezone : $wanted_timezone;

        $return = new \StdClass();

        $query_format = 'Y-m-d H:i:s';

        if($start_date && $end_date) {

            $return->start_date = $start_date;
            $return->end_date = $end_date;

            $return->start_date_query = (new \DateTime($start_date, new \DateTimeZone($current_timezone)))->setTimezone(new \DateTimeZone($wanted_timezone))->format($query_format);
            $return->end_date_query = (new \DateTime($end_date, new \DateTimeZone($current_timezone)))->setTimezone(new \DateTimeZone($wanted_timezone))->modify('+1 day')->format($query_format);

        } else {
            $return->start_date_query = (new \DateTime('now', new \DateTimeZone($current_timezone)))->setTimezone(new \DateTimeZone($wanted_timezone))->modify('-30 day')->format($query_format);
            $return->end_date_query = (new \DateTime('now', new \DateTimeZone($current_timezone)))->setTimezone(new \DateTimeZone($wanted_timezone))->modify('+1 day')->format($query_format);

            $return->start_date = (new \DateTime('now', new \DateTimeZone($current_timezone)))->setTimezone(new \DateTimeZone($wanted_timezone))->modify('-30 day')->format('Y-m-d');
            $return->end_date = (new \DateTime('now', new \DateTimeZone($current_timezone)))->setTimezone(new \DateTimeZone($wanted_timezone))->format('Y-m-d');
        }

        $return->input_date_range = $return->start_date . ',' . $return->end_date;

        return $return;
    }

    /* Another helper function, expecting Y-m-d format */
    public static function get_start_end_dates_new($start_date = null, $end_date = null, $current_timezone = null, $wanted_timezone = null) {

        $current_timezone = new \DateTimeZone(($current_timezone ? $current_timezone : self::$timezone));
        $wanted_timezone = new \DateTimeZone(($wanted_timezone ? $wanted_timezone : self::$default_timezone));

        if(is_null($start_date) && is_null($end_date)) {
            $start_date = isset($_GET['start_date']) ? (new \DateTime($_GET['start_date'], $current_timezone)) : (new \DateTime('now', $current_timezone))->modify('-30 day');
            $end_date = isset($_GET['end_date']) ? (new \DateTime($_GET['end_date'], $current_timezone)) : (new \DateTime('now', $current_timezone));
        } else {
            $start_date = (new \DateTime($start_date, $current_timezone));
            $end_date = (new \DateTime($end_date, $current_timezone));
        }

        $difference = $start_date->diff($end_date);

        $return = [];

        /* Display hours on chart */
        if($start_date == $end_date) {
            $return['query_date_format'] = '%Y-%m-%d %H';

            $return['process'] = function($date) {
                $date = explode(' ', $date);
                return ((new \DateTime($date[0]))->setTime($date[1], 0)->setTimezone(new \DateTimeZone(\Altum\Date::$timezone))->format('H A'));
            };
        }

        /* Display days on chart */
        $days_difference = $difference->d + ($difference->m * 30) + ($difference->y * 365);
        if($days_difference >= 1) {
            $return['query_date_format'] = '%Y-%m-%d';

            $return['process'] = function($date) {
                return \Altum\Date::get($date, 2);
            };
        }

        /* Display months on chart */
        $months_difference = ($difference->d / 30) + $difference->m + ($difference->y * 12);
        if($months_difference >= 2) {
            $return['query_date_format'] = '%Y-%m';

            $return['process'] = function($date) {
                return \Altum\Date::get($date, 'Y-m');
            };
        }

        /* Display years on chart */
        $years_difference = ($difference->d / 365) + ($difference->m / 12) + $difference->y;
        if($years_difference >= 2) {
            $return['query_date_format'] = '%Y';

            $return['process'] = function($date) {
                return ((new \DateTime())->setDate($date, 6, 1)->setTimezone(new \DateTimeZone(\Altum\Date::$timezone))->format('Y'));
            };
        }

        $return['start_date'] = $start_date->format('Y-m-d');
        $return['end_date'] = $end_date->format('Y-m-d');

        $return['query_start_date'] = $start_date->setTimezone($wanted_timezone)->format('Y-m-d H:i:s');
        $return['query_end_date'] = $end_date->setTimezone($wanted_timezone)->modify('+1 day')->format('Y-m-d H:i:s');

        return $return;
    }

    /* Seconds to his */
    public static function get_seconds_to_his($seconds) {
        $hours = sprintf('%02d', floor($seconds / 3600));
        $minutes = sprintf('%02d', floor(($seconds / 60) % 60));
        $seconds = sprintf('%02d', $seconds % 60);

        return sprintf(
            l('global.date.datetime_his_format'),
            $hours,
            $minutes,
            $seconds
        );
    }

    public static function get_elapsed_time($date, $end_date = null, $timings_to_display = 3) {

        $end_date = $end_date ? (new \DateTime($end_date))->getTimestamp() : time();

        $estimate_time = $end_date - (new \DateTime($date))->getTimestamp();

        if($estimate_time < 1) {
            return l('global.date.now');
        }

        $condition = [
            12 * 30 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60       =>  'month',
            24 * 60 * 60            =>  'day',
            60 * 60                 =>  'hour',
            60                      =>  'minute',
            1                       =>  'second'
        ];

        $result = '';
        $counter = 1;

        foreach($condition as $seconds => $string) {
            if($counter > $timings_to_display) break;

            $d = $estimate_time / $seconds;

            if($d >= 1) {
                $r = floor($d);

                /* Determine the language string needed */
                $language_string_time = $r > 1 ? l('global.date.' . $string . 's') : l('global.date.' . $string);

                /* Append it to the result */
                $result .= ' ' . $r . ' ' . $language_string_time;

                $estimate_time -= $r * $seconds;

                $counter++;
            }
        }

        return $result;
    }

    /* Helper to have the timeago from one point to now */
    public static function get_timeago($date) {

        $estimate_time = time() - (new \DateTime($date))->getTimestamp();

        if($estimate_time < 1) {
            return l('global.date.now');
        }

        $condition = [
            12 * 30 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60       =>  'month',
            24 * 60 * 60            =>  'day',
            60 * 60                 =>  'hour',
            60                      =>  'minute',
            1                       =>  'second'
        ];

        foreach($condition as $secs => $str) {
            $d = $estimate_time / $secs;

            if($d >= 1) {
                $r = round($d);

                /* Determine the language string needed */
                $language_string_time = $r > 1 ? l('global.date.' . $str . 's') : l('global.date.' . $str);

                return sprintf(
                    l('global.date.time_ago'),
                    $r,
                    $language_string_time
                );
            }
        }
    }

    /* Helper to have the time left from now to one point in time */
    public static function get_time_until($date) {

        $estimate_time = (new \DateTime($date))->getTimestamp() - time();

        if($estimate_time < 1) {
            return l('global.date.now');
        }

        $condition = [
            12 * 30 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60       =>  'month',
            24 * 60 * 60            =>  'day',
            60 * 60                 =>  'hour',
            60                      =>  'minute',
            1                       =>  'second'
        ];

        foreach($condition as $secs => $str) {
            $d = $estimate_time / $secs;

            if($d >= 1) {
                $r = round($d);

                /* Determine the language string needed */
                $language_string_time = $r > 1 ? l('global.date.' . $str . 's') : l('global.date.' . $str);

                return sprintf(
                    l('global.date.time_until'),
                    $r,
                    $language_string_time
                );
            }
        }
    }
}
