<?php

namespace SM\Common\Collection;


class Entity extends Base
{
    var $entity_type = null;

    static function create($entity_type, $items=[])
    {
         if (is_object($items)) return $items;

         $cls = 'sm_collection_entity_'.$entity_type;

         return new $cls($items);
    }

    function add_item($item, $id=null)
    {
        $entity = \SM\Entity::i()->load_entity($this->entity_type, $item);

        parent::add_item($entity, $entity->id());
    }

    function add_items($items)
    {
        $items = \SM\Entity::i()->load_multiple($this->entity_type, $items);

        parent::add_items($items);
    }
}
