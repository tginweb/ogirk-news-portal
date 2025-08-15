<?php

namespace SM_Site_Content;

use SM\Assets;
use SM\Common;

class Plugin extends Common\Module
{
    function init_events()
    {
        parent::init_events();

        $this->add_action('admin_menu');
        $this->add_filter('sm/entity/bundles');

        add_filter('manage_post_posts_columns', [$this, 'posts_columns']);
        add_action('manage_post_posts_custom_column', [$this, 'posts_column'], 10, 2);
    }

    function assets()
    {
        $path = $this->get_path_rel();

        return [
            'sm_site_content.backend' => [
                $path . '/assets/js/backend.js',
                $path . '/assets/css/backend.css',
            ],
        ];
    }


    function enqueue_assets()
    {

        if (is_admin()) {
            Assets::i()->wp_enqueue('sm_site_content.backend');
        } else {

        }
    }

    function posts_columns($columns)
    {
        $columns['views'] = 'Views';
        return $columns;
    }

    function posts_column($column, $post_id)
    {
        switch ($column) {

            case 'views' :
                print '<span class="sm-manage-pvc" data-entity-id="' . $post_id . '"></span>';
                break;

        }
    }

    function _action_admin_menu()
    {
        $td = $this->get_text_domain();
    }

    function _filter_sm_entity_bundles($bundles)
    {

        return $bundles + $this->sm_class_set([
                'term:sm-role' => [
                    'label' => 'Роли материала',
                    'labels' => array('singular_name' => 'Роль материала'),
                    'object_type' => array('post', 'sm-topic', 'sm-online', 'sm-note'),
                    'public' => true,
                    'show_admin_column' => true,
                    'register' => true,
                ]
            ]);
    }
}


