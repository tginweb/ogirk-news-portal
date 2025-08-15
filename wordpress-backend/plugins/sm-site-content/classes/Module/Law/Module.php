<?php



namespace SM_Site_Content\Module\Law;

use SM\Common;

class Module extends Common\Module
{

    var $info = array(
        'title'             => 'Smart DOC',
        'description'       => '',
        'path'              => __DIR__,
    );

    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');

    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([
            'page_sd_law_doc'  => ['condition'  => function() { return is_singular('sd-law-doc');  }],
            'page_sd_law_docs' => ['condition'  => function() { return is_post_type_archive('sd-law-doc'); }]
        ]);
    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
            'term:sm-law-doc-cat' => array(
                'label_single'      => 'Рубрика документа',
                'label_plural'      => 'Рубрики документа',
                'object_type'       => array('sd-doc'),
                'meta_box_cb'       => false,
                'register'          => true,
            ),
            'post:sm-law-doc' => array(
                'label_single'      => 'Нормативный документ',
                'label_plural'      => 'Нормативные документы',
                'register'          => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'rewrite'           => true,
                'capability_type'   => 'sd-doc',
                'map_meta_cap'      => true,
                'grant_admin_access'=> true,
                'can_be_current'    => true,
                'hierarchical'      => false,
                'supports'          => array('title'),
                'menu_position'     => 4
            ),
        ]);
    }
}




