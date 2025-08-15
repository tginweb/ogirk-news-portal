<?php

namespace SM_Elementor\Module\Slider\QueryBlock;

use Elementor\Controls_Manager;

class Block_Player_Single extends Block_Slider {


    static function get_block_module_regions()
    {
        return array_merge(parent::get_block_module_regions(), ['2']);
    }

    function get_enqueue_assets() {
        return ['sm_elementor.swiper'];
    }

    function inner() {

        $player_params = [];

        $slider_params['slider'] = [];

        $player_params['autoplay'] = true;

        $container_attrs = [
            'data-boot' => [],
            'data-sm-elementor-player' => $player_params,
            'class' => ['row']
        ];


        ob_start();


        $region_2_params = $this->customizer_element_params('region_2', []);
        $region_2_params['attrs']['class'] = array_merge($region_2_params['attrs']['class'], $this->get_col_width_classes_array('modules_region_2_width', 12, 12, 12));


        $region_2_params['attrs']['data-boot'] = [];

        $region_2_params['attrs']['data-sm-elementor-overflow-scroll'] = [
           'max_height' => '500px'
        ];

        $current_post = get_post();

        ?>

        <div <?print\SM\Util\Html::attributes($container_attrs);?>>

            <?php print $this->render_region_column('1', 'module_101', [$current_post]); ?>

            <?php if ($this->settings['slider_thumbs']=='yes'): ?>

                <div <?print\SM\Util\Html::attributes($region_2_params['attrs']);?>>

                    <div class="c-thumbs modules-region-2">

                        <div class="modules">

                            <? foreach ($this->entities as $entity) {


                                $module_attrs = [
                                    'class' => []
                                ];

                                if ($entity->ID==$current_post->ID)
                                {
                                    $module_attrs['class'][] = 'active';
                                }

                                ?>

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

    function register_block_settings_controls()
    {
        parent::register_block_settings_controls();
    }

}