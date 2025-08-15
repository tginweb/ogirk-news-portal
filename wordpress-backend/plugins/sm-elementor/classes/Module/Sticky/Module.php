<?php


namespace SM_Elementor\Module\Sticky;

use SM\Common;
use SM\Context;
use Elementor\Controls_Stack;
use Elementor\Controls_Manager;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    public $disabled_elements = [];

    function init_events()
    {
        $this->add_action('elementor/element/after_section_end', null, 10, 3);
        $this->add_action('elementor/frontend/before_render');

    }

    function _action_elementor_element_after_section_end(Controls_Stack $element, $section_id, $args)
    {
        $el_type = $element->get_name();

        if ($args['tab']=='advanced' && in_array($section_id, ['_section_style', 'section_advanced']))
        {
            $element->start_controls_section(
                'section_sm_sticky',
                [
                    'label' => __( 'SM: Sticky'),
                    'tab' => Controls_Manager::TAB_ADVANCED,
                ]
            );

            $element->add_control(
                'sm_sticky',
                [
                    'label' => 'Enable',
                    'type' => Controls_Manager::SWITCHER,
                ]
            );


            $element->add_control(
                'sm_sticky_stick_to',
                [
                    'label' => 'Stick to',
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        ''  => '',
                        'parent'          => 'Parent',
                        'column_closest'  => 'Column closest',
                        'column_root'     => 'Column root',
                        'row_closest'     => 'Row closest',
                        'custom_selector' => 'Custom selector',
                        'widget_closest'  => 'Widget closest'
                    ]
                ]
            );

            $element->add_control(
                'sm_sticky_stick_to_selector',
                [
                    'label' => 'Stick to selector',
                    'type' => Controls_Manager::TEXT,
                    'condition' => [
                        'sm_sticky_stick_to' => 'custom_selector'
                    ]
                ]
            );


            $element->add_control(
                'sm_sticky_inner_sticker',
                [
                    'label' => 'Inner sticker',
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => '',
                        'parent'          => 'Parent',
                        'column_closest'  => 'Column closest',
                        'column_root'     => 'Column root',
                        'row_closest'     => 'Row closest',
                        'custom_selector' => 'Custom selector'
                    ]
                ]
            );

            $element->add_control(
                'sm_sticky_inner_sticker_selector',
                [
                    'label' => 'Inner sticker selector',
                    'type' => Controls_Manager::TEXT,
                    'condition' => [
                        'sm_sticky_inner_sticker' => 'custom_selector'
                    ]
                ]
            );

            $element->add_control(
                'sm_sticky_follow_scroll',
                [
                    'label' => 'Follow scroll',
                    'type' => Controls_Manager::SWITCHER,
                ]
            );


            $element->add_control(
                'sm_sticky_show_on_stick',
                [
                    'label' => 'Show on stick',
                    'type' => Controls_Manager::SWITCHER,
                ]
            );


            $element->end_controls_section();
        }

    }

    function _action_elementor_frontend_before_render($widget)
    {
        $settings = $widget->get_settings_for_display();

        if ($settings['sm_sticky']==='yes')
        {
	        if (!empty($_REQUEST['demo_ad_zone'])) return;

            $data_options = \SM\Util\Base::sub_params($settings, 'sm_sticky_');

            $widget->add_render_attribute('_wrapper', 'data-boot', '', true);
            $widget->add_render_attribute('_wrapper', 'data-sm-elementor-sticky', json_encode($data_options), true);

            if ($settings['sm_sticky_show_on_stick'] === 'yes')
            {
                $widget->add_render_attribute('_wrapper', 'class', ['sm-elementor-sticky-show-on-stick']);
            }
        }
    }

}



