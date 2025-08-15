<?php

namespace SM\Query;

use SM;
use SM\Common;
use SM\Cache;

class Archive extends Common\Component
{
    /**
     * @return Archive
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {
        parent::init_events();

        add_filter('getarchives_where', array($this, '_filter_getarchives_where'), 10, 2);
        add_filter('getarchives_join', array($this, '_filter_getarchives_join'), 10, 2);
    }

    function getarchives_helper_query($qv)
    {
        $query = new \WP_Query();

        $query->init();

        $query->query = $query->query_vars = wp_parse_args( $qv );

        $query->parse_tax_query($qv);

        return $query;
    }

    function _filter_getarchives_join($sql, $r)
    {
        global $wpdb;

        if (!empty($r['query_vars']))
        {
            $qv = $r['query_vars'];

            $query = $this->getarchives_helper_query($qv);

            $tax_sql = $query->tax_query->get_sql( $wpdb->posts, 'ID' );

            if ($tax_sql['join'])
            {
                $sql .= $tax_sql['join'];
            }
        }

        return $sql;
    }

    function _filter_getarchives_where($sql, $r)
    {
        global $wpdb;

        if (!empty($r['query_vars']))
        {
            $qv = $r['query_vars'];

            $query = $this->getarchives_helper_query($qv);

            $tax_sql = $query->tax_query->get_sql( $wpdb->posts, 'ID' );

            if ($tax_sql['where'])
            {
                $sql .= $tax_sql['where'];
            }

            if ($qv['monthnum'])
            {
                $sql .= ' AND MONTH(post_date)='.ltrim($qv['monthnum'], 0).' ';
            }

            if ($qv['year'])
            {
                $sql .= ' AND YEAR(post_date)='.ltrim($qv['year'], 0).' ';
            }

            if ($qv['s'])
            {
                $qv['s'] = urldecode($qv['s']);

                $sql .= $query->parse_search($qv);
            }

        }

        return $sql;
    }

    function get_archives( $args = '' )
    {
        global $wpdb, $wp_locale;

        $defaults = array(
            'type'            => 'monthly',
            'query_vars'      => null,
            'limit'           => '',
            'format'          => 'html',
            'before'          => '',
            'after'           => '',
            'show_post_count' => false,
            'order'           => 'DESC',
            'year'            => null,
            'month'           => null,
        );

        $r = wp_parse_args( $args, $defaults );

        $items = array();


        if ( ! empty( $r['limit'] ) )
        {
            $r['limit'] = absint( $r['limit'] );
            $r['limit'] = ' LIMIT ' . $r['limit'];
        }

        $order = strtoupper( $r['order'] );

        if ( $order !== 'ASC' )
        {
            $order = 'DESC';
        }

        //fb($r);

        //$sql_where = $wpdb->prepare( "WHERE post_type = %s AND post_status = 'publish'", $r['post_type'] );

        if ($r['post_type'])
            $sql_where = $wpdb->prepare( "WHERE post_type = %s AND post_status = 'publish'", $r['post_type'] );
        else
            $sql_where = $wpdb->prepare( "WHERE post_status = 'publish'", []);


        $where = apply_filters( 'getarchives_where', $sql_where, $r );

        $join = apply_filters( 'getarchives_join', '', $r );

        $limit = $r['limit'];



        if ( 'monthly' == $r['type'] )
        {
            $query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date $order $limit";


            $key = 'wp_get_archives:'.md5($query);


            $results = $wpdb->get_results( $query );

            /*
            if ( ! $results = Cache::i()->get($key, 'posts') )
            {

                Cache::i()->set($key, $results, 'posts');
            }
            */

            if ( $results )
            {
                foreach ( (array) $results as $result )
                {
                    $item = array();
                    $item['count'] = $result->posts;
                    $item['year']  = $result->year;
                    $item['month'] = $result->month;

                    $items[$result->year][$result->month] = $item;
                }
            }
        }
        elseif ( 'yearly' == $r['type'] )
        {
            $query = "SELECT YEAR(post_date) AS `year`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date) ORDER BY post_date $order $limit";

            $key = 'wp_get_archives:'.md5($query);

            $results = $wpdb->get_results( $query );

            /*
            if ( ! $results = Cache::i()->get($key, 'posts') )
            {

                Cache::i()->set($key, $results, 'posts', 24*3600*360);
            }
            */

            if ( $results )
            {
                foreach ( (array) $results as $result)
                {
                    $item = array();
                    $item['count'] = $result->posts;
                    $item['year']  = $result->year;

                    $items[$result->year] = $item;
                }
            }
        }
        elseif ( 'daily' == $r['type'] )
        {
            $query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date $order $limit";

            $results = $wpdb->get_results( $query );


            /*
            $key = 'wp_get_archives:'.md5($query);

            if (! $results = Cache::i()->get($key, 'posts') )
            {

                Cache::i()->set($key, $results, 'posts');
            }
            */

            if ( $results )
            {
                foreach ( (array) $results as $result )
                {
                    $item = array();
                    $item['count'] = $result->posts;
                    $item['year']  = $result->year;
                    $item['month'] = $result->month;
                    $item['day']   = $result->dayofmonth;

                    $items[$result->year][$result->month][$result->dayofmonth] = $item;
                }
            }
        }

        return $items;
    }
}


