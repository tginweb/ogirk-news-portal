<?php


namespace SM;

use SM\Cache;
use SM\Util;

class Router extends Common\Component
{
    public $menu_loaded = array();
    public $menu_items = array();

    public $menu_active_item = null;
    public $menu_active_path_item = null;

    /* @return Router */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {
        parent::init_events();
    }

    static function params_defaults()
    {
        return array(
            'router_menu_location'          => null,
            'router_menu'                   => null,
            'router_menu_current_item_id'   => null,
            'router_menu_current_post'      => null,
            'router_menu_current_url'       => null,
        );
    }

    function find_menu_currents($menus=[])
    {
        $result = [];

        $this->build_menus($menus);

        foreach ($this->menu_items as $menu_item)
        {
            if ($menu_item->current) $result['current'] = $menu_item;
            if ($menu_item->current_item_ancestor) $result['current_item_ancestor']= $menu_item;
            if ($menu_item->current_item_parent) $result['current_item_parent']= $menu_item;
        }

        return $result;
    }

    function build_menus($menus)
    {
        if ($this->builded) return $this;

        $menus = array();

        $sys_menus = get_terms('nav_menu');

        foreach($sys_menus as $menu)
        {
            $menus[] = $menu->term_id;
        }

        $this->fill_menu_items($menus);

        $this->builded = true;

        return $this;
    }

    function fill_menu_items($menus=array())
    {
        foreach ($menus as $menu)
        {
            if ($menu_object = wp_get_nav_menu_object($menu))
            {
                $mid = $menu_object->term_id;

                if (!isset($this->menu_loaded[$mid]))
                {
                    $this->menu_loaded[$mid] = $mid;

                    $items = $this->get_menu_items($menu_object->term_id);

                    _wp_menu_item_classes_by_context($items);

                    foreach ($items as $item) $this->menu_items[$item->ID] = $item;
                }
            }
        }
    }

    function get_menu_items($term_id, $args =array())
    {
        $items = wp_get_nav_menu_items( $term_id, $args );

        return $items;

        $cid = 'wp_get_nav_menu_items:'.$term_id.':'.serialize(array($term_id, $args));

        if ($cache = Cache::i()->get($cid, 'system'))
        {
            $items = $cache;
        }
        else
        {
            $items = wp_get_nav_menu_items( $term_id, $args );

            Cache::i()->set($cid, $items, 'system');
        }

        return $items;
    }

    function get_menu_active_path()        { return $this->menu_active_item; }

    function get_menu_active_item()        { return $this->menu_active_item; }

    function get_menu_active_item_id()     { return $this->get_menu_active_item() ? $this->get_menu_active_item()->ID : null; }

    function get_menu_active_item_title()  { return $this->get_menu_active_item() ? $this->get_menu_active_item()->title : null; }

    function get_menu_active_path_item()   { return $this->menu_active_path_item; }

    function get_menu_active_item_parent()
    {
        return $this->get_item_parent($this->get_menu_active_item());
    }

    function get_menu_active_item_root()
    {
        $path = $this->get_menu_active_item_trail();

        if (!empty($path)) return $path[0];
    }

    function get_menu_active_item_trail($include_current=true)
    {
        if (!isset($this->menu_active_item_trail))
        {
            $this->menu_active_item_trail = array();

            $menu_item = $current_menu_item = $this->get_menu_active_item();

            $i = 0;

            while ($menu_item = $this->get_item_parent($menu_item))
            {
                $menu_item->menu_breadcrumb_level = $i++;

                $this->menu_active_item_trail[$menu_item->ID] = $menu_item;
            }

            if ($include_current) $this->menu_active_item_trail[$current_menu_item->ID] = $current_menu_item;
        }

        return $this->menu_active_item_trail;
    }

    function get_item_menu_term($item)
    {
        if ($found_menu = wp_get_post_terms($item->ID, 'nav_menu'))
        {
            return current($found_menu);
        }
    }

    function get_menu_active_submenu($args=array())
    {
        return $this->get_current_branch_items($args);
    }

    function get_menu_active_branch_items($args=array())
    {
        $branch_items = array();

        $active_item = $this->get_menu_active_item();

        $child_items = $this->get_item_children($active_item, $args);

        if (!empty($child_items))
        {
            //$branch_items[] = $active_item;
            $branch_items = array_merge($branch_items, $child_items);
        }
        else
        {
            $branch_items = $this->get_item_siblings_no_root($active_item, $args);
        }

        return $branch_items;
    }

    function get_menu_shortcode_branch()
    {
        return $this->get_menu_active_item();
    }

    function find_active_menu_item()
    {
        if (empty($this->menu_items)) return;

        foreach ($this->menu_items as $menu_item)
        {
            if ($menu_item->current)
            {
                return $menu_item;
            }
        }
    }



    function find_menu_item_by_url($find_url=null, $path_only=false)
    {
        if (empty($this->menu_items)) return;

        $find_url = Util\Base::url_to_compare($find_url, $path_only);
        $site_url = Util\Base::url_to_compare(WP_SITEURL);

        $found_menu_item = null;

        foreach ( $this->menu_items as $menu_item )
        {
            $menu_item_url = Util\Base::url_to_compare($menu_item->url, $path_only);

            if ($menu_item_url==$find_url || $menu_item_url==$site_url.'/'.$find_url)
            {
                $found_menu_item = $menu_item;
                //break;
            }

            if ($menu_item->current) $found_menu_item = $menu_item;
        }

        return $found_menu_item;
    }


    function get_item_parent($menu_item)
    {
        if (!$menu_item || empty($this->menu_items) || !$menu_item->menu_item_parent) return null;

        return $this->menu_items[$menu_item->menu_item_parent];
    }

    function get_item_children($menu_item, $args=array())
    {
        $items = array();

        foreach ($this->menu_items as $item) if ($item->menu_item_parent==$menu_item->ID) $items[] = $item;
        return $items;
    }

    function get_item_siblings_no_root($menu_item, $args=array())
    {
        $items = array();
        if ($menu_item->menu_item_parent)
        {
            foreach ($this->menu_items as $item)
                if ($item->menu_item_parent==$menu_item->menu_item_parent) $items[] = $item;
        }
        return $items;
    }

    function reset()
    {
        $this->menu_items = array();
        $this->menu_active_item = null;
    }

}


