<?php


namespace SM_Elementor\Module\Query;


use SM\Elementor\Widget;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    var $filter_controller_types;
    var $filter_widget_types;
    var $query_entity_types;


    /* @return Module */
    static function i($info=[], $context=null) { return parent::i($info, $context); }

    static function info()
    {
        return array(
            'title'        => 'Query',
            'path'         => __DIR__,
            'classmap'     => [
                'widget' => [
                    'SM_Elementor\Module\Query\Widget\Query'  => array('label' => 'Query', 'init'=>true),
                ],
            ],
        );
    }

    function get_entity_types($type=null)
    {
        if (!isset($this->query_entity_types))
        {
            $this->query_entity_types = apply_filters('sm_elementor/query/types', []);
        }

        return $type ? $this->query_entity_types[$type] : $this->query_entity_types;
    }

    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm_elementor/query_view/types');
        $this->add_filter('sm_elementor/query_module/types');
        $this->add_filter('sm_elementor/query_filters_view/types');
        $this->add_filter('sm_elementor/query_module/templates');

        $this->add_filter('sm_elementor/query/filter_controller/types');
        $this->add_filter('sm_elementor/query/filter_widget/types');


        $this->add_filter('sm_elementor/widget_header/types');
        $this->add_filter('sm_elementor/widget_footer/types');


        add_action('wp_ajax_sm_elementor_module_query_request', [$this, '_action_ajax_query_request']);
        add_action('wp_ajax_nopriv_sm_elementor_module_query_request', [$this, '_action_ajax_query_request']);

    }

    function _action_ajax_query_request()
    {
        $view_object = View\Common\Base::create_object($_REQUEST['query_view']);

        if ($view_object)
        {
            if ($view_object->init_ajax($_REQUEST))
            {
                $result['inner_content'] = $view_object->get_inner_content();

                $result['pagination']    = $view_object->render_pagination();

                wp_send_json_success($result);
            }
        }

        wp_send_json_error([]);
    }

    function _filter_sm_elementor_widget_header_types($types)
    {
        $types += [
            'header_query_1'       => ['label'=>'Query Header 1', 'class'=> '\SM_Elementor\Module\Query\Widget_Header\header_query_1', 'widget_types'=>['sm-query']],
            'header_query_2'       => ['label'=>'Query Header 2', 'class'=> '\SM_Elementor\Module\Query\Widget_Header\header_query_2', 'widget_types'=>['sm-query']],
        ];

        return $types;
    }

    function _filter_sm_elementor_widget_footer_types($types)
    {
        $types += [
            'footer_query_1'       => ['label'=>'Query Footer 1', 'class'=> '\SM_Elementor\Module\Query\Widget_Footer\footer_query_1', 'widget_types'=>['sm-query']],
        ];

        return $types;
    }

    function _filter_sm_elementor_query_filters_view_types($types)
    {
        $types += [
            'view_default'        => ['label'=>'Default',  'class'=> '\SM_Elementor\Module\Query\Filters_View\view_default'],
        ];

        return $types;
    }

    function _filter_sm_elementor_query_view_types($types)
    {
        $types += [
            'view_list'            => ['label'=>'List',                 'class'=> '\SM_Elementor\Module\Query\View\view_list'],
            'view_grid_r1'         => ['label'=>'Grid 1 region',        'class'=> '\SM_Elementor\Module\Query\View\view_grid_r1'],
            'view_grid_r2'         => ['label'=>'Grid 2 regions',       'class'=> '\SM_Elementor\Module\Query\View\view_grid_r2'],
            'view_grid_r3'         => ['label'=>'Grid 3 regions',       'class'=> '\SM_Elementor\Module\Query\View\view_grid_r3'],
            'view_slider_swiper'   => ['label'=>'Slider Swiper',        'class'=> '\SM_Elementor\Module\Query\View\view_slider_swiper'],
            'view_player'          => ['label'=>'Player',               'class'=> '\SM_Elementor\Module\Query\View\view_player'],

            'view_map'             => ['label'=>'Map',                  'class'=> '\SM_Elementor\Module\Query\View\view_map'],
        ];

        return $types;
    }

    function _filter_sm_elementor_query_module_types($types)
    {
        $types += [
            'module_1'            =>  ['label'=>'Module 1',             'class'=> '\SM_Elementor\Module\Query\Module\module_1'],
            'module_slider_thumb' =>  ['label'=>'Module Slider Thumb',  'class'=> '\SM_Elementor\Module\Query\Module\module_slider_thumb'],
            'module_player'       =>  ['label'=>'Module Player',        'class'=> '\SM_Elementor\Module\Query\Module\module_player'],
            'module_player_thumb' =>  ['label'=>'Module Player Thumb',  'class'=> '\SM_Elementor\Module\Query\Module\module_player_thumb'],
        ];

        return $types;
    }

    function _filter_sm_elementor_query_filter_controller_types($types)
    {
        $types += [
            'taxonomy'  => ['label'=>'Taxonomy', 'class'=> '\SM_Elementor\Module\Query\Filter_Controller\Taxonomy'],
        ];

        return $types;
    }

    function _filter_sm_elementor_query_filter_widget_types($types)
    {
        $types += [
            'select'   => ['label'=>'Select', 'class'=> '\SM_Elementor\Module\Query\Filter_Widget\Select'],
            'links'    => ['label'=>'Links', 'class'=> '\SM_Elementor\Module\Query\Filter_Widget\Links'],
        ];

        return $types;
    }


    function get_filter_controller_type($type_id=null)
    {
        if (!isset($this->filter_controller_types))
        {
            $this->filter_controller_types = apply_filters('sm_elementor/query/filter_controller/types', []);
        }

        return isset($type_id) ? (!empty($this->filter_controller_types[$type_id]) ? $this->filter_controller_types[$type_id] : null) : $this->filter_controller_types;

    }

    function get_filter_widget_type($type_id=null)
    {
        if (!isset($this->filter_widget_types))
        {
            $this->filter_widget_types = apply_filters('sm_elementor/query/filter_widget/types', []);
        }

        return isset($type_id) ? (!empty($this->filter_widget_types[$type_id]) ? $this->filter_widget_types[$type_id] : null) : $this->filter_widget_types;
    }


    var $query_module_templates;

    function get_query_module_templates($id=null) {

        if (!isset($this->query_module_templates))
            $this->query_module_templates = apply_filters('sm_elementor/query_module/templates', []);

        return $id ? $this->query_module_templates[$id] : $this->query_module_templates;
    }


}



