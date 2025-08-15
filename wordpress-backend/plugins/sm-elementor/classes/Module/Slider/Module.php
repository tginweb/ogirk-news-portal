<?php


namespace SM_Elementor\Module\Slider;

use SM\Elementor\Widget;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    static function info()
    {
        return [
            'classmap' => [
                'widget' => [

                ],
            ]
        ];
    }


    function init_events()
    {
        $this->add_filter('lae_block_types');

    }

    function _filter_lae_block_types($types)
    {
        $types += [
            'block_slider_swiper'         => ['label'=>__('Слайдер'), 'class'=> '\SM_Elementor\Module\Slider\QueryBlock\Block_Slider_Swiper'],
            'block_player'                => ['label'=>__('Плеер'), 'class'=> '\SM_Elementor\Module\Slider\QueryBlock\View_Player'],
            'block_player_single'         => ['label'=>__('Плеер Single'), 'class'=> '\SM_Elementor\Module\Slider\QueryBlock\viewplayersingle'],

            'block_content_media_slider'  => ['label'=>__('Контент: Галерея слайдер'), 'class'=> '\SM_Elementor\Module\Slider\QueryBlock\viewcontentmediaslider'],
            'block_content_media_grid'    => ['label'=>__('Контент: Галерея сетка'), 'class'=> '\SM_Elementor\Module\Slider\QueryBlock\viewcontentmediagrid'],
        ];

        return $types;
    }
}



