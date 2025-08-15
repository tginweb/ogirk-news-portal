<?php

namespace SM\Entity\Obj;

use SM\Entity;

class Post extends Base
{
    var $terms = array();

    var $cat_main = null;
    var $cat_root = null;
    var $cat_child = null;


    public function __construct($values)
    {
        parent::__construct($values);

        $this->type = 'post';
        $this->bundle = $this->host->post_type;
        $this->id = $this->host->ID;
    }


    function id()                      { return $this->host->ID; }
    function uid()                     { return $this->host->post_author; }
    function get_created()             { return $this->host->post_date; }
    function get_changed()             { return $this->host->post_modified; }
    function get_slug()                { return $this->host->post_name; }
    function get_label()               { return $this->host->post_title; }
    function get_title()               { return $this->host->post_title; }
    function get_excerpt()             { return $this->host->post_excerpt; }
    function get_content()             { return $this->host->post_content; }
    function get_parent()              { return $this->host->post_parent; }

    function get_url_permalink()       { return get_permalink($this->host); }

    function get_thumb($format='id')   { return get_post_thumbnail_id($this->host); }

    function get_format()              { return get_post_format($this->host); }

    function get_the_terms($taxonomy=null)  { return get_the_terms($this->host, $taxonomy); }

    function get_the_time($f=null)     { return get_the_time($f, $this->host); }


    /* ------- FIELDS -------- */

    function get_post_field($field, $context = 'display')
    {
        return get_post_field($field, $this, $context);
    }


    /* ------- CACHE -------- */

    function clear_own_cache()
    {
        clean_post_cache($this);
        $this->sm_cache = array();
    }


    /* ------- TAXONOMY -------- */

    function _have_term($terms)
    {

    }

    function get_tags()                  { return $this->get_terms('post_tag'); }
    function have_tag($term_id)          { $term_id = (array)$term_id; foreach ($this->get_terms('post_tag') as $term) if (in_array($term->term_id, $term_id)) return $term; }
    function get_tags_by_type($type)
    {
        $tags = $this->get_tags();

        $result = array();

        if (!empty($tags)) foreach ($tags as $tag) if ($tag->get_meta_field('tag_type')==$type) $result[] = $tags;

        return $result;
    }

    function get_tag_by_type($type)
    {
        $tags = $this->get_tags_by_type($type);

        if (!empty($tags)) return current($tags);
    }

    function have_cat($term_id)
    {
        foreach ($this->get_cats() as $cat)
        {
            if ($cat->id()==$term_id) return true;
        }
    }

    function get_cats($dest_format=F_OBJECT_SMART)
    {
        return $this->get_terms('category', $dest_format);
    }

    function load_cats()
    {
        if ($this->sm_cache['cats_loaded']) return;

        foreach ($this->get_cats(F_OBJECT_WP) as $cat)
        {
            if (!$cat->parent)
            {
                if (!$this->cat_root) $this->cat_root = $cat;

                if (!$this->cat_main) $this->cat_main = $cat;
            }
            else
            {
                if (!$this->cat_child) $this->cat_child = $cat;

                if (!$this->cat_root)
                {
                    $term_parents = get_ancestors($cat->term_id, 'category');

                    if ($term_root = end($term_parents))
                    {
                        $this->cat_root = Entity::i()->format('term', 'category', $term_root, F_OBJECT_WP);
                    }
                }
            }
        }

        $this->sm_cache['cats_loaded'] = true;
    }

    function get_cat($dest_format=F_OBJECT_SMART)
    {
        return $this->get_term('category', $dest_format);
    }

    function get_cat_root($dest_format=F_OBJECT_SMART)
    {
        $this->load_cats();

        return $this->cat_root;
    }

    function get_cat_child($dest_format=F_OBJECT_SMART)
    {
        $this->load_cats();

        return $this->cat_child;
    }


    function get_terms($taxs = array(), $dest_format=F_OBJECT_SMART)
    {
        $taxs = (array)$taxs;

        $result = array();

        foreach ($taxs as $tax)
        {
            if (!isset($this->terms[$tax]))
            {
                $this->terms[$dest_format][$tax] = array();

                $terms = wp_get_post_terms($this->id(), $tax, ['orderby'=>'none']);

                if (is_wp_error($terms))
                {
                    //something is very wrong
                }
                else
                {
                    foreach ($terms as $term)
                    {
                        $this->terms[$dest_format][$tax][$term->term_id] = Entity::i()->format('term', $term->taxonomy, $term, $dest_format);
                    }
                }
            }

            $result += $this->terms[$dest_format][$tax];
        }

        return $result;
    }

    function have_term($id, $taxonomy=null)
    {
        $terms = $this->get_terms($taxonomy, F_OBJECT_WP);

        $id = (array)$id;

        foreach ($terms as $term)
        {
            if (in_array($term->term_id, $id) || in_array($term->slug, $id)) return true;
        }
    }

    function get_term($tax = null, $dest_format=F_OBJECT_SMART)
    {
        $terms = $this->get_terms($tax, $dest_format);

        return !empty($terms) ? current($terms) : null;
    }


    function set_terms($tags = '', $taxonomy = 'post_tag', $append = false)
    {
        return wp_set_post_terms($this->id(), $tags, $taxonomy, $append);
    }


    /* ------- POSTS TREE -------- */

    function &get_path($options=array())
    {
        $options += array(
            'use_menu'        => false,
            'use_parent'      => true,
            'include_current' => false,
            'from_root'       => false
        );

        $parents = array();

        foreach (get_post_ancestors($this) as $parent_id) if ($parent_post = Entity::i()->load_entity('post', $parent_id)) $parents[] = $parent_post;

        if ($options['include_current']) array_unshift($parents, $this);

        if ($options['from_root']) $parents = array_reverse($parents);

        return $parents;
    }


    function get_children_posts($options=array())
    {
        $options += array(
            'level'        => 0,
            'numberposts'  => -1,
            'order'        => 'ASC',
            'post_type'    => $this->entity_bundle
        );

        $options['post_parent'] = $this->id();

        $posts = Entity::i()->get_posts($options);

        if ($options['level'])
        {
            $result = array();

            foreach ($posts as $k=>$post)
            {
                $result[$k] = $post;

                $result = array_merge($result, $post->get_children_posts($options));
            }
        }
        else
        {
            $result = $posts;
        }

        return $result;
    }

    function have_children_posts($args=array())
    {
        $args += array(
            'numberposts'  => 1,
            'post_parent'  => $this->id(),
            'post_type'    => $this->entity_bundle
        );

        $posts = get_posts($args);

        return !empty($posts) ? true : false;
    }



    /* ----- UPDATING ------ */

    function save()
    {
        if ($this->is_new())
        {
            $this->ID = wp_insert_post($this);
        }
        else
        {
            wp_update_post($this);
        }
        return $this->id();
    }

    function update_entity_db($data)
    {
        $ctrl = $this->controller();

        if (method_exists($ctrl, 'action_wp_insert_post')) remove_action('wp_insert_post', array($ctrl, 'action_wp_insert_post'));

        $data['ID'] = $this->id();

        wp_update_post($data);

        if (method_exists($ctrl, 'action_wp_insert_post')) add_action('wp_insert_post', array($ctrl, 'action_wp_insert_post'), 10, 2);
    }

    function delete($force_delete=false)
    {
        if (!$this->is_new()) wp_delete_post($this->id(), $force_delete);
    }


    /* ----- COMMENTS ------ */

    function get_comments($args=array())
    {
        $args += array(
            'status' => 'approve',
            'number' => 500
        );

        $args['post_id'] = $this->id();

        $comments = Entity::i()->get_comments($args);

        return $comments;
    }

    function get_comments_count()
    {
        $info = wp_count_comments($this->id);

        return $info->total_comments;
    }

    function prev_post($in_same_term = false, $excluded_terms = '', $taxonomy = 'category')
    {
        global $post;
        $oldGlobal = $post;
        $post = get_post( $this->id() );
        $np_post = get_previous_post($in_same_term, $excluded_terms, $taxonomy);
        $post = $oldGlobal;

        if ( '' == $np_post ) return null;

        return Entity::i()->load_entity('post', $np_post);
    }

    function next_post($in_same_term = false, $excluded_terms = '', $taxonomy = 'category')
    {
        global $post;
        $oldGlobal = $post;
        $post = get_post( $this->id() );
        $np_post = get_next_post($in_same_term, $excluded_terms, $taxonomy);
        $post = $oldGlobal;
        if ( '' == $np_post ) return null;

        return Entity::i()->load_entity('post', $np_post);
    }


    function view_category($params=array())
    {
        if ($cat = $this->get_cat_root())
        {
            return $cat->view($params['category_format']);
        }
    }



}

