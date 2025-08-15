<?php


namespace SM\Util;

class Image
{
    static $sizes;

    static function is_thumb_on_request_generate_mode()
    {
        return true;
    }

    static function regenerate_thumbnails($image_id)
    {
        $fullsizepath = get_attached_file( $image_id );

        if ( false === $fullsizepath || !file_exists($fullsizepath) ) return;

        return wp_update_attachment_metadata( $image_id, wp_generate_attachment_metadata( $image_id, $fullsizepath ) );
    }

    static function thumb($params = array())
    {
        $params += array(
            'attach_id'   => null,
            'post_id'     => null,
            'thumb_size'  => 'thumbnail',
            'attrs'       => array(),
            'holder'      => false,
            'large_size'  => null
        );

        if (!$params['attach_id'] && !$params['post_id']) return false;

        $orig_params = $params;

        $attach_id = $params['post_id'] ? get_post_thumbnail_id($params['post_id']) : $params['attach_id'];

        //$result = apply_filters('sm/thumb/generate', false, $attach_id, $params['thumb_size'], $orig_params);


        if ($result) return $result;

        if (!$attach_id || !($attachment = get_post($attach_id))) return;


        $size = new \sm_image_size($params['thumb_size']);

        $params = $size->info + $params;

        $attrs = $params['attrs'] + array(
            'alt'   => '',
            'title' => '',
            'class' => array()
        );

        $attrs['class'][] = 'img attachment-'. $size->get_size_name();

        if (!empty($params['holder']))
        {
            $holder = (array)$params['holder'] + array(
                'sized' => true,
                'attrs' => array(),
            );

            /*
            if ($holder['sized']===true || $holder['sized']==='w')
            {
                $attrs['style']['width'] = $size->width.'px';
                $attrs['class'][] = 'in-hcenter';
            }

            if ($holder['sized']===true || $holder['sized']==='h')
            {
                $attrs['style']['height'] = $size->height.'px';
                $attrs['class'][] = 'in-vmiddle';
            }
            */

            $holder['attrs']['class'][] = 'image-holder';
        }

        $result = array();

        if ($size->size_name)
        {

            if ($result['thumb_tag'] = wp_get_attachment_image($attach_id, $size->size_name, false, Html::attributes_flatten($attrs, false)))
            {

                $thumb_info = wp_get_attachment_image_src($attach_id, $size->size_name);

                $result += array(
                    'thumb_url'    => $thumb_info[0],
                    'thumb_width'  => $thumb_info[1],
                    'thumb_height' => $thumb_info[2],
                );
            }
        }

        if ($holder)
        {
            $result['thumb_tag'] = '<div '.Html::attributes($holder['attrs']).'>'.$result['thumb_tag'].'</div>';
        }

        if ($params['large_size'])
        {
            if ($large_linfo = wp_get_attachment_image_src( $attach_id, $params['large_size'] ))
            {
                $result['large_url']    = $large_linfo[0];
                $result['large_width']  = $large_linfo[1];
                $result['large_height'] = $large_linfo[2];
            }
        }

        $result = apply_filters('sm/thumb/result', $result, $attach_id, $params['thumb_size'], $orig_params);

        return $result;
    }

    static function resize( $attach_id = null, $img_url = null, $width, $height, $crop = false, $create=true)
    {
        // this is an attachment, so we have the ID
        $image_src = array();

        if ( $attach_id )
        {
            $image_src = wp_get_attachment_image_src( $attach_id, 'full' );
            $actual_file_path = get_attached_file( $attach_id );
            // this is not an attachment, let's use the image url
        }
        elseif ( $img_url )
        {
            $file_path = parse_url( $img_url );
            $actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
            $orig_size = getimagesize( $actual_file_path );
            $image_src[0] = $img_url;
            $image_src[1] = $orig_size[0];
            $image_src[2] = $orig_size[1];
        }


        if ( ! empty( $actual_file_path ) )
        {
            $file_info = pathinfo( $actual_file_path );
            $extension = '.' . $file_info['extension'];

            // the image path without the extension
            $no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

            $cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;


            // checking if the file size is larger than the target size
            // if it is smaller or the same size, stop right here and return
            if ( $image_src[1] > $width || $image_src[2] > $height )
            {
                // the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
                if ( file_exists( $cropped_img_path ) )
                {
                    $cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

                    $vt_image = array(
                        'url' => $cropped_img_url,
                        'width' => $width,
                        'height' => $height,
                    );

                    return $vt_image;
                }

                if ( !$crop )
                {
                    // calculate the size proportionaly
                    $proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );

                    $resized_img_path = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;

                    // checking if the file already exists
                    if ( file_exists( $resized_img_path ) )
                    {
                        $resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

                        $vt_image = array(
                            'url' => $resized_img_url,
                            'width' => $proportional_size[0],
                            'height' => $proportional_size[1],
                        );

                        return $vt_image;
                    }
                }

                if ($create)
                {


                    // no cache files - let's finally resize it
                    $img_editor = wp_get_image_editor($actual_file_path);

                    if (is_wp_error($img_editor) || is_wp_error($img_editor->resize($width, $height, $crop)))
                    {
                        return array('url' => '', 'width' => '', 'height' => '');
                    }

                    $new_img_path = $img_editor->generate_filename();

                    if (is_wp_error($img_editor->save($new_img_path))) return array('url' => '', 'width' => '', 'height' => '');

                    if (!is_string($new_img_path)) return array('url' => '', 'width' => '', 'height' => '');

                    $new_img_size = getimagesize($new_img_path);
                    $new_width    = $new_img_size[0];
                    $new_height   = $new_img_size[1];
                    $new_url      = str_replace(basename($image_src[0]), basename($new_img_path), $image_src[0]);
                }
                else
                {

                    $rinfo        = image_resize_dimensions( $image_src[1], $image_src[2], $width, $height, $crop );
                    $new_width    = $rinfo[4];
                    $new_height   = $rinfo[5];
                    $new_url      =  preg_replace('/\.(jpg|jpeg|png|tiff|gif)/i', '-' . $new_width . 'x' . $new_height . '.\1',  Wp::path_to_url($actual_file_path));
                }

                // resized output
                $vt_image = array(
                    'url'    => $new_url,
                    'width'  => $new_width,
                    'height' => $new_height,
                );

                return $vt_image;
            }

            $vt_image = array(
                'url'    => $image_src[0],
                'width'  => $image_src[1],
                'height' => $image_src[2],
            );

            return $vt_image;
        }

        return false;
    }

    function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false, $upscale = false ) {

        // this is an attachment, so we have the ID
        if ( $attach_id ) {

            $image_src = wp_get_attachment_image_src( $attach_id, 'full' );
            $file_path = get_attached_file( $attach_id );

            // this is not an attachment, let's use the image url
        } else if ( $img_url ) {

            $file_path = parse_url( $img_url );
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

            // Look for Multisite Path
            if(file_exists($file_path) === false){
                global $blog_id;
                $file_path = parse_url( $img_url );
                if (preg_match("/files/", $file_path['path'])) {
                    $path = explode('/',$file_path['path']);
                    foreach($path as $k=>$v){
                        if($v == 'files'){
                            $path[$k-1] = 'wp-content/blogs.dir/'.$blog_id;
                        }
                    }
                    $path = implode('/',$path);
                }
                $file_path = $_SERVER['DOCUMENT_ROOT'].$path;
            }
            //$file_path = ltrim( $file_path['path'], '/' );
            //$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];


            $orig_size = getimagesize( $file_path );

            $image_src[0] = $img_url;
            $image_src[1] = $orig_size[0];
            $image_src[2] = $orig_size[1];
        }

        $file_info = pathinfo( $file_path );

        // check if file exists
        $base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
        if ( !file_exists($base_file) )
            return;

        $extension = '.'. $file_info['extension'];

        // the image path without the extension
        $no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

        $cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

        // checking if the file size is larger than the target size
        // if it is smaller or the same size, stop right here and return
        if ( $image_src[1] > $width || 1) {


            // the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
            if ( file_exists( $cropped_img_path )) {

                $cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

                $vt_image = array (
                    'url' => $cropped_img_url,
                    'width' => $width,
                    'height' => $height
                );

                return $vt_image;
            }

            // $crop = false or no height set
            if ( $crop == false OR !$height ) {

                // calculate the size proportionaly
                $proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
                $resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

                // checking if the file already exists
                if ( file_exists( $resized_img_path )) {

                    $resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

                    $vt_image = array (
                        'url' => $resized_img_url,
                        'width' => $proportional_size[0],
                        'height' => $proportional_size[1]
                    );

                    return $vt_image;
                }
            }

            // check if image width is smaller than set width
            $img_size = getimagesize( $file_path );

            if (!$upscale)
            {
                if ( $img_size[0] <= $width ) $width = $img_size[0];
            }


            // Check if GD Library installed
            if (!function_exists ('imagecreatetruecolor')) {
                echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
                return;
            }

            // no cache files - let's finally resize it
            $new_img_path = image_resize( $file_path, $width, $height, $crop, $width.'x'.$height);

            $new_img_size = getimagesize( $new_img_path );


            $new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

            // resized output
            $vt_image = array (
                'url' => $new_img,
                'width' => $new_img_size[0],
                'height' => $new_img_size[1]
            );

            return $vt_image;
        }

        // default output - without resizing
        $vt_image = array (
            'url' => $image_src[0],
            'width' => $width,
            'height' => $height
        );

        return $vt_image;
    }

    static function download_image_copy( $url, $post_id, $desc = '' )
    {
        if ( !function_exists('media_handle_upload') )
        {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        }

        $types = 'jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG';
        $file_array = array();

        $file_array['tmp_name'] = download_url( $url );

        if( is_wp_error( $file_array['tmp_name'] ) )
        {
            @unlink( $file_array['tmp_name'] );
            return $url;
        }

        $pathparts = pathinfo( $file_array['tmp_name'] );
        $file_array['name'] = basename( $url );

        // fix file extension
        if( '' == $pathparts['extension'] || ! in_array( $pathparts['extension'], explode( '|', $types ) ) )
        {
            $_file = $pathparts['dirname'] . '/' . $file_array['name'];
            rename( $file_array['tmp_name'], $_file );
            $file_array['name'] = basename( $_file );
            $file_array['tmp_name'] = $_file;
        }

        $id = media_handle_sideload( $file_array, $post_id, $desc );

        if( is_wp_error( $id ) ) {
            @unlink( $file_array['tmp_name'] );
            return $url;
        }

        return $id;
    }

    static function get_size_info( $size = '' )
    {
        global $_wp_additional_image_sizes;

        if (!isset(self::$sizes))
        {
            self::$sizes = array();

            // Create the full array with sizes and crop info
            foreach( get_intermediate_image_sizes() as $_size )
            {
                if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) )
                {
                    self::$sizes[ $_size ] = array(
                        'name'    => $_size,
                        'width'   => get_option( $_size . '_size_w' ),
                        'height'  => get_option( $_size . '_size_h' ),
                        'crop'    => (bool) get_option( $_size . '_crop' )
                    );
                }
                elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) )
                {
                    self::$sizes[ $_size ] = array(
                        'name'    => $_size,
                        'width'   => $_wp_additional_image_sizes[ $_size ]['width'],
                        'height'  => $_wp_additional_image_sizes[ $_size ]['height'],
                        'crop'    => $_wp_additional_image_sizes[ $_size ]['crop']
                    );
                }
                self::$sizes[ $_size ]['fullname'] = $_size.' ['.self::$sizes[$_size]['width'].'x'.self::$sizes[$_size]['height'].(self::$sizes[$_size]['crop'] ? ' crop' : '').']';
            }
        }

        // Get only 1 size if found
        if ( $size )
        {
            if ($size=='full')
            {
                return array('name'=>'full');
            }

            return isset( self::$sizes[ $size ] ) ? self::$sizes[ $size ] : false;
        }

        return self::$sizes;
    }


    function upload_attach_image( $file, $post_id = 0, $desc = null)
    {
        if ( ! empty( $file ) )
        {
            // Set variables for storage, fix file filename for query strings.
            preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
            $file_array = array();
            $file_array['name'] = basename( $matches[0] );


            // Download file to temp location.
            $file_array['tmp_name'] = download_url( $file );


            // If error storing temporarily, return the error.
            if ( is_wp_error( $file_array['tmp_name'] ) ) {
                return $file_array['tmp_name'];
            }

            // Do the validation and storage stuff.

            $id = media_handle_sideload( $file_array, $post_id, $desc );


            // If error storing permanently, unlink.
            if ( is_wp_error( $id ) ) {
                @unlink( $file_array['tmp_name'] );
                return $id;
            }
            else
            {
                set_post_thumbnail($post_id, $id);
            }

            $src = wp_get_attachment_url( $id );
        }

        // Finally, check to make sure the file has been saved, then return the HTML.
        if ( ! empty( $src ) )
        {
            return $id;
        }
        else
        {
            return new \WP_Error( 'image_sideload_failed' );
        }
    }
}

