<?php


namespace SM_Elementor\Widget;

use Elementor;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use ElementorPro;
use ElementorPro\Plugin;
use SM_Elementor\Common;

class Nav_Links extends Common\Widget {

    public function get_name() {
        return 'sm-nav-links';
    }

    public function get_title() {
        return __( 'Nav Links', 'elementor' );
    }


    protected function _register_controls() {

        $this->start_controls_section(
            'section_layout',
            [
                'label' => __( 'Layout', 'elementor-pro' ),
            ]
        );

        $menu_links_repeater = new \Elementor\Repeater();

        $menu_links_repeater->add_control(
            'title',
            array(
                'label' => 'Menu title',
                'default' => '',
                'type' => Controls_Manager::TEXT,
                'default' => '',
            )
        );

        $menu_links_repeater->add_control(
            'url',
            array(
                'label' => 'Menu url',
                'default' => '',
                'type' => Controls_Manager::TEXT,
            )
        );

        $this->add_control(
            'items',
            [
                'type' => Controls_Manager::REPEATER,
                'prevent_empty' => false,
                'default' => array(),
                'fields' =>  array_values( $menu_links_repeater->get_controls() ),
                'title_field' => '{{{ title }}}',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        if (!empty($settings['items']))
        {
            $output = '<div class="nav">';

            foreach ($settings['items'] as $item) {

                $output .= '<li class="nav-item">';

                $item_url = $item['url'];

                $output .= '<a class="nav-link" href="' . $item_url . '">' . $item['title'] . '</a>';

                $output .= '</li>';

            }

            $output .= '</div>';

        }

        echo $output;
    }

    protected function content_template() {

    }

}
