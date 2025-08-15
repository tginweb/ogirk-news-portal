<?php

namespace SM_Elementor\Module\Repeater\Control;

use Elementor\Plugin;
use Elementor\Base_Data_Control;


class Repeater extends \Elementor\Control_Repeater {

    static $repeater_items = [];

    public function get_element_source_id($control, $settings)
    {
        return rand(100000, 900000).'_'.$control['name'];
    }

	/**
	 * Get repeater control value.
	 *
	 * Retrieve the value of the repeater control from a specific Controls_Stack.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $control  Control
	 * @param array $settings Controls_Stack settings
	 *
	 * @return mixed Control values.
	 */
	public function get_value( $control, $settings ) {

        $value = \Elementor\Base_Data_Control::get_value( $control, $settings );

        if (!empty($settings[$control['name'].'_source']))
        {
            list($source_engine, $source_field_name, $source_field_meta) = explode(':', $settings[$control['name'].'_source']);

            switch ($source_engine)
            {
                case 'acf':

                    $source_field = acf_get_field($source_field_name);

                    $source_repeater_items = get_field($source_field_meta);

                    $model_values = $value;

                    $value = [];

                    $element_source_id = $this->get_element_source_id($control, $settings);


                    if (!empty($source_repeater_items))
                    {
                        foreach ($source_repeater_items as $index=>$item)
                        {
                            $item = $item + $model_values[0];

                            $id = $index;

                            $item['_id'] = $id;

                            self::$repeater_items[$element_source_id][$id] = $item;

                            foreach ( $control['fields'] as $field )
                            {
                                $control_obj = Plugin::$instance->controls_manager->get_control( $field['type'] );

                                // Prior to 1.5.0 the fields may contains non-data controls.
                                if ( ! $control_obj instanceof Base_Data_Control ) {
                                    continue;
                                }

                                $item_field_value = $control_obj->get_value( $field, $item );

                                $item[ $field['name'] ] = $item_field_value;
                            }

                            if (!empty($item['__dynamic__']))
                            {
                                foreach ($item['__dynamic__'] as $field=>$tag)
                                {
                                    $tag_data = \ElementorPro\Plugin::elementor()->dynamic_tags->tag_text_to_tag_data($tag);

                                    $tag_data['settings']['source_list_id'] = $element_source_id;

                                    $tag_data['settings']['source_item_id'] = $id;

                                    $item['__dynamic__'][$field] = \ElementorPro\Plugin::elementor()->dynamic_tags->tag_data_to_tag_text( $tag_data['id'], $tag_data['name'], $tag_data['settings']);

                                    $item[$field] = $this->parse_tags($item['__dynamic__'][$field], ['returnType'=>'object']);

                                    unset($item['__dynamic__'][$field]);
                                }
                            }

                            $value[] = $item;
                        }

                    }

                    break;

                case 'custom':

                    break;
            }


        }
        else
        {
            if ( ! empty( $value ) ) {
                foreach ( $value as &$item ) {
                    foreach ( $control['fields'] as $field ) {
                        $control_obj = Plugin::$instance->controls_manager->get_control( $field['type'] );

                        // Prior to 1.5.0 the fields may contains non-data controls.
                        if ( ! $control_obj instanceof Base_Data_Control ) {
                            continue;
                        }

                        $item[ $field['name'] ] = $control_obj->get_value( $field, $item );
                    }
                }
            }
        }

        if (!empty($settings[$control['name'].'_rep_id']))
        {
            $repeater_id = $settings[$control['name'].'_rep_id'];

            $value = apply_filters('sm_elementor/repeater/value/'.$repeater_id, $value);
        }

		return $value;
	}


}
