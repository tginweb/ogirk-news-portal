<?php

namespace SM_Elementor\Module\Query\View;

use Elementor\Controls_Manager;
use SM_Elementor\Module;
use WPMFDropbox\Util;

class view_slider_swiper extends Common\View_Slider {

    static function info()
    {
        return [
            'regions_count' => 2
        ] + parent::info();
    }

    static function get_elements_info()
    {
        return parent::get_elements_info() + [
            'slider_pagination' => ['label'=>'Slide pagination'],
        ];
    }

    static function enqueue_assets()
    {
        parent::enqueue_assets();

        \SM\Assets::i()->wp_enqueue('sm_elementor.swiper');
    }


    function get_inner_template() {


        if (empty($this->entities)) return;

        if (count($this->entities)==1) return $this->inner_single();

        $slider_params = [];

        $slider_params['slider'] = [];

        if ($this->settings['slider_navigation']==='yes')
        {
            $slider_params['slider']['navigation']['nextEl'] = $this->get_selector().' .c-slides .swiper-button-next';
            $slider_params['slider']['navigation']['prevEl'] = $this->get_selector().' .c-slides .swiper-button-prev';
        }

        if ($this->settings['slider_pagination']==='yes')
        {
            $slider_params['slider']['pagination']['el'] = $this->get_selector().' .c-slides .swiper-pagination';
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

	    $slider_params['slider']['autoHeight'] = true;

        if (!empty($this->settings['slider_responsive']))
        {
            foreach ($this->settings['slider_responsive'] as $item)
            {
                $breakpoint = [];
                $breakpoint_value = null;

                if ($grid_engine = Module\Framework\Module::i()->get_grid_engine())
                {
                    $breakpoint_value = $grid_engine->get_breakpoint_value_by_device_max($item['breakpoint']);
                }

                if (!$breakpoint_value)
                {
                    continue;
                }

                if (!empty($item['slides']))
                {
                    $breakpoint['slidesPerView'] = intval($item['slides']);
                }

                if (!empty($item['slides_space']))
                {
                    $breakpoint['spaceBetween'] = intval($item['slides_space']);
                }

                $slider_params['slider']['breakpoints'][$breakpoint_value] = $breakpoint;
            }
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
                $slider_params['thumbs']['navigation']['nextEl'] = $this->get_selector().' .c-thumbs .swiper-button-next';
                $slider_params['thumbs']['navigation']['prevEl'] = $this->get_selector().' .c-thumbs .swiper-button-prev';
            }

            if ($this->settings['slider_thumbs_direction']!='horizontal')
            {
                $slider_params['thumbs']['direction'] = $this->settings['slider_thumbs_direction'];
            }
        }


        $container_attrs = [
            'data-boot' => [],
            'data-sm-elementor-swiper' => $slider_params,
            'class' => ['row', 'q-regions']
        ];

        $module_params['attrs']['class'][] = 'swiper-slide';

        ob_start();

        $region_1_params = $this->customizer_element_params('region_1', []);
        $region_1_params['attrs']['class'] = array_merge($region_1_params['attrs']['class'], ['q-region', 'c-slides'], $this->get_col_width_classes_array('modules_region_1_width'));

        $region_2_params = $this->customizer_element_params('region_2', []);
        $region_2_params['attrs']['class'] = array_merge($region_2_params['attrs']['class'], ['q-region', 'c-thumbs'], $this->get_col_width_classes_array('modules_region_2_width'));

        $slider_pagination_params = $this->customizer_element_params('slider_pagination');

        $slider_pagination_params['attrs']['class'][] = 'swiper-pagination';

        ?>

        <div <?print\SM\Util\Html::attributes($container_attrs);?>>

            <div <?print\SM\Util\Html::attributes($region_1_params['attrs']);?>>

                <div>

                <div class="swiper-container q-region-modules-1">

                    <div class="swiper-wrapper modules">

                        <? foreach ($this->entities as $entity) { ?>

                            <?php print $this->get_region_module('1', $entity, $module_params + ['type'=>'module_2']); ?>

                        <? } ?>

                    </div>

                    <?php if ($this->settings['slider_pagination']=='yes' && $this->settings['slider_pagination_outside']!=='yes'): ?>

                        <div <?php print \SM\Util\Html::attributes($slider_pagination_params['attrs']);?>></div>

                    <?php endif; ?>

                    <?php if ($this->settings['slider_navigation']=='yes' && $this->settings['slider_navigation_outside']!=='yes'): ?>

                        <div class="swiper-button-prev s-icon"><i class="fa fa-angle-left" aria-hidden="true"></i></div>
                        <div class="swiper-button-next s-icon"><i class="fa fa-angle-right" aria-hidden="true"></i></div>

                    <?php endif; ?>


                </div>
                </div>


                <?php if ($this->settings['slider_pagination']=='yes' && $this->settings['slider_pagination_outside']==='yes'): ?>

                    <div <?php print \SM\Util\Html::attributes($slider_pagination_params['attrs']);?>></div>

                <?php endif; ?>

                <?php if ($this->settings['slider_navigation']=='yes' && $this->settings['slider_navigation_outside']==='yes'): ?>

                    <div class="swiper-button-prev s-icon"><i class="fa fa-angle-left" aria-hidden="true"></i></div>
                    <div class="swiper-button-next s-icon"><i class="fa fa-angle-right" aria-hidden="true"></i></div>

                <?php endif; ?>

            </div>

            <?php if ($this->settings['slider_thumbs']=='yes'): ?>

                <div <?print\SM\Util\Html::attributes($region_2_params['attrs']);?>>

                    <div class="swiper-container q-region-modules-2">

                        <div class="swiper-wrapper modules">

                            <? foreach ($this->entities as $entity) { ?>

                                <?php print $this->get_region_module('2', $entity, $module_params + ['type'=>'module_2']); ?>

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

        $module_params['attrs']['class'][] = '';

        ob_start();

        $region_1_params = $this->customizer_element_params('region_1', []);
        $region_1_params['attrs']['class'] = array_merge($region_1_params['attrs']['class'], $this->get_col_width_classes_array('modules_region_1_width'));

        ?>

        <div <?print\SM\Util\Html::attributes($container_attrs);?>>

            <div <?print\SM\Util\Html::attributes($region_1_params['attrs']);?>>

                <div class="q-region-modules-1">

                    <div class="modules">

                        <? foreach ($this->entities as $entity) { ?>

                            <?php print $this->get_region_module('1', $entity, $module_params + ['type'=>'module_2']); ?>

                        <? } ?>

                    </div>

                </div>

            </div>

        </div>

        <?

        return ob_get_clean();
    }



}
