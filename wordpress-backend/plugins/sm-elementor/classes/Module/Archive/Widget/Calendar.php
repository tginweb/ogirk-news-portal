<?php


namespace SM_Elementor\Module\Archive\Widget;

use SM_Elementor\Common;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

class Calendar extends Common\Widget {

    public function get_name() {
        return 'sm-archive-calendar';
    }

    public function get_title() {
        return __( 'SM: Archive Calendar', 'elementor' );
    }

    public function get_icon() {
        return 'eicon-type-tool';
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_calendar',
            [
                'label' => __( 'Календарь'),
            ]
        );

        $this->add_control(
            'calendar_base_url',
            [
                'type' => Controls_Manager::URL,
                'label' => __( 'Base Url'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );


        $this->add_control(
            'context_query_type',
            array(
                'label' => 'Context query',
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => '',
                    'archive' => 'Archive',
                ],
                'default' => '',
            )
        );

        $this->add_control(
            'context_query_term_id',
            array(
                'label' => 'Term ID',
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'context_query_type' => 'archive',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            )
        );

        $this->add_control(
            'context_query_post_type',
            array(
                'label' => 'Post Type',
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'context_query_type' => 'archive',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            )
        );



        $this->end_controls_section();
    }

    protected function render() {

        global $wp_query;

        $settings = $this->get_settings_for_display();

        if (!empty($settings['context_query_type']))
        {
            $context_query_args['post_type'] = $settings['context_query_post_type'];
        }
        else
        {
            $context_query_args = $wp_query->query_vars;
        }

        $view = new \SM_Elementor\Module\Archive\Calendar\View();

        $view->init(\SM\Util\Base::sub_params($settings, 'calendar_'), $context_query_args);

        print $view->render();
    }

}
