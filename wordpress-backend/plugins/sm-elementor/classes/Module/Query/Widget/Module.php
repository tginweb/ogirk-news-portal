<?php


namespace SM_Elementor\Module\Query\Widget;

use SM_Elementor\Common;
use SM\Assets;
use SM\Util;

use SM_Elementor\Module\Query\view;
use SM_Elementor\Module\Query\Filters_View;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Scheme_Typography;


class Module extends Common\Widget {


    public function get_name() {
        return 'sm-module';
    }

    public function get_title() {
        return __( 'SM: Query Module', 'elementor' );
    }


    public function get_script_depends() {

        //$view = $this->get_view_object();

        //if ($view) $view::enqueue_assets();

        if (\Elementor\Plugin::$instance->preview->is_preview_mode())
        {
            foreach ($this->get_query_view_types() as $type_id=>$type_info)
            {
               // $type_info['class']::enqueue_assets();
            }
        }

        return [];
    }


    function get_query_view_types_options()
    {
        return View\Common\Base::get_object_types_options();
    }

    function get_query_module_types()
    {
        return \SM_Elementor\Module\Query\Module\Common\Base::get_object_types();
    }

    function get_query_module_types_options()
    {
        return \SM_Elementor\Module\Query\Module\Common\Base::get_object_types_options();
    }



    protected function _register_controls() {


        die();

        $taxonomies_options = Util\Wp::get_taxonomies([], 'names');


        $this->start_controls_section('section_source', ['label' => __( 'Источник'),]);

            $this->add_control(
                'source_type',
                [
                    'type' => 'select',
                    'label' => __('Тип источника'),
                    'options' => [
                        'post' => 'Пост',
                        'term' => 'Термин',
                    ]
                ]
            );

            $this->add_control(
                'source_post_id',
                [
                    'type' => 'query',
                    'label' => __('Пост'),
                    'label_block' => true,
                    'filter_type' => 'by_id',
                    'include_type' => true,
                    'condition' => [
                        'source_type' => 'post',
                    ],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'source_term_id',
                [
                    'type' => 'query',
                    'label' => __('Термин'),
                    'label_block' => true,
                    'filter_type' => 'taxonomy',
                    'include_type' => true,
                    'condition' => [
                        'source_type' => 'term',
                    ],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );


        $this->end_controls_section();



        $this->start_controls_section('section_module_style', ['label' => __('Модуль '), 'tab' => Controls_Manager::TAB_STYLE]);
        

        $this->add_control(
            'module_type',
            [
                'label' => __('Класс модуля'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'options' => $this->get_query_module_types_options(),
            ]
        );

        $this->add_responsive_control(
            'module_row_gap_horizontal',
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
            'module_full_from',
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
            'module_display_thumb',
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
                'name' => 'module_image_size',
                'label' => __( 'Изображение - размер' ),
                'default' => 'large',
                'condition' => [
                    'module_display_thumb!' => 'no'
                ]
            ]
        );

        $this->add_control(
            'module_thumb_link',
            [
                'label' => __('Изображение - ссылка'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'module_display_thumb!' => 'no'
                ]
            ]
        );

        $this->add_control(
            'module_thumb_empty_hook',
            [
                'label' => __('Изображение - заглушка при пустом'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'module_display_thumb!' => 'no'
                ]
            ]
        );

        $this->add_control(
            'module_display_title',
            [
                'label' => __('Заголовок'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'module_title_link',
            [
                'label' => __('Заголовок - ссылка'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'module_display_title' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'module_display_summary',
            [
                'label' => __('Тизер'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );


        $this->add_control(
            'module_excerpt_link',
            [
                'label' => __('Тизер - ссылка'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'condition' => [
                    'module_display_summary' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'module_rich_text_excerpt',
            [
                'label' => __('Тизер - тэги и шорткоды'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'condition' => [
                    'module_display_summary' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'module_excerpt_length',
            [
                'label' => __('Тизер - длина в словах'),
                'type' => Controls_Manager::NUMBER,
                'default' => 25,
                'condition' => [
                    'module_display_summary' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'module_display_taxonomy',
            [
                'label' => __('Показать таксономию'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'module_taxonomies',
            [
                'label' => __('Ограничить таксономии'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $taxonomies_options,
                'condition' => [
                    'module_display_taxonomy' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'module_display_author',
            [
                'label' => __('Показать автора'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );


        $this->add_control(
            'module_display_date',
            [
                'label' => __('Показать дату'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'module_date_format',
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
                    'module_display_date' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'module_display_comments',
            [
                'label' => __('Комментарии'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'module_comments_caption',
            [
                'label' => __('Комментарии - подпись'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'module_display_comments' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'module_display_read_more',
            [
                'label' => __('Показать ссылку Подробнее'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'module_read_more_text',
            [
                'label' => __('Текст ссылки Подробнее'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Read More'),
                'condition' => [
                    'module_display_read_more' => 'yes'
                ]
            ]
        );


        $this->add_control(
            'module_display_lightbox',
            [
                'label' => __('Показать ссылку лайтбокс'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        Common\Customizable::add_template_controls($this, 'module_');

        $this->add_group_control(
            'background',
            [
                'name' => 'module_overlay_bg',
                'selector' => '{{WRAPPER}} .q-region-modules-'.$region_id.' .m-overlay',
                'types' => [ 'classic', 'gradient' ],
                'condition' => [
                    'module_display_thumb!' => 'no'
                ]
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
                'fields' =>  array_values( \SM_Elementor\Module\Query\Module\Common\Base::customizer_get_repeater()->get_controls() ),
                'title_field' => '{{{ regions }}}: {{{ target_names }}} {{ target_names_custom }}',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

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

            $this->view_object = \SM_Elementor\Module\Query\Module\Common\Base::create_object($settings['module_view'], [$this]);

            if ($this->view_object)
            {
                $this->view_object->init($settings);
            }
        }

        return $this->view_object;
    }
}
