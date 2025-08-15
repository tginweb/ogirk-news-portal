<?php

namespace SM\Image;

use SM\Util;

class Size
{
    var $size_name;
    var $width;
    var $height;
    var $crop;
    var $info;

    var $upload_info;

    static $sizes;

    function __construct($data=null)
    {
        if (is_string($data))
        {
            $this->load_by_name($data);
        }
        else if (is_array($data))
        {
            $this->load_by_info($data);
        }
    }

    function load_by_name($size_name)
    {
        $this->info      = self::parse_size_info($size_name);
        $this->size_name = $this->info['name'];
        $this->width     = $this->info['width'];
        $this->height    = $this->info['height'];
        $this->crop      = $this->info['crop'];
    }

    function load_by_info($size_info)
    {
        if (isset($size_info[0]))        $this->width = $size_info[0];
        if (isset($size_info[1]))        $this->height = $size_info[1];
        if (isset($size_info[2]))        $this->crop = $size_info[1];

        if (isset($size_info['width']))  $this->width = $size_info['width'];
        if (isset($size_info['height'])) $this->height = $size_info['height'];
        if (isset($size_info['crop']))   $this->crop = $size_info['crop'];
    }

    function upload_info()
    {
        if (!isset($this->upload_info))
        {
            $this->upload_info = wp_upload_dir();
        }

        return $this->upload_info;
    }

    function get_size_name()
    {
        if ($this->size_name)
        {
            return $this->size_name;
        }
        else
        {
            return ($this->crop ? 'c' : 'r') . '_' . $this->width . 'x' . $this->height;
        }
    }

    function thumb_filepath_replace($source_filepath)
    {
        $upinfo = $this->upload_info();

        $path = str_replace($upinfo['basedir'], $upinfo['basedir'].'/thumbs/'.$this->get_size_name(), $source_filepath);

        return $path;
    }


    static function parse_size_info( $size = '' )
    {
        if (is_string($size))
        {
            $parts = preg_split('/\s+/', $size);

            $sizename = array_shift($parts);

            $size_info = Util\Base::map_assoc($parts);

            if ($info = Util\Image::get_size_info($sizename))
            {
                return $info + $size_info;
            }
            else if (preg_match( '/(\d+)x(\d+)/', $sizename, $mt))
            {
                list($size_info['width'], $size_info['height']) = array($mt[1], $mt[2]);

                if (!$size_info['resize']) $size_info['crop'] = true;
            }
        }
        else
        {
            $size_info = $size;
        }

        $size_info['custom'] = true;

        return $size_info;
    }


}
