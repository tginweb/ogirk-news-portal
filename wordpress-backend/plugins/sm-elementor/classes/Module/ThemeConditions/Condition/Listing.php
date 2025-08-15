<?php

namespace ElementorPro\Modules\ThemeBuilder\Conditions;


class Listing extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base {

    public static function get_type() {
        return 'archive';
    }

    public function get_name() {
        return 'listing';
    }

    public function get_label() {
        return __( 'Listing Archive', 'elementor-pro' );
    }

    public function check( $args ) {
        return !empty($_REQUEST['listing']);
    }
}

