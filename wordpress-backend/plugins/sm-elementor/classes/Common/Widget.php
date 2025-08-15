<?php


namespace SM_Elementor\Common;

use Elementor;
use Elementor\Controls_Manager;
use SM;
use SM\Util;
use SM\Util\Html;
use SM\Cache;
use SM_Elementor\Plugin;
use SM_Elementor\Common;



abstract class Widget extends \Elementor\Widget_Base
{
    var $context_widget;
    var $context_args;
    var $control_prefix;

    var $child_widgets = [];

    var $layout_object;

    function get_categories()
    {
        return [ 'sm-elementor' ];
    }

    function get_icon()
    {
        return 'eicon-post-info';
    }

    function get_type_class()
    {
        return 'sm-widget';
    }

    function get_uname()
    {
        return strtr($this->get_name(), '-', '_');
    }



    public function get_render_attributes1($element)
    {
        $attrs = $this->get_render_attribute_string($element);

        return \SM\Util\html::parse_attributes($attrs);
    }

    protected function _register_controls()
    {
        if ($this->support_widget_header)
        {
            $this->start_controls_section(
                'section_widget_header',
                [
                    'label' => __( 'Widget Header', 'elementor' ),
                ]
            );

            $this->add_control(
                'header_enable',
                [
                    'label' => __( 'Header Enable'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                ],
                [
                    'position' => [
                        'type' => 'control',
                        'at' => 'before',
                        'of' => 'header__skin'
                    ]
                ]
            );

            $this->add_control(
                'header_title',
                [
                    'label' => __( 'Header Title', 'elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                ]
            );

            $this->add_control(
                'header_title_more_url',
                [
                    'label' => __( 'Header More URL', 'elementor'),
                    'type' => \Elementor\Controls_Manager::URL,
                ]
            );

            $this->add_control(
                'header_title_more_text',
                [
                    'label' => __( 'Header More Text', 'elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                ]
            );

            $this->end_controls_section();
        }

        parent::_register_controls();
    }


    public function print_element() {

        $this->sm_is_printed = true;

        parent::print_element();
    }

    var $widget_header;
    var $widget_footer;

    function get_widget_header()
    {
        $settings = $this->get_settings_for_display();

        if ($settings['sm_widget_header']==='yes' && !isset($this->widget_header))
        {
            if ($this->widget_header = \SM_Elementor\Widget_Header\Common\Base::create_object($settings['sm_widget_header_type_'.$this->get_name()], [$this]))
            {
                $this->widget_header->init(\SM\Util\Base::sub_params($settings, 'sm_widget_header_'));

                if (!empty($settings['sm_header_customizer'])) $this->widget_header->customizer_init($settings['sm_header_customizer']);
            }
        }

        return $this->widget_header;
    }

    function get_widget_footer()
    {
        $settings = $this->get_settings_for_display();

        if ($settings['sm_widget_footer']==='yes' && !isset($this->widget_footer))
        {
            if ($this->widget_footer = \SM_Elementor\Widget_Footer\Common\Base::create_object($settings['sm_widget_footer_type_'.$this->get_name()], [$this]))
            {
                $this->widget_footer->init(\SM\Util\Base::sub_params($settings, 'sm_widget_footer_'));

                if (!empty($settings['sm_footer_customizer'])) $this->widget_footer->customizer_init($settings['sm_footer_customizer']);
            }
        }

        return $this->widget_footer;
    }

    function render_widget_header()
    {
        if ($header = $this->get_widget_header())
        {
            return $header->render();
        }
    }

    function render_widget_footer()
    {
        if ($footer = $this->get_widget_footer())
        {
            return $footer->render();
        }
    }


    function get_cache_id()
    {
        return $this->get_id();
    }

    function get_cache_contexts()
    {
        $contexts = [];

        $contexts_rules = $this->get_settings_for_display('sm_cache_contexts_rules');

        if (!empty($contexts_rules))
        {
            $rules_by_providers = [];

            foreach ($contexts_rules as $item)
            {
                list($rule_provider, $rule_name) = explode(':', $item);

                $rules_by_providers[$rule_provider][$rule_name] = $rule_name;
            }

            if (!empty($rules_by_providers))
            {
                foreach ($rules_by_providers as $provider_id=>$rules)
                {
                    switch ($provider_id)
                    {
                        case 'user':    $cls = '\SM\Cache\Context\User'; break;
                        case 'request': $cls = '\SM\Cache\Context\Request'; break;
                        case 'query':   $cls = '\SM\Cache\Context\Query'; break;
                        case 'menu':    $cls = '\SM\Cache\Context\Menu'; break;
                        default:        $cls = '';
                    }

                    if ($cls)
                    {
                        $contexts[] = new $cls(array_values($rules));
                    }
                }
            }
        }

        return $contexts;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (!Plugin::i()->is_editor_mode() && $settings['sm_cache']=='yes' && empty($_REQUEST['demo_ad_zone']))
        {
	        $cache_id = $this->get_cache_id();

            $cache_contexts = $this->get_cache_contexts();

            $debug_tag = $this->get_name().'.'.$this->get_id();

            fb('CACHE ON', $debug_tag);

            Cache::i()->item('element:'.$cache_id)->contexts($cache_contexts)->load()->callback(function() use ($debug_tag) {

                fb('REBUILD', $debug_tag);

                $this->render_cacheable();

            })->output();
        }
        else
        {
            $this->render_cacheable();
        }
    }

    function render_cacheable()
    {

    }

    static function call_render_widget_header(\Elementor\Widget_Base $element)
    {
        $settings = $element->get_settings_for_display();

        if ($settings['sm_widget_header']==='yes')
        {
            $widget_type = $element->get_name();

            $header = \SM_Elementor\Widget_Header\Common\Base::create_object($settings['sm_widget_header_type_'.$widget_type], [$element]);

            if ($header)
            {
                $header->init(\SM\Util\Base::sub_params($settings, 'sm_widget_header_'));

                if (!empty($settings['sm_header_customizer'])) $header->customizer_init($settings['sm_header_customizer']);

                return $header->render();
            }
        }
    }

    static function call_render_widget_footer(\Elementor\Widget_Base $element)
    {
        $settings = $element->get_settings_for_display();

        if ($settings['sm_widget_footer']==='yes')
        {
            $widget_type = $element->get_name();

            $footer = \SM_Elementor\Widget_Footer\Common\Base::create_object($settings['sm_widget_footer_type_'.$widget_type], [$element]);

            if ($footer)
            {
                $footer->init(\SM\Util\Base::sub_params($settings, 'sm_widget_footer_'));

                if (!empty($settings['sm_footer_customizer'])) $footer->customizer_init($settings['sm_footer_customizer']);

                return $footer->render();
            }
        }
    }


    static function add_widget_header_controls(\Elementor\Widget_Base $element)
    {
        $widget_type = $element->get_name();

        $element->start_controls_section(
            'section_sm_widget_header',
            [
                'label' => __( 'Widget Header', 'elementor-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT
            ]
        );

        $element->add_control(
            'sm_widget_header',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => 'Header',
            ]
        );

        $element->add_control(
            'sm_widget_header_type_'.$widget_type,
            [
                'type' => Controls_Manager::SELECT,
                'label' => 'Header type',
                'options' => \SM_Elementor\Widget_Header\Common\Base::get_types_options_by_widget($widget_type),
                'default' => 'header_1',
                'condition' => [
                    'sm_widget_header' => 'yes'
                ]
            ]
        );

        $element->add_control(
            'sm_widget_header_caption',
            [
                'type' => Controls_Manager::TEXT,
                'label' => 'Header text',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'sm_widget_header' => 'yes'
                ]
            ]
        );

        $element->add_control(
            'sm_widget_header_more',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => 'Header more',
                'condition' => [
                    'sm_widget_header' => 'yes'
                ]
            ]
        );

        $element->add_control(
            'sm_widget_header_more_link_url',
            [
                'type' => Controls_Manager::URL,
                'label' => 'Header more Url',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'sm_widget_header' => 'yes',
                    'sm_widget_header_more' => 'yes'
                ]
            ]
        );

        $element->add_control(
            'sm_widget_header_more_text',
            [
                'type' => Controls_Manager::TEXT,
                'label' => 'Header more Text',
                'condition' => [
                    'sm_widget_header' => 'yes',
                    'sm_widget_header_more' => 'yes'
                ]
            ]
        );

        Common\Customizable::add_template_controls($element, 'sm_widget_header_');

        $element->end_controls_section();

    }

    static function add_widget_footer_controls(\Elementor\Widget_Base $element)
    {
        $widget_type = $element->get_name();

        $element->start_controls_section(
            'section_sm_widget_footer',
            [
                'label' => __( 'Widget Footer', 'elementor-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT
            ]
        );

        $element->add_control(
            'sm_widget_footer',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => 'Footer',
            ]
        );

        $element->add_control(
            'sm_widget_footer_type_'.$widget_type,
            [
                'type' => Controls_Manager::SELECT,
                'label' => 'Footer type',
                'options' => \SM_Elementor\Widget_Footer\Common\Base::get_types_options_by_widget($widget_type),
                'default' => 'footer_1',
                'condition' => [
                    'sm_widget_footer' => 'yes'
                ]
            ]
        );

        $element->add_control(
            'sm_widget_footer_caption',
            [
                'type' => Controls_Manager::TEXT,
                'label' => 'Footer text',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'sm_widget_footer' => 'yes'
                ]
            ]
        );

        $element->add_control(
            'sm_widget_footer_more',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => 'Footer more',
                'condition' => [
                    'sm_widget_footer' => 'yes'
                ]
            ]
        );

        $element->add_control(
            'sm_widget_footer_more_link_url',
            [
                'type' => Controls_Manager::URL,
                'label' => 'Footer more Url',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'sm_widget_footer' => 'yes',
                    'sm_widget_footer_more' => 'yes'
                ]
            ]
        );

        $element->add_control(
            'sm_widget_footer_more_text',
            [
                'type' => Controls_Manager::TEXT,
                'label' => 'Footer more Text',
                'condition' => [
                    'sm_widget_footer' => 'yes',
                    'sm_widget_footer_more' => 'yes'
                ]
            ]
        );

        Common\Customizable::add_template_controls($element, 'sm_widget_header_');

        $element->end_controls_section();
    }


    static function add_widget_header_customizer_controls(\Elementor\Widget_Base $element)
    {
        $element->start_controls_section(
            'section_sm_header_customizer',
            [
                'label' => __('Кастомизер заголовка виджета'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $element->add_control(
            'sm_header_customizer',
            [
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'default' => array(),
                'fields' =>  array_values( \SM_Elementor\Widget_Header\Common\Base::customizer_get_repeater()->get_controls() ),
                'title_field' => '{{{ target_names }}} {{ target_names_custom }}',
            ]
        );

        $element->end_controls_section();

    }

    static function add_widget_footer_customizer_controls(\Elementor\Widget_Base $element)
    {
        $element->start_controls_section(
            'section_sm_footer_customizer',
            [
                'label' => __('Кастомизер подвала виджета'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $element->add_control(
            'sm_footer_customizer',
            [
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'default' => array(),
                'fields' =>  array_values( \SM_Elementor\Widget_Footer\Common\Base::customizer_get_repeater()->get_controls() ),
                'title_field' => '{{{ target_names }}} {{ target_names_custom }}',
            ]
        );

        $element->end_controls_section();

    }
}
