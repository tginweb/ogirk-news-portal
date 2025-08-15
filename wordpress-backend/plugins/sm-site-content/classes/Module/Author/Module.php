<?php


namespace SM_Site_Content\Module\Author;

use SM\Common;

class Module extends Common\Module
{
    function init_events()
    {
        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');
    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
            'term:sm-author' => [
                'label' => 'Автор',
                'labels' => 'Авторы',
                'object_type' => array('post', 'attachment'),
                'public' => true,
                'show_admin_column' => true,
                'register' => true,
            ]
        ]);
    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([
            'page_sm_author' => ['condition' => function() { return is_tax('sm-author'); } ]
        ]);
    }
}

