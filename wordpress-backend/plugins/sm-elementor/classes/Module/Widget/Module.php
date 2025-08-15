<?php


namespace SM_Elementor\Module\Widget;

use Elementor\Controls_Stack;
use Elementor\Element_Base;
use Elementor\Widget_Base;
use ElementorPro\Plugin;
use Elementor\Controls_Manager;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    var $controls_added_types = [];

    function init_events()
    {
        $this->add_action('elementor/element/after_section_end', null, 10, 3);

        $this->add_filter('elementor/widget/render_content', null, 10, 2);

        $this->add_filter('sm_elementor/widget/support/wrapper/controls', null, 10, 2);

        $this->add_filter('sm_elementor/widget/support/wrapper/output', null, 10, 2);

        $this->add_filter('sm_elementor/widget/support/wrapper/customizer', null, 10, 2);
    }

    function _filter_sm_elementor_widget_support_wrapper_controls($result, $widget_type)
    {
        if (strpos($widget_type, 'sm-')===0 || in_array($widget_type, ['shortcode'])) return true;

        return false;
    }

    function _filter_sm_elementor_widget_support_wrapper_customizer($result, $widget_type)
    {
        if (strpos($widget_type, 'sm-')===0 && !in_array($widget_type, ['sm-query'])) return true;

        return false;
    }

    function _filter_sm_elementor_widget_support_wrapper_output($result, $widget_type)
    {
        if (in_array($widget_type, ['shortcode'])) return true;

        if (strpos($widget_type, 'sm-')===0 && !in_array($widget_type, ['sm-query'])) return true;

        return false;
    }


    function _filter_elementor_widget_render_content($widget_content, Widget_Base $element)
    {
        if ($element->get_type()!='widget') return $widget_content;

        $widget_type = $element->get_name();

        if (!apply_filters('sm_elementor/widget/support/wrapper/output', false, $widget_type)) return $widget_content;

        $widget_content =
            \SM_Elementor\Common\Widget::call_render_widget_header($element) .
            $widget_content .
            \SM_Elementor\Common\Widget::call_render_widget_footer($element);

        return $widget_content;
    }

    function _action_elementor_element_after_section_end(Controls_Stack $element, $section_id, $args)
    {
        $widget_type = $element->get_name();

        if ($element->get_type()!=='widget') return;

        if (!in_array($widget_type, ['shortcode']) && ($args['tab']!=='style')) return;


        if (!apply_filters('sm_elementor/widget/support/wrapper/controls', false, $widget_type)) return;

        if (!isset($this->controls_added_types[$widget_type]))
        {
            $this->controls_added_types[$widget_type] = true;

            \SM_Elementor\Common\Widget::add_widget_header_controls($element);

            \SM_Elementor\Common\Widget::add_widget_footer_controls($element);

            if (apply_filters('sm_elementor/widget/support/wrapper/customizer', false, $widget_type))
            {

                \SM_Elementor\Common\Widget::add_widget_header_customizer_controls($element);

                \SM_Elementor\Common\Widget::add_widget_footer_customizer_controls($element);

            }


        }

    }


}



