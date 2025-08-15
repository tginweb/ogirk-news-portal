<?php

namespace SM\Entity\Controller;

use SM\Common;
use SM\Util;

abstract class Base extends Common\Base
{
    var $entity_type;
    var $entity_bundle;
    var $support_bundles;
    var $field_name_slug = null;
    var $field_name_id = null;
    var $field_bundle = null;

    abstract function entity_wp_load($id = null, $output = OBJECT, $filter = 'raw');
    abstract function select($data=array(), $cache=false);

    function __construct($entity_bundle=null)
    {
        $this->entity_bundle = $entity_bundle;
    }

    function register_wp_bundle($args=array())
    {
        return null;
    }

    function get_wp_bundle()
    {
        return null;
    }

    function init_events()
    {

    }

    function get_bundle_arg($param, $def=null)
    {
        $wp_bundle = $this->entity_bundle ? $this->get_wp_bundle() : new \StdClass();

        return isset($wp_bundle->$param) ? $wp_bundle->$param : $def;
    }

    function get_bundle_label($type='name')
    {
        $wp_bundle = $this->get_wp_bundle();

        $labels = (array)$wp_bundle->labels;

        if ($type=='name')
        {
            return isset($labels[$type]) ? $labels[$type] : $wp_bundle->label;
        }
        else if ($type=='singular_name')
        {
            return isset($labels['singular_name']) ? $labels['singular_name'] : $wp_bundle->label;
        }
        else
        {
            return isset($labels[$type]) ? $labels[$type] : '';
        }
    }

    function get_bundles_options($args=[], $prepend=[], $inc_type=false)
    {
        $result = $prepend;

        $objects = $this->get_bundles($args, 'object');

        foreach ($objects as $entity_bundle=>$bundle_object)
        {
            $key = $inc_type ? $this->entity_type.':'.$entity_bundle : $entity_bundle;

            $result[$key] = $bundle_object->label;
        }

        return $result;
    }

    function entity_wp_extract_id($entity)
    {
        if     ( is_numeric($entity) )  $id = $entity;
        elseif ( is_object($entity) )   $id = $entity->{$this->field_name_id};
        elseif ( is_array($entity) )    $id = $entity[$this->field_name_id];
        return $id;
    }

    function entity_create($data = null)
    {
        if ($entity = $this->entity_build($data))
        {
            $entity->init_create();

            return $entity;
        }
    }

    function entity_build($data)
    {
        $data = (array)$data;

        $cls = $this->get_entity_class($data);

        return $cls ? new $cls($data, $this->entity_bundle) : null;
    }

    function get_entity_class(&$data)
    {
        return Util\Base::class_suggestion_find(rtrim('sm_entity_object_'.$this->entity_type.'__'.$this->entity_bundle, '_'));
    }
}


