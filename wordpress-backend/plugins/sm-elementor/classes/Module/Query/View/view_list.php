<?php

namespace SM_Elementor\Module\Query\View;

class view_list extends Common\View_List {

    function get_inner_template()
    {
        $row_class = $this->get_grid_row_class();

        return <<<EOT
        
        <div class="q-regions">
        
            {{region_1}}

        </div>
EOT;
    }

    function render_region_1($params)
    {
        $params = $this->customizer_element_params('region_1', $params) + [

        ];

        $def_region_limit = round(count($this->entities) / 1);

        return $this->get_region(1, 'module_1', $this->shift_region_entities(1, $def_region_limit), $params);
    }
}