<?php


namespace SM_Elementor\Module\ThirdParty\ElementorPro;


class Module extends \SM_Elementor\Common\Plugin_Module
{
    static function info()
    {
        return [
            'classmap' => [
                'widget' => [
                   // 'SM_Elementor\Module\ThirdParty\ElementorPro\Widget\Nav_Menu' => array('init'=>true),
                ],
            ]
        ];
    }


    function _action_elementor_widgets_widgets_registered($widgets_manager)
    {

        $widgets_manager->register_widget_type(new Widget\Nav_Menu_Override());
    }
}



