<?php


namespace SM_Elementor\Module\ElementStyle;

use Elementor\Controls_Stack;
use Elementor\Controls_Manager;


class Module extends \SM_Elementor\Common\Plugin_Module
{
    var $controls_added_types = [];

    function init_events()
    {
        parent::init_events();

        $this->add_action('elementor/element/after_section_end', null, 10, 3);

    }


    function _action_elementor_controls_controls_registered($manager)
    {

        $manager->add_group_control('typography', new Control\Typography());
    }

    function _action_elementor_element_after_section_end(Controls_Stack $element, $section_id, $args)
    {

        $el_type = $element->get_name();

        $path = join('.', [$el_type, $args['tab'], $section_id]);

        if ($element->get_type()=='widget' && $args['tab']=='content' && empty($this->controls_added_types[$el_type]))
        {
            $this->controls_added_types[$el_type] = true;

            $element->start_controls_section(
                'section_sm_content_style',
                [
                    'label' => __('Стиль контента'),
                    'tab' => Controls_Manager::TAB_SETTINGS,
                ]
            );

            $element->add_control(
                'sm_content_style_width',
                [
                    'label' => __('Ширина'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'default',
                    'options' => [
                        'default'   => 'По умолчанию',
                        'wide'      => 'Широкий',
                        'wider'     => 'Более широкий',
                        'fullwidth' => 'Вся ширина',
                    ],
                    'prefix_class' => 'elementor-sm-content-style-width-',
                ]
            );

            $element->end_controls_section();

        }

        if ($args['tab']=='advanced' && in_array($section_id, ['_section_style', 'section_advanced']))
        {


            $element->start_controls_section(
                'section_sm_element_styling',
                [
                    'label' => __( 'Additional styles', 'elementor-pro' ),
                    'tab' => Controls_Manager::TAB_ADVANCED,
                ]
            );

            $element->add_control(
                'sm_color_style',
                [
                    'label' => __('Стиль'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => '',
                        'dark' => __( 'Dark'),
                        'gray' => __( 'Gray'),
                    ],
                    'prefix_class' => 'sm-elementor-color-class-',
                ]
            );

            $element->add_responsive_control(
                'sm_text_align',
                [
                    'label' => __( 'Text Align', 'elementor' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __( 'Left', 'elementor' ),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __( 'Center', 'elementor' ),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __( 'Right', 'elementor' ),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} ' => 'text-align: {{VALUE}};',
                    ],
                ]
            );


            $element->add_control(
                'sm_display_none_class',
                [
                    'label' => __( 'Display none class' ),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                    'return_value' => 'sm-display-none',
                    'prefix_class' => 'elementor-',
                ]
            );

            $element->add_responsive_control(
                'sm_display',
                [
                    'label' => __( 'Display'),
                    'type' => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => [
                        '' => '',
                        'none' => 'None',
                        'block' => 'Block',
                        'inline-block' => 'Inline block'
                    ],
                    'prefix_class' => 'elementor-sm-display-',
                    'selectors' => [
                        '{{WRAPPER}}' => 'display:{{VALUE}};',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_position',
                [
                    'label' => __( 'Position'),
                    'type' => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => [
                        '' => '',
                        'static' => 'Static',
                        'absolute' => 'Absolute',
                        'relative' => 'Relative',
                    ],
                    'selectors' => [
                        '{{WRAPPER}}' => 'position:{{VALUE}};',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_absolute_top',
                [
                    'label' => __( 'Absolute top'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => -100, 'max' => 100,],
                        'px' => ['min' => -1000, 'max' => 1000,],
                        'vw' => ['min' => -100, 'max' => 100,],
                    ],
                    'condition' => ['sm_position' => 'absolute'],
                    'selectors' => [
                        '{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}} !important;',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_absolute_bottom',
                [
                    'label' => __( 'Absolute bottom'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => -100, 'max' => 100,],
                        'px' => ['min' => -1000, 'max' => 1000,],
                        'vw' => ['min' => -100, 'max' => 100,],
                    ],
                    'condition' => ['sm_position' => 'absolute'],
                    'selectors' => [
                        '{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}} !important;',
                    ],
                ]
            );


            $element->add_responsive_control(
                'sm_absolute_left',
                [
                    'label' => __( 'Absolute left'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => -100, 'max' => 100,],
                        'px' => ['min' => -1000, 'max' => 1000,],
                        'vw' => ['min' => -100, 'max' => 100,],
                    ],
                    'condition' => ['sm_position' => 'absolute'],
                    'selectors' => [
                        '{{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}} !important;',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_absolute_right',
                [
                    'label' => __( 'Absolute right'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => -100, 'max' => 100,],
                        'px' => ['min' => -1000, 'max' => 1000,],
                        'vw' => ['min' => -100, 'max' => 100,],
                    ],
                    'condition' => ['sm_position' => 'absolute'],
                    'selectors' => [
                        '{{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}} !important;',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_width',
                [
                    'label' => __( 'Width', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => 0, 'max' => 100,],
                        'px' => ['min' => 0, 'max' => 1999,],
                        'vw' => ['min' => 0, 'max' => 100,],
                    ],
                    'selectors' => [
                        '{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_min_width',
                [
                    'label' => __( 'Min Width', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => 0, 'max' => 100,],
                        'px' => ['min' => 0, 'max' => 1999,],
                        'vw' => ['min' => 0, 'max' => 100,],
                    ],
                    'selectors' => [
                        '{{WRAPPER}}' => 'min-width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_max_width',
                [
                    'label' => __( 'Max Width', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => 0, 'max' => 100,],
                        'px' => ['min' => 0, 'max' => 1999,],
                        'vw' => ['min' => 0, 'max' => 100,],
                    ],
                    'selectors' => [
                        '{{WRAPPER}}' => 'max-width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_height',
                [
                    'label' => __( 'Height', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => 0, 'max' => 100,],
                        'px' => ['min' => 0, 'max' => 1999,],
                        'vw' => ['min' => 0, 'max' => 100,],
                    ],
                    'selectors' => [
                        '{{WRAPPER}}' => 'height: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} > .elementor-widget-container' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_min_height',
                [
                    'label' => __( 'Min Height', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => 0, 'max' => 100,],
                        'px' => ['min' => 0, 'max' => 1999,],
                        'vw' => ['min' => 0, 'max' => 100,],
                    ],
                    'selectors' => [
                        '{{WRAPPER}}' => 'min-height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->end_controls_section();
        }


        if ($path == 'section.layout.section_layout')
        {

            $element->start_controls_section(
                'section_sm_section_gaps',
                [
                    'label' => __( 'Gaps'),
                    'tab' => Controls_Manager::TAB_LAYOUT,
                ]
            );

            $element->add_responsive_control(
                'sm_gap_margin_outer',
                [
                    'label' => __( 'Gap Outer Negative'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 250,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100
                        ],
                    ],
                    'size_units' => [ '%', 'px' ],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} > .elementor-container > .elementor-row' => 'width:calc(100% + 2 * {{SIZE}}{{UNIT}}); margin-left:-{{SIZE}}{{UNIT}}; margin-right:-{{SIZE}}{{UNIT}}; padding-top:0;',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_gap_margin_hor',
                [
                    'label' => __( 'Gap Margin Horizontal'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 250,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100
                        ],
                    ],
                    'size_units' => [ '%', 'px' ],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} > .elementor-container > .elementor-row > .elementor-column > .elementor-element-populated' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_gap_margin_vert',
                [
                    'label' => __( 'Gap Margin Vertical'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 400,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100
                        ],
                    ],
                    'size_units' => [ '%', 'px' ],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} > .elementor-container > .elementor-row > .elementor-column > .elementor-element-populated' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_gap_hor',
                [
                    'label' => __( 'Gap Padding Horizontal'),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 250,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100
                        ],
                    ],
                    'size_units' => [ '%', 'px' ],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} > .elementor-container > .elementor-row > .elementor-column > .elementor-element-populated' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->add_responsive_control(
                'sm_gap_vert',
                [
                    'label' => __( 'Gap Padding Vertical'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 400,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100
                        ],
                    ],
                    'size_units' => [ '%', 'px' ],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} > .elementor-container > .elementor-row > .elementor-column > .elementor-element-populated' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $element->end_controls_section();
        }


        /*
        if ($element->get_type()!=='widget' && $args['tab']==='style')
        {
            if (!isset($this->controls_added_types[$el_type]))
            {


            }
        }
        */



     }
}



