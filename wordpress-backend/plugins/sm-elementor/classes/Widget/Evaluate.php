<?php


namespace SM_Elementor\Widget;

use SM_Elementor\Common;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

class Evaluate extends Common\Widget {

    public function get_name() {
        return 'sm-evaluate';
    }

    public function get_title() {
        return __( 'SM: Evaluate', 'elementor' );
    }

    public function get_icon() {
        return 'eicon-type-tool';
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_controller',
            [
                'label' => __( 'Контроллер', 'elementor' ),
            ]
        );

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

        $this->end_controls_section();


        $this->start_controls_section(
            'section_value',
            [
                'label' => __( 'Значение', 'elementor' ),
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

        $this->end_controls_section();


        $this->start_controls_section(
            'section_title',
            [
                'label' => __( 'Формат', 'elementor' ),
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __( 'Link', 'elementor' ),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __( 'Alignment', 'elementor' ),
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
                    'justify' => [
                        'title' => __( 'Justified', 'elementor' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'value_size',
            [
                'label' => __( 'Size', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __( 'Default', 'elementor' ),
                    'small' => __( 'Small', 'elementor' ),
                    'medium' => __( 'Medium', 'elementor' ),
                    'large' => __( 'Large', 'elementor' ),
                    'xl' => __( 'XL', 'elementor' ),
                    'xxl' => __( 'XXL', 'elementor' ),
                ],
            ]
        );

        $this->add_control(
            'value_tag',
            [
                'label' => __( 'HTML Tag', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h2',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_value_style',
            [
                'label' => __( 'Value', 'elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'value_color',
            [
                'label' => __( 'Text Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}}.elementor-widget-sm-evaluate .c-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'value_typography',
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .c-value',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'value_text_shadow',
                'selector' => '{{WRAPPER}} .c-value',
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        $data_settings = [
            'controller_type'       => $settings['controller_type'],
            'controller_selector'   => $settings['controller_selector'],
            'controller_event_type' => $settings['controller_event_type'],
            'controller_event_on'   => $settings['controller_event_on'],
            'value_type'            => $settings['value_type'],
            'value_js'              => $settings['value_js'],
            'value_placeholder'     => $settings['value_placeholder'],
        ];

        $this->add_render_attribute( 'wrapper', [
            'class' => 'c-wrapper',
        ]);

        $this->add_render_attribute( 'caption', [
            'class' => 'c-caption',
        ]);

        $this->add_render_attribute( 'value', [
            'class' => 'c-value',
            'data-boot' => [],
            'data-sm-elementor-trigger-value' => json_encode($data_settings),
        ]);

        if ( ! empty( $settings['value_size'] ) ) {
            $this->add_render_attribute( 'value', 'class', 'elementor-size-' . $settings['value_size'] );
        }

        ?>

        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>

            <? print sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['value_tag'], $this->get_render_attribute_string( 'value' ), $settings['value_placeholder'] ); ?>

        </div>

        <?
    }

}
