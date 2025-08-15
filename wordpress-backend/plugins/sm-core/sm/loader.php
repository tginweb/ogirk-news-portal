<?php

namespace SM;

use SM\Classes;

require_once(__DIR__.'/includes/api.php');
require_once(__DIR__.'/classes/Classes.php');

define('SM_CORE_LOADER_FILE', __FILE__ );
define('SM_CORE_LOADER_PATH', trailingslashit(dirname(SM_CORE_LOADER_FILE)));

Classes::i()->register_namespace(__NAMESPACE__, SM_CORE_LOADER_PATH.'classes');

require_once(__DIR__.'/Plugin.php');

Classes::i()->add_typed_classes_map('SM\Plugin', [

    'module' => [
       'SM\Module\Debug\Module'       => array('label' => 'Debug', 'pluggable'=>true, 'init'=>true),
       'SM\Module\Acf\Module'         => array('label' => 'ACF', 'pluggable'=>true),
       'SM\Module\Wc\Module'          => array('label' => 'Woocommerce', 'pluggable'=>true),
    ],

    'cache_storage' => [
       'SM\Cache\Storage\Db'          => array('label' => 'DB'),
    ],

]);


define('SM_CORE_LOADED', true);
