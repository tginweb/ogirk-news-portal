<?php

namespace SM;

class Icon extends Common\Component
{
    var $icon_providers;

    /**
     * @return Icon
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function _filter_icon_providers($providers)
    {
        $providers += array(

            'semantic_ui' => array('css_processor' => function($file) { return SM_Icon::css_file_parse_icons($file, '/i\.icon\.([\-\w\d]+?)\:before/', array('class' => 1, 'name' => 1), 'icon '); } ),

            'flaticon'    => array('css_processor' => function($file) { return SM_Icon::css_file_parse_icons($file, '/\.(flaticon\-([^\:]+?))\:before/', array('class' => 1, 'name' => 2)); } ),

            'smarticon'   => array('css_processor' => function($file) { return SM_Icon::css_file_parse_icons($file, '/\.(smarticon\-([^\:]+?))\:before/', array('class' => 1, 'name' => 2)); } )

        );

        return $providers;
    }

    function _init_icon_providers()
    {
        if (!isset($this->icon_providers))
        {
            $this->icon_providers = apply_filters('sm/icon/providers', array());
        }
    }


    function extract_icons_from_set_info($info)
    {
        self::init_icon_providers();

        $icons = array();

        if (!($provider = self::$icon_providers[$info['provider']])) return $icons;

        if (!empty($info['css_file']))
        {
            $set_icons = $provider['css_processor']($info['css_file']);

            foreach ($set_icons as $icon_class=>$icon)
            {
                if (!isset($icon['group']))
                {
                    $icon['group'] = $info['group'];
                }

                $icons[$icon_class] = $icon;
            }
        }

        return $icons;
    }

    function css_file_parse_icons($file, $pattern, $schema=array(), $class_preffix='')
    {
        $items = array();

        if (file_exists($file) && ($content = file_get_contents($file)))
        {
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER))
            {
                foreach ($matches as $match)
                {
                    $item = array();

                    foreach ($schema as $item_key=>$item_val)
                    {
                        $item[$item_key] = is_numeric($item_val) ? $match[$item_val] : $item_val;
                    }

                    $icon_class = str_replace('.', ' ', $item['class']);

                    unset($item['class']);

                    $items[$class_preffix.$icon_class] = $item;
                }
            }
        }

        return $items;
    }
}



