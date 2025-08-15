<?php

namespace SM;

class Menu extends Common\Component
{
    var $menu_item_additional_form;

    /* @return Menu */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {

        $this->add_filter( 'wp_nav_menu_objects', null, 10, 2);
    }

    function nav_menu_cache_tag_data( $args = array() )
    {
        static $menu_id_slugs = array();

        $defaults = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'container_id' => '', 'menu_class' => 'menu', 'menu_id' => '',
            'echo' => true, 'fallback_cb' => 'wp_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '', 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'item_spacing' => 'preserve',
            'depth' => 0, 'walker' => '', 'theme_location' => '' );

        $args = wp_parse_args( $args, $defaults );

        $args = apply_filters( 'wp_nav_menu_args', $args );
        $args = (object) $args;


        // Get the nav menu based on the requested menu
        $menu = wp_get_nav_menu_object( $args->menu );

        // Get the nav menu based on the theme_location
        if ( ! $menu && $args->theme_location && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $args->theme_location ] ) )
            $menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );

        // get the first menu that has items if we still can't find a menu
        if ( ! $menu && !$args->theme_location ) {
            $menus = wp_get_nav_menus();
            foreach ( $menus as $menu_maybe ) {
                if ( $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
                    $menu = $menu_maybe;
                    break;
                }
            }
        }

        if ( empty( $args->menu ) ) {
            $args->menu = $menu;
        }

        // If the menu exists, get its items.
        if ( $menu && ! is_wp_error($menu) && !isset($menu_items) )
            $menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );


        // Set up the $menu_item variables
        _wp_menu_item_classes_by_context( $menu_items );


        foreach ($menu_items as $item)
        {
            if ($item->current)
                $result['current'][] = $item->ID;

            if ($item->current_item_parent)
                $result['current_item_parent'][] = $item->ID;

            if ($item->current_item_parent)
                $result['current_item_parent'][] = $item->ID;
        }

        return $result;
    }

    function _filter_wp_nav_menu_objects($sorted_menu_items, $args)
    {


        if ($args->sub_menu)
        {
            $original_sorted_menu_items = $sorted_menu_items;

            $root_id = 0;

            // find the current menu item
            foreach ( $sorted_menu_items as $menu_item ) {
                if ( $menu_item->current ) {
                    // set the root id based on whether the current menu item has a parent or not
                    $root_id = ( $menu_item->menu_item_parent ) ? $menu_item->menu_item_parent : $menu_item->ID;
                    $root_item = $menu_item;
                    break;
                }
            }

            // find the top level parent
            if ($args->sub_menu_direct_parent) {

                $prev_root_id = $root_id;

                while ( $prev_root_id != 0 ) {

                    foreach ( $sorted_menu_items as $menu_item )
                    {
                        if ( $menu_item->ID == $prev_root_id )
                        {
                            $prev_root_id = $menu_item->menu_item_parent;
                            // don't set the root_id to 0 if we've reached the top of the menu
                            if ( $prev_root_id != 0 ) $root_id = $menu_item->menu_item_parent;
                            break;
                        }
                    }
                }
            }

            $menu_item_parents = array();

            foreach ( $sorted_menu_items as $key => $item ) {
                // init menu_item_parents
                if ( $item->ID == $root_id ) $menu_item_parents[] = $item->ID;

                if ( in_array( $item->menu_item_parent, $menu_item_parents ) ) {
                    // part of sub-tree: keep!
                    $menu_item_parents[] = $item->ID;
                } else {
                    // not part of sub-tree: away with it!
                    unset( $sorted_menu_items[$key] );
                }
            }

            if ($args->sub_menu_current_if_empty && empty($sorted_menu_items))
            {
                foreach ( $original_sorted_menu_items as $menu_item )
                {
                    if ($menu_item->menu_item_parent==$root_item->menu_item_parent)
                    {
                        $sorted_menu_items[] = $menu_item;
                    }
                }
            }

        }


        /*
        foreach ($sorted_menu_items as &$item)
        {
            $datasource = !empty($item->datasource) ? $item->datasource : 'menu';

            $addit = sm_menu_datasource::load_menu_item_additional($datasource, $item->ID);

            foreach ($addit as $key=>$val)
            {
                $item->{$key} = $val;
            }

            if ($item->current)
            {
                $item = apply_filters('sm/menu/item/current', $item);
            }

            if ($item->has_children)
            {
                $item = apply_filters('sm/menu/item/has_children', $item);
            }

            if ($item->current_item_ancestor)
            {
                $item = apply_filters('sm/menu/item/current_item_ancestor', $item);
            }

            if ($item->current_item_parent)
            {
                $item = apply_filters('sm/menu/item/current_item_parent', $item);
            }
        }
        */

        return $sorted_menu_items;
    }



    function _filter_sm_nav_menu_item_additional($data, $item)
    {
        $data += $this->get_menu_item_additional_values($item->ID);

        return $data;
    }

    function _action_save_post($post_id, $post)
    {
        if ( $post->post_type !== 'nav_menu_item' )
        {
            return $post_id;
        }

        $form_key = $this->get_menu_item_additional_form_key();

        if (isset($_POST[$form_key][$post_id]))
        {
            $values = stripslashes_deep($_POST[$form_key][$post_id]);

            $this->save_menu_item_additional_values($post_id, $values);
        }
    }

    function get_nav_menu_item_additional_form()
    {
        if (!isset($this->menu_item_additional_form))
        {
            $this->menu_item_additional_form = apply_filters('sm/menu/item/form', array());
        }

        return $this->menu_item_additional_form;
    }

    function get_menu_item_additional_form_key()
    {
        return 'menu-item-sm-form';
    }

    function get_menu_item_additional_values($item_id)
    {
        $data = get_post_meta($item_id, $this->get_menu_item_additional_form_key(), true);

        return $data ?: array();
    }

    function save_menu_item_additional_values($item_id, $values)
    {
        update_post_meta($item_id, $this->get_menu_item_additional_form_key(), $values);
    }
}

