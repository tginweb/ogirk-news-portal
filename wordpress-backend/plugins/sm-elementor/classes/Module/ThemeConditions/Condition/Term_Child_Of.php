<?php
namespace ElementorPro\Modules\ThemeBuilder\Conditions;

use ElementorPro\Modules\QueryControl\Module as QueryModule;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Term_Child_Of extends Condition_Base {

    public static function get_type() {
        return 'archive';
    }

    public function get_name() {
        return 'term_child_of';
    }

    public function get_label() {
        return __( 'Term Child Of', 'elementor-pro' );
    }

    public function check( $args ) {

        $id = (int) $args['id'];
        //$parent_id = wp_get_post_parent_id( get_the_ID() );

        if ($term = get_term($id))
        {
            $term_children_ids = get_term_children($term->term_id, $term->taxonomy);

            if ( 'category' === $term->taxonomy ) {
                return is_category( $id );
            }

            if ( 'post_tag' === $term->taxonomy ) {
                return is_tag( $id );
            }

            return is_tax( $term->taxonomy, $term_children_ids);
        }

        return false;
    }

    protected function _register_controls() {
        $this->add_control(
            'parent_id',
            [
                'section' => 'settings',
                'type' => QueryModule::QUERY_CONTROL_ID,
                'select2options' => [
                    'dropdownCssClass' => 'elementor-conditions-select2-dropdown',
                ],
                'filter_type' => 'taxonomy',
                //'object_type' => 'page',
            ]
        );
    }
}
