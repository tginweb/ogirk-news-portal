<?php


namespace SM_Elementor\Util;

use Elementor\Group_Control;
use Elementor\Group_Control_Image_Size;

class Image  {


    static function get_image_html($image_setting, $image_size_key, $settings, $image_attrs=[])
    {
        $image_html = '';

        $attachment_id = $image_setting['id'];

        // Old version of image settings.
        if (!isset($settings[$image_size_key . '_size'])) {
            $settings[$image_size_key . '_size'] = '';
        }

        $size = $settings[$image_size_key . '_size'];


        $image_attrs['class'][] = 'lae-image';

        // If is the new version - with image size.
        $image_sizes = get_intermediate_image_sizes();

        $image_sizes[] = 'full';

        if (!empty($settings['thumb_lazyload']) && $settings['thumb_lazyload']==='yes')
        {
            $lazyload = true;
        }



        if (!empty($attachment_id) && in_array($size, $image_sizes)) {

            $image_attrs['class'][] = "attachment-$size size-$size";

            $image_html .= self::wp_get_attachment_image($attachment_id, $size, false, \SM\Util\Html::attributes_flatten($image_attrs), $lazyload);
        }
        else
        {
            $image_src = Group_Control_Image_Size::get_attachment_image_src($attachment_id, $image_size_key, $settings);

            if (!$image_src && isset($image_setting['url']))
            {
                $image_src = $image_setting['url'];
            }

            if (!empty($image_src))
            {
                if ($lazyload)
                {
                    $image_attrs['class'][] = 'lazy';
                    $image_attrs['data-src'] = $image_src;
                }
                else
                {
                    $image_attrs['src'] = $image_src;
                }

                $image_html .= sprintf('<img '.\SM\Util\Html::attributes($image_attrs).' />', esc_attr($image_src), $image_attrs);
            }
        }

        return $image_html;
    }

    function wp_get_attachment_image($attachment_id, $size = 'thumbnail', $icon = false, $attr = '', $lazyload=false)
    {
        $html = '';
        $image = wp_get_attachment_image_src($attachment_id, $size, $icon);

        if ( $image )
        {
            list($src, $width, $height) = $image;
            $hwstring = image_hwstring($width, $height);
            $size_class = $size;

            if ( is_array( $size_class ) ) {
                $size_class = join( 'x', $size_class );
            }

            $attachment = get_post($attachment_id);

            $default_attr = array(
                'src'	=> $src,
                'class'	=> "attachment-$size_class size-$size_class",
                'alt'	=> trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
            );

            $attr = wp_parse_args( $attr, $default_attr );

            if ($lazyload)
            {
                $attr['data-src'] = $src;

                $attr['class'] .= ' lazy';

                unset($attr['src']);
            }

            // Generate 'srcset' and 'sizes' if not already present.
            if ( empty( $attr['srcset'] ) ) {
                $image_meta = wp_get_attachment_metadata( $attachment_id );

                if ( is_array( $image_meta ) ) {
                    $size_array = array( absint( $width ), absint( $height ) );
                    $srcset = wp_calculate_image_srcset( $size_array, $src, $image_meta, $attachment_id );
                    $sizes = wp_calculate_image_sizes( $size_array, $src, $image_meta, $attachment_id );

                    if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
                        $attr['srcset'] = $srcset;

                        if ( empty( $attr['sizes'] ) ) {
                            $attr['sizes'] = $sizes;
                        }
                    }
                }
            }

            /**
             * Filters the list of attachment image attributes.
             *
             * @since 2.8.0
             *
             * @param array        $attr       Attributes for the image markup.
             * @param WP_Post      $attachment Image attachment post.
             * @param string|array $size       Requested size. Image size or array of width and height values
             *                                 (in that order). Default 'thumbnail'.
             */
            $attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment, $size );
            $attr = array_map( 'esc_attr', $attr );
            $html = rtrim("<img $hwstring");
            foreach ( $attr as $name => $value ) {
                $html .= " $name=" . '"' . $value . '"';
            }
            $html .= ' />';
        }

        return $html;
    }
}
