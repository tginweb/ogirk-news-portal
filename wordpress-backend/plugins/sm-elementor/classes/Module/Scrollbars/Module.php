<?php


namespace SM_Elementor\Module\Scrollbars;

use Elementor\Controls_Stack;
use Elementor\Controls_Manager;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    function init_events()
    {
        $this->add_action('elementor/element/after_section_end', null, 10, 3);

        $this->add_action('elementor/frontend/before_render');
    }

    function _action_elementor_element_after_section_end(Controls_Stack $element, $section_id, $args)
    {
        if ($args['tab']=='advanced' && in_array($section_id, ['_section_style', 'section_advanced']))
        {
            $element->start_controls_section(
                'section_sm_scrollbars',
                [
                    'label' => 'Scrollbars',
                    'tab' => Controls_Manager::TAB_ADVANCED,
                ]
            );

            $element->add_control(
                'sm_scrollbars',
                [
                    'label' => __('Scrollbars enable'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $element->add_control(
                'sm_scrollbars_height_source',
                [
                    'label' => __('Height source'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'column_closest' => 'Column closest',
                        'column_root' => 'Column root',
                        'parent' => 'Parent',
                        'custom' => 'Custom',
                    ],
                    'condition' => ['sm_scrollbars' => 'yes'],
                ]
            );

            $element->add_control(
                'sm_scrollbars_height',
                [
                    'label' => __('Max height'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => 0, 'max' => 100,],
                        'px' => ['min' => 0, 'max' => 1000,],
                        'vw' => ['min' => 0, 'max' => 100,],
                    ],
                    'condition' => [
                        'sm_scrollbars' => 'yes',
                        'sm_scrollbars_height_source' => 'custom',
                    ],
                    'selectors' => [
                        '{{WRAPPER}}' => 'max-height: {{SIZE}}{{UNIT}} !important; overflow:hidden;',
                    ],
                ]
            );

            $element->end_controls_section();
        }
    }

    function _action_elementor_frontend_before_render($widget)
    {
        $settings = $widget->get_settings_for_display();

        if ($settings['sm_scrollbars']=='yes')
        {

            $widget->add_render_attribute('_wrapper', 'data-boot', '', true);

            $widget->add_render_attribute('_wrapper', 'data-sm-elementor-scrollbars', json_encode(\SM\Util\Base::sub_params($settings, 'sm_scrollbars_')), true);
        }
    }

}



