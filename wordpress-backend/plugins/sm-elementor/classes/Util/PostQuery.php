<?php


namespace SM_Elementor\Util;

use Elementor\Group_Control;
use Elementor\Group_Control_Image_Size;

class PostQuery  {


    static function get_paginate_links( $args = '' ) {

        global $wp_query, $wp_rewrite;

        $page_links = array();

        if ($args['query'])
            $wp_query = $args['query'];

        // Setting up default values based on the current URL.
        $pagenum_link = $args['query_url'] ?  $args['query_url'] : html_entity_decode( get_pagenum_link() );

        $url_parts    = explode( '?', $pagenum_link );

        // Append the format placeholder to the base URL.
        $pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

        // URL base depends on permalink settings.
        $format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
        $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

        $defaults = array(
            'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
            'format'             => $format, // ?page=%#% : %#% is replaced by the page number
            'total'              => isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1,
            'current'            => get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1,
            'aria_current'       => 'page',
            'show_all'           => false,
            'prev_next'          => true,
            'prev_next_only'     => false,
            'prev_text'          => __( '&laquo; Previous' ),
            'next_text'          => __( 'Next &raquo;' ),
            'end_size'           => 1,
            'mid_size'           => 2,
            'type'               => 'plain',
            'add_args'           => array(), // array of query args to add
            'add_fragment'       => '',
            'before_page_number' => '',
            'after_page_number'  => '',
        );


        $args = wp_parse_args( $args, $defaults );

        if ( ! is_array( $args['add_args'] ) ) {
            $args['add_args'] = array();
        }

        // Merge additional query vars found in the original URL into 'add_args' array.
        if ( isset( $url_parts[1] ) ) {
            // Find the format argument.
            $format = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
            $format_query = isset( $format[1] ) ? $format[1] : '';
            wp_parse_str( $format_query, $format_args );

            // Find the query args of the requested URL.
            wp_parse_str( $url_parts[1], $url_query_args );

            // Remove the format argument from the array of query arguments, to avoid overwriting custom format.
            foreach ( $format_args as $format_arg => $format_arg_value ) {
                unset( $url_query_args[ $format_arg ] );
            }

            $args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
        }

        // Who knows what else people pass in $args
        $total = (int) $args['total'];

        if ( $total < 2 ) {
            return $page_links;
        }

        $current  = (int) $args['current'];
        $end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.

        if ( $end_size < 1 ) {
            $end_size = 1;
        }

        $mid_size = (int) $args['mid_size'];
        if ( $mid_size < 0 ) {
            $mid_size = 2;
        }

        $add_args = $args['add_args'];
        $r = '';

        $dots = false;

        if ( $args['prev_next'] && $current && 1 < $current ) :

            $link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
            $link = str_replace( '%#%', $current - 1, $link );

            if ( $add_args ) $link = add_query_arg( $add_args, $link );

            $link = apply_filters( 'paginate_links', $link.$args['add_fragment']);

            $page_links['prev'] = [
                'role' => 'prev',
                'page' => $current - 1,
                'href' => $link,
                'text' => $args['prev_text'],
                'html' => '<a class="prev page-numbers" href="' . esc_url( $link ) . '">' . $args['prev_text'] . '</a>'
            ];

        endif;

        if ( !$args['prev_next_only'] )
        {
            for ( $n = 1; $n <= $total; $n++ ) :

                if ( $n == $current ) :

                    $item = [];
                    $item['role'] = 'number';
                    $item['page'] = $n;
                    $item['text'] = $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'];
                    $item['html'] = "<span aria-current='" . esc_attr( $args['aria_current'] ) . "' class='page-numbers current'>" . $item['text'] . "</span>";

                    $page_links[] = $item;

                    $dots = true;

                else :

                    if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :

                        $link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
                        $link = str_replace( '%#%', $n, $link );

                        if ( $add_args )
                            $link = add_query_arg( $add_args, $link );

                        $link .= $args['add_fragment'];

                        $item = [];
                        $item['role'] = 'number';
                        $item['page'] = $n;
                        $item['href'] = apply_filters( 'paginate_links', $link );
                        $item['text'] = $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'];
                        $item['html'] = "<a class='page-numbers' href='" . esc_url( $item['href'] ) . "'>" . $item['text'] . "</a>";

                        $page_links[] = $item;

                        $dots = true;

                    elseif ( $dots && ! $args['show_all'] ) :

                        $item = [];
                        $item['role'] = 'dots';
                        $item['text'] = __( '&hellip;' );
                        $item['html'] = '<span class="page-numbers dots">' . $item['text'] . '</span>';

                        $page_links[] = $item;

                        $dots = false;

                    endif;

                endif;

            endfor;

        }

        if ( $args['prev_next'] && $current && $current < $total ) :

            $link = str_replace( '%_%', $args['format'], $args['base'] );
            $link = str_replace( '%#%', $current + 1, $link );

            if ( $add_args )
                $link = add_query_arg( $add_args, $link );

            $link = apply_filters( 'paginate_links', $link.$args['add_fragment']);

            $page_links['next'] = [
                'role' => 'next',
                'page' => $current + 1,
                'href' => $link,
                'text' => $args['next_text'],
                'html' => '<a class="next page-numbers" href="' . esc_url( $link ) . '">' . $args['next_text'] . '</a>'
            ];

        endif;


        if ($args['query'])
            wp_reset_query();

        return $page_links;
    }
}
