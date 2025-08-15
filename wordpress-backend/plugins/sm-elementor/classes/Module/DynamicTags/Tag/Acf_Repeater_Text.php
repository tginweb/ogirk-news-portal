<?php

namespace SM_Elementor\Module\DynamicTags\Tag;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use ElementorPro\Modules\DynamicTags\ACF\Module;
use SM_Elementor\Module\Repeater\Control\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_Repeater_Text extends Tag {

	public function get_name() {
		return 'acf-repeater-text';
	}

	public function get_title() {
		return __( 'SM: ACF Repeater Text SubField', 'elementor-pro' );
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

        $field = get_field_object( $field_key );




        if (!isset(Repeater::$repeater_items[$this->get_settings( 'source_list_id' )][$this->get_settings( 'source_item_id' )][$meta_key]))
         return;

        $value = Repeater::$repeater_items[$this->get_settings( 'source_list_id' )][$this->get_settings( 'source_item_id' )][$meta_key];


		if ( $field && ! empty( $field['type'] ) ) {

			switch ( $field['type'] ) {
				case 'radio':
					if ( isset( $field['choices'][ $value ] ) ) {
						$value = $field['choices'][ $value ];
					}
					break;
				case 'select':
					// Usa as array for `multiple=true` or `return_format=array`.
					$values = (array) $value;

					foreach ( $values as $key => $item ) {
						if ( isset( $field['choices'][ $item ] ) ) {
							$values[ $key ] = $field['choices'][ $item ];
						}
					}

					$value = implode( ', ', $values );

					break;
				case 'checkbox':
					$values = [];
					foreach ( $value as $item ) {
						if ( isset( $field['choices'][ $item ] ) ) {
							$values[] = $field['choices'][ $item ];
						} else {
							$values[] = $item;
						}
					}

					$value = implode( ', ', $values );

					break;
				case 'oembed':
					// Get from db without formatting.
					$value = get_post_meta( get_the_ID(), $meta_key, true );
					break;
				case 'google_map':
					$meta = get_post_meta( get_the_ID(), $meta_key, true );
					$value = isset( $meta['address'] ) ? $meta['address'] : '';
					break;
			} // End switch().
		}

		echo wp_kses_post( $value );
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
				'groups' => $this->get_control_options( $this->get_supported_fields() ),
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
			'text',
			'textarea',
			'number',
			'email',
			'password',
			'wysiwyg',
			'select',
			'checkbox',
			'radio',
			'true_false',

			// Pro
			'oembed',
			'google_map',
			'date_picker',
			'time_picker',
			'color_picker',
		];
	}
}
