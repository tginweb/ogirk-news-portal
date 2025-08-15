<?php


namespace SM\Entity\Controller;

use SM\Common;
use SM\Util;

class Post extends Base
{
    var $entity_type     ='post';
    var $support_bundles = true;
    var $field_name_slug = 'post_name';
    var $field_name_id   = 'ID';
    var $field_bundle    = 'post_type';

    function register_wp_bundle($args=array())
    {

        if (isset($args['grant_admin_access']) && $args['grant_admin_access'] && is_admin())
        {
           unset($args['capability_type']);
           unset($args['map_meta_cap']);
        }

        register_post_type($this->entity_bundle, $args);
    }

    function get_wp_bundle()
    {
        return get_post_type_object($this->entity_bundle);
    }

    function entity_wp_load($id = null, $output = OBJECT, $filter = 'raw' )
    {
        return get_post($id, $output, $filter);
    }

    function entity_wp_load_by($by, $value = null )
    {
        switch ($by)
        {
            case F_ID:     return get_post($value); break;

            case F_SLUG:   $rows = get_posts(array('name' => $value, 'post_type' => $this->entity_bundle, 'posts_per_page' => 1));
                           return !empty($rows) ? current($rows) : null;
                           break;
        }
    }

    function get_bundle($id)    { return get_post_type($id); }

    function get_bundles($args = array(), $output='names') { return get_post_types($args, $output); }


    function select($args=array(), $cache=false)
    {
        if ($cache)
        {
            $cid = serialize($args);

            if (isset($this->sm_cache[$cid])) return $this->sm_cache[$cid];
        }

        $results = Entity::i()->load_multiple('post', get_posts($args));

        if ($cache) $this->sm_cache[$cid] = $results;

        return $results;
    }

    function select_tree($query=array())
    {
        $defaults = array(
            'numberposts' => -1,
            'post_type'   => $this->entity_bundle
        );

        $query = wp_parse_args( $query, $defaults );

        $entities = $this->select($query);

        foreach ($entities as $id=>&$entity)
        {
            if ($entity->post_parent && !empty($entities[$entity->post_parent]))
            {
                if (empty($entities[$entity->post_parent]->children))
                {
                    $entities[$entity->post_parent]->children = array();
                }

                $entities[$entity->post_parent]->children[$id] = $entity;

                unset($entities[$id]);
            }
        }

        return $entities;
    }

    function get_select_options($query=array(), $prepend=array())
    {
        $result = $prepend;
        foreach ($this->select($query) as $item) { $result[$item->ID] = $item->post_title; }
        return $result;
    }

    function get_acf_location_query()
    {
        return array('post_type'=>$this->entity_bundle);
    }

    function get_count()
    {
        global $wpdb;

        return $wpdb->get_results("SELECT COUNT(*) as cnt FROM wp_posts WHERE post_type='".$this->entity_bundle."' ",OBJECT)->cnt;
    }

    function check_save_entity_form($entity)
    {
        if (!isset($_POST['original_publish']) && !isset($_POST['save_post'])) return;

        if (!current_user_can('edit_post', $entity->ID)) return;

        if($entity->post_status == 'auto-draft') return;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        return true;
    }
}

