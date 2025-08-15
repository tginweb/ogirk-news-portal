<?php


namespace SM_Elementor\Widget;

use Elementor\Controls_Manager;
use ElementorPro\Base\Base_Widget;
use ElementorPro\Modules\Library\Module;
use ElementorPro\Plugin;
use SM_Elementor\Common;


class Template extends Common\Widget {

    public function get_name() {
        return 'sm-template';
    }

    public function get_title() {
        return __( 'Template', 'elementor' );
    }


    public function is_reload_preview_required() {
        return false;
    }

    protected function _register_controls() {


        $this->start_controls_section(
            'section_template',
            [
                'label' => __( 'Template', 'elementor-pro' ),
            ]
        );

        $templates = Module::get_templates();

        if ( empty( $templates ) ) {

            $this->add_control(
                'no_templates',
                [
                    'label' => false,
                    'type' => Controls_Manager::RAW_HTML,
                    'raw' => Module::empty_templates_message(),
                ]
            );

            return;
        }

        $options = [
            '0' => '— ' . __( 'Select', 'elementor-pro' ) . ' —',
        ];

        $types = [];

        foreach ( $templates as $template ) {
            $options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
            $types[ $template['template_id'] ] = $template['type'];
        }

        $this->add_control(
            'template_id',
            [
                'label' => __( 'Choose Template', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'default' => '0',
                'options' => $options,
                'types' => $types,
                'label_block' => 'true',
            ]
        );


        $this->add_control(
            'context_query_type',
            array(
                'label' => 'Context query',
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => '',
                    'post' => 'Post',
                    'term' => 'Term',
                ],
                'default' => '',
            )
        );

        $this->add_control(
            'context_query_term_term_id',
            array(
                'label' => 'Term ID',
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'context_query_type' => 'term',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            )
        );

        $this->add_control(
            'context_query_post_post_id',
            array(
                'label' => 'Post ID',
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'context_query_type' => 'post',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            )
        );


        $this->end_controls_section();
    }

    function render_cacheable() {

        $settings = $this->get_settings_for_display();

        $context_query_args = [];

        if ($settings['context_query_type'])
        {
            switch ( $settings['context_query_type'] ) {

                case 'term':

                    $term_id = $settings['context_query_term_term_id'];

                    if ($term_id && ($term = get_term($term_id)) && ! is_wp_error( $term ))
                    {
                        $context_query_args['tax_query'][] = [
                            'taxonomy' => $term->taxonomy,
                            'terms' => [ $term->term_id ],
                            'field' => 'id',
                        ];
                    }
                    else
                    {
                        $disable = true;
                    }

                    break;

                case 'post':

                    $post_id = $settings['context_query_post_post_id'];

                    if ($post_id && ($post = get_post($post_id))) {

                        $context_query_args = [
                            'p' => $post->ID,
                            'post_type' => $post->post_type,
                            'sm-subpage' => get_query_var('sm-subpage')
                        ];
                    }
                    else
                    {
                        $disable = true;
                    }

                    break;
            }

            if ($disable) return;

            if (!empty($context_query_args))
            {
                Plugin::elementor()->db->switch_to_query( $context_query_args );
            }
        }

        $template_id = $settings['template_id'];

        if ( 'publish' !== get_post_status( $template_id ) ) {
            return;
        }


        ?>
        <div class="elementor-template">
            <?php echo Plugin::elementor()->frontend->get_builder_content_for_display( $template_id ); ?>
        </div>
        <?php

        if (!empty($context_query_args))
        {
            Plugin::elementor()->db->restore_current_query();
        }
    }

    public function render_plain_content() {}
}
