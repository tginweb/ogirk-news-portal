<?php

namespace SM_Elementor\Module\Query\Filters_View\Common;

use SM_Elementor;
use SM_Elementor\Module\QueryControls\Group_Control\Entity_Query;


abstract class Base extends \SM_Elementor\Common\Customizable
{
    static $customizer_class_name = 'query_filters_view';
    static $customizer_class_types = null;
    var $customizer_element_class_preffix = 'q-';

    var $object_type_setting = 'filters_view';

    static function enqueue_assets()
    {

    }

    static function info()
    {
        return [

        ];
    }

    static function get_elements_info()
    {
        $result = [
            'container'           => ['label'=>'Container'],
            'filter'              => ['label'=>'Filter'],
            'filter_label'        => ['label'=>'Filter label'],
            'filter_control'      => ['label'=>'Filter control'],
            'filter_control_item' => ['label'=>'Filter control item'],
        ];

        return $result;
    }

    function init($settings) {

        $this->settings = $this->prepare_settings($settings);

        if (!empty($this->settings['customizer']))
            $this->customizer_init($this->settings['customizer']);

        return $this;
    }

    function render_filter($params) {

        $filter_name = $params['name'];

        if ($this->host->filters[$filter_name])
        {
            $filter = $this->host->filters[$filter_name];

            $params = array_filter([
                'columns'        => $filter->settings['columns'],
                'columns_tablet' => $filter->settings['columns_tablet'],
                'columns_mobile' => $filter->settings['columns_mobile'],
            ]) + $params;

            $filter->settings += [
                'container_attrs'    => $this->customizer_element_params(['filter', 'filter_'.$filter_name], $params, 'attrs'),
                'label_attrs'        => $this->customizer_element_params(['filter_label', 'filter_'.$filter_name.'_label'], [], 'attrs'),
                'control_attrs'      => $this->customizer_element_params(['filter_control', 'filter_'.$filter_name.'_control'], [], 'attrs'),
                'control_item_attrs' => $this->customizer_element_params(['filter_control_item', 'filter_'.$filter_name.'_control_item'], [], 'attrs'),
            ];

            return $filter->render();
        }
    }

    function render_filters_items($params) {

        $output = '';

        $params = $this->customizer_element_params('filters_items', $params);

        foreach ($this->host->filters as $filter_name => $filter)
        {
            $output .= $this->render_filter($this->get_children_element_params(['name'=>$filter_name], $params));
        }

        return $this->render_element_wrapper('filters', $output, $params);
    }

    function get_template()
    {
        return <<<EOT

        <div {{container.attrs}}>
                                          
            {{filters_items is_grid="1"}}
                        
        </div>

EOT;
    }
}