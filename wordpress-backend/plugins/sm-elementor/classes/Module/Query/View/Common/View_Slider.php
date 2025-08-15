<?php

namespace SM_Elementor\Module\Query\View\Common;

use Elementor\Controls_Manager;

abstract class View_Slider extends Base {


    function register_settings_controls()
    {

        $this->add_control(
            'slider_slides',
            [
                'label'     => 'Количество слайдов',
                'type'      => Controls_Manager::NUMBER,
                'default'   => 1
            ],
            true
        );


        $this->add_control(
            'slider_thumbs',
            [
                'label'     => 'Миниатюры слайдов',
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no'
            ],
            true
        );

        $this->add_control(
            'slider_thumbs_slides',
            [
                'label'     => 'Количество миниатюр',
                'type'      => Controls_Manager::NUMBER,
                'default'   => 5,
                'condition' => [
                    'slider_thumbs' => 'yes'
                ]
            ],
            true
        );
        
    }

    function register_advanced_settings_controls()
    {
        $this->add_control(
            'slider_direction',
            [
                'label'     => 'Direction',
                'type'      => Controls_Manager::SELECT,
                'default'   => 'horizontal',
                'options'   => [
                    'horizontal' => 'Horizontal',
                    'vertical' => 'Vertical',
                ],
            ],
            true
        );

        $this->add_control(
            'slider_height',
            [
                'label'     => 'Height',
                'type'      => Controls_Manager::NUMBER,
            ],
            true
        );

        $this->add_control(
            'slider_pagination',
            [
                'label'     => 'Pagination',
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes'
            ],
            true
        );

        $this->add_control(
            'slider_navigation',
            [
                'label'     => 'Navigation',
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes'
            ],
            true
        );

        $this->add_control(
            'slider_auto_height',
            [
                'label'     => 'Auto Height',
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no'
            ],
            true
        );

        $this->add_control(
            'slider_slides_space',
            [
                'label'     => 'Slides space',
                'type'      => Controls_Manager::NUMBER,
                'default'   => 1
            ],
            true
        );

        $this->add_control(
            'slider_thumbs_slides_space',
            [
                'label'     => 'Thumbs Space',
                'type'      => Controls_Manager::NUMBER,
                'default'   => 1,
                'condition' => [
                    'slider_thumbs' => 'yes'
                ]
            ],
            true
        );

        $this->add_control(
            'slider_thumbs_direction',
            [
                'label'     => 'Thumbs Direction',
                'type'      => Controls_Manager::SELECT,
                'default'   => 'horizontal',
                'options'   => [
                    'horizontal' => 'Horizontal',
                    'vertical' => 'Vertical',
                ],
                'condition' => [
                    'slider_thumbs' => 'yes'
                ]
            ],
            true
        );

        $this->add_control(
            'slider_thumbs_height',
            [
                'label'     => 'Thumbs Height',
                'type'      => Controls_Manager::NUMBER,
                'condition' => [
                    'slider_thumbs' => 'yes'
                ]
            ],
            true
        );

        $this->add_control(
            'slider_thumbs_auto_height',
            [
                'label'     => 'Thumbs Auto Height',
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no',
                'condition' => [
                    'slider_thumbs' => 'yes'
                ]
            ],
            true
        );


        $responsive_repeater = new \Elementor\Repeater();

        $responsive_repeater->add_control(
            'breakpoint',
            array(
                'label' => 'Breakpoint before',
                'default' => '',
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => '',
                    'desktop' => 'Desktop',
                    'tablet' => 'Tablet',
                    'mobile' => 'Mobile',
                ],
                'default' => '',
            )
        );

        $responsive_repeater->add_control(
            'slides',
            array(
                'label' => 'Slides',
                'type' => Controls_Manager::NUMBER,
            )
        );

        $responsive_repeater->add_control(
            'slides_space',
            array(
                'label' => 'Slides space',
                'type' => Controls_Manager::NUMBER,
            )
        );

        $this->add_control(
            'slider_responsive',
            [
                'label' => 'Responsive',
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'default' => array(),
                'fields' =>  array_values( $responsive_repeater->get_controls() ),
                'title_field' => '{{{ breakpoint }}}',
            ],
            true
        );

    }

    function register_style_controls()
    {
        $this->start_controls_section('slider_section_style', [
            'label' => __('Слайдер'),
            'tab' => Controls_Manager::TAB_STYLE
        ]);

        $this->add_control(
            'slider_pagination_outside',
            [
                'label'     => 'Пагинация снаружи',
                'type'      => Controls_Manager::SWITCHER,
            ],
            true
        );

        $this->add_control(
            'slider_navigation_outside',
            [
                'label'     => 'Навигация снаружи',
                'type'      => Controls_Manager::SWITCHER,
            ],
            true
        );

        $this->add_control(
            'slider_pagination_bullet_size',
            [
                'label' => __( 'Pagination bullet size'),
                'type' => Controls_Manager::NUMBER,
                'selectors' => [
                    '{{WRAPPER}} .q-slider-pagination .swiper-pagination-bullet' => 'width:{{VALUE}}px;height:{{VALUE}}px;'
                ],
            ],
            true
        );

        $this->add_control(
            'slider_pagination_bullet_color',
            [
                'label' => __( 'Pagination bullet color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .q-slider-pagination .swiper-pagination-bullet' => 'background:{{VALUE}};'
                ],
            ],
            true
        );

        $this->add_control(
            'slider_pagination_bullet_active_color',
            [
                'label' => __( 'Pagination bullet active color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .q-slider-pagination .swiper-pagination-bullet-active' => 'background:{{VALUE}};'
                ],
            ],
            true
        );


        $this->add_control(
            'slider_navigation_arrow_offset',
            [
                'label' => __( 'Navigation arrow offset'),
                'type' => Controls_Manager::NUMBER,
                'selectors' => [
                    '{{WRAPPER}} .swiper-button-prev' => 'left:{{VALUE}}px;',
                    '{{WRAPPER}} .swiper-button-next' => 'right:{{VALUE}}px;'
                ],
            ],
            true
        );

        $this->add_control(
            'slider_pagination_arrow_size',
            [
                'label' => __( 'Navigation arrow size'),
                'type' => Controls_Manager::NUMBER,
                'selectors' => [
                    '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'font-size:{{VALUE}}px;margin-top:calc(-{{VALUE}}px/2);',
                ],
            ],
            true
        );

        $this->end_controls_section();
    }
}