<?php

namespace SM_Elementor\Module\Slider\QueryBlock;

use Elementor\Controls_Manager;

class Block_Content_Media_Slider extends Block_Slider {

    static function get_block_module_regions()
    {
        return array_merge(parent::get_block_module_regions(), ['2']);
    }

    function get_enqueue_assets() {
        return ['sm_elementor.swiper'];
    }

    function inner() {

        if (empty($this->entities)) return;

        if (count($this->entities)==1) return $this->inner_single();


        $slider_params = [];

        $slider_params['slider'] = [];

        if ($this->settings['slider_navigation']==='yes')
        {
            $slider_params['slider']['navigation']['nextEl'] = '#'.$this->block_uid.' .c-slides .swiper-button-next';
            $slider_params['slider']['navigation']['prevEl'] = '#'.$this->block_uid.' .c-slides .swiper-button-prev';
        }

        if ($this->settings['slider_pagination']==='yes')
        {
            $slider_params['slider']['pagination']['el'] = '#'.$this->block_uid.' .c-slides .swiper-pagination';
            $slider_params['slider']['pagination']['clickable'] = true;
        }

        if ($this->settings['slider_slides']>=1)
        {
            $slider_params['slider']['slidesPerView'] = $this->settings['slider_slides'];
            $slider_params['slider']['spaceBetween'] = $this->settings['slider_slides_space'];
        }

        if ($this->settings['slider_auto_height']==='yes')
        {
            $slider_params['slider']['autoHeight'] = true;
        }


        if ($this->settings['slider_thumbs']==='yes')
        {
            $slider_params['slider_thumbs'] = true;

            $slider_params['thumbs'] = [];

            if ($this->settings['slider_thumbs_slides']>=1)
            {
                $slider_params['thumbs']['slidesPerView'] = $this->settings['slider_thumbs_slides'];
                $slider_params['thumbs']['spaceBetween'] = $this->settings['slider_thumbs_slides_space'];
            }

            if ($this->settings['slider_thumbs_navigation']==='yes')
            {
                $slider_params['thumbs']['navigation']['nextEl'] = '#'.$this->block_uid.' .c-thumbs .swiper-button-next';
                $slider_params['thumbs']['navigation']['prevEl'] = '#'.$this->block_uid.' .c-thumbs .swiper-button-prev';
            }

            if ($this->settings['slider_thumbs_direction'] && $this->settings['slider_thumbs_direction']!='horizontal')
            {
                $slider_params['thumbs']['direction'] = $this->settings['slider_thumbs_direction'];
            }
        }


        $container_attrs = [
            'data-boot' => [],
            'data-sm-elementor-swiper' => $slider_params,
            'class' => ['row']
        ];

        $module_attrs = [
            'class' => ['swiper-slide']
        ];

        ob_start();

        $region_1_params = $this->customizer_element_params('region_1', []);
        $region_1_params['attrs']['class'] = array_merge($region_1_params['attrs']['class'], $this->get_col_width_classes_array('modules_region_1_width', 12, 12, 12));

        $region_2_params = $this->customizer_element_params('region_2', []);
        $region_2_params['attrs']['class'] = array_merge($region_2_params['attrs']['class'], $this->get_col_width_classes_array('modules_region_2_width', 12, 12, 12));

        ?>

        <div <?print\SM\Util\Html::attributes($container_attrs);?>>

            <div <?print\SM\Util\Html::attributes($region_1_params['attrs']);?>>

                <div class="swiper-container c-slides modules-region-1">

                    <div class="swiper-wrapper modules">

                        <? foreach ($this->entities as $entity) { ?>

                            <?php print $this->render_region_module('1', $entity, 'module_2', $module_attrs); ?>

                        <? } ?>

                    </div>

                    <?php if ($this->settings['slider_pagination']=='yes' && $this->settings['slider_pagination_outside']!=='yes'): ?>

                        <div class="swiper-pagination"></div>

                    <?php endif; ?>

                    <?php if ($this->settings['slider_navigation']=='yes' && $this->settings['slider_navigation_outside']!=='yes'): ?>

                        <div class="swiper-button-prev s-icon"><i class="fa fa-angle-left" aria-hidden="true"></i></div>
                        <div class="swiper-button-next s-icon"><i class="fa fa-angle-right" aria-hidden="true"></i></div>

                    <?php endif; ?>

                </div>

            </div>

            <?php if ($this->settings['slider_thumbs']=='yes'): ?>

                <div <?print\SM\Util\Html::attributes($region_2_params['attrs']);?>>

                    <div class="swiper-container c-thumbs modules-region-2">

                        <div class="swiper-wrapper modules">

                            <? foreach ($this->entities as $entity) { ?>

                                <?php print $this->render_region_module('2', $entity, 'module_2', $module_attrs); ?>

                            <? } ?>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

        </div>

        <?

        return ob_get_clean();
    }

    function inner_single()
    {
        $container_attrs = [
            'class' => ['row']
        ];

        $module_attrs = [
            'class' => []
        ];

        ob_start();

        $region_1_params = $this->customizer_element_params('region_1', []);
        $region_1_params['attrs']['class'] = array_merge($region_1_params['attrs']['class'], $this->get_col_width_classes_array('modules_region_1_width', 12, 12, 12));

        ?>

        <div <?print\SM\Util\Html::attributes($container_attrs);?>>

            <div <?print\SM\Util\Html::attributes($region_1_params['attrs']);?>>

                <div class="modules-region-1">

                    <div class="modules">

                        <? foreach ($this->entities as $entity) { ?>

                            <?php print $this->render_region_module('1', $entity, 'module_2', $module_attrs); ?>

                        <? } ?>

                    </div>

                </div>

            </div>

        </div>

        <?

        return ob_get_clean();
    }

}