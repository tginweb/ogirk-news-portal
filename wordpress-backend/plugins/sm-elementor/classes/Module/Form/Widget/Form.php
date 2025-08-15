<?php


namespace SM_Elementor\Module\Form\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use ElementorPro\Classes\Utils;
use ElementorPro\Plugin;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
use ElementorPro\Modules\Forms\Classes\Form_Base;
use ElementorPro\Modules\Forms\Module;


class Form extends \ElementorPro\Modules\Forms\Widgets\Form {

    public function get_name() {
        return 'sm-elementor-pro-form';
    }

    public function get_title() {
        return __( 'SM: Pro Form', 'elementor' );
    }

    protected function _register_controls() {

        parent::_register_controls();

        $this->start_controls_section(
            'section_sm_form_template',
            [
                'label' => __('Form template'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $templates_options = \SM_Elementor\Common\Plugin_Module::get_templates_options();

        $templates_options = ['0' => '— ' . __( 'Select', 'elementor-pro' ) . ' —'] + $templates_options;

        $this->add_control(
            'sm_form_template_id',
            [
                'label' => __( 'Choose Template', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'default' => '0',
                'options' => $templates_options,
                'label_block' => 'true',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $instance = $this->get_active_settings();

        $this->add_render_attribute(
            [
                'wrapper' => [
                    'class' => [
                        'elementor-form-fields-wrapper',
                        'elementor-labels-' . $instance['label_position'],
                    ],
                ],
                'submit-group' => [
                    'class' => [
                        'elementor-field-group',
                        'elementor-column',
                        'elementor-field-type-submit',
                    ],
                ],
                'button' => [
                    'class' => 'elementor-button',
                ],
                'icon-align' => [
                    'class' => [
                        empty( $instance['button_icon_align'] ) ? '' :
                            'elementor-align-icon-' . $instance['button_icon_align'],
                        'elementor-button-icon',
                    ],
                ],
            ]
        );

        if ( empty( $instance['button_width'] ) ) {
            $instance['button_width'] = '100';
        }

        $this->add_render_attribute( 'submit-group', 'class', 'elementor-col-' . $instance['button_width'] );

        if ( ! empty( $instance['button_width_tablet'] ) ) {
            $this->add_render_attribute( 'submit-group', 'class', 'elementor-md-' . $instance['button_width_tablet'] );
        }

        if ( ! empty( $instance['button_width_mobile'] ) ) {
            $this->add_render_attribute( 'submit-group', 'class', 'elementor-sm-' . $instance['button_width_mobile'] );
        }

        if ( ! empty( $instance['button_size'] ) ) {
            $this->add_render_attribute( 'button', 'class', 'elementor-size-' . $instance['button_size'] );
        }

        if ( ! empty( $instance['button_type'] ) ) {
            $this->add_render_attribute( 'button', 'class', 'elementor-button-' . $instance['button_type'] );
        }

        if ( $instance['button_hover_animation'] ) {
            $this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $instance['button_hover_animation'] );
        }

        if ( ! empty( $instance['form_id'] ) ) {
            $this->add_render_attribute( 'form', 'id', $instance['form_id'] );
        }

        if ( ! empty( $instance['form_name'] ) ) {
            $this->add_render_attribute( 'form', 'name', $instance['form_name'] );
        }

        if ( ! empty( $instance['button_css_id'] ) ) {
            $this->add_render_attribute( 'button', 'id', $instance['button_css_id'] );
        }

        $fields_content = [];

        ?>
        <form class="elementor-form" method="post" <?php echo $this->get_render_attribute_string( 'form' ); ?>>
            <input type="hidden" name="post_id" value="<?php echo Utils::get_current_post_id(); ?>"/>
            <input type="hidden" name="form_id" value="<?php echo $this->get_id(); ?>"/>

            <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>

                <?php


                foreach ( $instance['form_fields'] as $item_index => $item ) :

                    ob_start();


                    $item['input_size'] = $instance['input_size'];
                    $this->form_fields_render_attributes( $item_index, $instance, $item );

                    $field_type = $item['field_type'];

                    /**
                     * Render form field.
                     *
                     * Filters the field rendered by Elementor Forms.
                     *
                     * @since 1.0.0
                     *
                     * @param array $item       The field value.
                     * @param int   $item_index The field index.
                     * @param Form  $this       An instance of the form.
                     */
                    $item = apply_filters( 'elementor_pro/forms/render/item', $item, $item_index, $this );

                    /**
                     * Render form field.
                     *
                     * Filters the field rendered by Elementor Forms.
                     *
                     * The dynamic portion of the hook name, `$field_type`, refers to the field type.
                     *
                     * @since 1.0.0
                     *
                     * @param array $item       The field value.
                     * @param int   $item_index The field index.
                     * @param Form  $this       An instance of the form.
                     */
                    $item = apply_filters( "elementor_pro/forms/render/item/{$field_type}", $item, $item_index, $this );

                    if ( 'hidden' === $item['field_type'] ) {
                        $item['field_label'] = false;
                        $this->add_render_attribute( 'input' . $item_index, 'value', $item['field_value'] );
                    }

                    ?>

                    <div <?php echo $this->get_render_attribute_string( 'field-group' . $item_index ); ?>>

                        <?php

                        if ( $item['field_label'] && 'html' !== $item['field_type'] ) {
                            echo '<label ' . $this->get_render_attribute_string( 'label' . $item_index ) . '>' . $item['field_label'] . '</label>';
                        }

                        switch ( $item['field_type'] ) :
                            case 'html':
                                echo $item['field_html'];
                                break;
                            case 'textarea':
                                echo $this->make_textarea_field( $item, $item_index );
                                break;

                            case 'select':
                                echo $this->make_select_field( $item, $item_index );
                                break;

                            case 'radio':
                            case 'checkbox':
                                echo $this->make_radio_checkbox_field( $item, $item_index, $item['field_type'] );
                                break;
                            case 'text':
                            case 'email':
                            case 'url':
                            case 'password':
                            case 'hidden':
                            case 'search':
                                $this->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual' );
                                echo '<input size="1" ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
                                break;
                            default:
                                $field_type = $item['field_type'];

                                /**
                                 * Elementor form field render.
                                 *
                                 * Fires when a field is rendered.
                                 *
                                 * The dynamic portion of the hook name, `$field_type`, refers to the field type.
                                 *
                                 * @since 1.0.0
                                 *
                                 * @param array $item       The field value.
                                 * @param int   $item_index The field index.
                                 * @param Form  $this       An instance of the form.
                                 */
                                do_action( "elementor_pro/forms/render_field/{$field_type}", $item, $item_index, $this );
                        endswitch;

                        ?>

                    </div>

                    <?php

                        $field_content = ob_get_clean();

                        $fields_content[$item['_id']] = $field_content;

                    ?>

                <?php endforeach;

                ob_start();

                ?>
                <div <?php echo $this->get_render_attribute_string( 'submit-group' ); ?>>
                    <button type="submit" <?php echo $this->get_render_attribute_string( 'button' ); ?>>
                            <span <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
                                <?php if ( ! empty( $instance['button_icon'] ) ) : ?>
                                    <span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
                                        <i class="<?php echo esc_attr( $instance['button_icon'] ); ?>"></i>
                                    </span>
                                <?php endif; ?>
                                <?php if ( ! empty( $instance['button_text'] ) ) : ?>
                                    <span class="elementor-button-text"><?php echo $instance['button_text']; ?></span>
                                <?php endif; ?>
                            </span>
                    </button>
                </div>
                <?

                $fields_content['submit'] = ob_get_clean();


                if ($instance['sm_form_template_id']) {

                    \SM_Elementor\Module\Form\Module::i()->set_current_form(null, $fields_content);

                    print Plugin::elementor()->frontend->get_builder_content_for_display( $instance['sm_form_template_id'] );

                    \SM_Elementor\Module\Form\Module::i()->unset_current_form();
                }
                else
                {
                    foreach ($fields_content as $id=>$field_content)
                    {
                        print $field_content;
                    }
                }

                ?>

            </div>
        </form>
        <?php
    }

    protected function _content_template() {

    }

}
