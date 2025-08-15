<?php

namespace SM_Elementor\Module\DynamicTags\Tag;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use ElementorPro\Modules\DynamicTags\ACF\Module;
use ElementorPro\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_Post_Content extends Tag {

	public function get_name() {
		return 'acf-post-content';
	}

	public function get_title() {
		return __( 'SM: ACF Post Content', 'elementor-pro' );
	}

	public function get_group() {
		return Module::ACF_GROUP;
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
			Module::POST_META_CATEGORY,
		];
	}


    public function render() {

        $key = $this->get_settings( 'key' );

        if ( empty( $key ) ) {
            return;
        }



        list( $field_key, $meta_key ) = explode( ':', $key );

        if ( 'options' === $field_key ) {
            $field = get_field_object( $meta_key, $field_key );
        } else {
            $field = get_field_object( $field_key );
        }

        if ( $field && ! empty( $field['type'] ) ) {
            $value = $field['value'];
        } else {
            // Field settings has been deleted or not available.
            $value = get_field( $meta_key );
        } // End if().

        if (!$value) return;

        $post = get_post($value);

        if (!$post) return;

        static $did_posts = [];

        if ( post_password_required( $post->ID ) ) {
            echo get_the_password_form( $post->ID );

            return;
        }

        // Avoid recursion
        if ( isset( $did_posts[ $post->ID ] ) ) {
            return;
        }

        $did_posts[ $post->ID ] = true;
        // End avoid recursion

        if ( Plugin::elementor()->preview->is_preview_mode( $post->ID ) ) {
            $content = Plugin::elementor()->preview->builder_wrapper( '' ); // XSS ok
        } else {

            $document = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_document( $post->ID );

            // On view theme document show it's preview content.
            if ( $document ) {
                $preview_type = $document->get_settings( 'preview_type' );
                $preview_id = $document->get_settings( 'preview_id' );

                if ( 0 === strpos( $preview_type, 'single' ) && ! empty( $preview_id ) ) {
                    $post = get_post( $preview_id );

                    if ( ! $post ) {
                        return;
                    }
                }
            }

            $editor = Plugin::elementor()->editor;

            // Set edit mode as false, so don't render settings and etc. use the $is_edit_mode to indicate if we need the CSS inline
            $is_edit_mode = $editor->is_edit_mode();
            $editor->set_edit_mode( false );

            // Print manually (and don't use `the_content()`) because it's within another `the_content` filter, and the Elementor filter has been removed to avoid recursion.
            $content = Plugin::elementor()->frontend->get_builder_content( $post->ID, true );

            // Restore edit mode state
            Plugin::elementor()->editor->set_edit_mode( $is_edit_mode );

            if ( empty( $content ) ) {
                Plugin::elementor()->frontend->remove_content_filter();

                // Split to pages.
                setup_postdata( $post );

                /** This filter is documented in wp-includes/post-template.php */
                echo apply_filters( 'the_content', get_the_content() );

                wp_link_pages( [
                    'before' => '<div class="page-links elementor-page-links"><span class="page-links-title elementor-page-links-title">' . __( 'Pages:', 'elementor-pro' ) . '</span>',
                    'after' => '</div>',
                    'link_before' => '<span>',
                    'link_after' => '</span>',
                    'pagelink' => '<span class="screen-reader-text">' . __( 'Page', 'elementor-pro' ) . ' </span>%',
                    'separator' => '<span class="screen-reader-text">, </span>',
                ] );

                Plugin::elementor()->frontend->add_content_filter();

                return;
            }
        } // End if().

        echo $content; // XSS ok.
    }

    public function get_panel_template_setting_key() {
        return 'key';
    }

    protected function _register_controls() {
        $this->add_control(
            'key',
            [
                'label' => __( 'Key', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'groups' => Module::get_control_options( $this->get_supported_fields() ),
            ]
        );
    }

    protected function get_supported_fields() {
        return [
            'text',
            'textarea',
            'number',
            'select',
            'radio',
            'post_object',
        ];
    }
}
