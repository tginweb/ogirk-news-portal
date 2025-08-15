<?php


namespace SM_Elementor\Module\Conditions;

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
        $this->add_action('elementor/frontend/before_render');

        $this->add_action([
            'elementor/frontend/widget/after_render',
            'elementor/frontend/column/after_render',
            'elementor/frontend/section/after_render'
        ], [$this, '_action_elementor_frontend_after_render']);

        $this->add_action('wp_head');

        $this->add_filter('elementor/widget/render_content', null, 10, 2);
    }

    function _action_wp_head($widget)
    {
        ?>
        <style>
            .sm-elementor-cond-action-hide
            {
                display: none;
            }
        </style>
        <?
    }

    function _action_elementor_element_after_section_end(Controls_Stack $element, $section_id, $args)
    {
        $el_type = $element->get_name();

        if ($args['tab']=='advanced' && in_array($section_id, ['_section_style', 'section_advanced']))
        {
            $element->start_controls_section(
                'section_sm_conditions',
                [
                    'label' => __( 'Conditions'),
                    'tab' => Controls_Manager::TAB_ADVANCED,
                ]
            );

            $context_options = [];

            foreach (Context::i()->get_contexts() as $context_id => $context_info)
            {
                $context_options[$context_id] = !empty($context_info['label']) ? $context_info['label'] : $context_id;
            }

            $context_options['empty_widget'] = 'Пустой виджет';

            $element->add_control(
                'sm_conditions',
                [
                    'type' => Controls_Manager::REPEATER,
                    'prevent_empty' => false,
                    'default' => [

                    ],
                    'fields' => [
                        [
                            'name' => 'contexts',
                            'type' => 'select2',
                            'label' => 'Contexts',
                            'options' => $context_options,
                            'multiple' => true
                        ],
                        [
                            'name' => 'contexts_logic',
                            'type' => 'select',
                            'label' => 'Contexts logic',
                            'default' => 'AND',
                            'options' => [
                                'AND' => 'AND',
                                'OR' => 'OR',
                                'NOT_OR' => 'NOT (OR)',
                                'NOT_AND' => 'NOT (AND)',
                            ],
                        ],
                        [
                            'name' => 'class',
                            'type' => 'text',
                            'label' => 'Class',
                        ],
                        [
                            'name' => 'style',
                            'type' => 'text',
                            'label' => 'Style',
                        ],
                        [
                            'name' => 'attrs',
                            'type' => 'text',
                            'label' => 'Attrs',
                        ],
                        [
                            'name' => 'hide',
                            'type' => 'switcher',
                            'label' => 'Hide',
                        ],
                        [
                            'name' => 'disable',
                            'type' => 'switcher',
                            'label' => 'Disable',
                        ],

                        [
                            'name' => 'prepend',
                            'type' => 'text',
                            'label' => 'Prepend',
                            'dynamic' => [
                                'active' => true,
                            ],
                        ],

                        [
                            'name' => 'append',
                            'type' => 'text',
                            'label' => 'Prepend',
                            'dynamic' => [
                                'active' => true,
                            ],
                        ],
                    ],
                ]
            );

            $element->end_controls_section();
        }

    }

    function item_is_active($item, $content=null)
    {

        $match = true;

        if ($item['contexts'])
        {
            $logic = 'AND';

            $neg = false;

            switch ($item['contexts_logic'])
            {
                case 'AND':     $logic = 'AND'; $neg = false; break;
                case 'OR':      $logic = 'OR';  $neg = false; break;
                case 'NOT_AND': $logic = 'AND'; $neg = true; break;
                case 'NOT_OR':  $logic = 'OR';  $neg = true; break;
            }

            $c_index = array_search('empty_widget', $item['contexts']);

            if ($c_index!==false)
            {
                //if (is_null($content)) return false;

                unset($item['contexts'][$c_index]);

                if (trim(strip_tags($content)))
                {
                    $match = $neg ? true : false;
                }
                else
                {
                    $match = $neg ? false : true;
                }
            }


            if (!empty($item['contexts']))
            {
                if (!Context::i()->is_active($item['contexts'], $logic))
                {
                    $match = $match && ($neg ? true : false);
                }
                else
                {
                    $match = $match && ($neg ? false : true);
                }
            }
        }

        return $match;
    }

    function _filter_elementor_widget_render_content($content, \Elementor\Widget_Base $widget)
    {
        $settings = $widget->get_settings_for_display();

        if ($settings['sm_conditions'])
        {
            foreach ($settings['sm_conditions'] as $item) {

                if ($this->item_is_active($item, $content))
                {
                    if (!empty($item['prepend']))
                    {
                        $content = '<span class="sm-elementor-widget-prepend">'.$item['prepend'].'</span>'.$content;
                    }

                    if (!empty($item['append']))
                    {
                        $content = $content.'<span class="sm-elementor-widget-append">'.$item['append'].'</span>';
                    }

                    /*
                    if ($item['disable']=='yes')
                    {
                        $content = '';
                    }
                    */
                }
            }
        }

        return $content;
    }

    function _action_elementor_frontend_before_render($widget)
    {

        $settings = $widget->get_settings_for_display();

        if ($settings['sm_conditions'])
        {
            foreach ($settings['sm_conditions'] as $item)
            {
                if ($this->item_is_active($item))
                {
                    if ($item['class']) $widget->add_render_attribute('_wrapper', 'class', [$item['class']]);

                    if ($item['style']) $widget->add_render_attribute('_wrapper', 'style', $item['style']);

                    if ($item['attrs'])
                    {
                        $attrs = \SM\Util\Html::parse_attributes($item['attrs']);

                      //  fb($attrs);

                        foreach ($attrs as $attr_name=>$attr_value)
                            $widget->add_render_attribute('_wrapper', $attr_name, $attr_value, true);
                    }

                    if ($item['hide'])  $widget->add_render_attribute('_wrapper', 'class', ['sm-elementor-cond-action-hide']);

                    if ($item['disable'])
                    {
                        $this->disabled_elements[$widget->get_id()] = $widget->get_id();

                        ob_start();
                    }
                }
            }
        }
    }

    function _action_elementor_frontend_after_render($widget)
    {
        if (isset($this->disabled_elements[$widget->get_id()]))
        {
            ob_get_clean();
        }
    }



}



