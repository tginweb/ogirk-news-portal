<?php


namespace SM_Elementor\Common;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Customizable
{

    var $object_type;
    var $object_type_info;
    var $object_type_setting = '_skin';

    var $elements_info = [];

    var $customizer_items = [];

    var $customizer_element_class_preffix = 'el-';

    var $elements_stack = [];

    var $printed_elements = [];

    var $settings = [];

    var $renderers = [];

    var $host;

    static $customizer_class_name;
    static $customizer_class_types;


    function __construct($host=null)
    {
        $this->host = $host;
    }

    function get_default_settings()
    {
        return array(

        );
    }

    function prepare_settings($settings)
    {
        return wp_parse_args($settings, $this->get_default_settings());
    }

    function get_setting($key, $default=null)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }

    function get_host_setting($name, $default=null)
    {
        return isset($this->host->settings[$name]) ? $this->host->settings[$name] : $default;
    }

    function get_host()
    {
        return $this->host;
    }

    function get_title()
    {
        return $this->object_type_info ? $this->object_type_info['label'] : $this->get_object_type_id();
    }

    function get_object_type_id()
    {
        if (empty($this->object_type))
        {
            $class_path = explode('\\', get_called_class());

            $this->object_type = strtolower(array_pop($class_path));
        }

        return $this->object_type;
    }

    function get_object_type_id_class()
    {
        return strtr($this->get_object_type_id(), '_','-');
    }


    function get_children_element_params($children_params, $parent_params=[])
    {
        $children_params += [

        ];

        if (isset($parent_params['is_grid']))
        {
            $children_params['in_grid'] = !empty($parent_params['is_grid']);
        }

        return $children_params;
    }

    function customizer_init($items, $default_context=null) {

        $this->customizer_init_items($items, $default_context);
    }

    function customizer_init_items($items, $default_context=null) {

        foreach ($items as $item)
        {
            if (!empty($item['target_names']) || !empty($item['target_names_custom']))
            {
                if ($item = $this->customizer_init_item($item))
                    $this->customizer_items[] = $item;
            }
        }
    }

    function customizer_init_item($item)
    {
        if (!empty($item['target_names']))
        {
            $item['target_names'] = (array)$item['target_names'];
        }
        else
        {
            $item['target_names'] = [];
        }

        if (!empty($item['target_names_custom']))
        {
            $item['target_names'] = array_merge($item['target_names'], explode(',', $item['target_names_custom']));
        }

        return $item;
    }

    function customizer_element_params($targets, $params = [], $key=null) {

        if (!is_array($params)) $params = [];

        $params += [
            'attrs' => [],
            'class' => []
        ];

        $params['attrs'] = [
            'class' => []
        ];

        if ($params['class'])
            $params['class'] = (array)$params['class'];

        foreach ((array)$targets as $target)
        {
            if (method_exists($this, 'get_'.$target.'_attrs'))
            {
                $params['attrs'] = call_user_func([$this, 'get_'.$target.'_attrs'], $params['attrs']);
            }

            if (method_exists($this, 'get_'.$target.'_class'))
            {
                $params['class'] = call_user_func([$this, 'get_'.$target.'_class']);
            }

            $params['attrs']['class'][] = $this->customizer_element_class_preffix.strtr($target, '_','-');

            $params = $this->customizer_get_params($params, $target);

            $params['attrs']['class'] = array_merge($params['attrs']['class'], $params['class']);
        }

        if (!empty($params['in_grid']))
        {
            if ($grid_engine = \SM_Elementor\Module\Framework\Module::i()->get_grid_engine())
                $params['attrs']['class'] = array_merge($params['attrs']['class'], $grid_engine->get_grid_col_width_classes('columns', $params));
        }

        if (!empty($params['is_grid']))
        {
            if ($grid_engine = \SM_Elementor\Module\Framework\Module::i()->get_grid_engine())
            {
                $params['attrs']['class'][] = $grid_engine->get_grid_row_class();
            }
        }

        return $key ? $params[$key] : $params;
    }

    function customizer_get_params($params, $target, $args=[]) {

        $items = $this->customizer_find_items($target, $args);

        $items_class = [];

        foreach ($items as $item)
        {
            if ($item['_id'])
                $params['attrs']['class'][] = 'elementor-repeater-item-' . $item['_id'];

            if ($item['tag'])
                $params['tag'] = $item['tag'];

            if ($item['parent'])
                $params['parent'] = $item['parent'];

            if (!empty($item['columns']))
                $params['columns'] = $item['columns'];

            if (!empty($item['columns_tablet']))
                $params['columns_tablet'] = $item['columns_tablet'];

            if (!empty($item['columns_width']))
                $params['columns_width'] = $item['columns_width'];

            if ($item['class'])
                $items_class = array_merge($items_class, $item['class']);
        }

        if (!empty($items_class))
            $params['class'] = (array)$items_class;

        return $params;
    }

    function customizer_find_elements_by_parent($parent) {

        $result = [];

        foreach ($this->customizer_items as $item)
        {
            if ($item['parent'] == $parent && $this->customizer_check_item($item))
            {
                foreach ($item['target_names'] as $element_id)
                {
                    $result[$element_id] = $item;
                }
            }
        }

        return $result;
    }

    function customizer_find_items($target=null, $args=[]) {

        $result = [];

        foreach ($this->customizer_items as $item)
        {
            if ($this->customizer_check_item($item, $target, $args))
                $result[] = $item;
        }

        return $result;
    }

    function customizer_check_item($item, $target=null, $args=[]) {

        if ($target && !in_array($target, $item['target_names']))
            return false;
        else
            return true;
    }


    function element_open($element_name, $params=[]) {

        $params += [
            'tag' => 'div'
        ];

        $params = $this->customizer_element_params($element_name, $params);

        $this->elements_stack[] = $params;

        return '<'.$params['tag'].' '.\SM\Util\Html::attributes($params['attrs']).'>';
    }

    function element_close() {

        $params = array_pop($this->elements_stack);

        if ($params) return '</'.$params['tag'].'>';
    }


    function render()
    {
        if ($this->settings['template_source']=='settings')
        {
            $tpl = $this->settings['template_code'];
        }
        else if ($this->settings['template_source']=='registry')
        {

        }
        else
        {
            $tpl = $this->get_template();
        }

        return $this->compile_template($tpl);
    }

    function compile_template($tpl)
    {
        $content = preg_replace_callback('/(\{\{([^\}]+?)(\s+[^\}]+?)?\}\})/', array($this, 'cb_replace_element'), $tpl);

        return $content;
    }

    function get_children_elements($parent, $elements_ids=[])
    {
        $result = [];

        $weight = 0;

        $elements = [];

        if (!empty($elements_ids))
        {
            foreach ($elements_ids as $element_id)
            {
                $weight += 10;
                $elements[$element_id]['parent'] = $parent;
                $elements[$element_id]['weight'] = $weight;
            }
        }
        else
        {
            foreach ($this->elements_info as $element_id => $element_info)
            {
                if (!empty($element_info['parent']) && ($element_info['parent']==$parent))
                {
                    $elements[$element_id]['parent'] = $parent;

                    if (!empty($element_info['weight']))
                        $elements[$element_id]['weight'] = $element_info['weight'];
                    else {
                        $weight += 10;
                        $elements[$element_id]['weight'] = $weight;
                    }
                }
            }
        }


        $elements_ids = array_keys($elements);

        $customizer_child_elements = [];

        foreach ($this->customizer_items as $item)
        {
            if ($this->customizer_check_item($item))
            {
                if (!empty(array_intersect($elements_ids, $item['target_names'])))
                {
                    if (!empty($item['parent']) && ($item['parent'] != $parent))
                    {
                        foreach ($item['target_names'] as $element_id)
                        {
                            unset($elements[$element_id]);
                        }
                    }
                    else
                    {
                        foreach ($item['target_names'] as $element_id)
                        {
                            if (isset($elements[$element_id]))
                            {
                                if ($item['weight'])
                                    $elements[$element_id]['weight'] = $item['weight'];
                            }
                        }
                    }
                }
                else if ($item['parent'] == $parent)
                {
                    foreach ($item['target_names'] as $element_id)
                    {
                        $elements[$element_id]['weight'] = $item['weight'];
                        $elements[$element_id]['parent'] = $parent;
                    }
                }
            }
        }


        foreach ($elements as $element_id=>$element)
        {
            if ($element['parent']==$parent)
                $result[$element_id] = $element;
        }

        uasort($result, function ($item1, $item2) {
            return $item1['weight'] <=> $item2['weight'];
        });


        return array_keys($result);
    }

    function cb_replace_element($match)
    {
        $element_id = $match[2];

        $tag_params = isset($match[3]) ? shortcode_parse_atts($match[3]) : [];

        $return_type = null;

        @list($element_id, $return_type) = explode('.', $element_id);

        $result = '';


        if (!empty($tag_params['class']))
            $tag_params['class'] = explode(' ', $tag_params['class']);
        else
            $tag_params['class'] = [];

        //if ($params['disable']) return;

        if ($return_type)
        {
            switch ($return_type)
            {
                case 'attrs':

                    $params = $this->customizer_element_params($element_id, $tag_params) ?: [];

                    $result = \SM\Util\Html::attributes($params['attrs']);

                    break;

                case 'children':

                    $result = $this->container_render_children($element_id, $tag_params);

                    break;
            }
        }
        else
        {
            $result = $this->_render_element($element_id, $tag_params);
        }

        return $result;
    }

    function _render_element($element_id, $params=[])
    {
        $element_info = isset($this->elements_info[$element_id]) ? $this->elements_info[$element_id] : [];

        if (!empty($params['once']) && isset($this->printed_elements[$element_id]))
        {
            return;
        }

        if (isset($this->renderers[$element_id]))
        {
            $result = call_user_func($this->renderers[$element_id], $params);
        }
        else if (method_exists($this, 'render_'.$element_id))
        {
            $result = call_user_func([$this, 'render_'.$element_id], $params);
        }
        else if (!empty($element_info['is_container']))
        {
            $result = $this->_render_container($element_id, $params);
        }

        $this->printed_elements[$element_id] = true;

        return $result;
    }

    function _render_container($element_id, $params=[])
    {
        if (!method_exists($this, 'visible_'.$element_id) || $this->{'visible_'.$element_id}())
        {
            $params = $this->customizer_element_params($element_id, $params);

            $children = $this->container_render_children($element_id, $params);

            if ($children)
            {
                $result = '<div '.\SM\Util\Html::attributes($params['attrs']).'>'.$this->wrap_link($element_id, $children, $params).'</div>';
            }
        }

        $this->printed_elements[$element_id] = true;

        return $result;
    }

    function get_container_classes() {

        return [];
    }

    function container_render_children($element_id, $tag_params)
    {
        $result = '';

        $tag_param_elements = [];

        if (!empty($tag_params['elements']))
        {
            if (is_string($tag_params['elements']))
                $tag_param_elements = $tag_params['elements'] ? preg_split('/\s*,\s*/', $tag_params['elements']) : [];
            else
                $tag_param_elements = $tag_params['elements'];
        }

        $children_elements = $this->get_children_elements($element_id, $tag_param_elements);

        foreach ($children_elements as $children_element_id)
        {
            $params = $this->customizer_element_params($children_element_id);

            if (method_exists($this, 'render_'.$children_element_id))
            {
                $result .= call_user_func([$this, 'render_'.$children_element_id], $params);
            }
        }

        return $result;
    }

    static function customizer_get_repeater()
    {
        $repeater = new \Elementor\Repeater();

        $targets = static::get_all_types_elements();


        static::customizer_fill_repeater_conditions($repeater, $targets);
        static::customizer_fill_repeater_style($repeater, $targets);

        return $repeater;
    }


    static function customizer_fill_repeater_conditions(&$repeater, $targets=[])
    {

        $targets_options = [
            'custom' => 'Произвольная'
        ];

        foreach ($targets as $target_id => $target)
        {
            $targets_options[$target_id] = $target['label'];
        }

        $repeater->add_control(
            'target_names',
            array(
                'label' => 'Цель',
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $targets_options
            )
        );

        $repeater->add_control(
            'target_names_custom',
            array(
                'label' => 'Произвольная цель',
                'default' => '',
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'target_names' => 'custom'
                ]
            )
        );
    }

    static function customizer_fill_repeater_style(&$repeater, $targets)
    {
        $elements_classes_options = sm_apply_filters_cached('sm/html/classes', []);

        $container_targets_options = [
            '' => ''
        ];

        foreach ($targets as $target_id => $target)
        {
            if (!empty($target['is_container']) && $target['is_container'])
                $container_targets_options[$target_id] = $target['label'];
        }

        $repeater->add_control(
            'parent',
            array(
                'label' => __('Переместить в контейнер'),
                'type' => Controls_Manager::SELECT,
                'options' => $container_targets_options,
                'default' => '',
            )
        );

        $repeater->add_control(
            'weight',
            array(
                'label' => __('Изменить вес'),
                'type' => Controls_Manager::TEXT,
            )
        );

        $repeater->add_responsive_control(
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

        $repeater->add_control(
            'tag',
            array(
                'label' => __('Tag'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    ''=>'', 'h1' => __('H1'), 'h2' => __('H2'), 'h3' => __('H3'), 'h4' => __('H4'), 'h5' => __('H5'), 'h6' => __('H6'), 'div' => __('div'),
                ],
                'default' => '',
            )
        );

        $repeater->add_control(
            'class',
            array(
                'label' => __('Class'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $elements_classes_options,
            )
        );


        $repeater->add_responsive_control(
            'text_color',
            array(
                'label' => __('Text color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
                ],
            )
        );

        $repeater->add_responsive_control(
            'link_color',
            array(
                'label' => __('Link color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}} a' => 'color: {{VALUE}};',
                ],
            )
        );

        $repeater->add_responsive_control(
            'link_hover_color',
            array(
                'label' => __('Link hover color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} a:hover' => 'color: {{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}} a:hover' => 'color: {{VALUE}};',
                ],
            )
        );


        $repeater->add_responsive_control(
            'bg_color',
            [
                'label' => __('BG Color'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_control(
            'float',
            array(
                'label' => __('Float'),
                'type' => Controls_Manager::SELECT,
                'multiple' => true,
                'options' => [
                    '' => '',
                    'left' => 'Left',
                    'right' => 'Right',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'float: {{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'float: {{VALUE}};',
                ],
            )
        );

        $repeater->add_responsive_control(
            'margin',
            [
                'label' => __( 'Margin', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'placeholder' => [
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'padding',
            [
                'label' => __( 'Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'placeholder' => [
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'width',
            [
                'label' => __( 'Width', 'elementor'),
                'type' => 'text',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'width: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'height',
            [
                'label' => __( 'Height', 'elementor'),
                'type' => 'text',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'height: {{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'height: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'display',
            [
                'label' => __( 'Display'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => '',
                    'none' => 'None',
                    'block' => 'Block',
                    'inline-block' => 'Inline block',
                    'flex' => 'Flex',
                ],
                //'prefix_class' => 'elementor-sm-display-',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'display:{{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'display:{{VALUE}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'text_align',
            [
                'label' => __( 'Text align'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => '',
                    'left' => 'Left',
                    'right' => 'Right',
                    'center' => 'Center'
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align:{{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'text-align:{{VALUE}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'position',
            [
                'label' => __( 'Position'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => '',
                    'static' => 'Static',
                    'absolute' => 'Absolute',
                    'relative' => 'Relative',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'position:{{VALUE}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'position:{{VALUE}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'absolute_top',
            [
                'label' => __( 'Absolute top'),
                'type' => 'text',
                'condition' => ['position' => 'absolute'],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{VALUE}} !important;',
                    '.outside-element {{CURRENT_ITEM}}' => 'top: {{VALUE}} !important;',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'absolute_bottom',
            [
                'label' => __( 'Absolute bottom'),
                'type' => 'text',
                'condition' => ['position' => 'absolute'],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'bottom: {{VALUE}} !important;',
                    '.outside-element {{CURRENT_ITEM}}' => 'bottom: {{VALUE}} !important;',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'absolute_left',
            [
                'label' => __( 'Absolute left'),
                'type' => 'text',
                'condition' => ['position' => 'absolute'],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{VALUE}} !important;',
                    '.outside-element {{CURRENT_ITEM}}' => 'left: {{VALUE}} !important;',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'absolute_right',
            [
                'label' => __( 'Absolute right'),
                'type' => 'text',
                'condition' => ['position' => 'absolute'],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'right: {{VALUE}} !important;',
                    '.outside-element {{CURRENT_ITEM}}' => 'right: {{VALUE}} !important;',
                ],
            ]
        );


        $repeater->add_responsive_control(
            'translate',
            [
                'label' => __( 'Translate'),
                'type' => 'text',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => '-webkit-transform: translate({{VALUE}}) !important; transform: translate({{VALUE}});',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'translate_vertical_percent',
            [
                'label' => __( 'Translate vertical %'),
                'type'  => Controls_Manager::NUMBER,
                'min'   => 0,
                'max'   => 100,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{VALUE}}%; position: relative; transform: translateY(-{{VALUE}}%);',
                ],
            ]
        );

        $repeater->add_group_control(
            'border',
            [
                'name' => 'border',
                'label' => __('Border'),
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}, .outside-element {{CURRENT_ITEM}}',
            ]
        );

        $repeater->add_control(
            'border_radius',
            [
                'label' => __( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '.outside-element {{CURRENT_ITEM}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_control(
            'box_shadow_head',
            [
                'type' => Controls_Manager::HEADING,
                'label' => 'Тень бокса',
                'separator' => 'before',
            ]
        );

        $repeater->add_group_control(
            'box-shadow',
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}, .outside-element {{CURRENT_ITEM}}',
            ]
        );

        $repeater->add_control(
            'text_shadow_head',
            [
                'type' => Controls_Manager::HEADING,
                'label' => 'Тень текста',
                'separator' => 'before',
            ]
        );

        $repeater->add_group_control(
            'text-shadow',
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}, .outside-element {{CURRENT_ITEM}}',
            ]
        );

        $repeater->add_control(
            'typography_head',
            [
                'type' => Controls_Manager::HEADING,
                'label' => 'Типографика',
                'separator' => 'before',
            ]
        );

        $repeater->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'typography',
                'label' => __('Typography'),
                'selector' => 'body {{WRAPPER}} {{CURRENT_ITEM}}, .outside-element {{CURRENT_ITEM}}',
            )
        );

        $repeater->add_responsive_control(
            'css',
            [
                'label' => __( 'CSS'),
                'type' => Controls_Manager::TEXTAREA,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => '{{VALUE}}',
                ],
            ]
        );

    }


    function render_element_wrapper($element_id, $content, $params=[])
    {
        $params += [
            'tag' => 'div'
        ];

        $output = '<' . $params['tag'] . ' '. \SM\Util\Html::attributes($params['attrs']). '>';

        $output .= $content;

        $output .= '</' . $params['tag'] . '>';

        return $output;
    }

    function wrap_link($element_id, $content, $params=[])
    {
        if ($params['link_url'])
        {
            $link = [
                'url' => $params['link_url'],
                'is_external' => !empty($params['link_is_external']),
            ];
        }
        else if (

            (isset($this->settings[$element_id.'_link']) && ($this->settings[$element_id.'_link'] == 'yes' || is_array($this->settings[$element_id.'_link']))) ||
            (!isset($this->settings[$element_id.'_link']) && !empty($this->settings[$element_id.'_link_url']))
        )
        {
            $link = $this->settings[$element_id.'_link_url'];
        }

        if (!empty($link))
        {
            $target = isset($link['is_external']) && $link['is_external']==='on' ? ' target="_blank"' : '';

            $content = '<a href="'.$link['url'].'"'.$target.'>'.$content.'</a>';
        }

        return $content;
    }


    static function enqueue_assets() {

        $assets = static::get_enqueue_assets();

        if (!empty($assets))
            \SM\Assets::i()->wp_enqueue($assets);
    }

    static function get_enqueue_assets() {

        return [];
    }

    static function get_elements_info()
    {
        return [
            'container' => ['label'=>'Container', 'is_container'=>true],
        ];
    }

    static function load_type_class($type_info) {

        if (!class_exists($type_info['class']))
        {
            if (isset($type_info['file']))
                include_once $type_info['file'];

            $type_info['loaded'] = class_exists($type_info['class']);
        }
        else
        {
            $type_info['loaded'] = true;
        }

        if ($type_info['loaded']===true)
        {
            if (method_exists($type_info['class'], 'info'))
            {
                $type_info += $type_info['class']::info();
            }
        }

        return $type_info;
    }

    static function get_object_types() {

        $cname = static::$customizer_class_name;

        if (!isset(static::$customizer_class_types))
        {
            $types = apply_filters('sm_elementor/'.$cname.'/types', []);

            foreach ($types as $type_id => $type_info)
            {
                if (!isset($type_info['loaded']))
                    $type_info = static::load_type_class($type_info);

                $types[$type_id] = $type_info;

                if (!$type_info['loaded'])
                {
                    unset($types[$type_id]);
                }

            }

            static::$customizer_class_types = $types;
        }

        return static::$customizer_class_types;
    }

    static function get_object_types_options() {

        $options = [];

        foreach (static::get_object_types() as $type_id => $type_info)
            $options[$type_id] = $type_info['label'];

        return $options;
    }

    static function get_object_types_instances() {

        $objects = [];

        foreach (static::get_object_types() as $type_id => $type_info)
        {
            if ($object = static::create_object($type_id))
            {
                $objects[$type_id] = $object;
            }
        }

        return $objects;
    }

    static function get_object_type_info($type_id, $load=false) {

        $types = static::get_object_types();

        $type_info = $types[$type_id];

        if ($type_info && $load) static::load_type_class($type_info);

        return $type_info;
    }


    /* @return Customizable */
    static function create_object($type_id, $construct_args=[]) {

        $type_info = static::get_object_type_info($type_id, true);

        $object = false;

        if ($type_info && isset($type_info['class'])) {

            if (!class_exists($type_info['class']))
            {
                if (isset($type_info['file']) && is_readable($type_info['file']))
                    include_once $type_info['file'];
                else
                    return false;
            }

            if (class_exists($type_info['class']))
            {
                $r = new \ReflectionClass($type_info['class']);

                $object = $r->newInstanceArgs($construct_args);

                $object->elements_info = $object->get_elements_info();

                $object->object_type = $type_id;
                $object->object_type_info = $type_info;
            }
        }

        return $object;
    }

    static function get_all_types_elements() {

        $elements = [];

        $types = static::get_object_types();

        foreach ($types as $type_id => $type_info)
        {
            if ($type_info['class'] && class_exists($type_info['class']))
            {
                foreach ($type_info['class']::get_elements_info() as $element_id => $element_info)
                {
                    $element_info['label'] = !empty($element_info['label']) ? $element_info['label'] : ucfirst($element_id);

                    $elements[$element_id] = $element_info;
                }
            }
        }

        return $elements;
    }


    /**
     * Parent widget.
     *
     * Holds the parent widget of the skin. Default value is null, no parent widget.
     *
     * @access protected
     *
     * @var \Elementor\Widget_Base|null
     */
    protected $parent = null;

    /**
     * Skin base constructor.
     *
     * Initializing the skin base class by setting parent widget and registering
     * controls actions.
     *
     * @since 1.0.0
     * @access public
     * @param Widget_Base $parent
     */
    function register_skin( \Elementor\Widget_Base $parent ) {
        $this->parent = $parent;

        $this->_register_controls_actions();
    }

    function register_settings_controls() {

    }

    function register_advanced_settings_controls() {

    }

    function register_style_controls() {

    }

    /**
     * Get skin ID.
     *
     * Retrieve the skin ID.
     *
     * @since 1.0.0
     * @access public
     * @abstract
     */
    public function get_id() { }




    /**
     * Render skin output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 1.0.0
     * @deprecated 1.7.6
     * @access public
     */
    public function _content_template() {}

    /**
     * Register skin controls actions.
     *
     * Run on init and used to register new skins to be injected to the widget.
     * This method is used to register new actions that specify the location of
     * the skin in the widget.
     *
     * Example usage:
     * `add_action( 'elementor/element/{widget_id}/{section_id}/before_section_end', [ $this, 'register_controls' ] );`
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls_actions() {}

    /**
     * Get skin control ID.
     *
     * Retrieve the skin control ID. Note that skin controls have special prefix
     * to distinguish them from regular controls, and from controls in other
     * skins.
     *
     * @since 1.0.0
     * @access protected
     *
     * @param string $control_base_id Control base ID.
     *
     * @return string Control ID.
     */
    protected function get_control_id( $control_base_id ) {
        $skin_id = str_replace( '-', '_', $this->get_object_type_id() );
        return $skin_id . '_' . $control_base_id;
    }

    /**
     * Get skin settings.
     *
     * Retrieve all the skin settings or, when requested, a specific setting.
     *
     * @since 1.0.0
     * @TODO: rename to get_setting() and create backward compatibility.
     *
     * @access public
     *
     * @param string $control_base_id Control base ID.
     *
     * @return Widget_Base Widget instance.
     */
    public function get_instance_value( $control_base_id ) {
        $control_id = $this->get_control_id( $control_base_id );
        return $this->parent->get_settings( $control_id );
    }


    /**
     * Start skin controls section.
     *
     * Used to add a new section of controls to the skin.
     *
     * @since 1.3.0
     * @access public
     *
     * @param string $id   Section ID.
     * @param array  $args Section arguments.
     */
    public function start_controls_section( $id, $args, $shared=false ) {

        if ($shared)
        {
            $control_data = \Elementor\Plugin::$instance->controls_manager->get_control_from_stack($this->parent->get_unique_name(), $id);

            if ( is_wp_error( $control_data ) )
            {
                $args['condition'][$this->object_type_setting][] = $this->get_object_type_id();


                $this->parent->add_control( $id, $args );
            }
            else
            {
                $update_args = [
                    'condition' => $control_data['condition']
                ];

                $update_args['condition'][$this->object_type_setting][] = $this->get_object_type_id();

                $this->parent->update_control( $id, $update_args, ['recursive'=>true]);
            }
        }
        else
        {
            $args['condition'][$this->object_type_setting] = $this->get_object_type_id();

            $this->parent->start_controls_section( $this->get_control_id( $id ), $args );
        }

    }

    /**
     * End skin controls section.
     *
     * Used to close an existing open skin controls section.
     *
     * @since 1.3.0
     * @access public
     */
    public function end_controls_section() {
        $this->parent->end_controls_section();
    }


    /**
     * Add new skin control.
     *
     * Register a single control to the allow the user to set/update skin data.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $id   Control ID.
     * @param array  $args Control arguments.
     *
     * @return bool True if skin added, False otherwise.
     */
    public function add_control( $id, $args, $shared=false ) {
        if ($shared)
        {
            $control_data = \Elementor\Plugin::$instance->controls_manager->get_control_from_stack($this->parent->get_unique_name(), $id);

            if ( is_wp_error( $control_data ) )
            {
                $args['condition'][$this->object_type_setting][] = $this->get_object_type_id();


                $this->parent->add_control( $id, $args );
            }
            else
            {
                $update_args = [
                    'condition' => $control_data['condition']
                ];

                $update_args['condition'][$this->object_type_setting][] = $this->get_object_type_id();

                $this->parent->update_control( $id, $update_args, ['recursive'=>true]);
            }
        }
        else
        {
            $args['condition'][$this->object_type_setting] = $this->get_object_type_id();
            return $this->parent->add_control( $this->get_control_id( $id ), $args );
        }
    }

    /**
     * Update skin control.
     *
     * Change the value of an existing skin control.
     *
     * @since 1.3.0
     * @since 1.8.1 New `$options` parameter added.
     *
     * @access public
     *
     * @param string $id      Control ID.
     * @param array  $args    Control arguments. Only the new fields you want to update.
     * @param array  $options Optional. Some additional options.
     */
    public function update_control( $id, $args, array $options = [], $shared=false ) {

        if ($shared)
        {
            $args['condition'][$this->object_type_setting][] = $this->get_object_type_id();
            $this->parent->update_control( $id, $args, $options );
        }
        else
        {
            $args['condition'][$this->object_type_setting] = $this->get_object_type_id();
            $this->parent->update_control( $this->get_control_id( $id ), $args, $options );
        }
    }

    /**
     * Remove skin control.
     *
     * Unregister an existing skin control.
     *
     * @since 1.3.0
     * @access public
     *
     * @param string $id Control ID.
     */
    public function remove_control( $id ) {
        $this->parent->remove_control( $this->get_control_id( $id ) );
    }

    /**
     * Add new responsive skin control.
     *
     * Register a set of controls to allow editing based on user screen size.
     *
     * @since 1.0.5
     * @access public
     *
     * @param string $id   Responsive control ID.
     * @param array  $args Responsive control arguments.
     */
    public function add_responsive_control( $id, $args ) {
        $args['condition'][$this->object_type_setting] = $this->get_object_type_id();
        $this->parent->add_responsive_control( $this->get_control_id( $id ), $args );
    }

    /**
     * Update responsive skin control.
     *
     * Change the value of an existing responsive skin control.
     *
     * @since 1.3.5
     * @access public
     *
     * @param string $id   Responsive control ID.
     * @param array  $args Responsive control arguments.
     */
    public function update_responsive_control( $id, $args ) {
        $this->parent->update_responsive_control( $this->get_control_id( $id ), $args );
    }

    /**
     * Remove responsive skin control.
     *
     * Unregister an existing skin responsive control.
     *
     * @since 1.3.5
     * @access public
     *
     * @param string $id Responsive control ID.
     */
    public function remove_responsive_control( $id ) {
        $this->parent->remove_responsive_control( $this->get_control_id( $id ) );
    }

    /**
     * Start skin controls tab.
     *
     * Used to add a new tab inside a group of tabs.
     *
     * @since 1.5.0
     * @access public
     *
     * @param string $id   Control ID.
     * @param array  $args Control arguments.
     */
    public function start_controls_tab( $id, $args ) {
        $args['condition'][$this->object_type_setting] = $this->get_object_type_id();
        $this->parent->start_controls_tab( $this->get_control_id( $id ), $args );
    }

    /**
     * End skin controls tab.
     *
     * Used to close an existing open controls tab.
     *
     * @since 1.5.0
     * @access public
     */
    public function end_controls_tab() {
        $this->parent->end_controls_tab();
    }

    /**
     * Start skin controls tabs.
     *
     * Used to add a new set of tabs inside a section.
     *
     * @since 1.5.0
     * @access public
     *
     * @param string $id Control ID.
     */
    public function start_controls_tabs( $id ) {
        $args['condition'][$this->object_type_setting] = $this->get_object_type_id();
        $this->parent->start_controls_tabs( $this->get_control_id( $id ) );
    }

    /**
     * End skin controls tabs.
     *
     * Used to close an existing open controls tabs.
     *
     * @since 1.5.0
     * @access public
     */
    public function end_controls_tabs() {
        $this->parent->end_controls_tabs();
    }

    /**
     * Add new group control.
     *
     * Register a set of related controls grouped together as a single unified
     * control.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $group_name Group control name.
     * @param array  $args       Group control arguments. Default is an empty array.
     */
    final public function add_group_control( $group_name, $args = [] ) {

        $args['name'] = $this->get_control_id( $args['name'] );
        $args['condition'][$this->object_type_setting] = $this->get_object_type_id();
        $this->parent->add_group_control( $group_name, $args );
    }

    /**
     * Set parent widget.
     *
     * Used to define the parent widget of the skin.
     *
     * @since 1.0.0
     * @access public
     *
     * @param Widget_Base $parent Parent widget.
     */
    public function set_parent( $parent ) {
        $this->parent = $parent;
    }



    static function add_template_controls($element, $prefix, $condition=[])
    {


        $element->add_control(
            $prefix.'template_source',
            [
                'label' => __('Источник шаблона'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default'  => 'По умолчанию',
                    'settings' => 'Задать',
                    'registry' => 'Из реестра',
                ],
                'condition' => $condition
            ]
        );

        $element->add_control(
            $prefix.'template_code',
            [
                'label' => __('HTML код шаблона'),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
                'condition' => $condition + [
                    $prefix.'template_source' => 'settings',
                ],
            ]
        );

        $element->add_control(
            $prefix.'template_id',
            [
                'label' => __('Шаблон'),
                'type' => Controls_Manager::SELECT,
                'options' => [],
                'condition' => $condition + [
                    $prefix.'template_source' => 'registry',
                ],
            ]
        );
    }
}


