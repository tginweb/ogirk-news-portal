<?php

namespace SM_Elementor\Module\Repeater;

use Elementor\Controls_Stack;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    var $sources_info = null;
    var $sources_options = null;

    function init_events()
    {
        parent::init_events();

        $this->add_action('elementor/element/before_section_end', null, 10, 3);

        $this->add_filter('sm_elementor/repeater/sources');
    }

    function _filter_sm_elementor_repeater_sources($list)
    {
        $acf_fieldgroups = \ElementorPro\Modules\DynamicTags\ACF\Module::get_control_options( ['repeater'] );

        foreach ($acf_fieldgroups as $acf_fieldgroup)
        {
            foreach ($acf_fieldgroup['options'] as $acf_field_name => $acf_field_title)
            {
                $list['acf:'.$acf_field_name] = [
                    'label' => 'ACF repeater: '.$acf_fieldgroup['label'].': '.$acf_field_title
                ];
            }
        }

        return $list;
    }

    function _action_elementor_controls_controls_registered($manager)
    {
        $controls_manager = \Elementor\Plugin::instance()->controls_manager;

        $controls_manager->register_control('repeater', new Control\Repeater());
    }

    function get_sources_info()
    {
        if (!isset($this->sources_info))
        {
            $this->sources_info = [];

            foreach (sm_apply_filters_cached('sm_elementor/repeater/sources', []) as $source_id=>$source_info)
            {
                $this->sources_info[$source_id] = $source_info;
            }
        }

        return $this->sources_info;
    }

    function get_sources_options()
    {
        if (!isset($this->sources_options))
        {
            $this->sources_options = [
                '' => ''
            ];

            foreach ($this->get_sources_info() as $source_id => $source_info)
            {
                $this->sources_options[$source_id] = $source_info['label'];
            }

        }

        return $this->sources_options;
    }

    function _action_elementor_element_before_section_end(Controls_Stack $element, $section_id, $args)
    {
        $el_type = $element->get_name();

        if ($element->get_type()!='widget') return;

        $path = join('.', [$el_type, $args['tab'], $section_id]);

        if ($args['tab']=='content')
        {
            foreach ($element->get_controls() as $i=>$control)
            {
                if (isset($control['section']) && $control['section']==$section_id && $control['type']=='repeater')
                {

                    $element->add_control(
                        $control['name'].'_source',
                        [
                            'label' => __( 'Repeater source', 'elementor-pro' ),
                            'type' => \Elementor\Controls_Manager::SELECT,
                            'options' => $this->get_sources_options(),
                            'condition' => isset($control['condition']) ? $control['condition'] : []
                        ],
                        [
                            'position' => [
                                'at' => 'after',
                                'of' => $control['name']
                            ]
                        ]
                    );

                    $element->add_control(
                        $control['name'].'_rep_id',
                        [
                            'label' => __( 'Repeater ID', 'elementor-pro' ),
                            'type' => \Elementor\Controls_Manager::TEXT,
                            'condition' => isset($control['condition']) ? $control['condition'] : []
                        ],
                        [
                            'position' => [
                                'at' => 'after',
                                'of' => $control['name'].'_source'
                            ]
                        ]
                    );
                }
            }
        }
    }



}



