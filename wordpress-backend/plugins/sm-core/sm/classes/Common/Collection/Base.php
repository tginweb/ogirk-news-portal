<?php

namespace SM\Common\Collection;

class Base
{
    var $items = array();

    function __construct($items=[])
    {
        $this->add_items($items);
    }

    function add_items($items)
    {
        foreach ($items as $id=>$item)
        {
            $this->items[$id] = $item;
        }

        return $this;
    }

    function add_item($item, $id=null)
    {
        if ($id)
        {
            $this->items[$id] = $item;
        }
        else
        {
            $this->items[] = $item;
        }

        return $this;
    }

    function set($name, $value=null)
    {
        if (is_array($name))
        {
            foreach ($name as $key=>$val) $this->items[$key] = $val;
        }
        else
        {
            $this->items[$name] = $value;
        }

        return $this;
    }

    function get($name, $default=null)
    {
        if (is_array($name))
        {
            $value = array();

            foreach ($name as $n) $value[$n] = isset($this->items[$n]) ? $this->items[$n] : null;
        }
        else if (is_string($name) && isset($this->items[$name]))
        {
            $value = $this->items[$name];
        }
        else
        {
            $value = $default;
        }

        return $value;
    }

    function get_models($item_method='get_model')
    {
        $results = [];

        foreach ($this->items as $id=>$item)
        {
            if (is_object($item))
            {
                if (method_exists($item, $item_method))
                {
                    $model = $item->{$item_method}();
                }
                else
                {
                    $model = (array)$item;
                }
            }
            else if (is_array($item))
            {
                $model = $item;
            }

            $model['_id'] = $id;

            $results[] = $model;
        }

        return $results;
    }

    function __isset($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }

    function __get($name)
    {
        if (array_key_exists($name, $this->items)) return $this->items[$name];
    }
}


