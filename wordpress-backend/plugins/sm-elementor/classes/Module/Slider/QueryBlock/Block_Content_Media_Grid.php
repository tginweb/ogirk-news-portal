<?php

namespace SM_Elementor\Module\Slider\QueryBlock;

use Elementor\Controls_Manager;

class Block_Content_Media_Grid extends \LivemeshAddons\Blocks\Block {

    static function get_block_module_regions()
    {
        return array_merge(parent::get_block_module_regions(), ['2']);
    }

    function get_enqueue_assets() {
        return [];
    }

    function inner() {

        switch ($this->settings['grid_schema'])
        {
            case '6':
                $grid_columns = 2;
                break;

            case '4':
                $grid_columns = 3;
                break;

            case '12-6':
                $grid_columns = 2;
                $this->settings['modules_customizer'][] = ['index' => 'first', 'target_names' => ['container'], 'width_in_columns' => 12];
                break;

            case '12-4':
                $grid_columns = 3;
                $this->settings['modules_customizer'][] = ['index' => 'first', 'target_names' => ['container'], 'width_in_columns' => 12];
                break;

            case '6-12':
                $grid_columns = 2;
                $this->settings['modules_customizer'][] = ['index' => 'last', 'target_names' => ['container'], 'width_in_columns' => 12];
                break;

            case '4-12':
                $grid_columns = 3;
                $this->settings['modules_customizer'][] = ['index' => 'last', 'target_names' => ['container'], 'width_in_columns' => 12];
                break;
        }


        $output = '';

        $output .= '<div class="row">';

        $output .= $this->render_region_column('1', 'module_102', $this->shift_region_entities('1'), [], [
            'desktop' => $grid_columns,
            'tablet' => 1,
            'mobile' => 1
        ]);

        $output .= '</div>';

        return $output;
    }

    function register_block_settings_controls()
    {

        $this->add_control(
            'grid_schema',
            [
                'label'     => 'Раскладка',
                'type'      => Controls_Manager::SELECT,
                'default'   => '6',
                'options'   => [
                    '6'    => '2 колонки',
                    '4'    => '3 колонки',
                    '12-6' => '1 большая + 2 колонки',
                    '12-4' => '1 большая + 3 колонки',
                    '6-12' => '2 колонки + 1 большая',
                    '4-12' => '3 колонки + 1 большая',
                ]
            ],
            true
        );

    }

}