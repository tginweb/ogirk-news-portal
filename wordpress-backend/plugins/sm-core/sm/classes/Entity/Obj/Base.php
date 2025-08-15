<?php

namespace SM\Entity\Obj;

use SM\Common;
use SM\Util;

abstract class Base
{
    public $host;

    public $id;
    public $type;
    public $bundle;
    public $parents = null;

    public $refs_loaded = [];
    public $prop_cache = [];
    public $sm_cache = [];

    function __construct($v)
    {
        $this->host = $v;
    }

    /* @return sm_entity_controller */
    function controller()               { return Entity::i()->controller($this->entity_type, $this->entity_bundle); }

    function init_create()              { return array(); }
    function is_default()               { return false; }
    function is_new()                   { return $this->id()>0 ? false : true; }


    function id()           {}
    function get_slug()     {}
    function get_label()    {}
    function get_title()    {}
    function get_created()  {}
    function get_changed()  {}

    function get_excerpt()  {}
    function get_content()  {}

    function get_url_permalink() {

    }

    function get_parent()               { }
    function get_the_terms($taxonomy=null)            { return []; }
    function get_the_time($f=null)      { return null; }
    function get_comments_count()       { return 0; }


    function get_url()
    {
        if (!isset($this->sm_cache['url']))  $this->sm_cache['url'] = $this->get_url_permalink();

        return $this->sm_cache['url'];
    }

    function get_format()
    {
        return null;
    }


    function get_media_info()
    {
        $info = [];

        $type = $this->get_format();


        if ($type)
        {
            $info['type'] = $type;

            switch ($type)
            {
                case 'video':

                    if ($video_url = get_field('sm_video_url', $this->host))
                    {
                        $info['provider'] = 'youtube';
                        $info['provider_id'] = $video_url;
                    }
                    else if ($video_file_id = get_field('sm_video_file', $this->host))
                    {
                        $info['src'] = wp_get_attachment_url($video_file_id);
                    }
                    else
                    {
                        $content = $this->get_content();

                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $content, $match))
                        {
                            $info['provider'] = 'youtube';
                            $info['provider_id'] = $match[1];
                        }
                        else if (preg_match('%\[playlist [^\]]*ids="(\d+)"%i', $content, $match))
                        {
                            $info['src'] = wp_get_attachment_url(intval($match[1]));
                        }
                        else if (preg_match('%\[video [^\]]*mp4="([^\"]+)"%i', $content, $match))
                        {
                            $info['src'] = $match[1];
                        }
                    }

                    break;
            }

        }

        return $info;
    }

    function render_player($player_params=[])
    {
        $media_info = $this->get_media_info();

        $player_params += [


        ];

        $control_attrs = [
            'data-boot' => 1,
            'data-sm-elementor-player' => $player_params,
            'class' => ['player-control'],
        ];

        switch ($media_info['type'])
        {
            case 'video':
            case 'audio':

                $handle_attrs = [
                    'class' => ['player', 'player-handle']
                ];

                if ($media_info['provider'])
                {
                    $handle_tag = 'div';
                    $handle_attrs['data-plyr-provider'] = $media_info['provider'];
                    $handle_attrs['data-plyr-embed-id'] = $media_info['provider_id'];
                }
                elseif ($media_info['src'])
                {
                    $handle_tag = 'video';
                    $handle_attrs['src'] = $media_info['src'];
                }

                if ($handle_tag)
                    $output = '<div '.\SM\Util\Html::attributes($control_attrs).'><'.$handle_tag.' '.\SM\Util\Html::attributes($handle_attrs).'></'.$handle_tag.'></div>';

                break;
        }

        return $output;
    }

    function get_thumb($return_format='id')
    {
        $image = get_field('image_main', $this->host);

        return is_array($image) ? $image['ID'] : $image;
    }

    function get_gallery($return_format='id')
    {
        return get_field('image_gallery', $this->host, false);
    }



    /* ------- META DATA -------- */

    function get_meta($key = '', $single=true)
    {
        return get_metadata($this->entity_type, $this->id(), $key, $single);
    }

    function update_meta($key = '', $value = null)
    {
        return update_metadata($this->entity_type, $this->id(), $key, $value);
    }

    function field($selector, $format_value = true, $reset=false)
    {
        if (is_array($selector))
        {
            $result = array();

            foreach ($selector as $fname)
            {
                $result[$fname] = $this->field($fname);
            }

            return $result;
        }
        else
        {
            if (!isset($this->sm_cache['fields'][$selector]) || $reset)
            {
                $this->sm_cache['fields'][$selector] = function_exists('get_field') ? get_field($selector, $this, $format_value) : $this->get_meta($selector);
            }

            return $this->sm_cache['fields'][$selector];
        }
    }

    function get_full_id()
    {
        return $this->type.'-'.$this->id;
    }
}


