<?php

namespace SM_Elementor\Module\Query\View\Common;

abstract class View_Grid extends Base {

    static function info()
    {
        return [
            'support_regions_grid' => true,
        ] + parent::info();
    }

    function get_render_params()
    {
        return [
            'region_view' => 'column',
            'modules_view' => 'grid',
        ];
    }
}