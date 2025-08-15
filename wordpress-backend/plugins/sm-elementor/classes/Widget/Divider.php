<?php


namespace SM_Elementor\Widget;

use Elementor;
use SM_Elementor\Common;

class Divider extends Common\Widget {


    public function get_name() {
        return 'sm-divider';
    }


    public function get_title() {
        return __( 'SM Divider', 'elementor' );
    }


    protected function _register_controls() {

        $this->start_controls_section(
            'section_divider',
            [
                'label' => __( 'Divider', 'elementor' ),
            ]
        );


        $this->add_control(
            'style',
            [
                'label' => __( 'Style', 'elementor' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => [
                    'solid' => __( 'Solid', 'elementor' ),
                    'double' => __( 'Double', 'elementor' ),
                    'dotted' => __( 'Dotted', 'elementor' ),
                    'dashed' => __( 'Dashed', 'elementor' ),
                ],
                'default' => 'solid',
                'selectors' => [
                    '{{WRAPPER}} .elementor-divider-splitted-side' => 'border-top-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'weight',
            [
                'label' => __( 'Weight', 'elementor' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-divider-splitted-side' => 'border-top-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'color',
            [
                'label' => __( 'Color', 'elementor' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'default' => '',
                'scheme' => [
                    'type' => Elementor\Scheme_Color::get_type(),
                    'value' => Elementor\Scheme_Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-divider-splitted-side' => 'border-top-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'side_width',
            [
                'label' => __( 'Side Width', 'elementor' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px' ],
                'range' => [
                    'px' => [
                        'max' => 1000,
                    ],
                    '%' => [
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 40,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-divider-splitted-side' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'splitter_width',
            [
                'label' => __( 'Splitter Width', 'elementor' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px' ],
                'range' => [
                    'px' => [
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-divider-splitted-split' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'graphic_element',
            [
                'label' => __( 'Graphic Element', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'image' => [
                        'title' => __( 'Image', 'elementor-pro' ),
                        'icon' => 'fa fa-picture-o',
                    ],
                    'icon' => [
                        'title' => __( 'Icon', 'elementor-pro' ),
                        'icon' => 'fa fa-star',
                    ],
                    'heading' => [
                        'title' => __( 'Heading', 'elementor-pro' ),
                        'icon' => 'fa fa-star',
                    ],
                ],
                'default' => 'icon',
            ]
        );

        $this->add_control(
            'heading_title',
            [
                'label' => __( 'Heading Title', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'placeholder' => __( 'Enter your title', 'elementor' ),
                'default' => __( 'Add Your Heading Text Here', 'elementor' ),
                'condition' => [
                    'graphic_element' => 'heading',
                ],
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => __( 'Choose Image', 'elementor-pro' ),
                'type' => Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
                'default' => 'large',
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'icon',
            [
                'label' => __( 'Icon', 'elementor' ),
                'type' => Elementor\Controls_Manager::ICON,
                'label_block' => true,
                'default' => 'fa fa-star',
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );


        $this->add_responsive_control(
            'align',
            [
                'label' => __( 'Alignment', 'elementor' ),
                'type' => Elementor\Controls_Manager::CHOOSE,
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
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-divider' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => __( 'Gap', 'elementor' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 2,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-divider-splitted' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'view',
            [
                'label' => __( 'View', 'elementor' ),
                'type' => Elementor\Controls_Manager::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->end_controls_section();



        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => __( 'Icon', 'elementor' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_view',
            [
                'label' => __( 'Icon View', 'elementor' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => [
                    'default' => __( 'Default', 'elementor' ),
                    'stacked' => __( 'Stacked', 'elementor' ),
                    'framed' => __( 'Framed', 'elementor' ),
                ],
                'default' => 'default',
                'prefix_class' => 'elementor-view-',
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_shape',
            [
                'label' => __( 'Icon Shape', 'elementor' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => [
                    'circle' => __( 'Circle', 'elementor' ),
                    'square' => __( 'Square', 'elementor' ),
                ],
                'default' => 'circle',
                'condition' => [
                    'view!' => 'default',
                ],
                'prefix_class' => 'elementor-shape-',
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_primary_color',
            [
                'label' => __( 'Primary Color', 'elementor' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Elementor\Scheme_Color::get_type(),
                    'value' => Elementor\Scheme_Color::COLOR_1,
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_secondary_color',
            [
                'label' => __( 'Secondary Color', 'elementor' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'icon_view!' => 'default',
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => __( 'Size', 'elementor' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_padding',
            [
                'label' => __( 'Padding', 'elementor' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'condition' => [
                    'icon_view!' => 'default',
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_rotate',
            [
                'label' => __( 'Rotate', 'elementor' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_border_width',
            [
                'label' => __( 'Border Width', 'elementor' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'icon_view' => 'framed',
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'icon_border_radius',
            [
                'label' => __( 'Border Radius', 'elementor' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'icon_view!' => 'default',
                    'graphic_element' => 'icon',
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_image',
            [
                'label' => __( 'Image', 'elementor' ),
                'tab'   => Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_width',
            [
                'label' => __( 'Width', 'elementor' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%', 'px', 'vw' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_space',
            [
                'label' => __( 'Max Width', 'elementor' ) . ' (%)',
                'type' => Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label' => __( 'Opacity', 'elementor' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );



        $this->add_group_control(
            Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .elementor-image img',
                'separator' => 'before',
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __( 'Border Radius', 'elementor' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .elementor-image img',
                'condition' => [
                    'graphic_element' => 'image',
                ],
            ]
        );


        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_heading',
            [
                'label' => __( 'Heading', 'elementor' ),
                'tab'   => Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'graphic_element' => 'heading',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'scheme' => Elementor\Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-heading-title',
            ]
        );


        $this->end_controls_section();
    }

    /**
     * Render divider widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();


        $this->add_render_attribute( 'graphic-wrapper', 'class', 'elementor-divider-splitted-graphic' );

        if ( ! empty( $settings['hover_animation'] ) ) {
            $this->add_render_attribute( 'graphic-wrapper', 'class', 'elementor-animation-' . $settings['hover_animation'] );
        }

        if ($settings['graphic_element']=='icon')
        {
            $this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-icon' );

            if ( ! empty( $settings['icon'] ) ) {
                $this->add_render_attribute( 'icon', 'class', $settings['icon'] );
                $this->add_render_attribute( 'icon', 'aria-hidden', 'true' );
            }
        }
        else if ($settings['graphic_element']=='image')
        {
            $this->add_render_attribute( 'image-wrapper', 'class', 'elementor-image' );

            if ( ! empty( $settings['image_shape'] ) ) {
                $this->add_render_attribute( 'image-wrapper', 'class', 'elementor-image-shape-' . $settings['shape'] );
            }
        }
        else if ($settings['graphic_element']=='heading')
        {
            $this->add_render_attribute( 'heading-wrapper', 'class', 'elementor-heading-title' );

        }

        ?>

        <div class="elementor-divider-splitted">
            <div class="elementor-divider-splitted-side"></div>
            <div class="elementor-divider-splitted-split">

                <div <?php echo $this->get_render_attribute_string( 'graphic-wrapper' ); ?>>

                    <?php if ($settings['graphic_element']=='icon') { ?>

                        <div <?php echo $this->get_render_attribute_string( 'icon-wrapper' ); ?>>
                            <i <?php echo $this->get_render_attribute_string( 'icon' ); ?>></i>
                        </div>

                    <?php } elseif ($settings['graphic_element']=='image') { ?>

                        <div <?php echo $this->get_render_attribute_string( 'image-wrapper' ); ?>>
                            <?php echo Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings ); ?>
                        </div>

                    <?php } elseif ($settings['graphic_element']=='heading') { ?>

                        <div class="elementor-heading-wrapper">

                            <div <?php echo $this->get_render_attribute_string( 'heading-wrapper' ); ?>>
                                <?php echo $settings['heading_title']; ?>
                            </div>

                        </div>

                    <?php } ?>

                </div>


            </div>
            <div class="elementor-divider-splitted-side"></div>
        </div>

        <?php
    }


}
