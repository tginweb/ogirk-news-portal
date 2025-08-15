<?php

namespace SM\Util;

class Html
{
    static $element_params = [
        'container_tag'   => null,
        'container_attrs' => null,
        'container_class' => null,
        'tag'             => null,
        'attrs'           => null,
        'class'           => null,
    ];

    static function attributes_flatten($items = array(), $remove_empty=true)
    {
        if (empty($items)) return ' ';

        $items = (array)$items;

        foreach ($items as $key => &$data)
        {
            if (!$data && $remove_empty)
            {
                unset($items[$key]);
                continue;
            }

            if ($key=='style' && is_array($data))
            {
                $data = self::style($data);
            }

            $data = implode(' ', (array)$data);
        }

        return $items;
    }

    static function tag($tag, $attrs = array(), $content=null, $sf=false)
    {
        if (isset($attrs['content_before']))
        {
            $content = $attrs['content_before'].$content;

            unset($attrs['content_before']);
        }

        if (isset($attrs['content_after']))
        {
            $content = $attrs['content_after'].$content;

            unset($attrs['content_after']);
        }

        if (!empty($attrs['tag']))
        {
            $tag = $attrs['tag'];

            unset($attrs['tag']);
        }

        if (empty($tag)) return $content;

        if (strlen(strval($content))>0)
        {
            return join('', array('<'.$tag.' '.self::attributes($attrs), ' >', $content, '</'.$tag.'>'));
        }
        else
        {
            return !$sf ? '<'.$tag.' '.self::attributes($attrs).' />' : '<'.$tag.' '.self::attributes($attrs).'></'.$tag.'>';
        }
    }


    static function attributes_params_fetch($params, $host_defaults=[], $host_name=null)
    {
        if (!is_array($params)) $params = [];

        $host_pref = $host_name ? $host_name.'_' : '';

        if (!$host_name)
        {
            $params += self::$element_params;
        }

        if (!isset($params['tag']) && isset($host_defaults[$host_pref.'tag']))

            $params['tag'] = $host_defaults[$host_pref.'tag'];

        if (!isset($params['container_tag']) && isset($host_defaults[$host_pref.'container_tag']))

            $params['container_tag'] = $host_defaults[$host_pref.'container_tag'];


        if (@$params[$host_pref.'container_tag'])
        {
            $params['container_attrs'] = isset($params['container_attrs']) ? $params['container_attrs'] : [];

            @self::attributes_extend_ref([

                $params['container_attrs'],

                $host_defaults[$host_pref.'container_attrs'],

                ['class'=>$host_defaults[$host_pref.'container_class']]

            ]);
        }

        $params['attrs'] = @self::attributes_extend(

            $host_defaults[$host_pref.'attrs'],

            ['class'=>$host_defaults[$host_pref.'class']],

            $params['attrs'],

            ['class'=>$params['class']]
        );

        return $params;
    }


    static function attributes_extend()
    {
        $args = func_get_args();

        return self::attributes_extend_ref($args);
    }

    static function attributes_extend_ref($args)
    {
        if (empty($args[0]) || !is_array($args[0])) $args[0] = [];

        $result = &$args[0];

        $cnt = 0;

        foreach ($args as &$arg)
        {
            if ($arg)
            {
                $arg = is_string($arg) ? shortcode_parse_atts($arg) : $arg;

                if (isset($arg['class']))
                {
                    $arg['class'] = is_array($arg['class']) ? $arg['class'] : preg_split('/\s+/', $arg['class']);
                }
                else
                {
                    $arg['class'] = [];
                }

                if ($cnt++) $result = Base::extend($result, $arg);
            }
        }

        return $result;
    }


    static function attributes_fetch(&$attrs, $class=null)
    {
        if (!empty($attrs))
        {
            if (is_string($attrs))
            {
                $attrs = shortcode_parse_atts($attrs);
            }
        }
        else
        {
            $attrs = [];
        }

        if (!empty($attrs['class']))
        {
            if (is_string($attrs['class']))
            {
                $attrs['class'] = preg_split('/\s+/', $attrs['class']);
            }
        }
        else
        {
            $attrs['class'] = [];
        }

        if ($class)
        {
            $attrs['class'] = is_array($class) ? array_merge($attrs['class'], $class) : array_merge($attrs['class'], preg_split('/\s+/', $class));
        }

        return $attrs;
    }

    static function attributes($items = array())
    {
        if (empty($items)) return ' ';

        $items = (array)$items;

        foreach ($items as $key => &$data)
        {
            if (!$data && $data!==false && !is_array($data) && $key!='value')
            {
                unset($items[$key]);
                continue;
            }

            if ($key=='style' && is_array($data))
            {
                $data = self::style($data);
            }
            elseif ($key=='class' && is_array($data))
            {
                $data = array_unique($data);
            }
            elseif (strpos($key,'data-')===0 && is_array($data))
            {
                $data = json_encode($data);
            }

            if (is_numeric($key))
            {
                $data = implode(' ', (array)$data);
            }
            else
            {
                $data = implode(' ', (array)$data);
                $data = $key . '="' . esc_attr($data) . '"';
            }
        }

        return ' ' . implode(' ', $items);
    }

    static function style_vars($items = array(), $pref='@', $delim=";\n")
    {
        $items = array_filter((array)$items);

        if (empty($items)) return '';

        foreach ($items as $key => &$data)
        {
            if (!is_numeric($key))
            {
                $data = $key . ': ' . $data;
            }
        }

        return implode($delim, $items);
    }

    static function style($items = array())
    {
        $items = array_filter((array)$items);

        if (empty($items)) return '';

        foreach ($items as $key => &$data)
        {
            if (!is_numeric($key))
            {
                $data = $key . ':' . $data;
            }
        }

        return implode(';', $items);
    }

    static function parse_style($str='')
    {
        if (is_array($str)) return $str;

        $res = array();

        $str = preg_replace('/^.*\{/s', '', $str);
        $str = preg_replace('/\}.*$/s', '', $str);

        foreach (preg_split('/\s*\;\s*/s', $str) as $pair)
        {
            if (!($pair = trim($pair))) continue;

            list($key, $val) = preg_split('/\s*\:\s*/', $pair, 2);

            $res[$key] = $val;
        }

        return $res;
    }

    static function parse_attributes($str)
    {
        $atts = shortcode_parse_atts('[parse ' . $str . ' ]');
        unset($atts[0], $atts[1]);
        return $atts;
    }

    static function html_code_is_empty($content)
    {
        if (!trim($content)) return true;

        if (strpos($content, '<!--SM-EMPTY-->') !== false) return true;

        return false;
    }

    static function less_import_file($file)
    {
        return $file ? "/* EMBED FROM $file */\n@import \"$file\";\n/* END EMBED */\n" : '';
    }

    static function value_from_safe($value, $encode = false)
    {
        $value = preg_match('/^#E\-8_/', $value) ? rawurldecode(base64_decode(preg_replace('/^#E\-8_/', '', $value))) : $value;
        if ($encode)
        {
            $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
        }
        return $value;
    }
}

