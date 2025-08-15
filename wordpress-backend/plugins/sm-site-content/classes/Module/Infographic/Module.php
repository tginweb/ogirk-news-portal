<?php

namespace SM_Site_Content\Module\Infographic;

use SM\Common;

class Module extends Common\Module
{

    function assets()
    {
        $path = $this->get_path_rel();

        return [
            'sm_content_infographic.common' => [
                'css' => $path.'/assets/css/common.css',
            ],
        ];
    }

    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm/entity/bundles');
        $this->add_action('admin_menu');
    }


    function _action_admin_menu()
    {
        $td = $this->get_text_domain();

        if (current_user_can('manage_options'))
        {
            $this->add_submenu_page('sm-content', __('Инфографика', $td), __('Инфографика', $td), 'manage_options', 'edit.php?post_type=sm-infographic');
        }
    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
            'post:sm-infographic' => array(
                'label'             => 'Инфографика',
                'labels'            => array('singular_name'=>'Инфографика'),
                'register'          => true,
                'public'            => true,
                'hierarchical'      => false,
                'has_archive'       => true,
                'show_in_menu'      => false,
                'supports'          => array('title','editor','thumbnail','current'),
                'menu_position'     => 4
            ),
        ]);
    }

}



