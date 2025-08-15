<?php

namespace SM_Elementor\Module\Query\Filter_Controller\Common;

use SM_Elementor;
use SM_Elementor\Module\QueryControls\Group_Control\Entity_Query;


abstract class Base
{
    var $parent;
    var $settings;
    var $name;
    var $value = [];
    var $widget;

    function __construct($parent, $settings, $value=null)
    {
        $this->parent = $parent;
        $this->settings = $settings;

        $this->name = $settings['name'];

        if (isset($value))
            $this->set_value($value);
    }

    function set_value($value)
    {
        $this->value = (array)$value;

        return $this;
    }

    function set_query_vars_value(&$query_vars)
    {

    }

    function validate()
    {
        return true;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function get_default_value()
    {
        return $this->settings['default_value'];
    }

    function get_widget()
    {
        if (!isset($this->widget))
        {
            $this->widget = false;

            if ($this->settings['widget'])
            {
                $type_info = \SM_Elementor\Module\Query\Module::i()->get_filter_widget_type($this->settings['widget']);

                if ($type_info && class_exists($type_info['class']))
                {
                    $this->widget = new $type_info['class']($this, $this->settings);
                }
            }
        }

        return $this->widget;
    }

    function render()
    {
        if ($this->validate() && ($widget = $this->get_widget()))
        {
            return $widget->render();
        }
    }
}