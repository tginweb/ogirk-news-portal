<?php

namespace SM\Module\Acf;

use SM\Common;
use SM\Util;

class Module extends Common\Module
{
    static function info() {
        return array(
            'title'        => 'Smart Acf',
            'description'  => '',
        );
    }

    function init_events()
    {
        add_action('acf/include_field_types', array($this,'_action_acf_include_field_types'));

        /*
        add_filter('acf/update_value/name=sm_date', array($this, '_filter_acf_update_value_sm_date'));

        add_filter('acf/update_value/name=sm_title', array($this, '_filter_acf_update_value_sm_title'));

        add_action('wp_insert_post_data', array($this, '_action_wp_insert_post_data'), 10, 2);
        */
    }

    function _action_acf_include_field_types()
    {
        Util\File::include_dir(__DIR__.'/fields');

        /*
        $types = sm()->fields()->get_fields();

        foreach ($types as $class=>$field)
        {
            if ($field->sm_class_info('acf')) new sm_field_acf_proxy($field);
        }
        */
    }

    function _filter_acf_update_value_sm_date($value, $post_id, $field)
    {
        $post = get_post($post_id);

        $post->sm_date = $value;

        wp_update_post($post);

        return $value;
    }

    function _filter_acf_update_value_sm_title($value, $post_id, $field)
    {
        $post = get_post($post_id);

        $post->sm_title = $value;

        wp_update_post($post);

        return $value;
    }

    function _action_wp_insert_post_data($data, $postarr)
    {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return $data;

        $post_date = isset($data['sm_date']) ? $data['sm_date'] : get_field('sm_date', $data['ID']);

        $post_title = isset($data['sm_title']) ? $data['sm_title'] : get_field('sm_title', $data['ID']);

        if ($post_date)
        {
            $data['post_date']  = gmdate('Y-m-d H:i:s', strtotime($post_date));
        }

        if ($post_title)
        {
            $data['post_title'] = $post_title;
        }

        return $data;
    }
}



