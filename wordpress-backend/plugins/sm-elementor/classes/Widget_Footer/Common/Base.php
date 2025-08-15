<?php


namespace SM_Elementor\Widget_Footer\Common;

use Elementor;
use Elementor\Controls_Manager;
use SM;
use SM\Util;
use SM\Util\Html;

abstract class Base extends \SM_Elementor\Widget_Header\Common\Base
{
    static $customizer_class_name = 'widget_footer';

    static $customizer_class_types;


    function init($settings) {

        $this->settings = $this->prepare_settings($settings);
    }

    function get_container_classes() {

        $classes = [
            'sm-widget-footer',
            strtr($this->get_object_type_id(), '_','-'),
        ];

        return $classes;
    }

    function get_template()
    {

        return <<<EOT

        <div {{container.attrs}}>
           
            {{more}}                                   
          
        </div>

EOT;
    }
}
