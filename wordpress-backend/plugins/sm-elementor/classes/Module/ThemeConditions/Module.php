<?php


namespace SM_Elementor\Module\ThemeConditions;

include_once WP_PLUGIN_DIR . '/elementor-pro/modules/theme-builder/conditions/condition-base.php';


class Module extends \SM_Elementor\Common\Plugin_Module
{
    function init_events()
    {
        $this->add_action('wp_loaded', null, 9);

        spl_autoload_register(function($class) {

            if ($class=='ElementorPro\Modules\ThemeBuilder\Conditions\Archive')
            {
                require __DIR__.'/Condition/Archive.php';
            }

            if ($class=='ElementorPro\Modules\ThemeBuilder\Conditions\Singular')
            {
                require __DIR__.'/Condition/Singular.php';
            }

        }, true, true);
    }

    function _action_wp_loaded()
    {
        require __DIR__.'/Condition/Listing.php';
        require __DIR__.'/Condition/Term_Child_Of.php';
        require __DIR__.'/Condition/Archive_Context.php';
        require __DIR__.'/Condition/Singular_Context.php';
        require __DIR__.'/Condition/Singular_Template.php';
    }
}



