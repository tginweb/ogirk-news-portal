<?php

namespace SM\Entity\Controller;

use SM\Common;
use SM\Util;

class Term extends Base
{
    var $entity_type     = 'term';
    var $support_bundles = true;
    var $field_name_slug = 'slug';
    var $field_name_id   = 'term_id';
    var $field_bundle    = 'taxonomy';

    /**
     * @return Term
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function register_wp_bundle($args=array())
    {
        register_taxonomy($this->entity_bundle, $args['object_type'], $args);
    }

    function get_wp_bundle()
    {
        return get_taxonomy($this->entity_bundle);
    }

    function bundle_link_proxy($termlink, $term, $taxonomy)
    {
        if ($this->entity_bundle == $taxonomy)
        {
            $termlink = $this->bundle_link($termlink, $term, $taxonomy);
        }
        return $termlink;
    }


    function entity_wp_load($id = null, $output = OBJECT, $filter = 'raw' )
    {
        return get_term($id, $this->entity_bundle, $output, $filter);
    }

    function entity_wp_load_by($by, $value = null )
    {
        switch ($by)
        {
            case F_ID:     return get_term($value, $this->entity_bundle);

            case F_SLUG:   return get_term_by('slug', $value, $this->entity_bundle);

            case F_ID_OLD: return null; //todo

            default:       return get_term_by($by, $value, $this->entity_bundle);
        }
    }

    function get_bundle($id)   { return $GLOBALS['wpdb']->get_var("SELECT taxonomy FROM ".$GLOBALS['wpdb']->term_taxonomy." WHERE term_id = ".(is_object($id) ? $id->term_id : intval($id))); }

    function get_bundles($args = array(), $output='names') { return get_taxonomies($args, $output); }

    function query_wp_object()  { return new WP_Term_Query(); }

    function get_pagination_query($query=array())
    {
        $pager = $this->get_pagination_info($query);

        $query['offset'] = $pager['offset'];

        return $query;
    }

    function get_pagination_links($query=array(), $args=array())
    {
        $pager = $this->get_pagination_info($query);

        $args += array(
            'base'      => @add_query_arg('paged','%#%'),
            'format'    => '',
            'total'     => $pager['totalterms'],
            'current'   => $pager['page'],
            'show_all'  => false,
            'type1'      => 'array',
            'prev_next' => true,
            'prev_text' => __('«'),
            'next_text' => __('»')
        );

        return paginate_links( $args );
    }

    function get_pagination_info($query=array())
    {
        $query += array(
            'number' => 20
        );

        $info['page']       = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $info['offset']     = ( $info['page'] > 0 ) ?  $query['number'] * ( $info['page'] - 1 ) : 1;

        $info['total']      = $this->select_count($query);

        $info['totalpages'] = ceil( $info['total'] / $query['number'] );

        return $info;
    }

    function select_count($query=array())
    {
        return wp_count_terms( $this->entity_bundle, $query );
    }

    function select($args=array(), $cache=false)
    {
        if (!$args) $data = array();

        if ($cache)
        {
            $cid = serialize($data);

            if (isset($this->sm_cache[$cid])) return $this->sm_cache[$cid];
        }

        $results = get_terms($args);

        if ($cache) $this->sm_cache[$cid] = $results;

        return $results;
    }

    function select_tree($query=array())
    {
        $defaults = array(
            'number'     => '',
            'hide_empty' => false,
            'taxonomy'   => $this->entity_bundle
        );

        $query = wp_parse_args( $query, $defaults );

        return $this->select($query);
    }


    function get_select_options($query=array(), $prepend=array(), $field_key='term_id')
    {
        $result = $prepend;

        foreach ($this->select($query) as $item) $result[$item->$field_key] = $item->name;

        return $result;
    }

    function get_count()
    {
        global $wpdb;

        return $wpdb->get_results("SELECT COUNT(*) as cnt FROM wp_term_taxonomy WHERE taxonomy='".$this->entity_bundle."' ",OBJECT)->cnt;
    }
}



