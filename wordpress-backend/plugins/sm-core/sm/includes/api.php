<?php

use SM\Page;
use SM\Context;

function sm_class_can_load($class, $file)
{
    return sm()->classes()->can_load($class, $file);
}

function sm_format($formats, $params=array())
{
    $formats = (array)$formats;

    foreach ($formats as $format)
    {
        if (has_filter('sm/format/'.$format))
        {
            if ($result = apply_filters_ref_array('sm/format/'.$format, array(null, &$params)))
            {
                break;
            }
        }
    }

    return $result;
}


function sm_page_current()
{
    return Page::i()->get_current();
}


function sm_is_context($name)
{
    return Context::i()->is_active($name);
}

function is_context($name)
{
    return Context::i()->is_active($name);
}

function sm_apply_filters_cached($tag, $value)
{
    static $cache = [];

    if (!isset($cache[$tag]))
    {
        $cache[$tag] = apply_filters($tag, $value);
    }

    return $cache[$tag];
}

function sm_add_filter($tags, $function_to_add, $priority = 10, $accepted_args = 1)
{
    foreach ((array)$tags as $tag)
    {
        add_filter($tag, $function_to_add, $priority, $accepted_args);
    }
}

function sm_ext_load($class, $info=[])
{
    $info += array(

    );

    if (!isset($info['filepath']))
    {
        if (isset($info['path']))
        {
            $info['filepath'] = $info['path'] . '/ext.php';
        }
    }
    else
    {
        if (!isset($info['path']))
        {
            $info['path'] = dirname($info['filepath']);
        }
    }

    if ($info['filepath'] && file_exists($info['filepath']))
    {
        include_once $info['filepath'];
    }

    if (!empty($info)) sm()->classes()->update_class_info($class, $info);

    return sm()->instance($class);
}


function sm_query_is_queried_terms($taxonomy = '', $term = '')
{
    global $wp_query, $wp_taxonomies;

    $tax_array = array_intersect( array_keys( $wp_taxonomies ), (array) $taxonomy );
    $term_array = (array) $term;

    if (!empty($wp_query->tax_query->queried_terms))
    {
        foreach ($wp_query->tax_query->queried_terms as $qtax => $qtax_query)
        {
            // Check that the taxonomy matches.
            if (in_array($qtax, $tax_array))
            {
                if (empty($term))
                    return true;

                foreach ((array)$qtax_query['terms'] as $qterm_id)
                {
                    if ( 'term_id' == $qtax_query['field'] ) {
                        $qterm = get_term( $qterm_id, $qtax );
                    } else {
                        $qterm = get_term_by( $qtax_query['field'], $qterm_id, $qtax );
                    }

                    if ($qterm && count(array_intersect(array( $qterm->term_id, $qterm->name, $qterm->slug ), $term_array))) return true;
                }
            }
        }
    }

    return false;
}


function sm_has_post_format( $format = array(), $post = null ) {
    $prefixed = array();

    if ( $format ) {
        foreach ( (array) $format as $single ) {
            $prefixed[] =  sanitize_key( $single );
        }
    }

    return has_term( $prefixed, 'post_format', $post );
}