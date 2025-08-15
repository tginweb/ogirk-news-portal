<?php


if (sm_class_can_load('sm_com_module_ad', __FILE__))
{
    class sm_com_module_ad extends sm_com_module_
    {
        static function params_defaults()
        {
            return array(
                'content_tpl'   => '',
            ) + parent::params_defaults();
        }

        function get_content()
        {

        }

        function render_content()
        {

            $entity = $this->get_entity();

            if ($ad_link = $entity->field('ad_link'))
            {
                $link_params = $ad_link;
            }

            switch ($entity->field('ad_type'))
            {
                case 'post' :

                    if ($post_id = $entity->field('ad_post'))
                    {
                        $content = ElementorPro\Plugin::elementor()->frontend->get_builder_content_for_display($post_id);
                    }

                    break;

                case 'image' :

                    if ($image_id = $entity->field('ad_image'))
                    {
                        $content = sm_format('thumb', [
                            'thumb_size' => 'medium',
                            'attach_id'  => $image_id
                        ]);
                    }

                    break;
            }

            if ($link_params)
            {
                $link_params['text'] = $content;

                $content = sm_format('link', $link_params);
            }

            return $content;
        }
    }

}


if (sm_class_can_load('sm_com_module_ad_', __FILE__))
{
    class sm_com_module_ad_ extends sm_com_module_ad
    {

    }
}