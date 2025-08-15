<?php
/*
 * Plugin Name: Smart Site Content
 * Version: 1.0
 */

namespace SM_Site_Content;


@include_once WP_PLUGIN_DIR . '/sm-core/smart.php';

if (!defined('SM_CORE_LOADED')) {
    add_action('admin_notices', function () { ?>

        <div class="error notice-error notice"><p><strong>Smart Core Plugin not found</strong></p></div>

    <? });

    return;
}

use SM\Classes;

define('SM_SITE_CONTENT_FILE', __FILE__);
define('SM_SITE_CONTENT_PATH', trailingslashit(dirname(SM_SITE_CONTENT_FILE)));

Classes::i()->register_namespace(__NAMESPACE__, SM_SITE_CONTENT_PATH . 'classes');

require_once(__DIR__ . '/Plugin.php');

Classes::i()->add_typed_classes_map('SM_Site_Content\Plugin', [
    'module' => [
        //'sm_content_ad'          => array('label' => 'Advert'),
        //'sm_content_infographic' => array('label' => 'Infographic'),
        'SM_Site_Content\Module\Chs\Module' => array('label' => 'Chs', 'init' => true),
        'SM_Site_Content\Module\Edu\Module' => array('label' => 'Edu', 'init' => true),
        'SM_Site_Content\Module\Issue\Module' => array('label' => 'Issue', 'init' => true),
        'SM_Site_Content\Module\Hub\Module' => array('label' => 'Hub', 'init' => true),
        'SM_Site_Content\Module\Author\Module' => array('label' => 'Author', 'init' => true),
        'SM_Site_Content\Module\Ad\Module' => array('label' => 'Ad', 'init' => true),
        'SM_Site_Content\Module\Conf\Module' => array('label' => 'Conference', 'init' => true),
        'SM_Site_Content\Module\QA\Module' => array('label' => 'QA', 'init' => true),
        'SM_Site_Content\Module\Card\Module' => array('label' => 'Карточки', 'init' => true),
        'SM_Site_Content\Module\Quiz\Module' => array('label' => 'Квизы', 'init' => true),
        'SM_Site_Content\Module\Note\Module' => array('label' => 'Заметки', 'init' => true),
        'SM_Site_Content\Module\OtherIssue\Module' => array('label' => 'Выпуски районные', 'init' => true),
        'SM_Site_Content\Module\VDay\Module' => array('label' => 'День победы', 'init' => true),
    ],
]);


Plugin::i([], 'plugin');







