<?php

namespace SM\Entity\Controller;

use SM\Common;
use SM\Util;

class User extends Base
{
    var $entity_type     = 'user';
    var $support_bundles = false;
    var $field_name_slug = 'user_login';
    var $field_name_id   = 'ID';
    var $field_bundle    = '';

    function get_bundle_label($type='name')
    {
        return __('User');
    }

    function entity_wp_load($id = null, $output = OBJECT, $filter = 'raw' )
    {
        return get_user_by('ID', $id);
    }

    function entity_wp_load_by($by, $value = null )
    {
        $rows = array();

        switch ($by)
        {
            case F_ID:     $rows = get_user_by('ID', $value); break;
            case F_SLUG:   $rows = get_user_by('slug', $value); break;
        }

        if (!empty($rows)) return current($rows);
    }



    function default_bundle_link($url, $entity)
    {
        if ( $this->entity_bundle == get_post_type($entity))
        {
            return '/'.$this->entity_bundle.'/'.$entity->ID;
        }
        return $url;
    }

    function select($query=array(), $cache=false)
    {
        $defaults = array(

        );

        $query = wp_parse_args( $query, $defaults );

        return get_users($query);
    }

    function get_select_options($query=array(), $prepend=array())
    {
        $result = $prepend;

        foreach ($this->select($query) as $item) { $result[$item->ID] = $item->post_title; }

        return $result;
    }

    function bulk_actions_entities_field() { return 'user'; }
}

