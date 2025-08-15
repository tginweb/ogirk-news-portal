<?php


namespace SM_Elementor\Module\Cache;

use SM\Common;
use SM\Context;
use Elementor\Controls_Stack;
use Elementor\Controls_Manager;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    public $disabled_elements = [];

    function init_events()
    {
        $this->add_action('elementor/element/after_section_end', null, 10, 3);

        if ($_REQUEST['stack_cache_test'])
        {
            $this->add_action('wp_head', null, -100000);
           // $this->add_action('wp_footer', null, 100000);
        }
    }

    function _action_wp_head()
    {
        \Elementor\Plugin::$instance->controls_manager->stacks = get_option('elementor_stack_cache', []);
    }

    function _action_wp_footer()
    {
        $stack_cache = [];

        foreach (\Elementor\Plugin::$instance->controls_manager->get_stacks() as $item_key=>$item_stack) {

            list($group, $id) = explode('-', $item_key);

            if (!in_array($group, ['repeater']))
                $stack_cache[$item_key] = $item_stack;
        }

        update_option('elementor_stack_cache', $stack_cache);
    }

    function _action_elementor_element_after_section_end(Controls_Stack $element, $section_id, $args)
    {
        $widget_type = $element->get_name();

        if ($args['tab']=='advanced' && in_array($section_id, ['_section_style', 'section_advanced']))
        {

            $element->start_controls_section(
                'section_sm_cache',
                [
                    'label' => __( 'SM: Cache'),
                    'tab' => Controls_Manager::TAB_ADVANCED,
                ]
            );

            $element->add_control(
                'sm_cache',
                [
                    'label' => 'Enable',
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $element->add_control(
                'sm_cache_timeout_type',
                [
                    'label' => 'Type',
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'permanent' => 'Постоянный',
                        'temporary' => 'Временный',
                    ],
                    'default' => 'permanent',
                    'condition' => [
                        'sm_cache' => 'yes'
                    ]
                ]
            );

            $element->add_control(
                'sm_cache_timeout_time',
                [
                    'label' => 'Timout',
                    'type' => Controls_Manager::NUMBER,
                    'default' => 600,
                    'condition' => [
                        'sm_cache' => 'yes',
                        'sm_cache_timeout_type' => 'temporary'
                    ]
                ]
            );


            $element->add_control(
                'sm_cache_contexts_rules',
                [
                    'label' => 'Rules',
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => [
                        'user:user_id'           => 'User: Id',
                        'user:user_roles'        => 'User: Roles',
                        'request:url'            => 'Request: Url',
                        'query:queried_object'   => 'Query: Queried object',
                        'menu:active_items'      => 'Menu: Active items',
                    ],
                    'condition' => [
                        'sm_cache' => 'yes',
                    ]
                ]
            );

            $element->end_controls_section();
        }

    }


}



