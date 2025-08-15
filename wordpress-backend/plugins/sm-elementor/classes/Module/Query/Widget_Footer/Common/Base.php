<?php


namespace SM_Elementor\Module\Query\Widget_Footer\Common;

use Elementor;
use Elementor\Controls_Manager;
use SM;
use SM\Util;
use SM\Util\Html;

class Base extends \SM_Elementor\Widget_Footer\Common\Base
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
            
            {{more}}                                   
          
        </div>

EOT;
    }

    function render_filters($params)
    {
        $params = $this->customizer_element_params('filters', $params);

        return $this->render_element_wrapper('filters', $this->host->get_view_object()->_render_element('filters', []), $params);
    }

    function render_pagination($params)
    {
        $params = $this->customizer_element_params('pagination', $params);

        return $this->render_element_wrapper('pagination', $this->host->get_view_object()->_render_element('pagination', ['show'=>true]), $params);
    }
}
