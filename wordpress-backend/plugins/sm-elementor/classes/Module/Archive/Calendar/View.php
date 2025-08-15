<?php


namespace SM_Elementor\Module\Archive\Calendar;

use Elementor\Controls_Stack;
use ElementorPro\Plugin;
use Elementor\Controls_Manager;

class View
{

    var $settings;
    var $query_date;
    var $query_vars;
    var $query_vars_filtered;

    function init($settings, $query_vars) {

        $settings = array_filter($settings);

        $this->settings = $this->prepare_settings($settings);

        if (empty($this->settings['base_url']['url']))
            $this->settings['base_url']['url'] = $this->get_base_url_default();

        $this->query_vars = $query_vars;

        return $this;
    }

    function init_ajax($settings, $query_vars) {

        $this->settings = $settings;

        $this->query_vars = $query_vars;

        return $this;
    }

    function get_base_url_default() {

        $current_url =  $_SERVER['REQUEST_URI'];

        $pattern = '@(page\/[0-9]+\/|\&page\=[0-9]+|page\=[0-9]+\&|date\_filter\=[\d\-]+)@i';

        $current_url = preg_replace($pattern, '', $current_url);

        return  $current_url;
    }

    function get_default_settings()
    {
        return array(
            'attrs'          => [],
            'base_url'       => [],
            'query_date_var' => 'date_filter'
        );
    }


    function prepare_settings($settings)
    {
        return wp_parse_args($settings, $this->get_default_settings());
    }

    function get_query_date($type=null)
    {
        if (!isset($this->query_date))
        {
            $this->query_date = [];

            $date_var_value = [];

            if ($this->settings['query_date_var']=='default')
            {
                $date_var_value = [
                    'year'  => !empty($this->query_vars['year'])     ? $this->query_vars['year'] : null,
                    'month' => !empty($this->query_vars['monthnum']) ? $this->query_vars['monthnum'] : null,
                    'day'   => !empty($this->query_vars['day'])      ? $this->query_vars['day'] : null,
                ];
            }
            else
            {
                $date_var = $this->settings['query_date_var'];

                if (!empty($this->query_vars[$date_var]))
                {
                    $date_var_value_str = $this->query_vars[$date_var];

                    list($date_var_value['year'], $date_var_value['month'], $date_var_value['day']) = explode('-', $date_var_value_str);
                }
            }


            if (!empty($date_var_value['year']))
            {
                $this->query_date['year'] = $date_var_value['year'];

                if (!empty($date_var_value['month']))
                {
                    $this->query_date['month'] = $date_var_value['month'];

                    if (!empty($date_var_value['day']))
                    {
                        $this->query_date['day'] = $date_var_value['day'];
                    }
                }
                else
                {
                    $this->query_date['month'] = 1;
                }
            }
            else
            {
                $this->query_date['year'] = intval(date('Y'));
                $this->query_date['month'] = intval(date('m'));
                $this->query_date['day'] = intval(date('d'));
            }

        }

        return $type ? $this->query_date[$type] : $this->query_date;
    }

    function client_data()
    {

        $data['state']['current_year']  = $this->get_query_date('year');
        $data['state']['current_month'] = $this->get_query_date('month');
        $data['state']['current_day']   = $this->get_query_date('day');

        return $data;
    }


    function get_query_vars_filtered()
    {
        if (!isset($this->query_vars_filtered))
        {
            $vars = $this->query_vars;

            unset($vars['year'], $vars['monthnum'], $vars['day'], $vars['limit'], $vars['paged'], $vars['date_filter']);

            $vars = array_filter($vars);

            $vars += [

            ];

            $this->query_vars_filtered = $vars;
        }

        return $this->query_vars_filtered;
    }

    function get_archive_by_year_month()
    {
        $qv = $this->get_query_vars_filtered();

        return \SM\Query\Archive::i()->get_archives(array(
            'type'       => 'monthly',
            'post_type'  => $qv['post_type'],
            'query_vars' => $qv
        ));
    }

    function get_archive_by_year_month_day($year, $month)
    {
        $qv = $this->get_query_vars_filtered();

        $qv['year'] = $year;
        $qv['monthnum'] = $month;

        return \SM\Query\Archive::i()->get_archives(array(
            'type'       => 'daily',
            'post_type'  => $qv['post_type'],
            'query_vars' => $qv
        ));
    }

    function get_month_grid($filter_year, $filter_month, $filter_day=null)
    {
        global $wp_locale;

        $today_day = intval(date('d'));

        $archive = $this->get_archive_by_year_month_day($filter_year, $filter_month);

        $daysInMonth = cal_days_in_month(0, $filter_month, $filter_year);

        $weekDays = array();

        foreach (array(1,2,3,4,5,6,0) as $i)
        {
            $weekDays[$i] = $wp_locale->get_weekday_abbrev($wp_locale->get_weekday($i));
        }

        $week_day_numbers = array(
            1 => 0,
            2 => 1,
            3 => 2,
            4 => 3,
            5 => 4,
            6 => 5,
            0 => 6,
        );

        $blank = $week_day_numbers[date('w', strtotime("{$filter_year}-{$filter_month}-01"))];

        ob_start();

        ?>

        <div class="el-month-grid">

            <table class="" style="table-layout: fixed;">

                <tr>
                    <th colspan="7" class="text-center"> <?php echo $wp_locale->get_month($filter_month) ?> <?php echo $filter_year ?> </th>
                </tr>

                <tr>
                    <?php foreach($weekDays as $key => $weekDay) : ?>
                        <td class="text-center"><?php echo $weekDay ?></td>
                    <?php endforeach ?>
                </tr>

                <tr>

                    <?php for($i = 0; $i < $blank; $i++): ?>
                        <td></td>
                    <?php endfor; ?>

                    <?php for($i = 1; $i <= $daysInMonth; $i++): ?>


                    <?php

                        $day_attrs = array();

                        $day_content = $i;


                        if ($today_day==$i)
                        {
                            $day_attrs['class'][] = 'today';
                        }

                        if ($filter_day==$i)
                        {
                            $day_attrs['class'][] = 'filter';
                        }

                        if (isset($archive[$filter_year][$filter_month][$i]))
                        {
                            $day_attrs['class'][] = 'archive';

                            $day_content = $this->get_day_link($day_content, $filter_year, $filter_month, $i);
                        }
                        else
                        {
                            $day_content = '<span class="">'.$day_content.'</span>';
                        }
                    ?>


                    <?php print \SM\Util\Html::tag('td', $day_attrs, $day_content); ?>

                    <?php if(($i + $blank) % 7 == 0): ?>

                </tr>
                <tr>

                    <?php endif; ?>

                    <?php endfor; ?>

                    <?php for($i = 0; ($i + $blank + $daysInMonth) % 7 != 0; $i++): ?>
                        <td></td>
                    <?php endfor; ?>

                </tr>

            </table>

        </div>

        <?

        return ob_get_clean();
    }

    function get_base_url()
    {
        return $this->settings['base_url'];
    }

    function get_day_link($content, $year, $month, $day)
    {
        $base_url = $this->get_base_url();

        if ($this->settings['query_date_var']==='default')
            $day_url = add_query_arg(array_filter(['year' => $year, 'monthnum'=>$month, 'day'=>$day]), $base_url['url']);
        else
            $day_url = add_query_arg([$this->settings['query_date_var'] => join('-', array_filter([$year, $month, $day]))], $base_url['url']);

        return '<a class="el-query-day" href="'.$day_url.'" data-year="'.$year.'" data-month="'.$month.'" data-day="'.$day.'">'.$content.'</a>';
    }


    function get_client_data()
    {
        $data = $this->settings;

        $data['settings_protected'] = $this->settings;
        $data['query_protected'] = $this->get_query_vars_filtered();

        return $data;
    }

    function get_view_attrs()
    {
        $attrs = $this->settings['attrs'];

        $attrs += [
            'class' => ['sm-elementor-calendar-view'],
            'data-boot' => 1,
            'data-sm-elementor-archive-calendar' => $this->get_client_data()
        ];

        return $attrs;
    }

    function get_current_query_date_filter_title()
    {
        global $wp_locale, $wp_query;

        if (!empty($wp_query->query_vars['date_filter']))
        {
            $result = [];

            $value = [];

            list($value['year'], $value['month'], $value['day']) = explode('-', $wp_query->query_vars['date_filter']);

            if ($value['day'])
                $result[] = $value['day'];

            if ($value['month'])
                $result[] = $wp_locale->get_month($value['month']);

            if ($value['year'])
                $result[] = $value['year'];

            return join(' ', $result);
        }
    }

    function render()
    {
        $output  = '<div '.\SM\Util\Html::attributes($this->get_view_attrs()).'>';

        $output .= $this->render_inner();

        $output .= '</div>';

        return $output;
    }

    function render_inner()
    {
        global $wp_locale;

        $query_year  = $this->get_query_date('year');
        $query_month = $this->get_query_date('month');
        $query_day   = $this->get_query_date('day');

        $archive_monthly = $this->get_archive_by_year_month();

        $count = 0;

        //fb($archive_monthly);

        foreach ($archive_monthly as $year=>$months)
        {
            $year_options[$year] = array(
                'value' => $year,
                'label' => $year,
                'months' => $months
            );

            foreach ($months as $mon)
            {
                $count += $mon['count'];
            }
        }

        foreach (array(12,11,10,9,8,7,6,5,4,3,2,1) as $month)
        {
            $month_options[$month] = array(
                'value' => $month,
                'label' => $wp_locale->get_month($month),
            );
        }

        ob_start();

        ?>

        <div class="el-calendar-filters">

            <div class="el-calendar-filter filter-year">

                <select name="year">

                    <?php foreach ($year_options as $num=>$item): ?>

                        <option <?php print $num==$query_year?'selected':'';?> data-months="<?print esc_attr(json_encode(array_keys($item['months'])));?>" value="<?php print $item['value'];?>"><?php print $item['label'];?></option>

                    <?php endforeach; ?>

                </select>

                <a href="#" class="el-query-link el-query-year action ">за год</a>

            </div>

            <div class="el-calendar-filter filter-month">

                <select name="month">

                    <?php foreach ($month_options as $num=>$item): ?>

                        <option <?php print $num==$query_month?'selected':'';?> value="<?php print $item['value'];?>"><?php print $item['label'];?></option>

                    <?php endforeach; ?>

                </select>

                <a href="#" class="el-query-link el-query-month action ">за месяц</a>

            </div>

        </div>

        <?print $this->get_month_grid($query_year, $query_month, $query_day); ?>

        <?

        return ob_get_clean();
    }
}



