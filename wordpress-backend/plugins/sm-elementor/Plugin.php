<?php

namespace SM_Elementor;

use SM\Common;
use SM\Assets;


class Plugin extends \SM_Elementor\Common\Plugin_Module
{
    /* @return Plugin */
    static function i($info=[], $context=null) { return parent::i($info, $context); }

    static function info()
    {
        return array(
            'title'        => 'Smart Elementor',
            'description'  => '',
            'classmap'     => [
                'module' => [

                    'SM_Elementor\Module\Conditions\Module'              => array('id'=>'conditions',         'label' => 'Conditions',          'required'=>true),
                    'SM_Elementor\Module\ElementStyle\Module'            => array('id'=>'element_style',      'label' => 'Element Style',       'required'=>true),
                    'SM_Elementor\Module\Dropdown\Module'                => array('id'=>'dropdown',           'label' => 'Dropdown',            'required'=>true),
                    'SM_Elementor\Module\Widget\Module'                  => array('id'=>'widget',             'label' => 'Widget',              'required'=>true),
                    'SM_Elementor\Module\Scrollbars\Module'              => array('id'=>'scrollbars',         'label' => 'Scrollbars',          'required'=>true),
                    'SM_Elementor\Module\Link\Module'                    => array('id'=>'link',               'label' => 'Link',                'required'=>true),
                    'SM_Elementor\Module\Sticky\Module'                  => array('id'=>'sticky',             'label' => 'Sticky',              'required'=>true),
                    'SM_Elementor\Module\Cache\Module'                   => array('id'=>'cache',              'label' => 'Cache',               'required'=>true),
                    'SM_Elementor\Module\Hacks\Module'                   => array('id'=>'hacks',              'label' => 'Hacks',               'required'=>true),
                    'SM_Elementor\Module\Repeater\Module'                => array('id'=>'repeater',           'label' => 'Repeater',            'required'=>true),

                    'SM_Elementor\Module\Document\Module'                => array('id'=>'document',           'label' => 'Document',            'required'=>true),
                    'SM_Elementor\Module\DynamicTags\Module'             => array('id'=>'dynamic_tags',       'label' => 'Dynamic Tags',        'required'=>true),
     //               'SM_Elementor\Module\Preset\Module'                  => array('id'=>'preset',             'label' => 'Preset',              'required'=>true),
                    'SM_Elementor\Module\Archive\Module'                 => array('id'=>'archive',            'label' => 'Archive',             'required'=>true),
                    'SM_Elementor\Module\Framework\Module'               => array('id'=>'framework',          'label' => 'Framework',           'required'=>true),
                    'SM_Elementor\Module\Porting\Module'                 => array('id'=>'porting',            'label' => 'Porting',             'required'=>true),
      //              'SM_Elementor\Module\Form\Module'                    => array('id'=>'form',               'label' => 'Form',                'required'=>true),
                    'SM_Elementor\Module\Query\Module'                   => array('id'=>'query',              'label' => 'Query',               'required'=>true),
                    'SM_Elementor\Module\Viewer\Module'                  => array('id'=>'viewer',             'label' => 'Viewer',              'required'=>true),
                    'SM_Elementor\Module\ThemeConditions\Module'         => array('id'=>'theme_conditions',   'label' => 'Theme Conditions',    'required'=>true),
                    'SM_Elementor\Module\QueryControls\Module'           => array('id'=>'query_controls',     'label' => 'Query Controls',      'required'=>true),
      //              'SM_Elementor\Module\ThirdParty\ElementorPro\Module' => array('id'=>'elementor_pro',      'label' => 'Elementor Pro',       'required'=>true),
                ],
                'widget' => [
                    'SM_Elementor\Widget\Divider'   => array('label' => 'Divider', 'init'=>true),
                    'SM_Elementor\Widget\Nav_Links' => array('label' => 'Nav Links', 'init'=>true),
                    'SM_Elementor\Widget\Template'  => array('label' => 'Template', 'init'=>true),
                    'SM_Elementor\Widget\Evaluate'  => array('label' => 'Evaluate', 'init'=>true),
                    'SM_Elementor\Widget\Titlebox'  => array('label' => 'Titlebox', 'init'=>true),
                ],
                'group_control' => [
                    'SM_Elementor\Module\QueryControls\Group_Control\Entity_Query' => array('control_id' => 'sm-entity-query', 'label' => 'Entity Query', 'init'=>true),
                ],
            ]
        );
    }

    static function params_info()
    {
        return array(

        );
    }

    function enqueue_assets()
    {
        if (!is_admin())
        {
            Assets::i()->wp_enqueue([
                'sm_elementor.frontend',
                'sm_elementor.sticky',
                'sm_elementor.swiper',
                'sm_elementor.plyr',
                'sm_elementor.overlayScrollbars',
                'sm_elementor.fancybox',
                'sm_elementor.lazy'
            ]);
        }
    }

    function assets()
    {
        $path = $this->get_path_rel();

        return [
            'sm_elementor.editor' => [
                $path.'/assets/css/editor.css',
                $path.'/assets/js/base.js',
                $path.'/assets/js/editor.js',
            ],

            'sm_elementor.frontend' => [
                $path.'/assets/css/frontend.css',
                $path.'/assets/js/base.js',
                $path.'/assets/js/frontend.js',
            ],

            'sm_elementor.query' => [
                $path.'/assets/css/query-widget.css',
                $path.'/assets/js/query-widget.js',
            ],

            'sm_elementor.sticky' => [
                $path.'/assets/lib/sticky/jquery.sticky-kit.js',
            ],

            'sm_elementor.fancybox' => [
                $path.'/assets/lib/fancybox/dist/jquery.fancybox.css',
                $path.'/assets/lib/fancybox/dist/jquery.fancybox.min.js',
            ],


            'sliderMenu' => [
                $path."/assets/lib/jquery.sliderMenu/dist/css/slider-menu.jquery.css",
                $path."/assets/lib/jquery.sliderMenu/dist/js/slider-menu.jquery.min.js"
            ],



            'slick' => [
                $path.'/assets/lib/slick/slick.css',
                $path.'/assets/lib/slick/slick-theme.css',
                $path.'/assets/lib/slick/slick.js',
            ],

            'zoom' => [
                $path.'/assets/lib/zoom/jquery.zoom.min.js',
            ],

            'sm_elementor.lazy' => [
                $path.'/assets/lib/jquery.lazy/jquery.lazy.js',
                $path.'/assets/lib/jquery.lazy/jquery.lazy.plugins.js',
            ],

            'sm_elementor.swiper' => [
                $path.'/assets/lib/swiper/css/swiper.css',
                $path.'/assets/lib/swiper/js/swiper.min.js',
            ],

            'sm_elementor.plyr' => [
                $path.'/assets/lib/plyr/plyr.css',
                $path.'/assets/lib/plyr/plyr.min.js',
            ],

            'sm_elementor.overlayScrollbars' => [
                $path.'/assets/lib/overlayScrollbars/css/OverlayScrollbars.css',
                $path.'/assets/lib/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
            ],

        ];
    }


    function init_events()
    {
        $this->add_action('wp_head');

        $this->add_action('elementor/init');

        $this->add_action('elementor/widgets/widgets_registered', null, 10000);

        $this->add_action('elementor/preview/init');

        //$this->add_action('elementor/editor/after_enqueue_scripts', null, 99999);

        $this->add_action('elementor/editor/before_enqueue_scripts');
        $this->add_action('elementor/editor/before_enqueue_styles', null, 99999);
        $this->add_action('elementor/frontend/before_enqueue_scripts');

        $this->add_filter('elementor/frontend/the_content');

        //$this->add_action(['wp_ajax_sm_elementor_widget', 'wp_ajax_nopriv_sm_elementor_widget'], '_action_ajax_elementor_widget');

        $this->add_filter('sm/entity/bundles');

        $this->add_filter('sm_elementor/widget_header/types');
        $this->add_filter('sm_elementor/widget_footer/types');

        parent::init_events();
    }

    function _action_wp_head()
    {
        ?>
        <style>
            .elementor-sm-display-none
            {
                display: none;
            }
        </style>
        <?
    }

    function _action_elementor_frontend_before_enqueue_scripts()
    {

        /*
        $locale_settings = [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'sm-elementor-frontend' ),
        ];

        $locale_settings = apply_filters( 'sm_elementor/frontend/localize_settings', $locale_settings );

        wp_localize_script(
            'sm-elementor-frontend-localize',
            'SM_Elementor_Frontend_Config',
            $locale_settings
        );
        */
    }


    function _filter_sm_elementor_widget_header_types($types)
    {
        $types += [
            'header_1' => ['label'=>'Header 1', 'class'=> '\SM_Elementor\Widget_Header\header_1'],
            'header_2' => ['label'=>'Header 2', 'class'=> '\SM_Elementor\Widget_Header\header_2'],
        ];

        return $types;
    }

    function _filter_sm_elementor_widget_footer_types($types)
    {
        $types += [
            'footer_1' => ['label'=>'Footer 1', 'class'=> '\SM_Elementor\Widget_Footer\footer_1'],
        ];

        return $types;
    }

    function _action_elementor_init()
    {
        $categories = [];

        $categories['sm-elementor'] = ['title'=>'SM Elementor', 'icon'=>'fa fa-plug'];

        foreach ($categories as $category=>$info)
        {
            \Elementor\Plugin::instance()->elements_manager->add_category($category, $info);
        }

        \Elementor\Controls_Manager::add_tab( 'preset', 'Preset' );

    }


    function _action_elementor_preview_init($preview)
    {

    }

    //

    function _action_elementor_editor_before_enqueue_styles()
    {




    }

    function _action_elementor_editor_before_enqueue_scripts()
    {
        wp_enqueue_script( 'sm-elementor-common', $this->get_path_rel() . '/assets/js/common.js', [], false, true);
        wp_enqueue_script( 'sm-elementor-editor', $this->get_path_rel() . '/assets/js/editor.js', [], false, true);


        /*

        $locale_settings = [
            'i18n' => [],
        ];

        $locale_settings = apply_filters( 'sm_elementor/editor/localize_settings', $locale_settings );

        wp_localize_script(
            'sm-elementor-config',
            'SM_Elementor_Config',
            $locale_settings
        );
        */
    }

    function _filter_elementor_frontend_the_content($content)
    {
        return $content;
    }


    function create_element($widget_type, $widget_settings)
    {
        $data['elType'] = 'widget';
        $data['widgetType'] = $widget_type;
        $data['settings'] = $widget_settings;

        if ($widget_type)
        {
            $element = \Elementor\Plugin::$instance->elements_manager->create_element_instance($data);
        }

        return $element;
    }


    function get_builder_post_settings($name=null)
    {
        if ($post_id = get_the_ID())
        {
            if (!isset($this->builder_post_setttings[$post_id]))
            {
                $page = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' )->get_model($post_id);

                $this->builder_post_setttings[$post_id] = $page->get_data('settings');
            }

            return \SM\Util::get_nested_value($this->builder_post_setttings[$post_id], $name);
        }
    }

    function build_post_content( $post_id )
    {
        return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post_id );
    }


    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
            'term:sm-elementor-template' => array(
                'label'             => 'Elementor templates',
                'labels'            => array('singular_name'=>'Elementor template'),
                'public'            => true,
                'show_ui'           => true,
                'object_type'       => array('page','post'),
                'register'          => true,
                'hierarchical'      => false,
            ),
        ]);
    }

    function is_editor_mode()
    {
        if (($_REQUEST['action'] == 'elementor') || ($_REQUEST['action'] == 'elementor_render_widget') || ($_REQUEST['action'] == 'elementor_save_builder')) {
            return true;
        } else {
            return false;
        }
    }



}


