<?php

use SM\Util;

class sm_entity_object_post_attachment extends sm_entity_object_post
{
    function __construct($values = array(), $entity_bundle=null)
    {
        parent::__construct($values, $entity_bundle);

        $this->load_bundle();
    }

    function load_bundle()
    {
        $mime = explode('/', $this->post_mime_type);

        $this->media_type     = $mime[0];

        if( $this->media_type === 'audio' || $this->media_type === 'video' )
        {
            if( $this->media_type == 'video' )
            {
                $meta = wp_get_attachment_metadata( $this->id() ) ?: array();
                $this->width = $meta['width'];
                $this->height = $meta['height'];
            }

            if( $featured_id = get_post_thumbnail_id($this->id()) )
            {
                $thumb_id = $featured_id;
            }
        }
        else
        {
            $thumb_id = $this->id();
        }

        $this->thumb_id = $thumb_id;
    }

    function get_image_thumb($size='full', $params=array())
    {
        $cid = serialize(array($size, $params));

        if (!isset($this->sm_cache['thumb'][$cid]))
        {
            $this->sm_cache['thumb'][$cid] = Util\Image::thumb($params + array('attach_id' => $this->thumb_id, 'thumb_size' => $size));
        }

        return $this->sm_cache['thumb'][$cid];
    }

    function get_image_url($size='full', $params=array())
    {
        $res = $this->get_image_thumb($size, $params);

        return $res['thumb_url'];
    }

    function get_image_tag($size='large', $params=array())
    {
        $res = $this->get_image_thumb($size, $params);

        return $res ? $res['thumb_tag'] : '';
    }

    function move_file($dest_dir)
    {
        $uploads = wp_upload_dir();

        $file_source_path = get_attached_file($this->id());

        $fname = basename($file_source_path);

        if (!file_exists($file_source_path)) return true;

        $file_dest_path = $uploads['basedir'] . "/$dest_dir/".$fname;
        $file_dest_rel  = $dest_dir."/".$fname;

        Util\File::mkdir($uploads['basedir']."/$dest_dir");

        if ($file_source_path!=$file_dest_path)
        {
            rename($file_source_path, $file_dest_path);
            update_post_meta($this->id(), '_wp_attached_file', $file_dest_rel);
        }

        return true;
    }



    function get_file_path()
    {
        return get_attached_file($this->id());
    }

    function get_file_url_abs()
    {
        return wp_get_attachment_url($this->id());
    }

    function view_size($decimals=0)
    {
        if (file_exists($this->get_file_path()))
        {
           return size_format(filesize($this->get_file_path()), $decimals);
        }
    }

    function get_doc_pages_count()
    {
        return '';
    }

    function view_icon_font()
    {
        $schema = array(
          'application/msword' => 'fa-file-word-o',
          'application/pdf'    => 'fa-file-pdf-o',
        );

        $class = $schema[$this->post_mime_type] ?: 'file-o';

        return "<i class=\"file-icon fa $class\"></i>";
    }

    function view_icon_img()
    {

    }

    function get_caption()
    {
        return $this->caption ?: $this->post_excerpt;
    }

    function is_support_view_online()
    {
        return in_array($this->post_mime_type, array('application/pdf'));
    }

}
