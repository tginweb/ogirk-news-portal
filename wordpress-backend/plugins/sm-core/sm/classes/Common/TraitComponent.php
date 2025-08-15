<?php


namespace SM\Common;

use SM\Util;
use SM\Common\ParamsProfile;

trait TraitComponent
{
    var $load_name;

    var $params_loaded        = array();
    var $params_values        = array();
    var $params_values_cache  = array();
    var $params_objects       = array();
    var $params_storage       = null;
    var $params_cache         = [];
    var $params_subset_cache  = [];

    var $parent_component;


    /* @return ParamsProfile */
    static function params()
    {
        $cls = rtrim(get_called_class(), '_');

        if (!isset(ParamsProfile::$instances[$cls]))
        {
            $params_info = method_exists($cls, 'params_info') ? static::params_info() : [];

            $params_groups = method_exists($cls, 'params_groups') ? static::params_info() : [];

            $params_defaults = method_exists($cls, 'params_defaults') ? static::params_defaults() : [];

            $profile = new ParamsProfile($cls, $params_info, $params_groups, $params_defaults);

            ParamsProfile::set_profile($cls, $profile);
        }

        return ParamsProfile::$instances[$cls];
    }

    function params_load($values)
    {
        $this->params_loaded = $values;
        $this->params_values = $values;
        $this->params_values_cache = array();
    }


    function param_prefix()
    {
        return $this->get_class_info('namespace');
    }

    function param_set($name, $value)
    {
        unset($this->params_values_cache[$name]);

        $this->params_values[$name] = $value;

        return $this;
    }

    function param_unset($name)
    {
        unset($this->params_values_cache[$name], $this->params_values[$name]);

        return $this;
    }

    function param_save($name, $value=null, $autoload=null)
    {
        $this->param_set($name, $value);

        $this->save_storage_param($name, $value);
    }

    function param($name, $def=null, $type=null)
    {
        //if (in_array($name, ['class','id','attrs','style','link','tag'])) return;

        $name = $this->param_prefix().$name;

        if (!isset($this->params_values_cache[$name]))
        {
            $vinfo = self::params()->info($name);

            if (isset($this->params_values[$name]))
            {
                $val = $this->params_values[$name];
            }
            else
            {
                $val = $this->get_storage_param($name);
            }

            if (!isset($val))
            {
                if ($vinfo && isset($vinfo['default']))
                {
                    $val = $vinfo['default'];
                }
                else if (isset($this->params_defaults[$name]))
                {
                    $val = $this->params_defaults[$name];
                }
            }

            switch ($type)
            {
                case 'bool':    $val = Util\Base::get_bool($val); break;
                case 'array':   $val = Util\Base::unpack_attr_array($val); break;
                case 'attrs':   $val = is_string($val) ? shortcode_parse_atts($val) : $val; break;
                case 'class':   $val = is_string($val) ? explode(' ', $val) : (array)$val; break;
                case 'style':   $val = Util\Html::parse_style($val);
            }

            $this->params_values_cache[$name] = $val;
        }

        return isset($this->params_values_cache[$name]) ? $this->params_values_cache[$name] : $def;
    }


    function params_values($c=array(), $map_keys=array())
    {
        $result = array();

        foreach (self::params()->info_query($c) as $vname=>$vinfo)
        {
            $result[$vname] = $this->param($vname);

            if (!empty($map_keys[$vname]))
            {
                $result[$map_keys[$vname]] = $this->param($vname);
            }
            else
            {
                $result[$vname] = $this->param($vname);
            }
        }

        return $result;
    }

    function params_values_subset($key)
    {
        if (!isset($this->params_subset_cache[$key]))
        {
            $result = [];

            if ($cval = $this->params_values[$key])
            {
                if (is_array($cval))
                {
                    $result = $cval;
                }
                else if (is_string($cval))
                {
                    $result = Util\Base::unpack_attr_array($cval);
                }
            }

            foreach ($this->params_values as $vname => $val)
            {
                if (strpos($vname, $key.'_')===0) $result[substr($vname, strlen($key.'_'))] = $val;
            }

            if (!empty($result['params']))
            {
                $result = Util\Base::unpack_attr_array($result['params']);

                unset($result['params']);
            }

            $this->params_subset_cache[$key] = $result;
        }

        return $this->params_subset_cache[$key];
    }


    function tpl_render($method=null)
    {
        $template = $this->param('template');

        if (!$template) return false;

        $cls = rtrim(get_called_class(), '_');

        ob_start();

        $renders = 0;

        do_action_ref_array('sm/theme/'.$cls.'/'.$template.'/'.$method, [$this, &$renders]);

        $result = ob_get_clean();

        return $renders ? $result : false;
    }

    function call_render($methods=null)
    {
        $args = func_get_args();

        array_shift($args);

        $result = false;

        foreach ((array)($methods) as $method_variant)
        {
            if (method_exists($this, $method_variant))
            {
                ob_start();

                $return = call_user_func_array(array($this, $method_variant), $args);

                if ($return===false) return false;

                $output = ob_get_clean();

                $result = $return.$output;

                break;
            }
        }

        return $result;
    }

    function child_component($class=null, $params=array(), $subparams_name=false, $store_name=false, $subclass_of=null)
    {
        if ($store_name && isset($this->child_components[$store_name])) return $this->child_components[$store_name];

        if (class_exists($class))
        {
            if ($subclass_of && !is_subclass_of($class, $subclass_of)) return;

            if ($subparams_name) $params = $params + $this->params_values_subset($subparams_name);

            $child_component = new $class($params);

            $child_component->parent_component = $this;
        }

        if ($store_name)
        {
            $this->child_components[$store_name] = $child_component;
        }

        return $child_component;
    }

    function get_parent_component()
    {
        return $this->parent_component;
    }
}


