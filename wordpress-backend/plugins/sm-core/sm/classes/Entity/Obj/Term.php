<?php

namespace SM\Entity\Obj;

use SM\Entity;


class Term extends Base
{
    public function __construct($values)
    {
        parent::__construct($values);

        $this->type = 'term';
        $this->bundle = $this->host->taxonomy;
        $this->id = $this->host->term_id;
    }

    function id()                       { return $this->host->term_id; }
    function get_created()              { return null; }
    function get_changed()              { return null; }
    function get_slug()                 { return $this->host->slug; }
    function get_label()                { return $this->host->name; }
    function get_title()                { return $this->host->name; }
    function get_excerpt()              { return $this->host->description; }
    function get_content()              { return get_field('sm_content', $this->host); }
    function get_parent()               { return $this->parent; }
    function get_url_permalink()        { return get_term_link($this->host); }
    function get_format()               { return ''; }

    function &get_path($options=array())
    {
        $options += array(
            'use_menu'        => false,
            'use_parent'      => true,
            'include_current' => false,
            'from_root'       => false
        );

        $parents = array();

        foreach (get_ancestors($this->id()) as $parent_id) if ($parent_entity = Entity::i()->load_entity('term', $parent_id)) $parents[] = $parent_entity;

        if ($options['include_current']) array_unshift($parents, $this);
        if ($options['from_root']) $parents = array_reverse($parents);

        return $parents;
    }

    function walk_to_root($callback, $include_current=false)
    {
        $stop = false;
        $result = null;

        $levels = Entity::i()->load_multiple('term', get_ancestors($this->id(), $this->entity_bundle, 'taxonomy'), $this->entity_bundle);

        if ($include_current) array_unshift($levels, $this);

        foreach ($levels as $term)
        {
            $callback($term, $result, $stop);
            if ($stop) break;
        }

        return $result;
    }


}


