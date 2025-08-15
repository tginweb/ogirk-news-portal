<?php

namespace SM_Elementor\Module\DynamicTags\Tag;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Core\DynamicTags\Data_Tag;
use ElementorPro\Classes\Utils;
use ElementorPro\Modules\DynamicTags\Module;
use ElementorPro\Modules\DynamicTags\Tags\Archive_URL;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Type_Url extends Data_Tag {

    public function get_name() {
        return 'sm-post-type-url';
    }

    public function get_group() {
        return Module::SITE_GROUP;
    }

    public function get_categories() {
        return [ Module::URL_CATEGORY ];
    }

    public function get_title() {
        return __( 'SM: Post type URL', 'elementor-pro' );
    }

    public function get_panel_template() {
        return ' ({{ url }})';
    }

    protected function _register_controls() {
        $this->add_control(
            'post_type',
            [
                'label' => __( 'Post type', 'elementor-pro' ),
                'type' => 'select',
                'options' => \SM\Util\Wp::get_post_types([], 'names'),
            ]
        );
    }

    public function get_value( array $options = [] ) {

        $settings = $this->get_settings();

        if (isset($settings['post_type']))
        {
            $link = get_post_type_archive_link($settings['post_type']);

            if ($link)
            {
                return $link;
            }
        }
    }
}
