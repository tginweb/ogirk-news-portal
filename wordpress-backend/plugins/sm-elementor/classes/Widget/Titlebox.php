<?php


namespace SM_Elementor\Widget;

use SM_Elementor\Common;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

class Titlebox extends Common\Widget
{

    public function get_name()
    {
        return 'sm-titlebox';
    }

    public function get_title()
    {
        return __('SM: Titlebox', 'elementor');
    }

    public function get_icon()
    {
        return 'eicon-type-tool';
    }

    protected function _register_controls()
    {

        $templates_options = \SM_Elementor\Common\Plugin_Module::get_templates_options();

        $templates_options = ['0' => '— ' . __( 'Select', 'elementor-pro' ) . ' —'] + $templates_options;

        $this->start_controls_section(
            'section_source',
            [
                'label' => __('Источник', 'elementor'),
            ]
        );

        $this->add_control(
            'source_type',
            [
                'label' => __('Источник', 'elementor-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => 'По умолчанию',
                    'template' => 'Шаблон',
                ],
            ]
        );

        $this->add_control(
            'source_template',
            [
                'label' => __('Шаблон источника'),
                'type' => Controls_Manager::SELECT,
                'options' => $templates_options,
                'condition' => [
                    'source_type' => 'template',
                ],
            ]
        );

        $this->add_control(
            'style_type',
            [
                'label' => __( 'Style type'),
                'type' => Controls_Manager::SELECT,
                'default' => 'column',
                'options' => [
                    'column' => 'In column',
                    'fullwidth' => 'Fullwidth',
                ],
                'prefix_class' => 's-type-',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title',
            [
                'label' => __('Заголовки', 'elementor'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Заголовок', 'elementor-pro'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label' => __('Подзаголовок', 'elementor-pro'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_calendar',
            [
                'label' => __('Календарь', 'elementor'),
            ]
        );

        $this->add_control(
            'calendar_display',
            [
                'label' => __('Enable'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $this->add_render_attribute('wrapper', [
            'class' => ['el-titlebox-wrapper'],
        ]);

        ?>

        <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>

            <?
                $this->render_source();
            ?>

        </div>

        <?
    }

    function render_source()
    {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('title', ['class' => 'el-titlebox-title']);

        ?>

            <div class="el-titlebox-titles">

                <h1 <?php echo $this->get_render_attribute_string('title'); ?>>
                    <?print $settings['title'];?>
                </h1>

            </div>

            <div class="el-titlebox-filters">

                <? if ($settings['calendar_display']==='yes') {

                    $this->add_render_attribute('calendar_toggle', [
                        'class' => ['el-titlebox-filter-toggle'],
                        'data-toggle' => 'dropdown',
                        'href' => '#'
                    ]);

                    $this->add_render_attribute('calendar_value', [
                        'class' => ['el-titlebox-filter-value'],
                    ]);

                    $this->add_render_attribute('calendar_content', [
                        'class' => ['el-calendar-content', 'dropdown-menu', 'dropdown-prevent-click-close', 'dropdown-menu-right']
                    ]);


                    global $wp_query;

                    $view = new \SM_Elementor\Module\Archive\Calendar\View();

                    $view->init(\SM\Util\Base::sub_params($settings, 'calendar_'), $wp_query->query_vars);

                    $filters_value_str = $view->get_current_query_date_filter_title();

                    ?>

                    <div class="el-titlebox-filter el-titlebox-filter-calendar dropdown">

                        <?php if ($filters_value_str) { ?>
                            <span <?php echo $this->get_render_attribute_string('calendar_value'); ?> >

                                <?php print $filters_value_str; ?>

                            </span>
                        <?php } ?>

                        <a <?php echo $this->get_render_attribute_string('calendar_toggle'); ?> >

                            <i class="fa fa-calendar"></i><span class="caption">Календарь</span>

                        </a>

                        <div <?php echo $this->get_render_attribute_string('calendar_content'); ?> >

                            <?php print $view->render(); ?>

                        </div>

                    </div>

                <? } ?>

            </div>

        <?
    }
}
