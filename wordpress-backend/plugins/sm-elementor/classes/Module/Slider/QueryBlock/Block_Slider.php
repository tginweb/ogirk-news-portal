<?php

namespace SM_Elementor\Module\Slider\QueryBlock;

use Elementor\Controls_Manager;

abstract class Block_Slider extends \LivemeshAddons\Blocks\Block {


    function register_block_settings_controls()
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

    function register_block_advanced_settings_controls()
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


    }

}