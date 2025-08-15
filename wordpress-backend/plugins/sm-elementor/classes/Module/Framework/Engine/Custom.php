<?php


namespace SM_Elementor\Module\Framework\Engine;

use SM\Assets;

class Custom extends Bootstrap4
{
    var $class_prefix = 'sm-grid-';


    function enqueue_grid($enqueue_style)
    {
        if ($enqueue_style==='inline')
        {
            Assets::i()->wp_enqueue(['sm_elementor.framework.custom.grid'=>['code'=>'<style>'.$this->get_style_css().'</style>']], ['action_type'=>'head']);
        }
        else if ($enqueue_style=='css')
        {
            Assets::i()->wp_enqueue(['sm_elementor.framework.custom.grid']);
        }
    }

}
