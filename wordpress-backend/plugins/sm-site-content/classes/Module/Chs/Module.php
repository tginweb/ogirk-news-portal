<?php

namespace SM_Site_Content\Module\Chs;

use SM\Common;

class Module extends Common\Module
{
    static function info()
    {
        return array(
            'title'        => 'ЧС',
            'classes_path' => __DIR__.'/classes/*',
            'classes_map'  => []
        );
    }

    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');
    }

    function _filter_sm_context_info(&$contexts)
    {


        return $contexts;
    }

    function _filter_sm_entity_bundles($bundles)
    {

        return $bundles + $this->sm_class_set([

            'post:sm-chs-qa' => array(
                'label'             => 'Вопрос-ответ',
                'labels'            => array('singular_name'=>'Вопрос-ответ'),
                'register'          => true,
                'public'            => true,
                'hierarchical'      => false,
                'has_archive'       => true,
                'show_in_menu'      => true,
                'supports'          => array('thumbnail'),
                'hierarchical'      => true,
                'menu_position'     => 4
            ),

            'term:sm-chs-qa-cat' => array(
                'label'             => 'Вопрос-ответ - категории',
                'labels'            => array('singular_name'=>'Вопрос-ответ - категории'),
                'object_type'       => array('sm-chs-qa'),
                'show_admin_column' => true,
                'hierarchical'      => true,
                'register'          => true,
            ),
        ]);
    }

}



