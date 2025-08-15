<?php


namespace SM\Query;

use SM\Common;

class Collector extends Common\Component
{

    var $collectors = array();
    var $collector_listen = null;

    /**
     * @return Collector
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {
        if (!is_admin())
        {
            add_action('init', array($this, '_action_init_frontend'));
            //add_action('sm/cache/output', array($this, '_action_sm_cache_output'));
            add_filter('the_posts', array($this, '_filter_the_posts'), 10, 2);
        }

        $this->add_filter('pre_get_posts', null, 1000);
    }

    function _action_init_frontend()
    {
        add_rewrite_tag('%collector%', '([\w\_]+)');
    }

    function _filter_the_posts($posts, $query)
    {
        if (isset($query->query_vars['collector']) && ($collector = $query->query_vars['collector']))
        {
            foreach ($posts as $post)
            {
                $this->add_items(array($post->ID), $collector);
            }
        }
        return $posts;
    }

    function _filter_pre_get_posts(&$query)
    {
        $collector_exclude = !empty($query->query_vars['collector_exclude']) ? $query->query_vars['collector_exclude'] : null;

        if ($query->is_main_query() || $collector_exclude)
        {
            if (($exclude_ids = $this->get_items($collector_exclude)) && !empty($exclude_ids))
            {
                $query->query_vars['post__not_in'] = array_merge((array)$this->query_vars['post__not_in'], $exclude_ids);
            }
        }

        return $query;
    }

    function get_items($entity_type='post', $name='default')
    {
        return isset($this->collectors[$name][$entity_type]) ? $this->collectors[$name][$entity_type] : null;
    }

    function add_items($ids=array(), $entity_type='post', $name='default')
    {
        foreach ((array)$ids as $id)
        {
            $this->collectors[$name][$entity_type][$id] = $id;
        }
    }
}

