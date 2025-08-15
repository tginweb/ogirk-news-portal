<?php

namespace SM_Elementor\Module\Query\Filter_Widget\Common;

use SM_Elementor;
use SM_Elementor\Module\QueryControls\Group_Control\Entity_Query;


abstract class Base
{
    var $settings;
    var $controller;

    function __construct($controller, $settings)
    {
        $this->controller = $controller;
        $this->settings = $settings;

        $this->settings += [
            'container_attrs'  => [],
            'label_attrs'      => [],
            'content_attrs'    => [],
            'control_attrs'    => [],
        ];
    }

    function get_type_id()
    {
        return static::$type_id;
    }

    function render()
    {
        $output = '';

        $type_id = $this->get_type_id();

        $this->settings['container_attrs']['data-filter-name'] = $this->controller->name;

        $this->settings['container_attrs']['class'][] = 'filter-container';
        $this->settings['container_attrs']['class'][] = 'filter-container-'.$type_id;

        $this->settings['control_attrs']['class'][] = 'filter-control';
        $this->settings['control_attrs']['class'][] = 'filter-control-'.$type_id;

        $output .= '<div '.\SM\Util\Html::attributes($this->settings['container_attrs']).'>';

        $output .= $this->render_label();

        $output .= $this->render_content();

        $output .= '</div>';

        return $output;
    }

    function render_label()
    {
        if ($this->settings['label'])
        {
            $this->settings['label_attrs']['class'][]   = 'filter-label';

            return '<div '.\SM\Util\Html::attributes($this->settings['label_attrs']).'>'.$this->settings['label'].'</div>';
        }
    }

    function render_content()
    {
        $this->settings['content_attrs']['class'][] = 'filter-content';

        $output = '';

        $output .= '<div '.\SM\Util\Html::attributes($this->settings['content_attrs']).'>';

        $output .= $this->render_control();

        $output .= '</div>';

        return $output;
    }

    function render_control()
    {

    }
}