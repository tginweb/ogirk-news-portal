<?php

namespace SM\Common;

use SM\Assets;
use SM\Util;
use SM\Classes;

class Component extends Base
{
    //use TraitComponent;
    static function params_info()         { return array(); }
    static function params_groups()       { return array(); }
    static function params_defaults()     { return array(); }

    var $inited = null;
    var $options;
    var $options_name_slug;
    var $sm_class_id = null;

    function load_options()
    {
        $options = [];

        if ($this->options_name_slug)
        {
            $options = get_option($this->options_name_slug, []);

            if (!is_array($options))
                $options = [];

            if ( file_exists(STYLESHEETPATH . '/options/' . $this->options_name_slug . '.php')) {
                $file_options = include(STYLESHEETPATH . '/options/' . $this->options_name_slug . '.php');
            } elseif ( file_exists(TEMPLATEPATH . '/options/' . $this->options_name_slug . '.php') ) {
                $file_options = include(TEMPLATEPATH . '/options/' . $this->options_name_slug . '.php');
            }

            if ($file_options)
                $options = Util\Base::extend($options, $file_options);
        }

        return $options;
    }

    function save_options()
    {

    }

    function get_option($name=null, $default=null)
    {
        if (!isset($this->options))
            $this->options = $this->load_options();

        return $name ? Util\Base::get_nested_value($this->options, $name, $default) : $this->options;
    }

    function sm_assets_context()
    {
        if ($assets_obj = ($this->get_class_info('assets_path') ? $this : $this->get_class_owner()))
        {
            $context = array(
                'assets_path' => $assets_obj->get_class_info('assets_path'),
                'namespace'   => $assets_obj->get_class_info('namespace')
            );
        }
        else
            $context = array();

        return $context;
    }

    function sm_class_set($array)
    {
        foreach ($array as $key=>&$item)
        {
            if (is_array($item))
            {
                $item['sm_invoke_class'] = get_class($this);
            }
        }
        return $array;
    }

    function validate()
    {
        return true;
    }

    function init()
    {
        if (isset($this->inited)) return $this->inited;

        if ($this->validate())
        {
            $this->inited = true;

            //Util\Db::check_tables($this->sm_class_invoke('table_schema'));

            $this->init_events();
            $this->register_assets();
            $this->init_related();
            $this->enqueue_assets();

            return $this;
        }
        else
        {
            $this->inited = false;
            return false;
        }
    }

    function init_related()
    {

    }

    function init_sub_classes($class_type)
    {
        foreach ($this->get_sub_classes($class_type, true) as $class=>$class_info)
        {
            if (!empty($class_info['required']) || $this->is_sub_class_active($class_type, $class, $class_info['init']))
            {
                $class::i();
            }
        }
    }

    function get_sub_classes($class_type, $load=true)
    {

        return Classes::i()->find_classes_info(array('class_type'=>$class_type, 'class_owner'=>get_class($this)), $load);
    }

    function is_sub_class_active($class_type, $cls, $default=false)
    {
        return $default;
        //return get_option($cls.'_init', $default);
    }

    function assets()
    {
        return [];
    }

    function register_assets()
    {
        $assets = $this->assets();

        if (!empty($assets))
        {
            Assets::i()->register_assets($assets);
        }
    }

    function enqueue_assets()
    {

    }

    function init_events()
    {
        if ($this->get_class_info('client_class_info'))
        {
            add_filter('sm/client/settings', function ($settings) {

                $settings['class_info'][$this->sm_class] = $this->get_client_class_info();

                return $settings;
            });
        }
    }

    function add_action($hook, $method=null, $priority=10, $accepted_args=1)
    {
        if (!$method)
        {
            $method = array($this, '_action_'.preg_replace('/[\/\-]/', '_', $hook));
        }
        else if (!is_callable($method))
        {
            $method = array($this, $method);
        }

        foreach ((array)$hook as $h) add_action($h, $method, $priority, $accepted_args);
    }

    function add_filter($hook, $method=null, $priority=10, $accepted_args=1)
    {
        if (!$method)
        {
            $method = array($this, '_filter_'.preg_replace('/[\/\-]/', '_', $hook));
        }
        else if (!is_callable($method))
        {
            $method = array($this, $method);
        }

        foreach ((array)$hook as $h) add_filter($h, $method, $priority, $accepted_args);
    }

    function add_shortcode($hook, $method=null)
    {
        if (!$method)
        {
            $method = array($this, '_shortcode_'.preg_replace('/[\/\-]/', '_', $hook));
        }
        else if (!is_callable($method))
        {
            $method = array($this, $method);
        }

        foreach ((array)$hook as $h) add_shortcode($h, $method);
    }

    function add_format($hook, $method=null, $priority=10, $accepted_args=2) {

        if (!$method)
        {
            $method = array($this, '_format_'.preg_replace('/[^\d\w]/', '_', $hook));
        }
        else if (!is_callable($method))
        {
            $method = array($this, $method);
        }

        foreach ((array)$hook as $h) add_filter('sm/format/'.$h, $method, $priority, $accepted_args);
    }


    function get_client_class_info() {
        return array(
            'class'   => get_class($this),
            'assets_url' => $this->get_class_info('assets_url'),
        );
    }




    function check_init_context($context) {
        return $this->get_class_info('init_context')==$context;
    }

    function get_text_domain() {
        return 'smart';
    }

    function include_sub_folder($folder, $recursive=true, $exclude_pattern='/\\!/') {

        Util\File::include_dir($this->get_path().'/'.$folder, $recursive, '*.{php,inc}', $exclude_pattern);

    }

    function get_class_id()
    {
        if (!isset($this->sm_class_id))
            $this->sm_class_id = $this->get_class_info('id', false);

        return $this->sm_class_id;
    }

    function get_table_name($table_key) { return $GLOBALS['wpdb']->prefix.$table_key; }

}




