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

class Term_Url extends Data_Tag {

    public function get_name() {
        return 'sm-term-url';
    }

    public function get_group() {
        return Module::SITE_GROUP;
    }

    public function get_categories() {
        return [ Module::URL_CATEGORY ];
    }

    public function get_title() {
        return __( 'SM: Term URL', 'elementor-pro' );
    }

    public function get_panel_template() {
        return ' ({{ url }})';
    }

    protected function _register_controls() {
        $this->add_control(
            'term_id',
            [
                'label' => __( 'Term', 'elementor-pro' ),
                'type' => 'query',
                'multiple' => false,
                'filter_type' => 'taxonomy',
                'include_type' => true,
            ]
        );
    }

    public function get_value( array $options = [] ) {

        $settings = $this->get_settings();

        if (isset($settings['term_id']) && intval($settings['term_id'])>0)
        {
            $term_id = $settings['term_id'];

            $term = get_term($term_id);

            if ($term && !is_wp_error($term))
            {
                $link = get_term_link($term);

                return $link;
            }
        }
    }
}
