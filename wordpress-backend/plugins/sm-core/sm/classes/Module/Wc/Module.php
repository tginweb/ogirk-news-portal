<?php


namespace SM\Module\Wc;

use SM\Common;

class Module extends Common\Module
{

    function get_endpoint_title()
    {
        global $wp_query;

        $endpoint_title = null;

        if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && is_page() && is_wc_endpoint_url() ) {
            $endpoint       = WC()->query->get_current_endpoint();
            $endpoint_title = WC()->query->get_endpoint_title( $endpoint );
        }

        return $endpoint_title;
    }
}




