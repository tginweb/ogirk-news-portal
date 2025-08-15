<?php


namespace SM\Elementor\Widget;

use Elementor;
use Elementor\Controls_Manager;


class FWP_Facets extends \SM\Elementor\Widget {

    public function get_name() {
        return 'sm-fwp-facets';
    }

    public function get_title() {
        return __( 'FWP Facets', 'elementor' );
    }

    public function get_icon() {
        return 'eicon-divider';
    }

    public function get_categories() {
        return [ 'sm_widget' ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_facets',
            [
                'label' => __( 'Facets', 'elementor' ),
            ]
        );

        $facet_options = [];
        $facet_default = [];

        if (FWP()->helper->settings)
        {
            $facets = FWP()->helper->settings['facets'];

            foreach ($facets as $facet)
            {
                $facet_options[$facet['name']] = $facet['label'];

                $facet_default[] = [
                   'name' => $facet['name'],
                   'show' => 'yes',
                ];
            }
        }

        $this->add_control(
            'facet_list',
            [
                'type' => Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'name',
                        'label' => 'Facet',
                        'type' => Elementor\Controls_Manager::SELECT2,
                        'options' => $facet_options
                    ],
                    [
                        'name' => 'show',
                        'label' => 'Visible',
                        'type' => Elementor\Controls_Manager::SWITCHER,
                        'label_on' => __( 'Show', 'elementor-pro' ),
                        'label_off' => __( 'Hide', 'elementor-pro' ),
                        'default' => 'yes',
                    ],
                    [
                        'name' => 'opened',
                        'label' => 'Opened',
                        'type' => Elementor\Controls_Manager::SWITCHER,
                        'default' => 'no',
                    ],
                ],
                'default' => $facet_default,
                'title_field' => '{{{ name }}}',
            ]
        );

        $this->add_control(
            'dropdown',
            [
                'label' => 'Breakpoint',
                'type' => Controls_Manager::SELECT,
                'default' => 'tablet',
                'options' => [
                    'none' => __( 'None', 'elementor-pro' ),
                    'mobile' => __( 'Mobile', 'elementor-pro' ),
                    'tablet' => __( 'Tablet', 'elementor-pro' ),
                ],
                'prefix_class' => 'el-dropdown-'
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_facet_wrapper',
            [
                'label' => __( 'Facet wrapper', 'elementor' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_control(
                'section_style_facet_wrapper_bottom',
                [
                    'label' => __( 'Spacing', 'elementor-pro' ),
                    'type' => Elementor\Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .el-facet-wrapper:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_facet_title',
            [
                'label' => __( 'Facet title', 'elementor' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


            $this->add_control(
                'facet_title_color',
                [
                    'label' => __( 'Color', 'elementor' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .el-facet-title' => 'color: {{VALUE}};',
                    ],
                    'scheme' => [
                        'type' => Elementor\Scheme_Color::get_type(),
                        'value' => Elementor\Scheme_Color::COLOR_1,
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'facet_title_typography',
                    'scheme' => Elementor\Scheme_Typography::TYPOGRAPHY_1,
                    'selector' => '{{WRAPPER}} .el-facet-title',
                ]
            );


        $this->end_controls_section();



        $this->start_controls_section(
            'section_style_facet_filter',
            [
                'label' => __( 'Facet filter', 'elementor' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'facet_filter_color',
            [
                'label' => __( 'Color', 'elementor' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .el-facet-content [data-value]' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Elementor\Scheme_Color::get_type(),
                    'value' => Elementor\Scheme_Color::COLOR_1,
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'facet_filter_typography',
                'scheme' => Elementor\Scheme_Typography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .el-facet-content [data-value]',
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

        if (!function_exists('FWP')) return;

        $settings = $this->get_settings_for_display();

        $id = $this->get_id();

        $this->add_render_attribute( 'toggle', 'class', 'el-toggle collapsed btn btn-secondary btn-block mb-4' );
        $this->add_render_attribute( 'toggle', 'role', 'button' );
        $this->add_render_attribute( 'facet-wrapper', 'class', 'el-facet-wrapper' );

        $editor_mode = $this->is_editor_mode() || in_array($_REQUEST['action'], ['elementor_ajax']);

        if (!$editor_mode)
        {
            $this->add_render_attribute( 'facet-wrapper', 'class', 'hidden' );
        }


        $com_attrs = [
            'id' => 'sm-fwp-facets-' . $id,
            'class' => ['sm-fwp-facets'],
            'data-sm-fwp-facets' => [
                'daa' => 111
            ]
        ];


        $toggle_id = 'el-facets-'.$id;
        ?>

            <div <?=\SM\Util\Html::attributes($com_attrs)?>>

                <div class="el-widget-header">
                    <h3 class="el-widget-title">
                        Поиск по критериям
                    </h3>
                </div>

                <button <?php echo $engine->toogle_trigger_attrs('#'.$toggle_id, $this->get_render_attribute_string( 'toggle' )); ?>>
                    <span class="on-opened">Закрыть фильтр</span>
                    <span class="on-collapsed">Открыть фильтр</span>
                </button>

                <div <?php echo $engine->toogle_content_attrs(['class'=>['el-facets'], 'id'=>$toggle_id]); ?>>

                    <?

                        if (!empty($settings['facet_list']))
                        {
                            foreach ($settings['facet_list'] as $facet_info)
                            {
                                if ($facet_info['show']!=='yes') continue;

                                $facet_name = $facet_info['name'];

                                $fwp_item['facet'] = FWP()->helper->get_facet_by_name( $facet_name );

                                if ($editor_mode)
                                {
                                    $the_facet = $fwp_item['facet'];
                                    $facet_type = $the_facet['type'];

                                    if ( ! isset( FWP()->facet->facet_types[ $facet_type ] ) ) {
                                        continue;
                                    }

                                    // Get facet labels
                                    $output['settings']['labels'][ $facet_name ] = facetwp_i18n( $the_facet['label'] );

                                    $the_facet['operator'] = 'and';

                                    $args = array(
                                        'facet' => $the_facet,
                                        'where_clause' => '',
                                        'selected_values' => $the_facet['selected_values'],
                                    );

                                    // Load facet values if needed
                                    if ( method_exists( FWP()->facet->facet_types[ $facet_type ], 'load_values' ) ) {
                                        $args['values'] = FWP()->facet->facet_types[ $facet_type ]->load_values( $args );
                                    }

                                    // Filter the render args
                                    $args = apply_filters( 'facetwp_facet_render_args', $args );

                                    // Return the number of available choices
                                    if ( isset( $args['values'] ) ) {
                                        $num_choices = 0;
                                        $is_ghost = FWP()->helper->facet_is( $the_facet, 'ghosts', 'yes' );

                                        foreach ( $args['values'] as $choice ) {
                                            if ( isset( $choice['counter'] ) && ( 0 < $choice['counter'] || $is_ghost ) ) {
                                                $num_choices++;
                                            }
                                        }

                                        $output['settings']['num_choices'][ $facet_name ] = $num_choices;
                                    }


                                    // Generate the facet HTML

                                    $fwp_item['content'] = FWP()->facet->facet_types[ $facet_type ]->render( $args );
                                }
                                else
                                {
                                    $fwp_item['content'] = FWP()->display->shortcode([
                                        'facet' => $facet_name,
                                    ]);
                                }

                                $this->add_render_attribute( 'facet-wrapper', 'id', 'facet-wrapper-'.$fwp_item['facet']['name'], true);

                                $facet_toggle_id = 'facet-toggle-'.$fwp_item['facet']['name'];

                                ?>

                                    <div <?php echo $this->get_render_attribute_string( 'facet-wrapper' ); ?>>
                                        <h4 class="el-facet-title elementor-clickable dropdown-toggle dropdown-toggle-left <?=$facet_info['opened']=='yes'?'':' collapsed'?>" data-target="#<?=$facet_toggle_id;?>" data-toggle="collapse" aria-expanded="false"><span><?=$fwp_item['facet']['label'];?></span></h4>
                                        <div id="<?=$facet_toggle_id;?>" class="el-facet-content collapse <?=$facet_info['opened']=='yes'?' show':''?>"><?=$fwp_item['content'];?></div>
                                    </div>

                                <?
                            }
                        }

                    ?>

                </div>

            </div>

        <?php


        if ($editor_mode) {
            ?>

            <link rel="stylesheet"
                  href="<?= plugins_url('/assets/css/front.css', WP_PLUGIN_DIR . '/facetwp/index.php'); ?>"
                  type="text/css" media="all"/>

            <?
        }
    }


}
