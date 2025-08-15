<?php

namespace SM\Util;

class Date
{
    static function date_format($format, $date, $translate = true, $source = 'mysql')
    {
        if (empty($date)) return '';

        if ($source == 'mysql')
        {
            $result = mysql2date($format, $date, $translate);
        }
        else if (($source == 'timestamp') && ($date>0))
        {
            $result = $translate ? date_i18n($format, $date) : date($format, $date);
        }
        else
        {
            $i = strtotime($date);

            $result = $translate ? date_i18n($format, $i) : date($format, $i);
        }

        return self::date_translate($result);
    }

    static function date_unix2mysql($unixtime)
    {
        return gmdate("Y-m-d H:i:s", $unixtime);
    }

    static function date_translate($tdate = '')
    {
        if (substr_count($tdate, '---') > 0) return str_replace('---', '', $tdate);

        $treplace = array(
            "Январь" => "января",
            "Февраль" => "февраля",
            "Март" => "марта",
            "Апрель" => "апреля",
            "Май" => "мая",
            "Июнь" => "июня",
            "Июль" => "июля",
            "Август" => "августа",
            "Сентябрь" => "сентября",
            "Октябрь" => "октября",
            "Ноябрь" => "ноября",
            "Декабрь" => "декабря",

            "January" => "января",
            "February" => "февраля",
            "March" => "марта",
            "April" => "апреля",
            "May" => "мая",
            "June" => "июня",
            "July" => "июля",
            "August" => "августа",
            "September" => "сентября",
            "October" => "октября",
            "November" => "ноября",
            "December" => "декабря",

            "Sunday" => "воскресенье",
            "Monday" => "понедельник",
            "Tuesday" => "вторник",
            "Wednesday" => "среда",
            "Thursday" => "четверг",
            "Friday" => "пятница",
            "Saturday" => "суббота",

            "Sun" => "воскресенье",
            "Mon" => "понедельник",
            "Tue" => "вторник",
            "Wed" => "среда",
            "Thu" => "четверг",
            "Fri" => "пятница",
            "Sat" => "суббота",

            "th" => "",
            "st" => "",
            "nd" => "",
            "rd" => ""

        );

        return strtr($tdate, $treplace);
    }
}