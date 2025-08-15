<?php


namespace SM_Elementor\Module\Dropdown;

use Elementor\Controls_Stack;
use ElementorPro\Plugin;
use Elementor\Controls_Manager;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    function init_events()
    {
        $this->add_action('elementor/element/after_section_end', null, 10, 3);

        $this->add_filter('elementor/widget/render_content', null, 10, 2);
    }

    function _action_elementor_element_after_section_end(Controls_Stack $element, $section_id, $args)
    {
        if ($args['tab']=='advanced' && in_array($section_id, ['_section_style', 'section_advanced']))
        {
            $element->start_controls_section(
                'section_sm_dropdown',
                [
                    'label' => __('Dropdown content'),
                    'tab' => Controls_Manager::TAB_ADVANCED,
                ]
            );

            $element->add_control(
                'sm_dropdown',
                [
                    'label' => __('Inner content'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $element->add_control(
                'sm_dropdown_source',
                [
                    'label' => __('Source'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'selector' => 'Selector',
                        'template' => 'Template'
                    ],
                    'default' => 'template'
                ]
            );



            $element->add_control(
                'sm_dropdown_selector',
                [
                    'label' => __( 'Selector', 'elementor-pro' ),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => 'true',
                    'condition' => ['sm_dropdown' => 'yes', 'sm_dropdown_source'=>'selector'],
                ]
            );

            $templates_options = $this->get_templates_options();


            $templates_options = [
                '0' => '— ' . __( 'Select', 'elementor-pro' ) . ' —',
            ] + $templates_options;


            $element->add_control(
                'sm_dropdown_template_id',
                [
                    'label' => __( 'Choose Template', 'elementor-pro' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => '0',
                    'options' => $templates_options,
                    'label_block' => 'true',
                    'condition' => ['sm_dropdown' => 'yes', 'sm_dropdown_source'=>'template'],
                ]
            );

            $element->add_control(
                'sm_dropdown_template_params',
                [
                    'label' => __( 'Params', 'elementor-pro' ),
                    'type' => Controls_Manager::TEXTAREA,
                    'default' => '',
                    'label_block' => 'true',
                    'condition' => ['sm_dropdown' => 'yes', 'sm_dropdown_source'=>'template'],
                ]
            );

            $element->add_control(
                'sm_dropdown_direction',
                [
                    'label' => __('Direction'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => '',
                        'down-left' => 'Down Left',
                        'down-right' => 'Down Right',
                        'up-left' => 'Up Left',
                        'up-right' => 'Up Right',
                    ],
                    'condition' => ['sm_dropdown' => 'yes'],
                    'selectors' => [
                        ///'{{WRAPPER}} .sm-inner-content' => 'position: {{VALUE}};',
                    ],
                ]
            );

            $element->end_controls_section();
        }
    }

    function _filter_elementor_widget_render_content1($content, \Elementor\Widget_Base $widget)
    {
        $settings = $widget->get_settings_for_display();

        if ($settings['sm_dropdown']=='yes')
        {
            $engine = \SM_Elementor\Module\Framework\Module::i()->get_engine();

            if ($settings['sm_dropdown_template_id'])
            {
                $dropdown_content = Plugin::elementor()->frontend->get_builder_content_for_display( $settings['sm_dropdown_template_id'] );
            }

            $content = $engine->dropdown_wrap([
                'trigger_content'   => $content,
                'trigger_attrs'     => $widget->get_render_attributes('dropdown_trigger'),
                'dropdown_contnet'  => $dropdown_content,
                'dropdown_attrs'    => $widget->get_render_attributes('dropdown_content')
            ], $settings, 'sm_dropdown');
        }

        return $content;
    }

    function _filter_elementor_widget_render_content($content, \Elementor\Widget_Base $widget)
    {

        $settings = $widget->get_settings_for_display();

        if ($settings['sm_dropdown']=='yes')
        {
            $widget->add_render_attribute( 'dropdown_content_wrapper', 'class', 'sm-dropdown-content-wrapper' );

            $widget->add_render_attribute( 'dropdown_content_wrapper', 'data-boot', 1, true);

            $widget->add_render_attribute( 'dropdown_content_wrapper', 'data-sm-elementor-dropdown', json_encode(\SM\Util\Base::sub_params($settings, 'sm_dropdown_')), true);

            $widget->add_render_attribute( 'dropdown_content', 'class', 'sm-dropdown-content' );

            $schema = [
                'down-left'  => ['wrapper_class'=>'dropdown'],
                'down-right' => ['wrapper_class'=>'dropdown', 'content_class'=>'dropdown-menu-right'],
                'up-left'    => ['wrapper_class'=>'dropdown'],
                'up-right'   => ['wrapper_class'=>'dropdown'],
            ];

            $dir = $settings['sm_dropdown_direction'] ?: 'down-left';

            if (!isset($schema[$dir]))
                $dir = 'down-left';


            $schema_item = $schema[$dir];


            $widget->add_render_attribute( 'dropdown_content_wrapper', 'class', $schema_item['wrapper_class'] );
            $widget->add_render_attribute( 'dropdown_content', 'class', 'dropdown-menu' );
            $widget->add_render_attribute( 'dropdown_trigger', 'data-toggle', 'dropdown');

            if ($schema_item['content_class'])
                $widget->add_render_attribute( 'dropdown_content', 'class', $schema_item['content_class'] );


            if ($settings['sm_dropdown_template_id']) {

                $dropdown_content = Plugin::elementor()->frontend->get_builder_content_for_display( $settings['sm_dropdown_template_id'] );
            }


            $trigger_content = sprintf( '<div %1$s>%2$s</div>', $widget->get_render_attribute_string( 'dropdown_trigger' ), $content );
            $dropdown_content = sprintf( '<div %1$s>%2$s</div>', $widget->get_render_attribute_string( 'dropdown_content' ), $dropdown_content );

            $content =   sprintf( '<div %1$s>%2$s</div>', $widget->get_render_attribute_string( 'dropdown_content_wrapper' ), $trigger_content.$dropdown_content );
        }

        return $content;
    }
}



