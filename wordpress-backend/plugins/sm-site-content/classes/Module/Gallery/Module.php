<?php


namespace SM_Site_Content\Module\Module;

use SM\Common;

class Module extends Common\Module
{

    function init_events()
    {
        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');
    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([
            'page_sm_gallery'   => ['condition'  => function() { return is_singular('sm-gallery'); }],
            'page_sm_galleries' => ['condition'  => function() { return is_post_type_archive('sm-gallery'); }]
        ]);
    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
            'post:sm-gallery' => array(
                'label_single' => 'Фоторепораж',
                'label_plural' => 'Фоторепоражи',
                'register' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'rewrite' => true,
                'capability_type' => 'sm-gallery',
                'map_meta_cap' => true,
                'hierarchical' => false,
                'supports' => array('title', 'editor', 'thumbnail'),
                'menu_position' => 4
            ),
        ]);
    }
}









