<?php


namespace SM_Elementor\Module\Query\Widget_Header;

use Elementor;
use Elementor\Controls_Manager;
use SM;
use SM\Util;
use SM\Util\Html;

class header_query_2 extends Common\Base
{
    function get_container_classes() {
        return array_merge(parent::get_container_classes(), ['header-2']);
    }
}
