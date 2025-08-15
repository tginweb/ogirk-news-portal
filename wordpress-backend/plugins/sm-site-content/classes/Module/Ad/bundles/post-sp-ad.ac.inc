<?php


class econtroller_post_sm_ad extends _econtroller_post
{


}


class entity_post_sm_ad extends _entity_post
{
    function get_style_vars()
    {
        $result = array();

        $vars = array(
            'ad_width',
            'ad_height',
            'ad_image_size',
            'ad_style_bg',
            'ad_style_center',
            'ad_style_border',
            'ad_style_padding',
        );

        foreach ($vars as $vname)
        {
            $val = $this->field($vname);

            if (strlen($val) && $val!='_')
            {
                $result[$vname] = $val;
            }
        }

        return $result;
    }

    function render($vars=array())
    {
        $vars = $this->get_style_vars() + $vars;

        $meia_type = $this->field('ad_media_type');

        if (!$meia_type) return;

        return $this->{'render_media_'.$meia_type}($vars);
    }

    function render_media_image($vars=array())
    {
        if (!($image = smart_entity_object::format('post', 'attachment', $this->get_meta_field('ad_image')))) return;

        $image_size = !empty($vars['ad_image_size']) ? $vars['ad_image_size'] : 'full';

        $link = $this->field('ad_link');

        $content = $image->get_image_tag($image_size);

        $content = $this->render_inner($content, $vars);

        $content = !empty($link['url']) ? sm::tpl('link', array('text'=>$content, 'path'=>$link['url'], 'blank'=>$link['blank'])) : $content;


        $content = $this->render_wrapper($content, $vars);

        return $content;
    }

    function render_media_flash($vars=array())
    {
        $vars = $this->get_style_vars() + $vars;


        return ;
    }

    function render_media_text($vars=array())
    {
        $vars = $this->get_style_vars() + $vars;

        return ;
    }

    function render_wrapper($content, $vars=array())
    {
        if (!$content) return;

        $attrs = array();

        $attrs['class'][]='ad-block-wrapper';
        $attrs['class'][]='ad-'.$this->ID;
        //$attrs['class'][]='ad-'.$this->post_name;

        if (!empty($vars['ad_style_center']))  $attrs['class'][]='ad-block-center';
        if (!empty($vars['ad_style_padding'])) $attrs['class'][]='ad-block-padding';
        if (!empty($vars['ad_style_border']))  $attrs['class'][]='ad-block-border';
        if (!empty($vars['ad_style_bg']))      $attrs['style']['background-color']=$vars['ad_style_bg'];

        if (!empty($vars['ad_width']))         $attrs['style']['width']=$vars['ad_width'];
        if (!empty($vars['ad_height']))        $attrs['style']['height']=$vars['ad_height'];

        return SM\Util\html::tag('div', $attrs, $content);
    }

    function render_inner($content, $vars=array())
    {
        if (!$content) return;

        $attrs = array();

        $attrs['class'][]='ad-block-inner';

        return SM\Util\html::tag('div', $attrs, $content);
    }
}


if (smart_class_can_load('_econtroller_post_sm_ad', __FILE__)) { class _econtroller_post_sm_ad extends econtroller_post_sm_ad { } }
if (smart_class_can_load('_entity_post_sm_ad', __FILE__)) { class _entity_post_sm_ad extends entity_post_sm_ad { } }
