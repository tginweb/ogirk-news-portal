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


class Post_Tax_Post_Title extends Tag {

	public function get_name() {
		return 'sm-post-tax-post-title';
	}

	public function get_title() {
		return __( 'SM: Post Taxonomy Post Title', 'elementor-pro' );
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

        $this->add_control(
            'taxonomy',
            [
                'label' => __( 'Taxonomy' ),
                'type' => Controls_Manager::SELECT,
                'options' => \SM\Util\Wp::get_taxonomies([], 'names'),
                'default' => 'category',
            ]
        );

        $this->add_control(
            'post_type',
            [
                'label' => __( 'Post type' ),
                'type' => Controls_Manager::SELECT,
                'options' => \SM\Util\Wp::get_post_types([], 'names'),
                'default' => 'category',
            ]
        );
    }

    public function render() {

        $settings = $this->get_settings();


        if (
            !empty($settings['post_type']) &&
            post_type_exists($settings['post_type']) &&
            !empty($settings['taxonomy']) &&
            taxonomy_exists($settings['taxonomy'])
        )
        {
            $terms = wp_get_post_terms( get_the_ID(), $settings['taxonomy'] );

            if (!empty($terms) && !is_wp_error($terms))
            {
                $term = reset($terms);

                $posts = get_posts(array(
                    'post_type' => $settings['post_type'],
                    'numberposts' => 1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => $settings['taxonomy'],
                            'field' => 'id',
                            'terms' => $term->term_id,
                            'include_children' => false
                        )
                    )
                ));


                if (!empty($posts))
                {
                    $post = current($posts);

                    print $post->post_title;
                }
            }
        }

    }
}
