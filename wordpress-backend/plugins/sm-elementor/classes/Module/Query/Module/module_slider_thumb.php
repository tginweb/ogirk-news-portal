<?php

namespace SM_Elementor\Module\Query\Module;


class module_slider_thumb extends Common\Base {


    function get_template()
    {
        return <<<EOT

        <article {{container.attrs}}>
                      
          <div {{content.attrs}}>
                     
            {{thumb}}                
          
          </div>
                        
        </article>

EOT;
    }

    function render_thumb_media($params=[]) {

        $params = $this->customizer_element_params('thumb_media', $params) + [

            ];


        $image_size_key = 'image_size';

        $image_setting = ['id' => $this->get_thumb_image_id()];

        $thumbnail_html = \SM_Elementor\Util\Image::get_image_html($image_setting, $image_size_key, $this->settings, $params['attrs']);


        return $this->wrap_link('thumb', $thumbnail_html);
    }
}