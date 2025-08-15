<?php

namespace SM_Elementor\Module\Query\View;

use Elementor\Controls_Manager;

class view_map extends Common\View_Grid {

    static function info()
    {
        return [
            'regions_count' => 1
        ] + parent::info();
    }

    function get_template()
    {
        $inner_template = $this->get_inner_template();

        return <<<EOT

        <div {{container.attrs}}>

           {{filters once="1"}}   
                     
           {{map}}
            
           <div {{inner.attrs}}>
                                        
              $inner_template                                      
                                
           </div>
           
           {{pagination}}                      
          
        </div>

EOT;
    }

    function get_inner_template()
    {
        $row_class = $this->get_grid_row_class();

        return <<<EOT
        
        <div class="$row_class">
        
            {{region_1}}
            
            {{region_2}}

        </div>
EOT;
    }

    function render_region_1($params)
    {
        $params += [
            'column_width' => 6,
            'column_width_tablet' => 6,
        ];

        $def_region_limit = round(count($this->entities) / 2);

        return $this->get_region(1, 'module_1', $this->shift_region_entities(1, $def_region_limit), $params);
    }

    function render_region_2($params)
    {
        $params += [
            'column_width' => 6,
            'column_width_tablet' => 6,
        ];

        $def_region_limit = round(count($this->entities) / 2);

        return $this->get_region(2, 'module_3', $this->shift_region_entities(2, $def_region_limit), $params);
    }


    function register_settings_controls()
    {

        $this->add_control(
            'map_slides',
            [
                'label'     => 'Количество слайдов',
                'type'      => Controls_Manager::NUMBER,
                'default'   => 1
            ],
            true
        );


        $this->add_control(
            'map_thumbs',
            [
                'label'     => 'Миниатюры слайдов',
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no'
            ],
            true
        );

        $this->add_control(
            'map_thumbs_slides',
            [
                'label'     => 'Количество миниатюр',
                'type'      => Controls_Manager::NUMBER,
                'default'   => 5,
                'condition' => [
                    'slider_thumbs' => 'yes'
                ]
            ],
            true
        );

    }
}