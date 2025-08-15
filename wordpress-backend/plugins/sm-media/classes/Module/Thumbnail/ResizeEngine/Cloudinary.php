<?php

namespace SM_Media\Module\Thumbnail\ResizeEngine;

class Cloudinary extends Base
{
    function init_events()
    {

        add_filter("intermediate_image_sizes_advanced", function($sizes) { return array(); });

        add_filter('image_downsize', array( $this, 'on_filter_image_downsize' ), 10, 3 );

    }

    function on_filter_image_downsize( $image, $attachment_id, $size )
    {
        $image_url = wp_get_attachment_url( $attachment_id );

        $filepath = get_attached_file($attachment_id);

        if (file_exists($filepath) && (filesize($filepath)>10000000))
        {
            return;
        }

        if ( $image_url )
        {
            $osize = new \SM\Image\Size($size);

            if ( !$osize->crop && $image_meta = wp_get_attachment_metadata( $attachment_id ) )
            {
                $smaller_width  = ( ( $image_meta['width']  < $osize->width  ) ? $image_meta['width']  : $osize->width );
                $smaller_height = ( ( $image_meta['height'] < $osize->height ) ? $image_meta['height'] : $osize->height );

                $osize->width  = $smaller_width;
                $osize->height = $smaller_height;
            }


            include_once __DIR__.'/cloudinary/src/Cloudinary.php';

            $schema['name'] = 'dypmdyykk';
            $schema['key'] = '789991345946526';
            $schema['secret'] = 'sz9tDwtK92V6avB91TJ2sPOt0Zo';

            \Cloudinary::config(array(
                "cloud_name" => $schema['name'],
                "api_key"    => $schema['key'],
                "api_secret" => $schema['secret']
            ));

            $size_name = $osize->width.'x'.$osize->height;

	        if ($size == 'd2.2' && get_field('gallery_crop_disable', $attachment_id)) {

		        $service_url = cloudinary_url($image_url, array('type'=>'fetch', 'crop'=>'limit', 'width'=>$osize->width, 'height'=>$osize->height));

	        } else {

		        if ($osize->crop)
		        {
			        $params = array('type'=>'fetch', 'crop'=>'thumb', 'width'=>$osize->width, 'height'=>$osize->height);

			        if (!in_array($osize->width, [252, 152]))
			        {
				        if (!get_field('facefind_disable', $attachment_id)) {
					        $params['gravity'] = 'custom:face';
				        }
			        }

			        $service_url = cloudinary_url($image_url, $params);
		        }
		        else
		        {
			        $service_url = cloudinary_url($image_url, array('type'=>'fetch', 'crop'=>'limit', 'width'=>$osize->width, 'height'=>$osize->height));
		        }

	        }

            $image = array(
                $service_url,
                $osize->width,
                $osize->height,
                $osize->crop
            );
        }

        return $image;
    }


}
