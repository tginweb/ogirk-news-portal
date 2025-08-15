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


class Archive_Url_Listing extends Archive_URL {

	public function get_name() {
		return 'sm-archive-url-listing';
	}

	public function get_title() {
		return __( 'SM: Archive URL Listing', 'elementor-pro' );
	}

    public function get_value( array $options = [] ) {
        return '/archive/'.get_queried_object_id();
    }
}
