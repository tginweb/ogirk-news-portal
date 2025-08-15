<?php

namespace SM_Elementor\Module\DynamicTags\Tag;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Core\DynamicTags\Data_Tag;
use ElementorPro\Modules\DynamicTags\ACF\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Custom extends Tag {

	public function get_name() {
		return 'sm-custom';
	}

	public function get_title() {
		return __( 'SM: Custom', 'elementor-pro' );
	}

    public function get_group() {
        return 'site';
    }

    public function get_categories() {
        return [
            Module::TEXT_CATEGORY,
        ];
    }

    protected function _register_controls() {
        $this->add_control(
            'source_id',
            [
                'label' => __( 'Source ID', 'elementor-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );
    }

    public function render()
    {
        $settings = $this->get_settings();

        if ( $source_id = $settings['source_id'] ) {

            $value = apply_filters('sm_elementor/tag/'.$source_id, null);

        }

        echo wp_kses_post( $value );
    }
}
