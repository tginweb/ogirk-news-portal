<?php


namespace SM\Module\Wc;

class Util
{

    public function get_attribute_taxonomy_id( $raw_name, $attribute_name='' )
    {
        global $wpdb, $wc_product_attributes;

        // These are exported as labels, so convert the label to a name if possible first.
        // $attribute_labels = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_name' );
        // $attribute_name   = array_search( $raw_name, $attribute_labels, true );

        if ( ! $attribute_name ) {
            $attribute_name = wc_sanitize_taxonomy_name( $raw_name );
        }

        $attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );

        // Get the ID from the name.
        if ( $attribute_id ) {
            return $attribute_id;
        }

        // If the attribute does not exist, create it.
        $attribute_id = wc_create_attribute( array(
            'name'         => $raw_name,
            'slug'         => $attribute_name,
            'type'         => 'select',
            'order_by'     => 'menu_order',
            'has_archives' => false,
        ) );

        if ( is_wp_error( $attribute_id ) ) {
            die($attribute_id->get_error_message());
        }

        // Register as taxonomy while importing.
        $taxonomy_name = wc_attribute_taxonomy_name( $attribute_name );
        register_taxonomy(
            $taxonomy_name,
            apply_filters( 'woocommerce_taxonomy_objects_' . $taxonomy_name, array( 'product' ) ),
            apply_filters( 'woocommerce_taxonomy_args_' . $taxonomy_name, array(
                'labels'       => array(
                    'name' => $raw_name,
                ),
                'hierarchical' => true,
                'show_ui'      => false,
                'query_var'    => true,
                'rewrite'      => false,
            ) )
        );

        // Set product attributes global.
        $wc_product_attributes = array();

        foreach ( wc_get_attribute_taxonomies() as $taxonomy ) {
            $wc_product_attributes[ wc_attribute_taxonomy_name( $taxonomy->attribute_name ) ] = $taxonomy;
        }

        return $attribute_id;
    }

}

