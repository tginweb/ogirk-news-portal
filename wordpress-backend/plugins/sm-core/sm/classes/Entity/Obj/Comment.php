<?php

use SM\Util;

class sm_entity_object_comment extends sm_entity_object
{
    function __construct($values = array(), $entity_bundle=null)
    {
        $this->entity_type = 'comment';
        $this->entity_bundle = $this->entity_bundle ?: $entity_bundle ?: '';
        return parent::__construct($values);
    }

    function init_create()
    {
        $this->comment_type = $this->entity_bundle;
    }

    function save()
    {
        if ($this->is_new())
        {
            $this->comment_ID = wp_insert_comment((array)$this);
        }
        else
        {
            wp_update_comment((array)$this);
        }

        return $this->id();
    }

    function delete($force=false)       { return wp_delete_comment($this->id(), $force); }

    function id()                       { return $this->comment_ID; }
    function label()                    { return $this->comment_content; }
    function get_slug()                     { return ''; }
    function get_created($f='')         { return Util\Date::date_format($f?:'d F Y', $this->comment_date, true, 'mysql'); }
    function get_changed($f='')         { return Util\Date::date_format($f?:'d F Y', $this->comment_date, true, 'mysql'); }

    function get_url($alias=true)       { return ''; }
    function get_url_abs($alias=true)   { return ''; }
    function get_url_edit()             { return ''; }

    function get_url_sys()           { return '/?c='.$this->id();  }
    function get_url_system_abs()       { return home_url('?c='.$this->id());  }
    function get_url_rss()              { return 'rss/'.$this->entity_type.'/'.$this->entity_bundle.'/'.$this->id(); }
}
