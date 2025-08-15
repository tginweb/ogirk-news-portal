<?php

namespace SM\Entity\Controller;

use SM\Common;
use SM\Util;

class Comment extends Base
{
    var $entity_type     = 'comment';
    var $support_bundles = false;
    var $field_name_slug = '';
    var $field_name_id   = 'comment_ID';
    var $field_bundle    = 'comment_type';

    function get_bundle_label($type='name')
    {
        return __('Comment');
    }

    public function entity_wp_load($id = null, $output = OBJECT, $filter = 'raw' )
    {
        return get_comment($id);
    }

    public function entity_wp_load_by($by, $value = null )
    {
        switch ($by)
        {
            default:
                        $value = intval($value);
                        return get_comment($value);
        }
    }

    public function get_pagination_query($query=array())
    {
        $pager = $this->get_pagination_info($query);

        $query['offset'] = $pager['offset'];

        return $query;
    }

    public function get_pagination_links($query=array(), $args=array())
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

    public function get_pagination_info($query=array())
    {
        $query += array(
            'number' => 20
        );

        $cid = md5($query);


        $info['page']       = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $info['offset']     = ( $info['page'] > 0 ) ?  $query['number'] * ( $info['page'] - 1 ) : 1;

        $info['total']      = $this->select_count($query);

        $info['totalpages'] = ceil( $info['total'] / $query['number'] );

        return $info;
    }


    public function select_count($query=array())
    {
        return 0;
    }

    public function select($query=array(), $cache=false)
    {
        $defaults = array(
            'number'     => '',
            'hide_empty' => false,
            'taxonomy'   => $this->entity_bundle
        );

        $query = wp_parse_args( $query, $defaults );

        return get_comments($query);
    }

    public function select_tree($query=array())
    {

    }

    function get_count()
    {
        global $wpdb;

        return $wpdb->get_results("SELECT COUNT(*) as cnt FROM wp_comments WHERE",OBJECT)->cnt;
    }
}





