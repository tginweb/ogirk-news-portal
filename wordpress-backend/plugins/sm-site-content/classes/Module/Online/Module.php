<?php

namespace SM_Site_Content\Module\Online;

use SM\Common;

class Module extends Common\Module
{
    function init_events()
    {

        $this->add_action('init');

        $this->add_filter('sm/entity/bundles');

        $this->add_filter('acf/settings/load_json');
    }


    function _action_init()
    {
        $term = get_term_by('slug', 'online', 'sm-role');

        if (!$term)
        {
            wp_insert_term(
                'Онлайн',
                'sm-role',
                array(
                    'description'=> '',
                    'slug' => 'online',
                )
            );
        }
    }

    function _filter_acf_settings_load_json( $paths ) {


        // remove original path (optional)
        unset($paths[0]);

        // append path
        $paths[] = __DIR__ . '/Acf/json';

        // return
        return $paths;

    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
            'post:sm-online-note' => array(
                'label'             => 'Онлайн-заметки',
                'labels'            => array('singular_name'=>'Онлайн-заметка'),
                'register'          => true,
                'public'            => true,
                'has_archive'       => true,
                'hierarchical'      => false,
                'supports'          => array('title','editor','excerpt','thumbnail'),
            ),
        ]);
    }
}

