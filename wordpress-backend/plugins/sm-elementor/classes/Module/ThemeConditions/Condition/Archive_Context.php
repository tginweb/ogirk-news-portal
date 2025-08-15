<?php
namespace ElementorPro\Modules\ThemeBuilder\Conditions;

use ElementorPro\Modules\QueryControl\Module as QueryModule;
use SM\Context;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Archive_Context extends Condition_Base {

    public static function get_type() {
        return 'archive';
    }

    public function get_name() {
        return 'archive_context';
    }

    public function get_label() {
        return __( 'Archive Context', 'elementor-pro' );
    }

    public function check( $args ) {

        $id = $args['id'];

        return Context::i()->is_active($id);
    }

    protected function _register_controls() {

        $context_options = [];

        foreach (Context::i()->get_contexts() as $context_id => $context_info)
        {
            $context_options[$context_id] = $context_info['label'] ?: $context_id;
        }

        $this->add_control(
            'context_id',
            [
                'section' => 'settings',
                'type' => 'select',
                'options' => $context_options,
            ]
        );
    }
}
