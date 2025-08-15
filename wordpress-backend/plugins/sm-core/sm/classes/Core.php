<?php

namespace SM;

use SM\Cache;
use SM\Util;

class Core extends Common\Component
{
    var $theme;

    var $env;

    /**
     * @return Core
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function __construct()
    {
        add_action('sm_rebuild', function()
        {
            Cache::i()->query('delete', null, 'system');
        });
    }


    function tpl($name, $params=array())
    {
        if (function_exists('_tpl_'.$name))
        {
            $func = '_tpl_'.$name;
        }
        else if (function_exists('tpl_'.$name))
        {
            $func = 'tpl_'.$name;
        }

        if ($func) return call_user_func($func, $params);
    }

    function render($data, &$values=array())
    {
        if (!$data) return;

        if (is_object($data))
        {
            $content = strval($data);
        }
        else if (is_array($data))
        {
            $content = sm_renderer::render($data, $values);
        }
        else
        {
            $content = $data;
        }

        return $content;
    }

    function get_env()
    {
        if (!isset($this->env))
        {
            $env = defined('WP_ENV') ? WP_ENV : null;

            $env = apply_filters('sm/env', $env);

            $this->env = $env ?: 'prod';
        }

        return $this->env;
    }

    function is_page_builder_mode()
    {
        if (!isset($this->is_page_builder))
        {
            $env = defined('WP_ENV') ? WP_ENV : null;

            $env = apply_filters('sm/env', $env);

            $this->env = $env ?: 'prod';
        }

        return $this->env;
    }

    /* @return \SM_Theme_Smart\Module */
    function get_theme()
    {
        return $this->theme;
    }

    function set_theme($theme)
    {
        return $this->theme = $theme;
    }

    function theme_var($name, $default)
    {
        if ($this->get_theme()) return $this->get_theme()->get_theme_var($name, $default);

        return $default;
    }
}
