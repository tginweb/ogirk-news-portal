<?php

namespace SM_Elementor\Module\Query\View;

use Elementor\Controls_Manager;

class view_player extends view_grid_r2 {

    static function info()
    {
        return [
            'regions_count' => 2
        ] + parent::info();
    }

    function shift_region_entities($region='1', $default_limit=null)
    {
        return $this->entities;
    }

    function get_module_render_params($module_entity, $region_id, $module_params=[])
    {
        $module_params = parent::get_module_render_params($module_entity, $region_id, $module_params);

        if ($region_id==1)
        {
            if ($this->index[1]>1)
            {
                $module_params['attrs']['style']['display'] = 'none';
	            $module_params['lazyload'] = 1;
            }
            else
            {

            }
        }

        return $module_params;
    }

}