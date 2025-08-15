<?php

namespace SM_Site_Content\Module\Edu;

use SM\Common;

class Module extends Common\Module
{
    static function info()
    {
        return array(
            'title' => 'Образование',
            'classes_path' => __DIR__ . '/classes/*',
            'classes_map' => []
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

             /*
                'post:sm-edu-qa' => array(
                    'label' => 'Вопрос-ответ: Образование',
                    'labels' => array('singular_name' => 'Вопрос-ответ: Образование'),
                    'register' => true,
                    'public' => true,
                    'hierarchical' => false,
                    'has_archive' => true,
                    'show_in_menu' => true,
                    'supports' => array('thumbnail'),
                    'hierarchical' => true,
                    'menu_position' => 4
                ),

                'term:sm-edu-qa-cat' => array(
                    'label' => 'Вопрос-ответ - категории: Образование',
                    'labels' => array('singular_name' => 'Вопрос-ответ - категории: Образование'),
                    'object_type' => array('sm-edu-qa'),
                    'show_admin_column' => true,
                    'hierarchical' => true,
                    'register' => true,
                ),


                'post:sm-socfond-qa' => array(
                    'label' => 'Вопрос-ответ: Соцфонд',
                    'labels' => array('singular_name' => 'Вопрос-ответ: Соцфонд'),
                    'register' => true,
                    'public' => true,
                    'has_archive' => true,
                    'show_in_menu' => true,
                    'supports' => array('thumbnail'),
                    'hierarchical' => true,
                    'menu_position' => 4
                ),

                'term:sm-socfond-qa-cat' => array(
                    'label' => 'Вопрос-ответ - категории: Соцфонд',
                    'labels' => array('singular_name' => 'Вопрос-ответ - категории: Соцфонд'),
                    'object_type' => array('sm-socfond-qa'),
                    'show_admin_column' => true,
                    'hierarchical' => true,
                    'register' => true,
                ),
 */

            ]);


    }

}



