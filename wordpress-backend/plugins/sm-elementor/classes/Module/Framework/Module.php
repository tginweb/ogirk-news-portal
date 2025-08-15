<?php


namespace SM_Elementor\Module\Framework;

use SM_Elementor\Plugin;
use SM\Assets;
use SM\Util;

class Module extends \SM_Elementor\Common\Plugin_Module
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


    function assets()
    {
        $plugin_path = Plugin::i()->get_path_rel();

        return [

            'sm_elementor.framework.bootstrap4.grid' => [
                $plugin_path.'/assets/lib/fw.bootstrap4/css/grid.css',
            ],

            'sm_elementor.framework.bootstrap4.all_css' => [
                $plugin_path.'/assets/lib/fw.bootstrap4/css/all.css',
            ],

            'sm_elementor.framework.bootstrap4.engine_css' => [
                $plugin_path.'/assets/lib/fw.bootstrap4/css/components.css',
            ],

            'sm_elementor.framework.bootstrap4.engine_js' => [
                'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
                $plugin_path.'/assets/lib/fw.bootstrap4/js/bootstrap.min.js'
            ],


        ];

        return $info;
    }

    function init_events()
    {
        $this->add_filter('sm_elementor/framework/engine/types');

        $this->add_action('wp_enqueue_scripts');

    }

    function _filter_sm_elementor_framework_engine_types($info)
    {
        $info += [
            'custom'     => ['label'=>'Custom', 'class'=> 'SM_Elementor\Module\Framework\Engine\Custom'],
            'bootstrap4' => ['label'=>'Bootstrap4', 'class'=> 'SM_Elementor\Module\Framework\Engine\Bootstrap4'],
        ];

        return $info;
    }


    function _action_wp_enqueue_scripts()
    {

        return;

        $grid_enqueue_style = $this->get_module_option('grid_enqueue_style', 'inline');

        if ($grid_enqueue_style && $grid_enqueue_style!=='none')
        {
            if ($grid_engine = $this->get_grid_engine())
                $grid_engine->enqueue_grid($grid_enqueue_style);
        }

        if ($engine = $this->get_engine())
        {
            $enqueue_style = $this->get_module_option('engine_enqueue_style');
            $enqueue_script = $this->get_module_option('engine_enqueue_script');

            $grid_engine->enqueue_engine($enqueue_style, $enqueue_script);
        }
    }


    /* @return \SM_Elementor\Module\Framework\Engine\Base */
    function get_grid_engine()
    {
        if (!isset($this->grid_engine)) {

            $type_id = $this->get_module_option('grid_type', 'bootstrap4');

            $this->grid_engine = $this->create_engine($type_id);
        }

        return $this->grid_engine;
    }

    /* @return \SM_Elementor\Module\Framework\Engine\Base */
    function get_engine()
    {
        if (!isset($this->engine)) {

            $type_id = $this->get_module_option('engine_type', 'bootstrap4');

            $this->engine = $this->create_engine($type_id);
        }

        return $this->engine;
    }


    function create_engine($engine_id)
    {
        $grid_engines = apply_filters('sm_elementor/framework/engine/types', []);

        if (isset($grid_engines[$engine_id]) && !isset($this->engines[$engine_id]))
        {
            $engine_info = $grid_engines[$engine_id];

            if (isset($engine_info['class']) && class_exists($engine_info['class']))
            {
                $options = $this->get_module_option();

                $this->engines[$engine_id] = new $engine_info['class'];

                $this->engines[$engine_id]->set_vars($options);
            }
        }

        if (empty($this->engines[$engine_id])) $this->engines[$engine_id] = false;

        return $this->engines[$engine_id];
    }




}

