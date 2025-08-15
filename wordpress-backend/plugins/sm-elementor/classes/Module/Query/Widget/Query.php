<?php


namespace SM_Elementor\Module\Query\Widget;

use SM_Elementor\Common;
use SM\Assets;
use SM\Util;

use SM_Elementor\Module\Query\View;
use SM_Elementor\Module\Query\Module;
use SM_Elementor\Module\Query\Filters_View;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Scheme_Typography;


class Query extends Common\Widget {


    public function get_name() {
        return 'sm-query';
    }

    public function get_title() {
        return __( 'SM: Query', 'elementor' );
    }


    public function get_script_depends() {


        //$view = $this->get_view_object();

        //if ($view) $view::enqueue_assets();

        if (\Elementor\Plugin::$instance->preview->is_preview_mode())
        {
            foreach ($this->get_query_view_types() as $type_id=>$type_info)
            {
                $type_info['class']::enqueue_assets();
            }
        }

        return [];
    }

    function get_query_filters_view_types()
    {
        return Filters_View\Common\Base::get_object_types();
    }

    function get_query_filters_view_types_options()
    {
        return Filters_View\Common\Base::get_object_types_options();
    }

    function get_query_view_types()
    {
        return View\Common\Base::get_object_types();
    }

    function get_query_view_types_options()
    {
        return View\Common\Base::get_object_types_options();
    }

    function get_query_module_types()
    {
        return Module\Common\Base::get_object_types();
    }

    function get_query_module_types_options()
    {
        return Module\Common\Base::get_object_types_options();
    }


    function get_query_view_all_regions()
    {
        return View\Common\Base::get_all_regions_info();
    }

    protected function _register_controls() {


        $taxonomies_options = Util\Wp::get_taxonomies([], 'names');

        $view_module_regions = $this->get_query_view_all_regions();

        $view_type_objects = View\Common\Base::get_object_types_instances();

        $view_types_support = [
            'regions_grid' => []
        ];

        foreach ($this->get_query_view_types() as $view_type=>$view_type_info)
        {
            if ($view_type_info['support_regions_grid'])
                $view_types_support['regions_grid'][] = $view_type;
        }

        $this->start_controls_section('section_sm_post_query_query', ['label' => __( 'Источник'),]);

            $this->add_group_control('sm-entity-query', ['name' => 'query', 'label' => __( 'Источник')]);

        $this->end_controls_section();


        $this->start_controls_section('section_view_type_settings', ['label' => __('Настроки варианта вида'), 'tab' => Controls_Manager::TAB_SETTINGS,]);

            $this->add_control(
                'query_view',
                [
                    'type' => Controls_Manager::SELECT,
                    'label' => __('Тип блока'),
                    'options' => $this->get_query_view_types_options(),
                    'default' => 'view_list',
                ]
            );

            foreach ($view_type_objects as $type_id => $view_type_object)
            {
                $view_type_object->register_skin($this);
                $view_type_object->register_settings_controls();
            }

        $this->end_controls_section();


        $this->start_controls_section('section_view_type_advanced_settings', ['label' => __('Настроки варианта вида - расширенные'), 'tab' => Controls_Manager::TAB_SETTINGS,]);

            foreach ($view_type_objects as $type_id => $view_type_object)
            {
                $view_type_object->register_skin($this);
                $view_type_object->register_advanced_settings_controls();
            }

        $this->end_controls_section();


        $this->start_controls_section('section_view_common_options', ['label' => __('Настройки вида - общие'), 'tab' => Controls_Manager::TAB_SETTINGS]);

            $this->add_control(
                'view_class', [
                    'type' => Controls_Manager::TEXT,
                    "label" => __("view Class"),
                    'default' => ''
                ]
            );

            $this->add_control(
                'view_id', [
                    'type' => Controls_Manager::TEXT,
                    "label" => __("view Id"),
                    'default' => ''
                ]
            );

            $this->add_control(
                'modules_taxonomies',
                [
                    'type' => Controls_Manager::SELECT2,
                    'label' => __('Таксономия в модулях'),
                    'label_block' => true,
                    'multiple' => true,
                    'options' => $taxonomies_options,
                    'default' => 'category',
                ]
            );

            Common\Customizable::add_template_controls($this, '');

        $this->end_controls_section();



        $this->start_controls_section(
            'section_filters_settings',
            [
                'label' => __('Фильтры'),
                'tab' => Controls_Manager::TAB_SETTINGS,
            ]
        );


        $filters_repeater = new \Elementor\Repeater();

        $filters_repeater->add_control(
            'label',
            array(
                'label' => 'Label',
                'default' => '',
                'type' => Controls_Manager::TEXT,
                'default' => '',
            )
        );

        $filters_repeater->add_control(
            'name',
            array(
                'label' => 'Filter name',
                'default' => '',
                'type' => Controls_Manager::TEXT,
            )
        );

        $filters_repeater->add_control(
            'type',
            array(
                'label' => 'Filter type',
                'default' => 'taxonomy',
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'taxonomy' => 'Taxonomy',
                ],
            )
        );

        /*
        $filters_repeater->add_control(
            'operator',
            array(
                'label' => 'Operator',
                'default' => '',
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'EQUAL' => 'EQUAL',
                    'NOT_EQUAL' => 'NOT EQUAL',
                    'IN' => 'IN',
                    'NOT_IN' => 'NOT IN',
                ]
            )
        );
        */

        $filters_repeater->add_control(
            'widget',
            array(
                'label' => 'Widget',
                'default' => '',
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'select'  => 'select',
                    'links'   => 'links',
                ]
            )
        );


        $filters_repeater->add_control(
            'multiple',
            array(
                'label' => 'Multiple',
                'type' => Controls_Manager::SWITCHER,
                'condition' => [

                ],
            )
        );

        $filters_repeater->add_control(
            '_widget_select_size',
            array(
                'label' => 'Select size',
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'widget' => 'select',
                    'multiple' => 'yes',
                ],
            )
        );

        $filters_repeater->add_control(
            '_widget_links_linkable',
            array(
                'label' => 'Linkable',
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'widget' => 'links',
                ],
            )
        );

        $filters_repeater->add_control(
            '_widget_links_trigger_hover',
            array(
                'label' => 'Trigger hover',
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'widget' => 'links',
                ],
            )
        );

        $filters_repeater->add_control(
            '_type_taxonomy_source_type',
            array(
                'label' => 'Source type',
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'taxonomy' => 'Taxonomy',
                    'terms' => 'Terms',
                ],
                'default' => 'taxonomy',
                'condition' => [
                    'type' => 'taxonomy',
                ],
            )
        );

        $filters_repeater->add_control(
            '_type_taxonomy_source_taxonomy',
            array(
                'label' => 'Taxonomy type',
                'default' => '',
                'type' => Controls_Manager::SELECT,
                'multuple' => true,
                'options' => $taxonomies_options,
                'condition' => [
                    'type' => 'taxonomy',
                    '_type_taxonomy_source_type' => 'taxonomy'
                ],
            )
        );

	    $filters_repeater->add_control(
		    '_type_taxonomy_source_parent',
		    array(
			    'label' => 'Parent term',
			    'type' => 'query',
			    'label_block' => true,
			    'filter_type' => 'taxonomy',
			    'include_type' => true,
			    'multiple' => false,
			    'options' => [],
			    'condition' => [
				    'type' => 'taxonomy',
				    '_type_taxonomy_source_type' => 'taxonomy'
			    ],
		    )
	    );

	    $filters_repeater->add_control(
            '_type_taxonomy_source_terms',
            array(
                'label' => 'Limit terms',
                'type' => 'query',
                'label_block' => true,
                'filter_type' => 'taxonomy',
                'include_type' => true,
                'multiple' => true,
                'options' => [],
                'condition' => [
                    'type' => 'taxonomy',
                    '_type_taxonomy_source_type' => 'terms'
                ],
            )
        );



	    $filters_repeater->add_control(
            '_type_date_min_date',
            array(
                'label' => 'Date min',
                'default' => '',
                'type' => Controls_Manager::DATE_TIME,
                'condition' => [
                    'type' => 'date',
                ],
            )
        );

        $filters_repeater->add_control(
            '_type_date_max_date',
            array(
                'label' => 'Date max',
                'default' => '',
                'type' => Controls_Manager::DATE_TIME,
                'condition' => [
                    'type' => 'date',
                ],
            )
        );

        $filters_repeater->add_responsive_control(
            'columns',
            array(
                'label' => __('Ширина в колонках'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => '',
                    1 => '1/12',
                    2 => '2/12',
                    3 => '3/12',
                    4 => '4/12',
                    5 => '5/12',
                    6 => '6/12',
                    7 => '7/12',
                    8 => '8/12',
                    9 => '9/12',
                    10 => '10/12',
                    11 => '11/12',
                    12 => '12/12',
                ],
            )
        );

        $this->add_control(
            'filters_display',
            [
                'label' => __('Использовать фильтры'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );

        $this->add_control(
            'filters',
            [
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'default' => array(),
                'fields' =>  array_values( $filters_repeater->get_controls() ),
                'title_field' => '{{{ label }}} {{{ type }}}',
                'condition' => [
                    'filters_display' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'filters_view',
            [
                'label' => __('Вид фильтров'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_query_filters_view_types_options(),
                'default' => 'view_default',
                'condition' => [
                    'filters_display' => 'yes',
                ],
            ]
        );

        Common\Customizable::add_template_controls($this, 'filters_', ['filters_display' => 'yes']);

        $this->end_controls_section();


        $this->start_controls_section('section_pagination', ['label' => __('Постраничная навигация'), 'tab' => Controls_Manager::TAB_SETTINGS]);

            $this->add_control(
                'pagination',
                [
                    'type' => Controls_Manager::SELECT,
                    'label' => __('Постраничная навигация'),
                    'options' => array(
                        'none' => __('Нет'),
                        'next_prev' => __('Назад - Впред'),
                        'paged' => __('Постранично'),
                        'load_more' => __('Загрузить еще'),
                    ),
                    'default' => 'none',
                ]
            );

            $this->add_control(
                'pagination_ajax',
                [
                    'type' => Controls_Manager::SWITCHER,
                    'label' => __('Pagination ajax'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'condition' => [
                        'pagination!' => 'none',
                    ],
                ]
            );

            $this->add_control(
                'pagination_seo',
                [
                    'type' => Controls_Manager::SWITCHER,
                    'label' => __('Pagination seo'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'condition' => [
                        'pagination!' => 'none',
                        'pagination_ajax' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'show_remaining',
                [
                    'label' => __('Показать количество?'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'pagination' => 'load_more',
                    ],
                ]
            );


        $this->end_controls_section();


        $this->start_controls_section('section_view_styling', ['label' => __('Вид'), 'tab' => Controls_Manager::TAB_STYLE]);


            $this->add_responsive_control(
                'regions_gap_horizontal',
                [
                    'type' => 'slider',
                    'label' => __('Отступы регионов горизонтальные'),
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => 0, 'max' => 100,],
                        'px' => ['min' => 0, 'max' => 200,],
                        'vw' => ['min' => 0, 'max' => 100,],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .q-regions' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .q-region' => 'padding-left: {{SIZE}}{{UNIT}} !important; padding-right: {{SIZE}}{{UNIT}} !important;',
                    ],
                    'condition' => [

                    ]
                ]
            );

            $this->add_responsive_control(
                'regions_gap_vertical',
                [
                    'type' => 'slider',
                    'label' => __('Отступы регионов вертиальные'),
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => 0, 'max' => 100,],
                        'px' => ['min' => 0, 'max' => 200,],
                        'vw' => ['min' => 0, 'max' => 100,],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .q-regions' => 'margin-top: -{{SIZE}}{{UNIT}}; margin-bottom: -{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .q-region' => 'margin-top: {{SIZE}}{{UNIT}} !important; margin-bottom: {{SIZE}}{{UNIT}} !important;',
                    ],
                    'condition' => [

                    ]
                ]
            );

        $this->end_controls_section();


        foreach ($view_type_objects as $type_id => $view_type_object)
        {
            $view_type_object->register_skin($this);
            $view_type_object->register_style_controls();
        }

        $this->start_controls_section('section_filters_styling', ['label' => __('Вид - Фильтры'), 'tab' => Controls_Manager::TAB_STYLE]);

            $this->add_control(
                'filter_color',
                [
                    'label' => __('Filter Color'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .lae-block .lae-taxonomy-filter .lae-filter-item a, {{WRAPPER}} .lae-block .lae-block-filter .lae-block-filter-item a, {{WRAPPER}} .lae-block .lae-block-filter .lae-block-filter-more span, {{WRAPPER}} .lae-block .lae-block-filter ul.lae-block-filter-dropdown-list li a' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'filter_hover_color',
                [
                    'label' => __('Filter Hover Color'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .lae-block .lae-taxonomy-filter .lae-filter-item a:hover, {{WRAPPER}} .lae-block-grid .lae-taxonomy-filter .lae-filter-item.lae-active a, {{WRAPPER}} .lae-block .lae-block-filter .lae-block-filter-item a:hover, {{WRAPPER}} .lae-block .lae-block-filter .lae-block-filter-item.lae-active a' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'filter_typography',
                    'selector' => '{{WRAPPER}} .lae-block .lae-taxonomy-filter .lae-filter-item a, {{WRAPPER}} .lae-block .lae-block-filter .lae-block-filter-item a, {{WRAPPER}} .lae-block .lae-block-filter .lae-block-filter-more span, {{WRAPPER}} .lae-block .lae-block-filter ul.lae-block-filter-dropdown-list li a',
                ]
            );

        $this->end_controls_section();



        foreach ($view_module_regions as $region_id => $region)
        {
            $region['view_types'] = array_values($region['view_types']);

            if (!in_array($region_id, ['lightbox']))
            {
                $this->start_controls_section('section_module_region_'.$region_id.'_style', ['label' => __('Регион '.$region['label']), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['query_view' => $region['view_types']]]);

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_width',
                    [
                        'label' => __('Ширина региона'),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            '' => '',
                            1 => '1/12',
                            2 => '2/12',
                            3 => '3/12',
                            4 => '4/12',
                            5 => '5/12',
                            6 => '6/12',
                            7 => '7/12',
                            8 => '8/12',
                            9 => '9/12',
                            10 => '10/12',
                            11 => '11/12',
                            12 => '12/12',
                        ],
                        'condition' => [
                            'query_view' => $view_types_support['regions_grid']
                        ]
                    ]
                );

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_item_columns',
                    [
                        'label' => __('Ширина модуля'),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            '' => '',
                            1 => '1/12',
                            2 => '2/12',
                            3 => '3/12',
                            4 => '4/12',
                            5 => '5/12',
                            6 => '6/12',
                            7 => '7/12',
                            8 => '8/12',
                            9 => '9/12',
                            10 => '10/12',
                            11 => '11/12',
                            12 => '12/12',
                        ],
                        'condition' => [
                            'query_view' => $view_types_support['regions_grid']
                        ]
                    ]
                );

                $this->add_control(
                    'modules_region_'.$region_id.'_items_limit',
                    [
                        'label' => __('Лимит количества модулей региона'),
                        'type' => Controls_Manager::NUMBER,
                    ]
                );


                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_item_gap_horizontal',
                    [
                        'type' => 'slider',
                        'label' => __('Отступы модулей горизнтальные'),
                        'default' => ['unit' => 'px',],
                        'tablet_default' => ['unit' => 'px',],
                        'mobile_default' => ['unit' => 'px',],
                        'size_units' => [ '%', 'px', 'vw' ],
                        'range' => [
                            '%' => ['min' => 0, 'max' => 100,],
                            'px' => ['min' => 0, 'max' => 200,],
                            'vw' => ['min' => 0, 'max' => 100,],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .modules' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .module' => 'padding-left: {{SIZE}}{{UNIT}} !important; padding-right: {{SIZE}}{{UNIT}} !important;',
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .module .m-delim-h' => 'padding-left: {{SIZE}}{{UNIT}} !important; padding-right: {{SIZE}}{{UNIT}} !important;',
                        ],
                        'condition' => [

                        ]
                    ]
                );

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_item_gap_vertical',
                    [
                        'type' => 'slider',
                        'label' => __('Отступы модулей вертиальные'),
                        'default' => ['unit' => 'px',],
                        'tablet_default' => ['unit' => 'px',],
                        'mobile_default' => ['unit' => 'px',],
                        'size_units' => [ '%', 'px', 'vw' ],
                        'range' => [
                            '%' => ['min' => 0, 'max' => 100,],
                            'px' => ['min' => 0, 'max' => 200,],
                            'vw' => ['min' => 0, 'max' => 100,],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .modules' => 'margin-top: -{{SIZE}}{{UNIT}}; margin-bottom: -{{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .module' => 'margin-top: {{SIZE}}{{UNIT}} !important; margin-bottom: {{SIZE}}{{UNIT}} !important;',
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .module .m-delim-h-bottom' => 'bottom: -{{SIZE}}{{UNIT}} !important;',
                        ],
                        'condition' => [

                        ]
                    ]
                );

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_item_margin_horizontal',
                    [
                        'type' => 'slider',
                        'label' => __('Оступы модулей горизнтальные - c одной стороны'),
                        'default' => ['unit' => 'px',],
                        'tablet_default' => ['unit' => 'px',],
                        'mobile_default' => ['unit' => 'px',],
                        'size_units' => [ '%', 'px', 'vw' ],
                        'range' => [
                            '%' => ['min' => 0, 'max' => 100,],
                            'px' => ['min' => 0, 'max' => 200,],
                            'vw' => ['min' => 0, 'max' => 100,],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' > .modules' => 'margin-right: -{{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .module' => 'padding-right: {{SIZE}}{{UNIT}} !important;',
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .module .m-delim-h' => 'padding-right: {{SIZE}}{{UNIT}} !important;',
                        ],
                        'condition' => [

                        ]
                    ]
                );

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_item_margin_vertical',
                    [
                        'type' => 'slider',
                        'label' => __('Оступы модулей вертиальные - с одной стороны'),
                        'default' => ['unit' => 'px',],
                        'tablet_default' => ['unit' => 'px',],
                        'mobile_default' => ['unit' => 'px',],
                        'size_units' => [ '%', 'px', 'vw' ],
                        'range' => [
                            '%' => ['min' => 0, 'max' => 100,],
                            'px' => ['min' => 0, 'max' => 200,],
                            'vw' => ['min' => 0, 'max' => 100,],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' > .modules' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .module' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
                            '{{WRAPPER}} .q-region-modules-'.$region_id.' .module .m-delim-h-bottom' => 'bottom: -{{SIZE}}{{UNIT}} !important;',
                        ],
                        'condition' => [

                        ]
                    ]
                );

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_margin',
                    [
                        'label' => __( 'Отступы снаружи', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'placeholder' => [
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .q-region-'.$region_id => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_padding',
                    [
                        'label' => __( 'Отступы внутри', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'placeholder' => [
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .q-region-'.$region_id => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_modules_padding',
                    [
                        'label' => __( 'Блок модулей - Отступы внутри', 'elementor' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'placeholder' => [
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .q-region-'.$region_id.' .modules' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );


                $this->add_control(
                    'modules_region_'.$region_id.'_scroll',
                    [
                        'label' => __('Scrollbars'),
                        'type' => Controls_Manager::SWITCHER,
                    ]
                );


                $this->add_control(
                    'modules_region_'.$region_id.'_scroll_height_target',
                    [
                        'label' => __('Scroll Height target'),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            'selector' => 'Selector',
                            'custom' => 'Custom',
                        ],
                        'condition' => [
                            'modules_region_'.$region_id.'_scroll' => 'yes'
                        ],

                    ]
                );

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_scroll_height_target_selector',
                    [
                        'label' => __('Target selector'),
                        'type' => Controls_Manager::TEXT,
                        'condition' => [
                            'modules_region_'.$region_id.'_scroll' => 'yes',
                            'modules_region_'.$region_id.'_scroll_height_target' => 'selector',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    'modules_region_'.$region_id.'_scroll_height_target_value',
                    [
                        'label' => __('Max height'),
                        'type' => Controls_Manager::SLIDER,
                        'default' => ['unit' => 'px',],
                        'tablet_default' => ['unit' => 'px',],
                        'mobile_default' => ['unit' => 'px',],
                        'size_units' => [ '%', 'px', 'vw' ],
                        'range' => [
                            '%' => ['min' => 0, 'max' => 100,],
                            'px' => ['min' => 0, 'max' => 2000,],
                            'vw' => ['min' => 0, 'max' => 100,],
                        ],
                        'condition' => [
                            'modules_region_'.$region_id.'_scroll' => 'yes',
                            'modules_region_'.$region_id.'_scroll_height_target' => 'custom',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .q-region-modules-'.$region_id => 'max-height: {{SIZE}}{{UNIT}}; overflow:hidden;',
                        ],
                    ]
                );


                $this->end_controls_section();

            }

            if (!in_array($region_id, ['lightbox']))
            {
                $this->start_controls_section('section_module_'.$region_id.'_style', ['label' => __('Модуль '.$region['label']), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [
                    'query_view' => $region['view_types']
                ]]);
            }
            else
            {
                $this->start_controls_section('section_module_'.$region_id.'_style', ['label' => __('Модуль '.$region['label']), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [
                    //'lightbox_as_module' => 'yes'
                ]]);
            }


            /*
            $this->add_control(
                'module_'.$region_id.'_link_new_window',
                [
                    'label' => __('Open post links in new window?'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => '',
                ]
            );
            */

            $this->add_control(
                'module_'.$region_id.'_type',
                [
                    'label' => __('Класс модуля'),
                    'type' => Controls_Manager::SELECT2,
                    'label_block' => true,
                    'options' => $this->get_query_module_types_options(),
                ]
            );

            $this->add_responsive_control(
                'module_'.$region_id.'_row_gap_horizontal',
                [
                    'type' => 'slider',
                    'label' => __('Оступы модуля горизонтальные'),
                    'default' => ['unit' => 'px',],
                    'tablet_default' => ['unit' => 'px',],
                    'mobile_default' => ['unit' => 'px',],
                    'size_units' => [ '%', 'px', 'vw' ],
                    'range' => [
                        '%' => ['min' => 0, 'max' => 100,],
                        'px' => ['min' => 0, 'max' => 200,],
                        'vw' => ['min' => 0, 'max' => 100,],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .q-region-modules-'.$region_id.' .m-row' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .q-region-modules-'.$region_id.' .m-row .m-row-col' => 'padding-left: {{SIZE}}{{UNIT}} !important; padding-right: {{SIZE}}{{UNIT}} !important;',
                    ],
                ]
            );


            $this->add_control(
                'module_'.$region_id.'_full_from',
                array(
                    'label' => __('Полный вид начиная с'),
                    'type' => 'select',
                    'options' => [
                        'always'  => 'Всегда',
                        'none'    => 'Никогда',
                        'mobile'  => 'Mobile',
                        'tablet'  => 'Tablet',
                        'desktop' => 'Desktop',
                    ],
                    'include_type' => true,
                    'multiple' => true,
                )
            );

            $this->add_control(
                'module_'.$region_id.'_display_thumb',
                [
                    'label' => __('Изображение'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => 'По умолчанию',
                        'yes' => 'Показать',
                        'no' => 'Скрыть'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'module_'.$region_id.'_image_size',
                    'label' => __( 'Изображение - размер' ),
                    'default' => 'large',
                    'condition' => [
                        'module_'.$region_id.'_display_thumb!' => 'no'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_thumb_link',
                [
                    'label' => __('Изображение - ссылка'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'condition' => [
                        'module_'.$region_id.'_display_thumb!' => 'no'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_thumb_empty_hook',
                [
                    'label' => __('Изображение - заглушка при пустом'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'condition' => [
                        'module_'.$region_id.'_display_thumb!' => 'no'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_thumb_lazyload',
                [
                    'label' => __('Изображение - отложенная загрузка'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'module_'.$region_id.'_display_thumb!' => 'no'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_display_title',
                [
                    'label' => __('Заголовок'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_title_link',
                [
                    'label' => __('Заголовок - ссылка'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'condition' => [
                        'module_'.$region_id.'_display_title' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_display_econtent',
                [
                    'label' => __('Контент'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_display_summary',
                [
                    'label' => __('Тизер'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
            );


            $this->add_control(
                'module_'.$region_id.'_excerpt_link',
                [
                    'label' => __('Тизер - ссылка'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'no',
                    'condition' => [
                        'module_'.$region_id.'_display_summary' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_rich_text_excerpt',
                [
                    'label' => __('Тизер - тэги и шорткоды'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'condition' => [
                        'module_'.$region_id.'_display_summary' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_excerpt_length',
                [
                    'label' => __('Тизер - длина в словах'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 25,
                    'condition' => [
                        'module_'.$region_id.'_display_summary' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_display_taxonomy',
                [
                    'label' => __('Показать таксономию'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_taxonomies',
                [
                    'label' => __('Ограничить таксономии'),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => $taxonomies_options,
                    'condition' => [
                        'module_'.$region_id.'_display_taxonomy' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_display_author',
                [
                    'label' => __('Показать автора'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'no',
                ]
            );


            $this->add_control(
                'module_'.$region_id.'_display_date',
                [
                    'label' => __('Показать дату'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_date_format',
                [
                    'label' => __('Формат даты'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => __( 'Default', 'elementor-pro' ),
                        'F j, Y' => date( 'F j, Y' ),
                        'Y-m-d' => date( 'Y-m-d' ),
                        'm/d/Y' => date( 'm/d/Y' ),
                        'j F, H:i' => date( 'j F, H:i'),
                    ],
                    'default' => '',
                    'condition' => [
                        'module_'.$region_id.'_display_date' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_display_comments',
                [
                    'label' => __('Комментарии'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'no',
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_comments_caption',
                [
                    'label' => __('Комментарии - подпись'),
                    'type' => Controls_Manager::TEXT,
                    'condition' => [
                        'module_'.$region_id.'_display_comments' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_display_read_more',
                [
                    'label' => __('Показать ссылку Подробнее'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'no',
                ]
            );

            $this->add_control(
                'module_'.$region_id.'_read_more_text',
                [
                    'label' => __('Текст ссылки Подробнее'),
                    'type' => Controls_Manager::TEXT,
                    'default' => __('Read More'),
                    'condition' => [
                        'module_'.$region_id.'_display_read_more' => 'yes'
                    ]
                ]
            );


            $this->add_control(
                'module_'.$region_id.'_display_lightbox',
                [
                    'label' => __('Показать ссылку лайтбокс'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'no',
                ]
            );


            $this->add_control(
                'module_'.$region_id.'_id_attr',
                [
                    'label' => __('Аттрибут ID'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => '',
                        'entity' => 'Сущность',
                    ],
                ]
            );


            Common\Customizable::add_template_controls($this, 'module_'.$region_id.'_');

            $this->add_group_control(
                'background',
                [
                    'name' => 'module_'.$region_id.'_overlay_bg',
                    'selector' => '{{WRAPPER}} .q-region-modules-'.$region_id.' .m-overlay',
                    'types' => [ 'classic', 'gradient' ],
                    'condition' => [
                        'module_'.$region_id.'_display_thumb!' => 'no'
                    ]
                ]
            );

            $this->end_controls_section();
        }


        /*
         *


        if ( function_exists( 'acf_get_field_groups' ) ) {
            $acf_groups = acf_get_field_groups();
        } else {
            $acf_groups = apply_filters( 'acf/get_field_groups', [] );
        }

        $data_fields = [];

        foreach ( $acf_groups as $acf_group ) {

            // ACF >= 5.0.0
            if ( function_exists( 'acf_get_fields' ) ) {
                $fields = acf_get_fields( $acf_group['ID'] );
            } else {
                $fields = apply_filters( 'acf/field_group/get_fields', [], $acf_group['id'] );
            }

            if ( ! is_array( $fields ) ) {
                continue;
            }

            foreach ( $fields as $field )
            {
                $key = $field['key'] . ':' . $field['name'];
                $data_fields[$key] = $acf_group['title'].': '.$field['label'];
            }

        }

        $filters_repeater = new \Elementor\Repeater();

        $filters_repeater->add_control(
            'field_label',
            array(
                'label' => 'Field label',
                'default' => '',
                'type' => Controls_Manager::TEXT,
                'default' => '',
            )
        );

        $filters_repeater->add_control(
            'field_source',
            array(
                'label' => 'Field source',
                'default' => '',
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => true,
                'options' => $data_fields,
            )
        );

        $filters_repeater->add_control(
            'field_name_custom',
            array(
                'label' => 'Field name custom',
                'default' => '',
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'field_name' => ''
                ]
            )
        );

        $this->add_control(
            'data_fields',
            [
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'default' => array(),
                'fields' =>  array_values( $filters_repeater->get_controls() ),
                'title_field' => '{{{ field_label }}}',
            ]
        );
        */

        $this->start_controls_section(
            'section_customizer_filters',
            [
                'label' => __('Кастомизер фильтра'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'filters_customizer',
            [
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'default' => array(),
                'fields' =>  array_values( Filters_View\Common\Base::customizer_get_repeater()->get_controls() ),
                'title_field' => '{{{ target_names }}} {{ target_names_custom }}',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_block_customizer_style',
            [
                'label' => __('Кастомизер блока'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'view_customizer',
            [
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'default' => array(),
                'fields' =>  array_values( View\Common\Base::customizer_get_repeater()->get_controls() ),
                'title_field' => '{{{ target_names }}} {{ target_names_custom }}',
            ]
        );


        $this->end_controls_section();


        $this->start_controls_section(
            'section_modules_customizer_style',
            [
                'label' => __('Кастомизер модулей'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'modules_customizer',
            [
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'default' => array(),
                'fields' =>  array_values( Module\Common\Base::customizer_get_repeater()->get_controls() ),
                'title_field' => '{{{ regions }}}: {{{ target_names }}} {{ target_names_custom }}',
            ]
        );

        $this->end_controls_section();




        $this->start_controls_section(
            'section_pagination_styling',
            [
                'label' => __('Постраничная навигация'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'pagination_prev_icon',
            [
                'label' => __('Prev Icon'),
                'type' => Controls_Manager::ICON,
            ]
        );

        $this->add_control(
            'pagination_next_icon',
            [
                'label' => __('Next Icon'),
                'type' => Controls_Manager::ICON,
            ]
        );

        $this->add_control(
            'pagination_border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-pagination .lae-page-nav' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_bg_color',
            [
                'label' => __('Hover Background Color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-pagination .lae-page-nav:hover, {{WRAPPER}} .lae-block .lae-pagination .lae-page-nav.lae-current-page' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_nav_icon_color',
            [
                'label' => __('Nav Icon Color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-pagination .lae-page-nav i' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'pagination_hover_nav_icon_color',
            [
                'label' => __('Hover Nav Icon Color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-pagination .lae-page-nav:hover i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_disabled_nav_icon_color',
            [
                'label' => __('Disabled Nav Icon Color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-pagination .lae-page-nav.lae-disabled i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'heading_nav_text',
            [
                'label' => __('Navigation text'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_control(
            'pagination_text_color',
            [
                'label' => __('Nav Text Color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-pagination .lae-page-nav' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_text_color',
            [
                'label' => __('Hover Nav Text Color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-pagination .lae-page-nav:hover, {{WRAPPER}} .lae-block .lae-pagination .lae-page-nav.lae-current-page' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __('Nav Text Typography'),
                'name' => 'pagination_text_typography',
                'selector' => '{{WRAPPER}} .lae-block .lae-pagination .lae-page-nav',
            ]
        );


        $this->end_controls_section();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_load_more_button_styling',
            [
                'label' => __('Load More Button'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'load_more_button_custom_color',
            [
                'label' => __('Button Color'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-load-more' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'load_more_button_custom_hover_color',
            [
                'label' => __('Button Hover Color'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-load-more:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'load_more_button_padding',
            [
                'label' => __('Custom Padding'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-load-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => false
            ]
        );

        $this->add_control(
            'load_more_button_border_radius',
            [
                'label' => __('Custom Border Radius'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-load-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'load_more_button_label_color',
            [
                'label' => __('Label Color'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-load-more' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'load_more_button_label_hover_color',
            [
                'label' => __('Label Hover Color'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-block .lae-load-more:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'load_more_button_typography',
                'label' => __('Typography'),
                'selector' => '{{WRAPPER}} .lae-block .lae-load-more',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section('section_lightbox_style', ['label' => __('Лайтбокс'), 'tab' => Controls_Manager::TAB_STYLE,]);

        $this->add_control('lightbox_mode',
            [
                'label' => __('Лайтбокс режим'),
                'type' => Controls_Manager::SELECT,
                'default' => 'image',
                'options' => [
                    'image' => 'Image',
                    'element' => 'Element',
                    'module_element' => 'Module element',
                ]
            ]
        );

        $this->add_group_control(Group_Control_Image_Size::get_type(), ['label' => __( 'Image Size' ),          'name' => 'lightbox_image_size', 'default' => 'large']);

        $this->add_control('lightbox_link_wrap',                       ['label' => __('Link wrap'),             'type' => Controls_Manager::SWITCHER]);

        $this->add_control('lightbox_display_title',                   ['label' => __('Display title'),         'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);

        $this->add_control('lightbox_display_excerpt',                 ['label' => __('Display excerpt'),       'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);

        $this->add_control('lightbox_display_excerpt_length',          ['label' => __('Excerpt length words'),  'type' => Controls_Manager::NUMBER, 'min' => 5, 'max' => 100, 'step' => 1, 'default' => 25]);

        $this->end_controls_section();


        \SM_Elementor\Common\Widget::add_widget_header_customizer_controls($this);

        \SM_Elementor\Common\Widget::add_widget_footer_customizer_controls($this);

    }

    function render_cacheable() {

        if ($view = $this->get_view_object())
        {
            if (!$view->is_empty())
            {
                $attrs = $view->get_view_attrs();

                ?>

                <div <?php echo Util\Html::attributes($attrs); ?>>

                    <?
                    print $this->render_widget_header();
                    print $view->get_widget_view_content();
                    print $this->render_widget_footer();
                    ?>

                </div>

                <?
            }
        }
    }

    /* @return View\Common\Base */

    function get_view_object()
    {
        if (!isset($this->view_object))
        {
            $settings = $this->get_settings_for_display();

            $this->view_object = View\Common\Base::create_object($settings['query_view'], [$this]);

            if ($this->view_object)
            {
                $this->view_object->init($settings);
            }
        }

        return $this->view_object;
    }
}
