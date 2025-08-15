<?php
/*
 * Plugin Name: Smart Media
 * Version: 1.0
 */

namespace SM_Media;

if (!defined('SM_CORE_LOADED'))
{
    add_action( 'admin_notices', function() { ?>

        <div class="error notice-error notice"><p><strong>Smart Core Plugin not found</strong></p></div>

    <? });

    return;
}

use SM\Classes;

define('SM_MEDIA_FILE', __FILE__ );
define('SM_MEDIA_PATH', trailingslashit(dirname(SM_MEDIA_FILE)));

Classes::i()->register_namespace(__NAMESPACE__, SM_MEDIA_PATH.'classes');

require_once(__DIR__.'/Plugin.php');

Classes::i()->add_typed_classes_map('SM_Media\Plugin', [
    'module' => [
        'SM_Media\Module\Thumbnail\Module' => array('label' => 'Thumbnail', 'init'=>true),
    ],
]);


Plugin::i([], 'plugin');







