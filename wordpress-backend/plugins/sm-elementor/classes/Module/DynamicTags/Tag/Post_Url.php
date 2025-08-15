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


class Post_Url extends Data_Tag {

    public function get_name() {
        return 'sm-post-url';
    }

    public function get_group() {
        return Module::SITE_GROUP;
    }

    public function get_categories() {
        return [ Module::URL_CATEGORY ];
    }

    public function get_title() {
        return __( 'SM: Post URL', 'elementor-pro' );
    }

    public function get_panel_template() {
        return ' ({{ url }})';
    }

    protected function _register_controls() {
        $this->add_control(
            'post_id',
            [
                'label' => __( 'Post', 'elementor-pro' ),
                'type' => 'query',
                'multiple' => false,
                'filter_type' => 'by_id',
                'include_type' => true,
            ]
        );
    }

    public function get_value( array $options = [] ) {

        $settings = $this->get_settings();

        if (isset($settings['post_id']) && intval($settings['post_id'])>0)
        {
            $post_id = $settings['post_id'];

            $post = get_post($post_id);

            if ($post && !is_wp_error($post))
            {
                $link = get_permalink($post);

                return $link;
            }
        }
    }
}
