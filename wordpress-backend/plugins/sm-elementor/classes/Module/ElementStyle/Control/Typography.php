<?php

namespace SM_Elementor\Module\ElementStyle\Control;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Typography extends \Elementor\Group_Control_Typography {

    protected function init_fields() {

        $fields = parent::init_fields();

        $fields['display'] = [
            'label' => 'Display',
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => '',
                'inline' => 'inline',
            ],
            'responsive' => true,
            'selector_value' => 'display: inline',
        ];

        $fields['border_bottom_style'] = [
            'label' => 'Underline style',
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => '',
                'solid' => 'solid',
                'dashed' => 'dashed',
                'dotted' => 'dotted',
            ],
            'responsive' => true,
            'selector_value' => 'border-bottom-style: {{VALUE}}',
            'condition' => [
                'display' => 'inline'
            ]
        ];

        $fields['border_bottom_width'] = [
            'label' => 'Underline size',
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
            'responsive' => true,
            'selector_value' => 'border-bottom-width: {{VALUE}}px',
            'condition' => [
                'display' => 'inline',
                'border_bottom_style!' => ''
            ]
        ];

        $fields['border_bottom_color'] = [
            'label' => 'Underline color',
            'type' => Controls_Manager::COLOR,
            'selector_value' => 'border-bottom-color: {{VALUE}}',
            'condition' => [
                'display' => 'inline',
                'border_bottom_style!' => ''
            ]
        ];

        return $fields;
    }

    /**
     * Prepare fields.
     *
     * Process typography control fields before adding them to `add_control()`.
     *
     * @since 1.2.3
     * @access protected
     *
     * @param array $fields Typography control fields.
     *
     * @return array Processed fields.
     */
    protected function prepare_fields( $fields ) {
        array_walk(
            $fields, function( &$field, $field_name ) {
            if ( in_array( $field_name, [ 'typography', 'popover_toggle' ] ) ) {
                return;
            }

            if (empty($field['selectors']))
            {
                $selector_value = ! empty( $field['selector_value'] ) ? $field['selector_value'] : str_replace( '_', '-', $field_name ) . ': {{VALUE}};';

                $field['selectors'] = [
                    '{{SELECTOR}}' => $selector_value,
                ];
            }

            $field['condition']['typography'] = 'custom';
        }
        );

        return \Elementor\Group_Control_Base::prepare_fields( $fields );
    }
}
