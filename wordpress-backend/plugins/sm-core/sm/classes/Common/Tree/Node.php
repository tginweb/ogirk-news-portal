<?php

namespace SM\Common\Tree;

class Node
{
    var $tree;
    var $tree_options;

    var $id;
    var $parent_id;
    var $weight;
    var $depth = -1;
    var $data;
    var $children = array();

    function __construct($data, &$tree=null)
    {
        $this->tree = $tree;
        $this->init($data);
    }

    function init($data)
    {
        $data = (array)$data;

        $opt             = $this->tree->options;

        $this->id        = $data[$opt['prop_id']];
        $this->parent_id = $data[$opt['prop_parent_id']];
        $this->weight    = $data[$opt['prop_weight']];
        $this->depth     = isset($data['depth']) ? $data['depth'] : $this->depth;
        $this->data      = $data;

        if (isset($data['children']))
        {
            foreach ($data['children'] as $child)
            {
                $this->add_child($child);
            }
        }

        $this->tree->index_node($this);
    }

    function children()
    {
        return $this->children;
    }

    function children_count()
    {
        return count($this->children);
    }

    function add_child($node)
    {
        if (is_object($node) && !is_subclass_of($node, 'sm_tree_node'))
        {
            $node = (array)$node;
        }

        if (!is_object($node))
        {
            $node['depth'] = $this->depth + 1;

            $node = $this->tree->create_node($node);
        }
        else
        {
            $node->depth = $this->depth + 1;
        }

        $this->children[$node->id] = $node;

        $this->tree->index_node($node);
    }

    function get_parent()
    {
        return $this->parent_id ? $this->tree->get_node($this->parent_id) : $this->tree->root_node();
    }

    function remove()
    {
        $parent = $this->get_parent();
        unset($parent->children[$this->id]);
        unset($this->tree->index[$this->id]);
    }

    function sort()
    {
        uasort($this->children, '\SM\Util\Base::sort_weight');

        foreach ($this->children() as $child)
        {
            $child->sort();
        }

        return $this;
    }

    function is_root()
    {
        return !$this->id;
    }

    function to_flat_array()
    {
        $items = array();
        $items[$this->id] = $this->data;

        foreach ($this->children() as $child)
        {
            $items = array_merge($items, $child->to_flat_array());
        }

        return $items;
    }

    function to_select_options($arg=array())
    {
        $arg += array(
            'label'           => null,
            'option_format'   => 'label', // data
            'node_attrs'      => array(),
            'option_class'    => array(),
            'depth_title_pad' => '-'
        );

        $items = array();

        if (!$this->is_root())
        {
            $label = $arg['label'] ? $this->data[$arg['label']] : $this->data[$this->tree->options['prop_label']];

            if ($arg['depth_title_pad'])
            {
                $label = $this->depth ? str_repeat($arg['depth_title_pad'], $this->depth) . ' ' . $label : $label;
            }

            if ($arg['option_format']=='label')
            {
                $items[$this->id] = $label;
            }
            else
            {
                $option_attrs = array('label' => $label, 'depth'=>$this->depth);

                foreach ($arg['node_attrs'] as $attr_key => $prop_key)
                {
                    if (is_numeric($attr_key)) $attr_key = $prop_key;

                    $option_attrs[$attr_key] = $this->data[$prop_key];
                }

                $items[$this->id] = $option_attrs;
            }
        }

        foreach ($this->children() as $child)
        {
            $child_options = $child->to_select_options($arg);

            foreach ($child_options as $ch_key=>$ch_val) $items[$ch_key] = $ch_val;
        }

        return $items;
    }

}
