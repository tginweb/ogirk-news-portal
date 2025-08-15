<?php


namespace SM_Elementor\Widget_Header\Common;

use Elementor;
use Elementor\Controls_Manager;
use SM;
use SM\Util;
use SM\Util\Html;

abstract class Base extends \SM_Elementor\Common\Customizable
{
    static $customizer_class_name = 'widget_header';
    static $customizer_class_types;

    public $customizer_element_class_preffix = 'w-';

    static function get_elements_info()
    {
        return parent::get_elements_info() + [
            'caption'          => ['label'=>'Caption', 'parent'=>'container'],
            'more'             => ['label'=>'More', 'is_container'=>true, 'parent'=>'container'],
            'more_text'        => ['label'=>'More text', 'parent'=>'more'],
            'more_icon'        => ['label'=>'More icon', 'parent'=>'more'],
        ];
    }

    static function get_types_options_by_widget($widget_type)
    {
        $options = [];

        foreach (static::get_object_types() as $type_id => $type_info)
        {
            if (empty($type_info['widget_types']) || in_array($widget_type, $type_info['widget_types']))
                $options[$type_id] = $type_info['label'];
        }

        return $options;
    }


    function init($settings) {

        $this->settings = $this->prepare_settings($settings);
    }

    function get_container_attrs($attrs)
    {
        $attrs['class'] = $this->get_container_classes();

        return $attrs;
    }

    function get_container_classes() {

        $classes = [
            'sm-widget-header',
            strtr($this->get_object_type_id(), '_','-'),
        ];

        return $classes;
    }

    function render_caption($params=[]) {

        $output = '';

        if (!empty($this->settings['caption'])) {

            $params = $this->customizer_element_params('caption', $params) + [

            ];

            $output = $this->render_element_wrapper('caption', $this->wrap_link('more', $this->settings['caption']), $params);

        }

        return $output;
    }

    function render_more_text($params=[]) {

        if (empty($this->settings['more_text'])) return;

        $params = $this->customizer_element_params('more_text', $params) + [
            'tag' => 'span'
        ];

        $output = $this->render_element_wrapper('more_text', $this->settings['more_text'], $params);

        return $output;
    }


    function render_more_icon($params=[]) {

        $params = $this->customizer_element_params('more_icon', $params) + [
            'tag' => 'span',
            'icon_class' => 'fa fa-angle-right'
        ];

        $params['attrs']['class'][] = $params['icon_class'];

        $output = $this->render_element_wrapper('more_icon', '', $params);

        return $output;
    }

    function visible_more()
    {
        return $this->settings['more']==='yes';
    }


    function get_template()
    {

        return <<<EOT

        <div {{container.attrs}}>
           
            {{caption}}                                    
            {{more}}                                   
          
        </div>

EOT;
    }
}
