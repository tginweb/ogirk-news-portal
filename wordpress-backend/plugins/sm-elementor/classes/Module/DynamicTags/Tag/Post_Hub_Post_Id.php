<?php

namespace SM_Elementor\Module\DynamicTags\Tag;


use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Core\DynamicTags\Data_Tag;
use ElementorPro\Modules\DynamicTags\ACF\Module;
use ElementorPro\Classes\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Post_Hub_Post_Id extends Data_Tag {

	public function get_name() {
		return 'sm-post-hub-post-id';
	}

	public function get_title() {
		return __( 'SM: Post Hub post ID', 'elementor-pro' );
	}

    public function get_group() {
        return 'post';
    }

    public function get_categories() {
        return [
            Module::TEXT_CATEGORY,
            Module::POST_META_CATEGORY,
        ];
    }

    protected function _register_controls() {

    }

    public function get_value(array $options = [] ) {

        $settings = $this->get_settings();

        $hub_terms = wp_get_post_terms( get_the_ID(), 'sm-hub-term' );

        $hub_term = reset($hub_terms);


        if ($hub_term)
        {
            $hub_posts = get_posts(array(
                'post_type' => 'sm-hub-post',
                'numberposts' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'sm-hub-term',
                        'field' => 'id',
                        'terms' => $hub_term->term_id,
                        'include_children' => false
                    )
                )
            ));


            if (!empty($hub_posts))
            {
                $hub_post = current($hub_posts);

                return $hub_post->ID;
            }
        }
    }
}
