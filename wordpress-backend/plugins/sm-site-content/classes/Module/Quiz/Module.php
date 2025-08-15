<?php

namespace SM_Site_Content\Module\Quiz;

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

            ]);
    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
                'post:sm-quiz' => array(
                    'label'             => 'Квизы',
                    'labels'            => array('singular_name'=>'Квиз'),
                    'register'          => true,
                    'public'            => true,
                    'has_archive'       => true,
                    'hierarchical'      => false,
                    'supports'          => array('title','editor','excerpt','thumbnail','comments'),
                ),
            ]);
    }
}

