<?php

/*
Plugin Name: Smart Core
Smart: Yes
Plugin URI:
Description:
Author: Xanderz
Version: 1
Author URI:
*/

namespace SM;

if (defined('SM_CORE_LOADED')) return;

require 'sm/loader.php';

Plugin::i([], 'plugin');

// Регистрация функции, которая сработает перед завершением работы скрипта
register_shutdown_function( function () {

    if (class_exists('\Elementor\Plugin')) {
   //     file_put_contents(ABSPATH.'/controls.json', json_encode(\Elementor\Plugin::$instance->controls_manager->stacks, JSON_PRETTY_PRINT));
    }

});
