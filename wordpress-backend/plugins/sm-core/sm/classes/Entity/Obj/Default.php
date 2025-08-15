<?php


class sm_entity_object_default extends sm_entity_object
{
    function __construct($values = array())
    {
        $this->entity_type = 'default';
        $this->entity_bundle = '';
    }

    function is_default()
    {
       return true;
    }

    function id() {}

    function get_slug() {}
    function get_label() {}
    function get_title() {}
    function get_created() {}
    function get_changed() {}
    function get_teaser() {}
    function get_content() {}
    function get_url_permalink() {}
    function get_url_edit() {}
    function get_url_sys() {}


    function field($selector, $format_value = true, $reset=false)
    {
        return null;
    }

    function cfield($field_name, $format_value = true, $reset=false)
    {
        return null;
    }

    function get_meta($key = '', $single=false)
    {
        return null;
    }
}



