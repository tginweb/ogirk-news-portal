<?php

namespace SM\Util;

class Base
{
    static function class_suggestion_find($classes, $subclass_of=null)
    {
        static $cache = [];

        foreach ($classes as $class)
        {
            if (class_exists($class))
            {
                return $class;
            }
        }

        return false;
    }

    static function array_alter_keys($array, $prefix = '', $suffix = '')
    {
        $result = array();

        foreach ((array)$array as $key=>$value)
        {
            $result[$prefix.$key.$suffix] = $value;
        }

        return $result;
    }

    static function array_insert_before(&$array, $position, $insert)
    {
        if (is_int($position))
        {
            array_splice($array, $position, 0, $insert);
        }
        else
        {
            $pos   = array_search($position, array_keys($array));
            $array = array_merge(
                array_slice($array, 0, $pos),
                $insert,
                array_slice($array, $pos)
            );
        }
    }

    static function array_insert_after(&$array, $position, $insert)
    {
        if (is_int($position))
        {
            array_splice($array, $position, 0, $insert);
        }
        else
        {
            $pos   = array_search($position, array_keys($array));
            $array = array_merge(
                array_slice($array, 0, $pos),
                $insert,
                array_slice($array, $pos)
            );
        }
    }

    static function first_noempty_arg()
    {
        foreach (func_get_args() as $arg)
        {
            if (!empty($arg)) return $arg;
        }
    }

    static function &get_static($name, $default_value = NULL, $reset = FALSE)
    {
        static $data = array(), $default = array();
        // First check if dealing with a previously defined static variable.
        if (isset($data[$name]) || array_key_exists($name, $data)) {
            // Non-NULL $name and both $data[$name] and $default[$name] statics exist.
            if ($reset) {
                // Reset pre-existing static variable to its default value.
                $data[$name] = $default[$name];
            }
            return $data[$name];
        }
        // Neither $data[$name] nor $default[$name] static variables exist.
        if (isset($name)) {
            if ($reset) {
                // Reset was called before a default is set and yet a variable must be
                // returned.
                return $data;
            }
            // First call with new non-NULL $name. Initialize a new static variable.
            $default[$name] = $data[$name] = $default_value;
            return $data[$name];
        }
        // Reset all: ($name == NULL). This needs to be done one at a time so that
        // references returned by earlier invocations of drupal_static() also get
        // reset.
        foreach ($default as $name => $value) {
            $data[$name] = $value;
        }
        // As the function returns a reference, the return should always be a
        // variable.
        return $data;
    }

    static function array_tree_flatten($array, $children_field=null, $depth=0)
    {
        $result = array();

        if (!empty($array))
        {
            foreach ($array as $key => $info)
            {
                if ($children_field)
                {
                    $result[$key] = $info;
                    $childs = $info[$children_field];
                }
                else
                {
                    $result[$key] = $info;
                }

                if (!empty($childs))
                {
                    $result = array_merge($result, self::array_tree_flatten($childs, $children_field, $depth+1));
                }
            }
        }

        return $result;
    }

    static function num_name($num=null)
    {
        $schema = array(
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen'
        );

        return  $num ? $schema[$num] : $schema;
    }

    static function extender(&$base)
    {
        $arrays = func_get_args();

        $base = call_user_func_array(['\SM\Util\Base', 'extend'], $arrays);
    }

    static function extend()
    {
        $arrays = func_get_args();

        $result = array();

        foreach ($arrays as $array)
        {
            if (!is_array($array)) $array = [];

            foreach ($array as $key => $value)
            {
                // Renumber integer keys as array_merge_recursive() does. Note that PHP
                // automatically converts array keys that are integer strings (e.g., '1')
                // to integers.
                if (is_integer($key)) {
                    $result[] = $value;
                }
                // Recurse when both values are arrays.
                elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    $result[$key] = self::extend($result[$key], $value);
                }
                // Otherwise, use the latter value, overriding any previous value.
                else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    static function extend_resolved_safe()
    {
        $arrays = func_get_args();

        $result = array();

        foreach ($arrays as $array)
        {
            if (!is_array($array)) $array = [];

            foreach ($array as $key => $value)
            {
                if (!is_string($value) && !is_array($value) && is_callable($value))
                {
                    $value = call_user_func($value);
                }

                if (is_integer($key)) {
                    $result[] = $value;
                }
                elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    $result[$key] = self::extend($result[$key], $value);
                }
                else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    static function extend_items(&$array, $extend)
    {
        foreach ($array as &$item)
        {
            $item = self::extend($item, $extend);
        }
    }

    static function array_replace_keys($array, $match, $replace='')
    {
        $result = array();

        foreach ($array as $i=>$v)
        {
            $result[preg_replace($match, $replace, $i)] = $v;
        }

        return $result;
    }

    static function array_select_options($array, $title_key='label', $prepend=array(), $depth_key=null)
    {
        $result = $prepend;

        if (!empty($array))
        {
            foreach ($array as $key=>$item)
            {
                $depth = 0;

                if (is_object($item))
                {
                    $title = $item->{$title_key};
                }
                else
                {
                    $title = $item[$title_key];
                }

                if ($depth_key)
                {
                    if (is_object($item))
                    {
                        $depth = $item->{$depth_key};
                    }
                    else
                    {
                        $depth = $item[$depth_key];
                    }
                }

                if ($depth)
                {
                    $title = str_repeat('-', $depth).' '.$title;
                }

                $result[$key] = $title;
            }
        }

        return $result;
    }

    static function map_assoc($array, $function = NULL)
    {
        $array = !empty($array) ? array_combine($array, $array) : array();

        if (is_callable($function)) $array = array_map($function, $array);

        return $array;
    }

    static function map_assoc_info($array, $function = NULL)
    {
        $result = array();

        foreach ((array)$array as $key=>$value)
        {
            if (is_numeric($key) && is_string($value))
                $result[$value] = $value;
            else
                $result[$key] = $value;
        }

        return $result;
    }


    static function &get_nested_value(&$array, $parents, $default=null, &$key_exists = NULL)
    {
        $ref = &$array;

        if (is_string($parents))
        {
            $parents = explode('.', $parents);
        }

        if (is_array($parents) && !empty($parents))
        {
            foreach ($parents as $parent) {
                if (is_array($ref) && array_key_exists($parent, $ref)) {
                    $ref = &$ref[$parent];
                }
                else {
                    $key_exists = FALSE;
                    $null = $default;
                    return $null;
                }
            }
        }

        $key_exists = TRUE;
        return $ref;
    }

    static function set_nested_value(array &$array, array $parents, $value, $force = FALSE)
    {
        if (is_string($parents))
        {
            $parents = explode('/', $parents);
        }

        $ref = &$array;
        foreach ($parents as $parent) {
            // PHP auto-creates container arrays and NULL entries without error if $ref
            // is NULL, but throws an error if $ref is set, but not an array.
            if ($force && isset($ref) && !is_array($ref)) {
                $ref = array();
            }
            $ref = &$ref[$parent];
        }
        $ref = $value;
    }

    static function sort_by($field, $a, $b)
    {
        if (is_array($a))
        {
            $a_weight = (isset($a[$field])) ? $a[$field] : 0;
            $b_weight = (isset($b[$field])) ? $b[$field] : 0;
        }
        else if (is_object($a))
        {
            $a_weight = (isset($a->{$field})) ? $a->{$field} : 0;
            $b_weight = (isset($b->{$field})) ? $b->{$field} : 0;
        }

        if ($a_weight == $b_weight) {
            return 0;
        }
        return ($a_weight < $b_weight) ? -1 : 1;
    }

    static function sort_weight($a, $b)
    {
        if (is_array($a))
        {
            $a_weight = (isset($a['weight'])) ? $a ['weight'] : 0;
            $b_weight = (isset($b['weight'])) ? $b ['weight'] : 0;
        }
        else if (is_object($a))
        {
            $a_weight = (isset($a->weight)) ? $a->weight : 0;
            $b_weight = (isset($b->weight)) ? $b->weight : 0;
        }

        if ($a_weight == $b_weight) {
            return 0;
        }
        return ($a_weight < $b_weight) ? -1 : 1;
    }

    static function sort_menu_order($a, $b)
    {
        if (is_array($a))
        {
            $a_weight = (isset($a['menu_order'])) ? $a ['menu_order'] : 0;
            $b_weight = (isset($b['menu_order'])) ? $b ['menu_order'] : 0;
        }
        else if (is_object($a))
        {
            $a_weight = (isset($a->menu_order)) ? $a->menu_order : 0;
            $b_weight = (isset($b->menu_order)) ? $b->menu_order : 0;
        }

        if ($a_weight == $b_weight) {
            return 0;
        }
        return ($a_weight < $b_weight) ? -1 : 1;
    }


    static function class_first_exists($classes)
    {
        foreach ($classes as $class)
        {
            if (class_exists($class)) return $class;
        }
    }

    static function is_slug($val)
    {
        if (is_scalar($val) && !is_numeric($val) && intval($val) <= 0) return true;
    }

    static function str_to_class($name)
    {
        static $cache;

        if (empty($name)) return '';

        if (empty($cache[$name]))
        {
            $cls = '';
            foreach (preg_split('/[\_\-]/', $name) as $part) $cls .= ucfirst($part);
            $cache[$name] = $cls;
        }
        return $cache[$name];
    }

    static function str_to_key($name)
    {
        return strtr($name, '-', '_');
    }

    static function str_to_name($name)
    {
        return strtr($name, '_', '-');
    }

    static function get_bool($val=null)
    {
        if (!$val) return false;

        if (is_bool($val))
        {
            return $val;
        }
        else if (is_numeric($val))
        {
            return $val>0;
        }
        else if (is_string($val))
        {
            $val = strtoupper($val);

            return in_array($val, array('1','YES','TRUE','ON','ENABLE'));
        }

        return $val;
    }

    static function callback_buffer_get($cb, $args=array())
    {
        if (!is_callable($cb)) return;

        ob_start();

        call_user_func_array($cb, $args);

        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }

    static function url_to_compare($url, $path_only=false)
    {
        if ($path_only)
        {
            $url = strtok($url,'?');
        }

        $url = esc_url( $url );
        $url = str_replace( array( 'http://', 'https://' ), '', strtolower( $url ) );
        $url = rtrim( $url, '/' );
        $url = ltrim( $url, '/' );

        return $url;
    }

    static function get_query_var($name)
    {
        $path = preg_split('/\]?\[/', trim($name,']'));

        $value = self::get_nested_value($_REQUEST, $path);

        return $value;
    }

    static function get_titles_concat($titles, $deilm='. ')
    {
        return join($deilm, $titles);
    }

    static function maybe_base64($data)
    {
        return self::str_is_base64($data) ? base64_decode($data) : $data;
    }

    static function str_is_base64($data)
    {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }


    static function unpack_attr_array($data, $options=array())
    {
        $array = array();

        if ($data)
        {
            if (is_array($data))
            {
                $array = $data;
            }
            else if (is_string($data))
            {
                $data = trim($data);

                if ($data[0] == '{' || $data[0] == '[')
                {
                    $data = preg_replace('/\n/', '\n', $data);

                    $array = json_decode($data, true);
                }
                else if (preg_match('/^[a-zA-Z0-9\_]=[\'"].+[\'"]/', $data))
                {
                    $array = Html::parse_attributes($data);
                }
                else if (preg_match('/^[a-zA-Z0-9\_]\:/', $data))
                {
                    $array = Html::parse_style($data);
                }
                else
                {
                    $array = preg_split('/\s*\,\s*/', $data);
                }
            }
        }

        return $array;
    }

    static function pack_array($data, $opt=array())
    {
        return $data ? json_encode($data) : '';
    }


    static function html_remove_spaces_beetween_tags($content)
    {
        return preg_replace('@>([\s\r\n]+?)<@is', '><', $content);
    }

    static function parse_url_cached($url, $c=null, $reset=false)
    {
        static $cache=array();

        if (!isset($cache[$url]) || $reset) $cache[$url] = self::parse_url($url);

        return $c ? $cache[$url][$c] : $cache[$url];
    }

    static function parse_url($url)
    {
        $info = parse_url($url);

        $path_parts = explode('/', $info['path']);

        $info['filename'] = array_pop($path_parts);

        if (preg_match('/.+\.([\w]+?)$/',$info['path'], $mt)) $info['ext'] = $mt[1];

        return $info;
    }

    static function build_url($parsed_url)
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . trim($parsed_url['query'], '&') : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    static function url_modify_query($url, $include=array(), $exclude=array())
    {
        $url = $url ?: $_SERVER['REQUEST_URI'];

        $parsed = parse_url($url);

        parse_str($parsed['query'], $query);

        if (!empty($exclude))
        {
            foreach($exclude as $param)
            {
                if (isset($query[$param])) unset($query[$param]);
            }
        }

        if (!empty($include))
        {
            foreach ($include as $param=>$value)
            {
                $query[$param] = $value;
            }
        }

        $parsed['query'] = http_build_query($query);

        return self::build_url($parsed);
    }

    static function user_ip()
    {
        if (!empty( $_SERVER['HTTP_CLIENT_IP'] ) )
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif  (!empty( $_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return apply_filters( 'sm_user_ip', $ip );
    }

    static function arg($index = NULL, $path = NULL)
    {
        static $cache=array();

        if (!isset($path))
        {
            $path = strtok($_SERVER["REQUEST_URI"],'?');
        }

        if (!isset($cache[$path]))
        {
            $cache[$path] = explode('/', $path);

            array_shift($cache[$path]);
        }

        if (!isset($index))
        {
            return $cache[$path];
        }

        if (isset($cache[$path][$index]))
        {
            return $cache[$path][$index];
        }
    }

    static function url($url)
    {
        if (strpos($url,'http')!==0)
        {
            $url = '/'.ltrim($url, '/');
        }

        return $url;
    }


    static function load_config($file, &$collector=null)
    {
        $vars = [];

        if (file_exists($file))
        {
            $vars = include $file;
        }

        if (!empty($vars) && is_array($vars))
        {
            if (isset($collector)) self::extender($collector, $vars);

            return $vars;
        }

        return [];
    }



    function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '',  'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    function str2url($str) {
        // переводим в транслит
        $str = rus2translit($str);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");
        return $str;
    }

    static function sub_params(&$params=[], $keys, $preserve_key=false)
    {
        $result = [];

        $keys = (array)$keys;

        foreach ($params as $vname => $val)
        {
            foreach ($keys as $key)
            {
                if (strpos($vname, $key)===0)
                {
                    $pname = substr($vname, strlen($key));

                    if ($preserve_key)
                    {
                        $result[$vname] = $val;
                    }
                    else
                    {
                        $result[$pname] = $val;
                    }
                }
            }
        }

        return $result;
    }
}


