<?php

namespace SM_Site_Content\Module\Hub;

use SM\Common;

class Module extends Common\Module
{
    static function info()
    {
        return array(
            'title' => 'Хаб',
            'classes_path' => __DIR__ . '/classes/*',
            'classes_map' => []
        );
    }

    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');

        add_action('wp_insert_post', function ($postID) {

            $post = get_post($postID);

            if ($post->post_type === 'sm-hub-post') {

                $hubTerms = wp_get_object_terms($post->ID, 'sm-hub-term');

                if (empty($hubTerms)) {

                    $createdTerm = wp_insert_term($post->post_title, 'sm-hub-term');

                    if (!is_wp_error($createdTerm))
                        wp_set_object_terms($postID, $createdTerm['term_id'], 'sm-hub-term');
                }
            }

        }, 999, 3);
    }

    function _filter_sm_context_info(&$contexts)
    {
        $contexts['post_term_sm_hub'] = array(
            'condition' => function () {
                return is_object_in_term(get_the_ID(), 'sm-hub-term');
            },
        );

        return $contexts;
    }

    function _filter_sm_entity_bundles($bundles)
    {

        return $bundles + $this->sm_class_set([
                'term:sm-hub-type' => array(
                    'label' => 'Типы хабов',
                    'labels' => array('singular_name' => 'Тип хаба'),
                    'object_type' => array('sm-hub-post'),
                    'show_admin_column' => true,
                    'hierarchical' => true,
                    'register' => true,
                ),
                'post:sm-hub-post' => array(
                    'label' => 'Хабы',
                    'labels' => array('singular_name' => 'Хаб'),
                    'register' => true,
                    'public' => true,
                    'hierarchical' => false,
                    'has_archive' => true,
                    'show_in_menu' => true,
                    'supports' => array('title', 'editor', 'thumbnail'),
                    'hierarchical' => true,
                    'menu_position' => 4
                ),
                'term:sm-hub-term' => array(
                    'label' => 'Хабы - термы',
                    'labels' => array('singular_name' => 'Хаб - терм'),
                    'object_type' => array('sm-hub-post', 'post'),
                    'show_admin_column' => true,
                    'hierarchical' => true,
                    'register' => true,
                ),
            ]);
    }

}



