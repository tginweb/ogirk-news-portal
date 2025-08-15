<?php

namespace SM_Elementor\Module\DynamicTags\Tag;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use ElementorPro\Modules\DynamicTags\ACF\Module;
use ElementorPro\Plugin;
use SM_Elementor\Common;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;



if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Evaluate extends Tag {

    public function get_name() {
        return 'sm-evaluate';
    }

    public function get_title() {
        return __( 'SM: Evaluate', 'elementor-pro' );
    }

    public function get_group() {
        return 'site';
    }

    public function get_categories() {
        return [
            Module::TEXT_CATEGORY,
        ];
    }

    protected function _register_controls() {


        $this->add_control(
            'controller_type',
            [
                'label' => __( 'Controller', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'document' => 'Document',
                    'form' => 'Parent Form tag',
                    'column' => 'Parent Column',
                    'row' => 'Parent Row',
                    'selector' => 'Custom selector',
                ],
                'default' => 'form'
            ]
        );

        $this->add_control(
            'controller_selector',
            [
                'label' => __( 'Controller selector', 'elementor-pro' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'controller_type' => 'selector',
                ],
            ]
        );

        $this->add_control(
            'controller_event_type',
            [
                'label' => __( 'Controller event', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'inputs_update' => 'Inputs update',
                    'inputs_change' => 'Inputs change',
                    'on' => 'On event',
                ],
                'default' => 'input',
            ]
        );

        $this->add_control(
            'controller_event_on',
            [
                'label' => __( 'Controller event ON', 'elementor-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'controller_event_type' => 'on',
                ],
            ]
        );

        $this->add_control(
            'controller_run',
            [
                'label' => __( 'Controller run', 'elementor-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [

                ],
            ]
        );


        $this->add_control(
            'value_type',
            [
                'label' => __( 'Format', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'js' => 'JS',
                ],
                'default' => 'js',
                'condition' => [

                ],
            ]
        );

        $this->add_control(
            'value_js',
            [
                'label' => __( 'Value JS', 'elementor-pro' ),
                'label_block' => true,
                'type' => Controls_Manager::TEXTAREA,
                'condition' => [
                    'value_type' => 'js',
                ],
            ]
        );


        $this->add_control(
            'value_placeholder',
            [
                'label' => __( 'Placeholder', 'elementor-pro' ),
                'type' => Controls_Manager::TEXTAREA,
                'condition' => [

                ],
            ]
        );

    }

    public function render()
    {
        $settings = $this->get_settings();

        $data_settings = [
            'controller_type'       => $settings['controller_type'],
            'controller_selector'   => $settings['controller_selector'],
            'controller_event_type' => $settings['controller_event_type'],
            'controller_event_on'   => $settings['controller_event_on'],
            'value_type'            => $settings['value_type'],
            'value_js'              => $settings['value_js'],
            'value_placeholder'     => $settings['value_placeholder'],
        ];

        $attrs = [
            'data-boot' => [],
            'data-sm-elementor-trigger-value' => $data_settings,
        ];

        $placeholder = $settings['value_placeholder'];

        $value = '<span '.\SM\Util\Html::attributes($attrs).'>'.$placeholder.'</span>';

        echo  $value ;
    }
}
