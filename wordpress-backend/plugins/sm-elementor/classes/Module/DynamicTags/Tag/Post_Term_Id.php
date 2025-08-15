<?php

namespace SM_Elementor\Module\DynamicTags\Tag;


use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Core\DynamicTags\Data_Tag;
use ElementorPro\Modules\DynamicTags\ACF;
use ElementorPro\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Post_Term_Id extends Data_Tag {

	public function get_name() {
		return 'sm-post-term-id';
	}

	public function get_title() {
		return __( 'SM: Post Term ID', 'elementor-pro' );
	}

    public function get_group() {
        return 'post';
    }

    public function get_categories() {
        return [
            ACF\Module::TEXT_CATEGORY,
            ACF\Module::POST_META_CATEGORY,
        ];
    }

    protected function _register_controls() {
        $taxonomy_filter_args = [
            'show_in_nav_menus' => true,
            'object_type' => [ get_post_type() ],
        ];

        $taxonomy_filter_args = apply_filters( 'elementor_pro/dynamic_tags/post_terms/taxonomy_args', $taxonomy_filter_args );

        $taxonomies = Utils::get_taxonomies( $taxonomy_filter_args, 'objects' );

        $options = [];

        foreach ( $taxonomies as $taxonomy => $object ) {
            $options[ $taxonomy ] = $object->label;
        }

        $this->add_control(
            'taxonomy',
            [
                'label' => __( 'Taxonomy', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'options' => $options,
                'default' => 'post_tag',
            ]
        );
    }

    public function get_value(array $options = [] ) {
        $settings = $this->get_settings();

        $terms = wp_get_post_terms( get_the_ID(), $settings['taxonomy'] );


        $term = reset($terms);

        if ($term) return $term->term_id;
    }
}
