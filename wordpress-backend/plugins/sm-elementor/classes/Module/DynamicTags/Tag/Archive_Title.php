<?php

namespace SM_Elementor\Module\DynamicTags\Tag;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Core\DynamicTags\Data_Tag;
use ElementorPro\Classes\Utils;
use ElementorPro\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Archive_Title extends Tag {

	public function get_name() {
		return 'sm-archive-title';
	}

	public function get_title() {
		return __( 'SM: Archive Title', 'elementor-pro' );
	}

    public function get_group() {
        return Module::ARCHIVE_GROUP;
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    public function render() {
        $include_context = 'yes' === $this->get_settings( 'include_context' );

        $parts = [];

        $parts['archive-title'] = Utils::get_page_title( $include_context );

        if ('yes' === $this->get_settings( 'include_queried_object_name' ))
        {
            $qobject = get_queried_object();

            if ($qobject)
            {
                switch (get_class($qobject))
                {
                    case 'WP_Post':
                        $name = $qobject->post_title;
                        break;

                    case 'WP_Term':
                        $name = $qobject->name;
                        break;

                    case 'WP_Post_Type':
                        //$name = $qobject->label;
                        break;
                }
            }

            if ($name) $parts['queried-title'] = $name;
        }

        $sep = $this->get_settings( 'separator' );

        $parts_result = [];

        foreach ($parts as $key=>$part)
        {
            $parts_result[] = '<span class="part-'.$key.'">'.$part.'</span>';
        }

        echo wp_kses_post( join($sep, $parts_result) );
    }

    protected function _register_controls() {
        $this->add_control(
            'include_context',
            [
                'label' => __( 'Include Context', 'elementor-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'separator',
            [
                'label' => __( 'Separator', 'elementor-pro' ),
                'type' => Controls_Manager::TEXT,
                'default' => ': ',
            ]
        );

        $this->add_control(
            'include_queried_object_name',
            [
                'label' => __( 'Include Queried object name', 'elementor-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
    }
}
