<?php


namespace SM_Elementor\Common;

use SM\Classes;
use SM\Common;
use SM\Util;
use ElementorPro\Plugin;
use ElementorPro\Modules\Library;

class Plugin_Module extends Common\Module
{
    var $options_name_slug = 'sm_elementor';

    function init_events()
    {
        $this->add_action('elementor/widgets/widgets_registered', null, 10000);

        $this->add_action('elementor/controls/controls_registered');
    }

    function get_module_option($name=null, $default=null)
    {
        if ($name)
            $name = 'modules.'.$this->get_class_id().'.'.$name;
        else
            $name = 'modules.'.$this->get_class_id();

        return \SM_Elementor\Plugin::i()->get_option($name, $default);
    }

    function _action_elementor_widgets_widgets_registered($widgets_manager)
    {

        foreach ($this->get_sub_classes('widget', true) as $class => $class_info)
        {
            if (!empty($class_info['required']) || $this->is_sub_class_active('widget', $class, $class_info['init']))
            {
                $widgets_manager->register_widget_type(new $class);
            }
        }
    }

    function _action_elementor_controls_controls_registered($manager)
    {
        $controls_manager = \Elementor\Plugin::instance()->controls_manager;

        foreach ($this->get_sub_classes('group_control', true) as $class => $class_info)
        {
            if ($class_info['required'] || $this->is_sub_class_active('group_control', $class, $class_info['init']))
            {
                $controls_manager->add_group_control($class_info['control_id'], new $class);
            }
        }
    }

    static $templates_options = null;

    static function get_templates_options()
    {
        if (!isset(self::$templates_options))
        {
            self::$templates_options = [

            ];

           // $templates = Library\Module::get_templates();

            $templates = [];

            if ( !empty( $templates ) ) {
                foreach ( $templates as $template ) {
                    self::$templates_options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
                }
            }
        }

        return self::$templates_options;
    }
}
