<?php

namespace SM_Elementor\Module\Query\View\Common;

abstract class View_List extends Base {

    static function info()
    {
        return parent::info() + [

        ];
    }

    function get_render_params()
    {
        return [
            'region_view' => 'block',
            'modules_view' => 'list',
        ];
    }
}