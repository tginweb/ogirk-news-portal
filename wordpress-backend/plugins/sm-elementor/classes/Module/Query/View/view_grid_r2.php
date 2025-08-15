<?php

namespace SM_Elementor\Module\Query\View;

class view_grid_r2 extends Common\View_Grid {

    static function info()
    {
        return [
            'regions_count' => 2
        ] + parent::info();
    }


    function get_inner_template()
    {
        $row_class = $this->get_grid_row_class();

        return <<<EOT
        
        <div class="q-regions $row_class">
        
            {{region id="1" column_width="6" column_width_tablet="6" }}
            
            {{region id="2" column_width="6" column_width_tablet="6" }}
            

        </div>
EOT;


    }



}