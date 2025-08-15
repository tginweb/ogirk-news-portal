<?php


namespace SM_Media\Module\Thumbnail;

use SM\Assets;
use SM\Util;

class Module extends \SM_Media\Common\Plugin_Module
{
    var $grid_engine;
    var $engine;

    /* @return Module */
    static function i($info=[], $context=null) { return parent::i($info, $context); }

    static function info()
    {
        return array(

        );
    }

    function init_events()
    {

        $this->add_filter('sm_media/thumbnail/resize_engine/types');

        $this->add_action('after_setup_theme', null, 100);
    }

    function _action_after_setup_theme()
    {


        if ($engine = $this->get_engine())
        {
            $engine->init_events();
        }

    }

    function _filter_sm_media_thumbnail_resize_engine_types($info)
    {
        $info += [
            'cloudinary'  => ['label'=>'Cloudinary', 'class'=> 'SM_Media\Module\Thumbnail\ResizeEngine\Cloudinary'],
        ];

        return $info;
    }


    /* @return ResizeEngine\Base */
    function get_engine()
    {
        if (!isset($this->engine)) {

            $type_id =  'cloudinary';

            $this->engine = $this->create_engine($type_id);
        }

        return $this->engine;
    }


    function create_engine($engine_id)
    {
        $grid_engines = apply_filters('sm_media/thumbnail/resize_engine/types', []);

        if (isset($grid_engines[$engine_id]) && !isset($this->engines[$engine_id]))
        {
            $engine_info = $grid_engines[$engine_id];

            if (isset($engine_info['class']) && class_exists($engine_info['class']))
            {

                $this->engines[$engine_id] = new $engine_info['class'];

                //$this->engines[$engine_id]->set_vars($options);
            }
        }

        if (empty($this->engines[$engine_id])) $this->engines[$engine_id] = false;

        return $this->engines[$engine_id];
    }


}

