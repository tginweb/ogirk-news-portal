<?php


namespace SM_Site_Content\Module\Story;

use SM\Common;

class Module extends Common\Module
{
    function init_events()
    {
        parent::init_events();
        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');


        register_post_type( 'my_custom_post_type',
            array(
                'labels' => array( 'name' => __( 'Products' ) ),
                'public' => true
            )
        );
    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([
            'page_sm_story'  => ['condition'  => function() { return is_singular('sm-story');  }],
            'page_sm_stories' => ['condition'  => function() { return is_post_type_archive('sm-story'); }]
        ]);
    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
                'term:sm-story' => array(
                    'label'         => 'Сюжет',
                    'labels'        => array('singular_name'=>'Сюжет'),
                    'object_type'   => array('post'),
                    'public'        => true,
                    'entity_fields' => [
                        'entity_date' => true,
                        'entity_status' => true,
                    ],
                    'register'      => true,
                ),
            ]);
    }
}

