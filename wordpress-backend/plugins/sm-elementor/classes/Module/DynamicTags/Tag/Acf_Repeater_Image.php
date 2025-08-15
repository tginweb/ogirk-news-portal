<?php

namespace SM_Elementor\Module\DynamicTags\Tag;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Core\DynamicTags\Data_Tag;
use ElementorPro\Modules\DynamicTags\ACF\Module;
use SM_Elementor\Module\Repeater\Control\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_Repeater_Image extends Data_Tag {

	public function get_name() {
		return 'acf-repeater-image';
	}

	public function get_title() {
		return __( 'SM: ACF Repeater Image SubField', 'elementor-pro' );
	}

    public function get_group() {
        return Module::ACF_GROUP;
    }

    public function get_categories() {
        return [ Module::IMAGE_CATEGORY ];
    }

    public function get_panel_template_setting_key() {
        return 'key';
    }

    public function get_value( array $options = [] ) {
        $key = $this->get_settings( 'key' );

        $image_data = [
            'id' => null,
            'url' => '',
        ];


        if ( ! empty( $key ) ) {

            list( $field_key, $meta_key ) = explode( ':', $key );

            $field = get_field_object( $field_key );

            if (!isset(Repeater::$repeater_items[$this->get_settings( 'source_list_id' )][$this->get_settings( 'source_item_id' )][$meta_key]))
                return;

            $field['value'] = Repeater::$repeater_items[$this->get_settings( 'source_list_id' )][$this->get_settings( 'source_item_id' )][$meta_key];

            if ( $field && ! empty( $field['return_format'] ) ) {
                switch ( $field['return_format'] ) {
                    case 'object':
                    case 'array':
                        $value = $field['value'];
                        break;
                    case 'url':
                        $value = [
                            'id' => 0,
                            'url' => $field['value'],
                        ];
                        break;
                    case 'id':
                        $src = wp_get_attachment_image_src( $field['value'], $field['preview_size'] );
                        $value = [
                            'id' => $field['value'],
                            'url' => $src[0],
                        ];
                        break;
                }
            }

            if ( ! isset( $value ) ) {
                // Field settings has been deleted or not available.
                $value = get_field( $meta_key );
            }

            if ( empty( $value ) && $this->get_settings( 'fallback' ) ) {
                $value = $this->get_settings( 'fallback' );
            }

            if ( ! empty( $value ) ) {
                $image_data['id'] = $value['id'];
                $image_data['url'] = $value['url'];
            }
        } // End if().

        return $image_data;
    }

    protected function _register_controls() {
        $this->add_control(
            'key',
            [
                'label' => __( 'Key', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'groups' => $this->get_control_options( $this->get_supported_fields() ),
            ]
        );

        $this->add_control(
            'fallback',
            [
                'label' => __( 'Fallback', 'elementor-pro' ),
                'type' => Controls_Manager::MEDIA,
            ]
        );


    }

    public static function get_control_options( $types ) {
        // ACF >= 5.0.0
        if ( function_exists( 'acf_get_field_groups' ) ) {
            $acf_groups = acf_get_field_groups();
        } else {
            $acf_groups = apply_filters( 'acf/get_field_groups', [] );
        }

        $groups = [];

        foreach ( $acf_groups as $acf_group ) {

            // ACF >= 5.0.0
            if ( function_exists( 'acf_get_fields' ) ) {
                $fields = acf_get_fields( $acf_group['ID'] );
            } else {
                $fields = apply_filters( 'acf/field_group/get_fields', [], $acf_group['id'] );
            }

            $options = [];

            if ( ! is_array( $fields ) ) {
                continue;
            }

            foreach ( $fields as $field )
            {
                if ($field['type']=='repeater')
                {
                    $subfields = acf_get_fields($field);

                    foreach ($subfields as $subfield)
                    {
                        if ( ! in_array( $subfield['type'], $types, true ) ) {
                            continue;
                        }

                        $key = $subfield['key'] . ':' . $subfield['name'];
                        $options[ $key ] = $field['label'].': '.$subfield['label'];
                    }
                }
            }

            if ( empty( $options ) ) {
                continue;
            }

            $groups[] = [
                'label' => $acf_group['title'],
                'options' => $options,
            ];
        } // End foreach().

        return $groups;
    }

    protected function get_supported_fields() {
        return [
            'image',
        ];
    }
}
