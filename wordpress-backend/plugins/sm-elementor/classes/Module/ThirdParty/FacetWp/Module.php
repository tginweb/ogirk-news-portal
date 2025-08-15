<?php


namespace SM\Elementor\Module;

use SM\Elementor\Widget;

class ElementorProWidgets extends \sm_module
{
    function init_events()
    {
        $this->add_action('elementor/widgets/widgets_registered', null, 10000);

    }

    function _action_elementor_widgets_widgets_registered($widgets_manager)
    {

        $widgets_manager->register_widget_type(new \SM\Elementor\Widget\FWP_Facets());


    }
}



