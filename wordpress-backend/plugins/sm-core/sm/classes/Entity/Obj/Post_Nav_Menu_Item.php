<?php

class sm_entity_object_post_nav_menu_item extends sm_entity_object_post
{

    function get_title()  { return $this->title; }

    function get_url_permalink()
    {
        if (in_array($this->object, ['page']))
        {
            return get_permalink($this->object_id);
        }
    }


}

