<?php

namespace SM_Site_Content\Module\VDay;

use SM\Common;


class Module extends Common\Module
{
    function init_events()
    {
        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');
        add_action('rest_api_init', [$this, 'rest_api_init']);
    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([

            ]);
    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
                'post:sm-vday' => array(
                    'label'             => 'День победы',
                    'labels'            => array('singular_name'=>'День победы'),
                    'register'          => true,
                    'public'            => true,
                    'has_archive'       => true,
                    'hierarchical'      => false,
                    'supports'          => array('title','editor','excerpt','thumbnail','comments'),
                ),
            ]);
    }

    function rest_api_init()
    {
        register_rest_route('vday/v1', '/posts/', array(
                'methods' => 'GET',
                'callback' => [$this, 'get_posts'],
            )
        );
    }

    function get_posts()
    {
        $posts = get_posts(['post_type' => 'sm-vday', 'posts_per_page' => -1]);

        $result = [];

        foreach ($posts as $post) {

            $content = $post->post_content;

            $content = preg_replace('/[\n\r]*(\[(tooltip).*?\])[\n\r]*/', '$1', $content);
            $content = preg_replace('/[\n\r]*(\[\/(tooltip).*?\])[\n\r]*/', '$1', $content);

            $item = [];
            $item['id'] = $post->ID;
            $item['title'] = $post->post_title;
            $item['image'] = get_the_post_thumbnail_url($post, 'original');
            $item['content'] = apply_filters('the_content', $content);

            $result[] = $item;
        }

        return $result;
    }

}

