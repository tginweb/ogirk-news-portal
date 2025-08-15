<?php


namespace SM\Util;

class Db
{
    static function db_link()
    {
        static $dbc;

        if (!$dbc) {
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
            mysqli_select_db($dbc, DB_NAME);
            mysqli_set_charset($dbc, 'utf8');
        }

        return $dbc;
    }

    static function db_query($query, $resultmode = MYSQLI_STORE_RESULT)
    {
        $dbc = self::db_link();

        return mysqli_query($dbc, $query, $resultmode);
    }

    static function db_insert($table, $data, $type = 'INSERT')
    {
        if (!in_array(strtoupper($type), array('REPLACE', 'INSERT'))) return false;

        $fields = $values = array();

        foreach ($data as $field => $value) {
            $fields[] = $field;
            $values[] = "'" . $value . "'";
        }

        $sql_fields = implode(', ', $fields);
        $sql_value = implode(', ', $values);

        $sql = "$type INTO `$table` ($sql_fields) VALUES ($sql_value)";

        return self::db_query($sql);
    }

    static function sql_where($conditions = array(), $logic = 'AND')
    {
        array_unshift($conditions, '1=1');

        $sql = join(' ' . $logic . ' ', array_filter($conditions));

        return $sql;
    }

    static function check_tables($info)
    {
        if (!empty($info))
        {
            foreach ($info as $table_key => $table_info)
            {
                if (!self::check_table($table_key))
                {
                    self::create_table($table_key, $table_info);
                }
            }
        }
    }

    static function check_table($table_key)
    {
        global $wpdb;

        $table_name = $wpdb->prefix.$table_key;

        return $wpdb->query("SHOW TABLES LIKE '".$table_name."'");
    }

    static function create_table($table_key, $table_info)
    {
        global $wpdb;

        $table_name = $wpdb->prefix.$table_key;

        if ( !empty($wpdb->charset) ) $charset_collate  = "DEFAULT CHARACTER SET ".$wpdb->charset;
        if ( !empty($wpdb->collate) ) $charset_collate .= " COLLATE ".$wpdb->collate;

        if (is_string($table_info['fields']))   $fields[] = $table_info['fields'];
        if (is_string($table_info['indexes']))  $fields[] = $table_info['indexes'];

        $wpdb->show_errors();

        $sql    = "CREATE TABLE IF NOT EXISTS {$table_name} (".join(',', $fields).") ".$charset_collate;
        $result = $wpdb->query($wpdb->prepare($sql, array()));

        $wpdb->hide_errors();

        return $result;
    }
}