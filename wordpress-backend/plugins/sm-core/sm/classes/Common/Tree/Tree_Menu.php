<?php

// catalog/category
namespace SM\Common\Tree;

class Tree_Menu extends Tree_Base
{
    function options()
    {
        return array(
            'prop_id'          => 'ID',
            'prop_parent_id'   => 'menu_item_parent',
            'prop_weight'      => 'menu_order',
            'prop_label'       => 'title'
        );
    }

    static function full_tree()
    {
        $items = array();

        foreach (wp_get_nav_menus() as $menu)
        {
            $items[] = array('ID'=>'menu:'.$menu->term_id, 'title'=>$menu->name);

            $menu_items = wp_get_nav_menu_items($menu->term_id);

            foreach ($menu_items as &$item)
            {
                if (!$item->menu_item_parent) $item->menu_item_parent = 'menu:'.$menu->term_id;
            }

            $items = array_merge($items, $menu_items);
        }

        $tree = new sm_tree_wp_menu();

        $tree->from_flat_array($items);

        return $tree;
    }
}