<?php

namespace SM_Elementor\Module\DynamicTags\Tag;


use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Core\DynamicTags\Data_Tag;
use ElementorPro\Modules\DynamicTags\ACF\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Queried_Object_Id extends Data_Tag {

	public function get_name() {
		return 'sm-queried-object-id';
	}

	public function get_title() {
		return __( 'SM: Queried object ID', 'elementor-pro' );
	}

    public function get_group() {
        return 'site';
    }

    public function get_categories() {
        return [
            Module::TEXT_CATEGORY,
            Module::POST_META_CATEGORY,
        ];
    }

    public function get_value( array $options = [] ) {

        return get_queried_object_id();
    }
}
