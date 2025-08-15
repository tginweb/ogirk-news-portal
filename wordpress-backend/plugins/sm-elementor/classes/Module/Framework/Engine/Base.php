<?php


namespace SM_Elementor\Module\Framework\Engine;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

abstract class Base
{

    var $grid_brakepoints;

    var $vars = [];

    var $brakepoints_by_device = [
        'mobile' => 'sm',
        'tablet' => 'md',
        'desktop' => 'lg',
        'ldesktop' => 'xl',
    ];

    var $brakepoints_by_device_max = [
        'mobile' => 'md',
        'tablet' => 'lg',
    ];

    abstract function get_grid_col_width_classes($settings_key, $settings=[], $defaults=[]);

    abstract function get_grid_col_classes($settings_key, $settings=[], $defaults=[], $override_width=[]);

    abstract function get_grid_row_class();

    abstract function get_style_css();

    function set_vars($vars)
    {
        $this->vars = array_filter($vars);
    }

    function get_var($var_name, $default=null)
    {
        return isset($this->vars[$var_name]) ? $this->vars[$var_name] : $default;
    }

    function get_grid_columns_count()
    {
        return $this->get_var('grid_columns_count', 12);
    }

    function get_grid_breakpoints($name=null)
    {
        if (!isset($this->grid_brakepoints))
        {
            $this->grid_brakepoints = [
                'xs' => $this->get_var('grid_breakpoint_xs', 0),
                'sm' => $this->get_var('grid_breakpoint_sm', 479),
                'md' => $this->get_var('grid_breakpoint_md', 767),
                'lg' => $this->get_var('grid_breakpoint_lg', 1024),
                'xl' => $this->get_var('grid_breakpoint_xl', 1200)
            ];
        }

        return $name ? $this->grid_brakepoints[$name] : $this->grid_brakepoints;
    }


    function get_grid_gutter_width()
    {
        return $this->get_var('grid_gutter_width', 30);
    }

    function get_procentage($size, $columns) {
        return (round($size / $columns, 10)*100).'%';
    }


    function get_breakpoint_value_by_device_max($name)
    {
        if (!empty($this->brakepoints_by_device_max[$name]))
        {
            $bp_name = $this->brakepoints_by_device_max[$name];

            return $this->get_grid_breakpoints($bp_name);
        }
    }

}

