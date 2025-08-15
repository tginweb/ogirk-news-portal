<?php
namespace ElementorPro\Modules\ThemeBuilder\Conditions;


use ElementorPro\Modules\QueryControl\Module as QueryModule;
use SM\Context;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Singular_Template extends Condition_Base {

    public static function get_type() {
        return 'singular';
    }

    public function get_name() {
        return 'singular_template';
    }

    public function get_label() {
        return __( 'Singular Template', 'elementor-pro' );
    }

    public function check( $args ) {

        $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( get_the_ID() );

        if ( $document ) {
            $the_template = $document->get_meta('_wp_page_template');
        }

        //$the_template = get_post_meta( get_the_ID(), '_wp_page_template', true );

        if (!$the_template)
            $the_template = 'default';


        if ($args['id']=='all') return true;

        else if ($args['id']=='not_default') return $the_template!=='default';

        return $the_template === $args['id'];
    }

    protected function _register_controls() {

        $template_options = [
            'all' => __( 'All', 'elementor' ),
            'default' => __( 'Default', 'elementor' ),
            'not_default' => __( 'Not default', 'elementor' ),
        ];

        $template_options += array_flip( get_page_templates( null, 'post' ) );

        $this->add_control(
            'template',
            [
                'section' => 'settings',
                'type' => 'select',
                'options' => $template_options,
            ]
        );
    }
}
