<?php

namespace SM_Site_Content\Module\Card\Query;

use SM\Common;

class Entity extends \SM\Entity\Obj\Base
{
    public function __construct($values)
    {
        parent::__construct($values);

        $this->type = 'cards';

        $this->id = $this->host->id;
    }

    function get_title()
    {
        return $this->host->title;
    }

    function get_excerpt()
    {
        return $this->host->content;
    }

    function get_content()
    {
        return $this->host->content;
    }

    function get_url()
    {
        return '#entity-'.$this->get_full_id();
    }
}

