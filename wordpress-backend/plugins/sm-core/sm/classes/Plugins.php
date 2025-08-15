<?php

namespace SM;

use SM\Util;

class Plugins extends Common\Component
{
    var $plugins_info = array();

    /* @return Plugins */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function add_plugins_info($plugins)
    {
        foreach ($plugins as $slug=>$dep)
        {
            if (is_numeric($slug))
            {
                $slug = $dep['slug'];
            }
            else if (!isset($dep['slug']))
            {
                $dep['slug'] = $slug;
            }

            if (empty($dep['load_filepath']) && ($plugin_basename=Util\Wp::plugin_basename_from_slug($slug)))
            {
                $dep['load_filepath'] = WP_PLUGIN_DIR.'/'.$plugin_basename;
            }

            $dep['loaded'] = null;

            $this->plugins_info[$slug] = $dep;
        }
    }

    function init_events()
    {
        parent::init_events();

        include_once __DIR__.'/../libs/tgm/class-tgm-plugin-activation.php';

        add_action('tgmpa_register', array($this, '_action_tgmpa_register'));
    }

    function _action_tgmpa_register()
    {
        $plugins = $this->get_plugins();

        $config = array(
            'id'           => 'smart',                 // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '',                      // Default absolute path to bundled plugins.
            'menu'         => 'smart-install-plugins', // Menu slug.
            'parent_slug'  => 'plugins.php',           // Parent menu slug.
            'capability'   => 'manage_options',        // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
            'has_notices'  => true,                    // Show admin notices or not.
            'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => true,                    // Automatically activate plugins after installation or not.
            'message'      => '',                      // Message to output right before the plugins table.
            'strings'      => array(
                'page_title'                      => __( 'SMART Install Required Plugins', 'smart' ),
                'menu_title'                      => __( 'SMART Install Plugins', 'smart' ),
            )
        );

        tgmpa($plugins, $config);
    }

    function get_plugins()
    {
        return $this->plugins_info;
    }

    function get_plugin($slug, $prop=null)
    {
        $plugins = $this->get_plugins();

        return isset($plugins[$slug]) ? ($prop ? (isset($plugins[$slug][$prop]) ? $plugins[$slug][$prop] : null) : $plugins[$slug]) : null;
    }

    function set_plugin_prop($slug, $prop, $value)
    {
        $this->plugins_info[$slug][$prop] = $value;
    }

    function get_plugin_is_active($slug)
    {
        if ($dep = $this->get_plugin($slug))
        {
            if (!isset($dep['is_active']))
            {
                if (!empty($dep['is_callable']))
                {
                    $dep['is_active'] = is_callable($dep['is_callable']);
                }
                else if (!empty($dep['plugin_file']))
                {
                    $dep['is_active'] = is_plugin_active($dep['plugin_file']);
                }
                else
                {
                    $dep['is_active'] = false;
                }

                $this->set_plugin_prop($slug, 'is_active', $dep['is_active']);
            }

            return $dep['is_active'];
        }
    }

    function get_plugin_is_loaded($slug)
    {
        return $this->get_plugin($slug, 'loaded');
    }

    function check_plugin($slug)
    {
        if ($this->get_plugin_is_active($slug))
        {
            return true;
        }
        else if ($dep = $this->get_plugin($slug))
        {
            if (empty($dep['loaded']))
            {
                if (!empty($dep['load_filepath']) && file_exists($dep['load_filepath']))
                {
                    include_once $dep['load_filepath'];

                    $this->set_plugin_prop($slug, 'loaded', true);
                }
            }

            return $this->get_plugin_is_active($slug);
        }
    }

    function check_plugins($slugs)
    {
        foreach ($slugs as $slug)
        {
            if (!$this->check_plugin($slug)) return false;
        }

        return true;
    }

}





