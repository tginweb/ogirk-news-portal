<?php

namespace SM;

use SM\Compiler;

class Shortcodes extends Common\Component
{
    /* @return Shortcodes */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm/shortcode/preprocess', null, 10, 2);

    }


    function _filter_sm_shortcode_preprocess($params, $content=null, $tag=null)
    {
        if (!empty($params['_shortcode_prepocessed'])) return $params;


        if (!empty($params['showif']) && !Compiler::i()->compile_condition(html_entity_decode($params['showif']))) $params['hidden'] = true;

        if (!empty($params['hideif']) && Compiler::i()->compile_condition(html_entity_decode($params['hideif']))) $params['hidden'] = true;


        $params['_shortcode_prepocessed'] = true;

        return $params;
    }
}

