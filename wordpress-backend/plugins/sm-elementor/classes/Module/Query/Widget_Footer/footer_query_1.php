<?php


namespace SM_Elementor\Module\Query\Widget_Footer;

use Elementor;
use Elementor\Controls_Manager;
use SM;
use SM\Util;
use SM\Util\Html;

class footer_query_1 extends Common\Base
{
    var $include_pagination = true;

    function get_container_classes() {
        return array_merge(parent::get_container_classes(), ['footer-1']);
    }


    function get_template()
    {

        return <<<EOT

        <div {{container.attrs}}>             

            {{pagination}}                                   
            
            {{more}}                                   
          
        </div>

EOT;
    }
}
