<?php

namespace SM\Util;

class Data
{
    static function parse_csv($content, $return_fields = array(), $opts = array())
    {
        $opts += array(
            'delim_rows' => "\n",
            'delim_fields' => "#",
            'limit' => 0,
            'key_field' => null
        );

        $lines = explode($opts['delim_rows'], $content);

        $items = array();

        $count = 0;

        foreach ($lines as $line) {
            if ($opts['limit'] && $count >= $opts['limit']) break;

            $item = array();

            $parsed_fields = explode($opts['delim_fields'], $line);

            foreach ($return_fields as $field_index => $field_name) {
                $item[$field_name] = $parsed_fields[$field_index];
            }

            $count++;

            if ($opts['key_field']) {
                $items[$item[$opts['key_field']]] = $item;
            } else {
                $items[] = $item;
            }
        }

        return $items;
    }
}