<?php


namespace SM_Elementor\Module\Hacks;


class Module extends \SM_Elementor\Common\Plugin_Module
{
    function init_events()
    {
        $this->add_action('elementor/element/before_section_end', null, 10, 3);
    }

    function _action_elementor_element_before_section_end($element, $section_id, $args)
    {
        $el_type = $element->get_name();

        $path = join('.', [$el_type, $args['tab'], $section_id]);

        /*
        if (in_array($path, ['column.advanced.section_advanced']))
        {
            $element->update_control(
                'margin',
                [
                    'selectors' => [
                        'body {{WRAPPER}} > .elementor-element-populated' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    ],
                ]
            );

            $element->update_control(
                'padding',
                [
                    'selectors' => [
                        'body  {{WRAPPER}} > .elementor-element-populated' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    ],
                ]
            );
        }
        */

        if ($section_id=='section_layout' && $args['tab']=='layout')
        {
            $element->update_control(
                'gap',
                [
                    'options' => [
                        'default' => __( 'Default', 'elementor' ),
                        'h-default' => __( 'Default Horizontal', 'elementor' ),
                        'v-default' => __( 'Default Vertical', 'elementor' ),
                        'no' => __( 'No Gap', 'elementor' ),
                        'narrow' => __( 'Narrow', 'elementor' ),
                        'extended' => __( 'Extended', 'elementor' ),
                        'wide' => __( 'Wide', 'elementor' ),
                        'wider' => __( 'Wider', 'elementor' ),

                        'layout' => __( 'Layout', 'elementor' ),
                        'layout_tablet' => __( 'Layout Tablet', 'elementor' ),
                    ],
                ]
            );
        }
    }
}



