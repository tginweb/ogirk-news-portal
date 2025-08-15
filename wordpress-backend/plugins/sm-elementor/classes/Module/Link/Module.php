<?php

namespace SM_Elementor\Module\Link;

use Elementor\Controls_Stack;
use ElementorPro\Plugin;
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
                'section_sm_link',
                [
                    'label' => __('Link'),
                    'tab' => Controls_Manager::TAB_ADVANCED,
                ]
            );

            $element->add_control(
                'sm_link',
                [
                    'label' => __('Ссылка'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $element->add_control(
                'sm_link_url',
                [
                    'label' => __('Url'),
                    'type' => Controls_Manager::URL,
                    'dynamic' => [
                        'active' => true
                    ],
                    'condition' => [
                        'sm_link' => 'yes'
                    ]
                ]
            );

            $element->end_controls_section();
        }
    }


    function _action_elementor_frontend_before_render($widget)
    {
        $settings = $widget->get_settings_for_display();

        if ($settings['sm_link']==='yes')
        {
            $data_options = [
              'link' => $settings['sm_link_url']
            ];

            $widget->add_render_attribute('_wrapper', 'data-boot', '', true);
            $widget->add_render_attribute('_wrapper', 'data-sm-elementor-link', json_encode($data_options), true);
        }
    }
}



