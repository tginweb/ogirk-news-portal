<?php


namespace SM_Elementor\Module\Preset;

use Elementor\Controls_Stack;
use ElementorPro\Plugin;
use Elementor\Controls_Manager;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    var $presets;
    var $presets_by_widget_type;
    var $controls_added_types = [];

    function init_events()
    {
        $this->add_action('elementor/element/after_section_end', null, 10, 3);
        $this->add_filter('sm_elementor/editor/localize_settings');
        $this->add_filter('elementor_pro/editor/localize_settings');
    }


    function get_presets_info()
    {
        if (!isset($this->presets))
        {
            $elementor = Plugin::elementor();

            $templates_manager = $elementor->templates_manager;

            $widgets_types = $elementor->widgets_manager->get_widget_types();


            $widget_templates = array_filter( $templates_manager->get_source( 'local' )->get_items(), function( $template ) use ( $widgets_types ) {
                return ! empty( $template['widgetType'] ) && ! empty( $widgets_types[ $template['widgetType'] ] );
            } );


            $this->presets = [];

            $this->presets_by_widget_type = [];

            foreach ( $widget_templates as $widget_template ) {

                $widget_settings = get_post_meta( $widget_template['template_id'], '_elementor_data', true );

                if ( is_string( $widget_settings ) && ! empty( $widget_settings ) ) {
                    $widget_settings = json_decode( $widget_settings, true );
                }

                if (!empty($widget_settings) && !empty($widget_settings[0]))
                {
                    $widget_settings = $widget_settings[0];

                    if (!empty($widget_settings['settings']['sm_preset_widget']) && $widget_settings['settings']['sm_preset_widget']==='yes')
                    {
                        $widget_template_info = [
                            'elType' => 'widget',
                            'title' => $widget_template['title'],
                            'widgetType' => $widget_template['widgetType'],
                        ];

                        $this->presets[ $widget_template['template_id'] ] = $widget_template_info;

                        $this->presets_by_widget_type[$widget_template['widgetType']][$widget_template['template_id']] = $widget_template_info;
                    }
                }
            }

        }

        return $this->presets;
    }


    function _filter_elementor_pro_editor_localize_settings($settings) {

        $this->get_presets_info();

        $settings = array_replace_recursive( $settings, [
            'preset_widgets' => $this->presets,
        ] );

        return $settings;
    }

    function _action_elementor_element_after_section_end(Controls_Stack $element, $section_id, $args)
    {
        if ($element->get_type()!='widget') return;

        $widget_type = $element->get_name();

        if (!isset($this->controls_added_types[$widget_type]))
        {
            $this->controls_added_types[$widget_type] = true;

            $this->get_presets_info();


            if (!empty($this->presets_by_widget_type[$widget_type]))
            {
                $preset_options = [];

                foreach ($this->presets_by_widget_type[$widget_type] as $widget_post_id=>$widget_type_info)
                {
                    $preset_options[$widget_post_id] = $widget_type_info['title'];
                }

                $element->start_controls_section(
                    'section_sm_preset',
                    [
                        'label' => __( 'Preset', 'elementor-pro' ),
                        'tab' => Controls_Manager::TAB_SETTINGS
                    ]
                );

                $element->add_control(
                    'sm_preset',
                    [
                        'type' => Controls_Manager::SELECT,
                        'label' => 'Пресет',
                        'options' => $preset_options
                    ]
                );

                $element->add_control(
                    'sm_preset_apply',
                    [
                        'type' => Controls_Manager::BUTTON,
                        'label' => __( 'Apply & Preview', 'elementor-pro' ),
                        'label_block' => true,
                        'show_label' => false,
                        'text' => __( 'Apply & Preview', 'elementor-pro' ),
                        'separator' => 'none',
                        'event' => 'smElementor:ApplyPreset',
                    ]
                );

                $element->end_controls_section();
            }

        }


        if ($args['tab']=='advanced' && in_array($section_id, ['_section_style', 'section_advanced']))
        {
            $element->start_controls_section(
                'section_sm_preset_widget',
                [
                    'label' => __('Preset Widget'),
                    'tab' => Controls_Manager::TAB_ADVANCED,
                ]
            );

            $element->add_control(
                'sm_preset_widget',
                [
                    'label' => __('Is preset?'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $element->end_controls_section();
        }
    }

}



