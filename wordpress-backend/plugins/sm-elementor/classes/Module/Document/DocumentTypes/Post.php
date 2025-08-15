<?php
namespace SM_Elementor\Module\Document\DocumentTypes;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Background;
use Elementor\Plugin;
use Elementor\Settings;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post extends \Elementor\Core\DocumentTypes\Post {


    public function save( $data ) {

        $orig_data = $data;

        foreach ($data['settings'] as $setting_key=>$setting_value)
        {
            if (strpos($setting_key, 'ns_')===0)
            {
                unset($data['settings'][$setting_key]);
            }
        }

        parent::save($data);

        fb('1111');

        do_action( 'elementor/editor/after_save_post', $this->post->ID, $orig_data);

        return true;
    }
}
