<?php

namespace SM\Common\Tree;

class Tree_Base
{

    var $options     = array();
    var $root        = null;
    var $index       = array();

    function __construct($options=array())
    {
        $this->options = $options + $this->options();
    }

    function options()
    {
        return array(
            'prop_id'          => 'id',
            'prop_parent_id'   => 'parent_id',
            'prop_weight'      => 'weight',
            'prop_label'       => 'title'
        );
    }

    function root_node()
    {
        if (!isset($this->root))
        {
            $this->root = $this->create_node(array());
        }

        return $this->root;
    }

    function create_node($data)
    {
        return new sm_tree_node($data, $this);
    }

    function index_node($node)
    {
        $this->index[$node->id] = $node;
    }

    /* @return sm_tree_node */
    function get_node($id)
    {
        return $this->index[$id];
    }

    function sort()
    {
        $this->root_node()->sort();
    }

    function array_flat_to_tree(array $flat)
    {
        $indexed = array();

        foreach ($flat as $key=>$row)
        {
            $row = (array)$row;

            $id = $row[$this->options['prop_id']];

            if (!$id) $id = $row[$this->options['prop_id']] = $key;

            $indexed[$id] = $row + array('children'=>array());
        }

        $root_items = array();

        foreach ($indexed as $id => $row)
        {
            $parent_id = $row[$this->options['prop_parent_id']];

            if (!$parent_id)
            {
                $root_items[$id] = &$indexed[$id];
            }
            else
            {
                $indexed[$parent_id]['children'][$id] = &$indexed[$id];
            }
        }

        return $root_items;
    }

    function from_flat_array($array)
    {
        foreach ($this->array_flat_to_tree($array) as $item) $this->root_node()->add_child($item);
    }

    function to_flat_array()
    {
        foreach ($this->root_node()->children() as $item)
        {
            $this->root_node()->add_child($item);
        }
    }
}

