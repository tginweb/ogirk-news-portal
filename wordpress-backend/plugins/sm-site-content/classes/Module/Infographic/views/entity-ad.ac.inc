<?php


if (smart_class_can_load('view_module_entity_sm_infographic', __FILE__))
{
    class sm_view_module_sm_infographic extends sm_com_module_
    {
        function get_module_image()
        {
            return $this->entity()->get_image_by_field('ad_image');
        }

        function get_module_url()
        {
            if ($link = $this->entity()->field('ad_link'))
            {
                return '/sm-action/sm-ad-redirect?'.http_build_query(array('url'=>$link['url']));
            }
        }

        function module_link_render($content, $elm=null)
        {
            $link = $this->get_entity()->field('ad_link');

            return '<a '.(!empty($link['blank']) ? ' target="_blank" ':'').' href="' . $this->get_module_url() . '" class="elm-'.$elm.'-link">'.$content.'</a>';
        }
    }
}


if (smart_class_can_load('_view_module_entity_sm_infographic', __FILE__))
{
    class _view_module_entity_sm_infographic extends sm_view_module_sm_infographic
    {

    }
}