<?php
/*
Plugin Name: Smart Elementor
Smart: Yes
Plugin URI:
Description:
Author: Xanderz
Version: 1
Author URI:
*/



namespace SM_Elementor;


@include_once WP_PLUGIN_DIR . '/sm-core/smart.php';


//require_once WP_PLUGIN_DIR . '/elementor/elementor.php';
//require_once WP_PLUGIN_DIR . '/elementor-pro/elementor-pro.php';


if (!defined('SM_CORE_LOADED'))
{
    add_action( 'admin_notices', function() { ?>

        <div class="error notice-error notice"><p><strong>Smart Core Plugin not found</strong></p></div>

    <?php });

    return;
}


use SM\Classes;

define('SM_ELEMENTOR_CONTENT_FILE', __FILE__ );
define('SM_ELEMENTOR_CONTENT_PATH', trailingslashit(dirname(SM_ELEMENTOR_CONTENT_FILE)));

Classes::i()->register_namespace(__NAMESPACE__, SM_ELEMENTOR_CONTENT_PATH.'classes');

require_once(__DIR__.'/Plugin.php');


Plugin::i([], 'plugin');
