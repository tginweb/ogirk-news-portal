<?php


namespace SM_Elementor\Module\Query\Widget_Header\Common;

use Elementor;
use Elementor\Controls_Manager;
use SM;
use SM\Util;
use SM\Util\Html;

class Base extends \SM_Elementor\Widget_Header\Common\Base
{
    static function get_elements_info()
    {
        return parent::get_elements_info() + [
            'filters'  => ['label'=>'Filters', 'parent'=>'container'],
        ];
    }

    function get_template()
    {

        return <<<EOT

        <div {{container.attrs}}>             
            
            {{caption}}
            {{filters}}                                                                      
            {{more}}                                   
          
        </div>

EOT;
    }

    function render_filters($params)
    {
        $params = $this->customizer_element_params('filters', $params);

        return $this->render_element_wrapper('filters', $this->host->get_view_object()->_render_element('filters', []), $params);
    }
}
