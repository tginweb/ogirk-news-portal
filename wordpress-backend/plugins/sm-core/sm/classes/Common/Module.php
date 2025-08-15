<?php

namespace SM\Common;

use SM\Util;
use SM\Classes;

class Module extends Component
{

    function init()
    {
        parent::init();

        return $this;
    }

    function init_related()
    {
        $this->init_sub_classes('module');
    }

    function add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $cb=null)
    {
        if (!current_user_can($capability))
        {
            return;
        }

        $page_title = $page_title ?: $menu_title;

        $function_slug = strtr($menu_slug,'-','_');

        $submenu = add_submenu_page($parent_slug, $page_title,  $menu_title, $capability, $menu_slug, $cb);

        if (method_exists($this, 'page_admin_'.$function_slug.'_assets'))
        {
            add_action('admin_print_styles-'.$submenu, array($this, 'page_admin_'.$function_slug.'_assets'));
        }
    }


    function init_events()
    {
        parent::init_events();

        if ($this->check_init_context('plugin'))
        {
            /*
            register_activation_hook($this->get_class_info('register_wp_file'), array($this, '_wp_install'));

            register_deactivation_hook($this->get_class_info('register_wp_file'), array($this, '_wp_deactivation'));

            register_uninstall_hook($this->get_class_info('register_wp_file'), array($this, '_wp_uninstall'));
            */
        }
    }


    function _wp_install()          { }
    function _wp_deactivation()     { }
    function _wp_uninstall()        { }
}




