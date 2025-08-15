<?php

namespace SM\Common;

class ParamsProfile
{
    static $instances = array();

    var $params          = array();
    var $params_defaults = array();
    var $params_groups   = array();

    /**
     * @return ParamsProfile
     */
    static function get_profile($class_id)
    {
        return self::$instances[$class_id];
    }

    static function set_profile($class_id, $profile)
    {
        self::$instances[$class_id] = $profile;
    }

    function __construct($class, $params=array(), $params_groups=array(), $params_defaults=array())
    {
        $this->params  = apply_filters('sm/class/params_info', $params, $class);

        $this->params_groups = apply_filters('sm/class/params_groups', $params_groups, $class);

        foreach ($params_defaults as $vname=>$value)
        {
            $this->params[$vname]['default'] = $value;
        }

        foreach ($this->params as $vname=> $vinfo)
        {
            if (isset($vinfo['default']))
            {
                $this->params_defaults[$vname] = $vinfo['default'];
            }
        }
    }

    function info($vname, $key=null, $prepare=true)
    {
        if (!isset($this->params[$vname])) return;

        if ($prepare && !isset($this->params[$vname]['_prepared']))
        {
            $this->info_prepare($vname);
        }

        return $key ? (isset($this->params[$vname][$key]) ? $this->params[$vname][$key] : null) : $this->params[$vname];
    }

    function defval($name)
    {
        $vinfo = $this->info($name);

        return $vinfo['default'];
    }

    function info_element($vname, $key=null, $prepare=true)
    {
        return $key ? (isset($this->params[$vname][$key]) ? $this->params[$vname][$key] : null) : $this->params[$vname];
    }

    function infos()
    {
        $result = array();

        foreach ($this->params as $vname=>$vinfo)
        {
            $result[$vname] = $this->info($vname);
        }

        return $result;
    }

    function info_prepare($vname)
    {
        if (isset($this->params[$vname]))
        {
            if (!isset($this->params[$vname]['_prepared']))
            {
                $this->params[$vname]['element'] = $this->form_element($this->params[$vname]);

                $this->params[$vname]['_prepared'] = true;
            }

            return $this->params[$vname];
        }
    }

    function defaults()
    {
        return $this->params_defaults;
    }

    function form_element($vinfo)
    {
        if (!is_array($vinfo)) $vinfo = $this->info($vinfo);

        if (!empty($vinfo['form_cb']) && is_callable($vinfo['form_cb']))
        {
            $vinfo['form'] = call_user_func($vinfo['form_cb']);
        }

        $element = is_array($vinfo['form']) ? $vinfo['form'] : array();

        /*
        if (is_string($vinfo['form']))
        {
            $element += sm_form_api::extract_element_placement($vinfo['form']);
        }
        */

        return $element;
    }

    function info_query($criteria=array(), $value_field=null)
    {
        $criteria += array(
            'form' => null,
            'type' => null,
            'keys_filter' => array(),
            'keys_filter_not' => array(),
        );

        $result = array();

        foreach (array_keys($this->params) as $vname)
        {
            $vinfo = $this->info($vname);

            if ($criteria['form'] && (empty($vinfo['form']) || ($vinfo['loadable']===false))) continue;

            if ($criteria['type'] && $vinfo['type']!=$criteria['type']) continue;

            if ($criteria['page'] && $vinfo['page']!=$criteria['page']) continue;

            if (!empty($criteria['keys_filter']) && !in_array($vname, $criteria['keys_filter'])) continue;

            if (!empty($criteria['keys_filter_not']) && in_array($vname, $criteria['keys_filter'])) continue;

            if ($value_field)
            {
                $result[$vname] = $vinfo[$value_field];
            }
            else
            {
                $result[$vname] = $vinfo;
            }
        }

        return $result;
    }

    function form($criteria=array())
    {
        $form = $this->info_query(array('form'=>true) + $criteria, 'element');

        $form['#groups'] = $this->params_groups;

        return $form;
    }



}

