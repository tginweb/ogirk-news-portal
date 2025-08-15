<?php

namespace SM_Elementor\Module\Query\View\Common;

use SM_Elementor;
use SM_Elementor\Module\QueryControls\Group_Control\Entity_Query;

// qv
abstract class Base extends \SM_Elementor\Common\Customizable
{
    static $customizer_class_name = 'query_view';
    static $customizer_class_types = null;
    var $customizer_element_class_preffix = 'q-';

    static $all_regions_info;

    var $module_settings_by_region = [];
    var $module_settings_common;

    var $cid;

    var $original_settings;

    var $query_result;
    var $query_args;

    var $entities;
    var $index = [];
    var $object_type_setting = 'query_view';

    var $filters = [];
    var $client_filters = [];
    var $client_page;

    static function enqueue_assets()
    {
        \SM\Assets::i()->wp_enqueue('sm_elementor.query');
    }

    static function info()
    {
        return [
            'regions_count' => 1,
        ];
    }

    static function get_elements_info()
    {
        $result = [
            'container'      => ['label'=>'Container'],
            'filter'         => ['label'=>'Filter'],
            'filter_label'   => ['label'=>'Filter label'],
            'filter_control' => ['label'=>'Filter control'],
        ];

        for ($region_id = 1; $region_id <= 3; $region_id++)
        {
            $result['region_'.$region_id] = ['label'=>'Регион '.$region_id];
        }

        return $result;
    }

    static function get_all_regions_info($filter_args=[], $value_field=false) {

        if (!isset(static::$all_regions_info))
        {
            static::$all_regions_info = [];

            $regions = &static::$all_regions_info;

            foreach (static::get_object_types() as $type_id => $type_info)
            {
                if ($type_info['class'] && class_exists($type_info['class']))
                {
                    for ($region_id = 1; $region_id <= $type_info['regions_count']; $region_id++)
                    {
                        if (!isset($regions[$region_id]))
                        {
                            $regions[$region_id]['label'] = ucfirst($region_id);
                        }

                        $regions[$region_id]['view_types'][$type_id] = $type_id;
                    }
                }
            }

            $regions['lightbox'] = ['label'=>'Лайтбокс', 'view_types'=>[]];
        }

        return wp_filter_object_list(static::$all_regions_info, $filter_args, 'and', $value_field);
    }

    function get_cid()
    {
        return $this->cid;
    }

    function get_selector()
    {
        return '[data-cid="'.$this->cid.'"]';
    }

    function get_title()
    {
        return $this->object_type_info ? $this->object_type_info['label'] : $this->get_object_type_id();
    }

    function get_default_settings()
    {
        return array(
            'attrs'                => [],
            'current_filter_term'  => '',
            'pagination'           => 'none',
            'show_remaining'       => true,
            'show_related_posts'   => false,
            'current_post_id'      => '',
            'pagination_prev_icon' => 'fa fa-angle-left',
            'pagination_next_icon' => 'fa fa-angle-right',
            'query_url'            => html_entity_decode( get_pagenum_link() )
        );
    }

    function is_debug_mode()
    {
        return !empty($_REQUEST['debug']);
    }

    function prepare_settings($settings)
    {
        $settings = wp_parse_args($settings, $this->get_default_settings());


        return $settings;
    }

    function is_empty() {

        return empty($this->entities);
    }

    function init($settings) {

        $settings = array_filter($settings);

        if (empty($settings['cid']))
            $settings['cid'] = uniqid();

        $this->cid = $settings['cid'];

        $this->original_settings = $settings;

        $this->settings = $this->prepare_settings($settings);

        $this->query_args = Entity_Query::build_query_args('query', $this->settings);

        if (!$this->settings['pagination'] || $this->settings['pagination']==='none')
        {
            $this->query_args['no_found_rows'] = true;
        }

        $this->query_result = Entity_Query::build_query_result('query', $this->settings, $this->query_args);

        $this->set_entities($this->query_result['entities']);


        if (!empty($this->settings['view_customizer']))
            $this->customizer_init($this->settings['view_customizer']);

        $this->init_filters();

        return $this;
    }


    function init_ajax($request) {

        $settings             = json_decode(base64_decode($request['settings_protected']), true);
        $query_args           = json_decode(base64_decode($request['query_protected']), true);


        $this->cid            = $request['cid'];
        $this->settings       = $this->prepare_settings($settings);
        $this->query_args     = $query_args;


        $this->client_filters = $request['filters'];
        $this->client_page    = $request['current_page'];

        $this->init_filters();

        $this->client_query_prepare();


        $this->query_result = Entity_Query::build_query_result('query', $this->settings, $this->query_args);

        $this->set_entities($this->query_result['entities']);

        if (!empty($this->settings['view_customizer']))
            $this->customizer_init($this->settings['view_customizer']);


        return $this;
    }

    function client_query_prepare()
    {
        if (!empty($this->client_page))
            $this->query_args['paged'] = filter_var($this->client_page, FILTER_VALIDATE_INT);

        foreach ($this->get_filters() as $ctrl)
        {
            $ctrl->set_query_vars_value($this->query_args);
        }
    }


    function get_filters()
    {
        return $this->filters;
    }

    function init_filters()
    {
        if ($this->settings['filters_display']=='yes')
        {
            if (!empty($this->settings['filters']))
            {
                foreach ($this->settings['filters'] as $item)
                {
                    if (!empty($item['type']))
                    {
                        $field_name = $item['name'];

                        $type_info = \SM_Elementor\Module\Query\Module::i()->get_filter_controller_type($item['type']);

                        if ($type_info && class_exists($type_info['class']))
                        {
                            $filter_settings =
                                \SM\Util\Base::sub_params($item, '_type_'.$item['type'].'_') +
                                \SM\Util\Base::sub_params($item, '_widget_'.$item['widget'].'_') +
                                $item;

                            $controller = new $type_info['class']($this, $filter_settings);


                            if (!empty($this->client_filters[$field_name]))
                            {
                                $controller->set_value($this->client_filters[$field_name]);
                            }


                            $this->filters[$field_name] = $controller;
                        }
                    }
                }
            }
        }
    }

    function get_view_classes() {

        $classes = array();

        $classes[] = 'sm-query-view';

        $classes[] = $this->get_object_type_id_class();

        return $classes;
    }

    function get_view_attrs()
    {
        $attrs = $this->settings['attrs'];

        $attrs += [
            'class' => []
        ];

        $attrs['class'] = array_merge($attrs['class'], $this->get_view_classes());

        if (!empty($this->settings['view_class']))
            $attrs['class'] = array_merge($attrs['class'], (array)$this->settings['view_class']);

        if (!empty($this->settings['view_id']))
            $attrs['id'] = $this->settings['view_id'];

        $attrs['data-cid'] = $this->get_cid();

        $attrs['data-boot'] = '1';

        $attrs['data-sm-elementor-query'] = $this->get_client_data();

        return $attrs;
    }

    function shift_region_entities($region='1', $default_limit=null)
    {
        $settings_value = $this->settings['modules_region_'.$region.'_items_limit'];

        if ($settings_value==='0' || intval($settings_value)>0)
            $count = intval($settings_value);
        else
            $count = $default_limit;

        return $this->shift_entities($count);
    }

    function set_entities($entities)
    {
        if (is_array($entities))
        {
            $this->entities_count = count($entities);
        }

        $this->entities = $entities;
    }

    function shift_entities($number=null)
    {
        $result = [];

        if ($number)
        {
            for ($i=1;$i<=$number;$i++)
            {
                if (count($this->entities)) $result[] = array_shift($this->entities);
            }
        }
        else
        {
            $result = $this->entities;
            $this->entities = [];
        }

        return $result;
    }

    function get_col_width_classes_array($settings_key, $defaults=[])
    {
        if ($grid_engine = SM_Elementor\Module\Framework\Module::i()->get_grid_engine())
            return $grid_engine->get_grid_col_width_classes($settings_key, $this->settings, $defaults);
        else
            return [];
    }

    function get_col_classes_array($settings_key, $defaults=[], $override_width=[])
    {
        if ($grid_engine = SM_Elementor\Module\Framework\Module::i()->get_grid_engine())
        {
            return $grid_engine->get_grid_col_classes($settings_key, $this->settings, $defaults, $override_width);
        }
        else
            return [];
    }

    function get_grid_row_class()
    {
        static $row_class;

        if (!isset($row_class))
        {
            if ($grid_engine = SM_Elementor\Module\Framework\Module::i()->get_grid_engine())
                $row_class = $grid_engine->get_grid_row_class();
            else
                $row_class = '';
        }

        return $row_class;
    }



    function get_template()
    {
        return <<<EOT

        
        <div {{container.attrs}}>

           {{filters once="1"}}   
                      
           {{inner}}                                       
           
           {{pagination}}                      
          
        </div>
        
EOT;
    }

    function render_inner($params)
    {
        $params = $this->customizer_element_params('inner', $params);

        return $this->render_element_wrapper('inner', $this->get_inner_content(), $params);
    }

    function get_inner_content()
    {
        return $this->compile_template($this->get_inner_template());
    }

    function get_inner_template()
    {
        return '';
    }

    function render_region($params=[])
    {
        $params = $this->customizer_element_params('region_'.$params['id'], $params) + [];

        $def_region_limit = round($this->entities_count / $this->regions_count());


        return $this->get_region($params['id'], 'module_1', $this->shift_region_entities($params['id'], $def_region_limit), $params);
    }

    function regions_count()
    {
        return $this->object_type_info['regions_count'];
    }


    function crypt_client_data($data)
    {
        return base64_encode(json_encode($data));
    }

    function get_client_settings()
    {
        return [
            'query_view'            => $this->settings['query_view'],
            'pagination_ajax'       => $this->settings['pagination_ajax'],
            'show_remaining'        => $this->settings['show_remaining'],
            'lightbox_link_wrap'    => $this->settings['lightbox_link_wrap'],
            'filters_display'       => $this->settings['filters_display'],
        ];
    }

    function get_client_settings_protected()
    {
        $settings = $this->original_settings;

        if ($settings['query_type']=='posts')
        {
            if ($settings['query_posts_query_mode']=='current_query')
                $settings['query_posts_query_mode'] = 'query';
        }

        return $settings;
    }

    function get_client_query()
    {

        return [
            'max_num_pages' => $this->query_result['max_num_pages'],
            'current_page' => $this->query_result['current_page']
        ];
    }

    function get_client_query_protected()
    {
        return $this->query_args;
    }


    function get_client_data()
    {

        $attrs = [
            'cid'                => $this->get_cid(),
            'settings'           => $this->get_client_settings(),
            'query'              => $this->get_client_query(),
            'filters'            => $this->get_client_filters_settings(),
            'settings_protected' => $this->crypt_client_data($this->get_client_settings_protected()),
            'query_protected'    => $this->crypt_client_data($this->get_client_query_protected()),
        ];

        return $attrs;
    }

    function get_client_filters_settings()
    {
        $result = [];

        foreach ($this->filters as $filter_name => $filter)
        {
            $result[$filter_name] = $filter->settings;
        }

        return $result;
    }

    function render_filters($params) {

        if ($this->settings['filters_display']=='yes')
        {
            $display = \SM_Elementor\Module\Query\Filters_View\Common\Base::create_object($this->settings['filters_view'], [$this]);

            if ($display)
            {
                $params = $this->customizer_element_params('filters', $params) + [];

                return $this->render_element_wrapper('filters', $display->init(\SM\Util\Base::sub_params($this->settings, 'filters_'))->render(), $params);
            }
        }
    }

    function render_pagination($params=[]) {

        if (empty($params['show']) && $this->host && $this->host->get_widget_footer() && $this->host->get_widget_footer()->include_pagination)
        {
            return;
        }

        $pagination_type = $this->settings['pagination'];

        if ($pagination_type == 'none' || $this->query_result['max_num_pages'] == 1) return;

        if ($this->settings['query_type']!=='posts') return;

        $params = $this->customizer_element_params('pagination', $params) + [];

        $params['attrs']['class'][] = 'q-pagination-type-'.preg_replace('/_/', '-', $pagination_type);

        $output = '';

        switch ($pagination_type) {

            case 'next_prev':

                $paginate_links = $this->get_paginate_links([
                    'query'     => $this->query_result['query'],
                    'total'     => $this->query_result['max_num_pages'],
                    'query_url' => $this->query_result['query_url'],
                    //'current' => $this->query['current_page'],
                    'prev_next_only' => true,
                    'prev_text' => '<i class="'.$this->settings['pagination_prev_icon'].'"></i>',
                    'next_text' => '<i class="'.$this->settings['pagination_next_icon'].'"></i>'
                ]);

                if (!$paginate_links['prev'])
                    $paginate_links =  array_merge([
                        'prev' => ['role'=>'prev', 'text' => '<i class="'.$this->settings['pagination_prev_icon'].'"></i>']
                    ], $paginate_links);

                if (!$paginate_links['next'])
                    $paginate_links =  array_merge($paginate_links, [
                        'next' => ['role'=>'next', 'text' => '<i class="'.$this->settings['pagination_next_icon'].'"></i>']
                    ]);

                $output .= $this->get_view_pagination_links($paginate_links, $this->query_result['current_page']);

                break;

            case 'load_more':

                $output .= '<a href="#" class="q-load-more">';

                $output .= __('Загрузить еще');

                //if ($this->settings['show_remaining']=='yes') $output .= ' - ' . '<span>' . (intval($this->query_result['count_all']) - $this->query_result['count']) . '</span>';

                $output .= '</a>';

                break;

            case 'paged':

                $paginate_links = $this->get_paginate_links([
                    'query'     => $this->query_result['query'],
                    'total'     => $this->query_result['max_num_pages'],
                    'query_url' => $this->query_result['query_url'],
                    //'current' => $this->query['current_page'],
                    'prev_text' => '<i class="'.$this->settings['pagination_prev_icon'].'"></i>',
                    'next_text' => '<i class="'.$this->settings['pagination_next_icon'].'"></i>'
                ]);

                $output .= $this->get_view_pagination_links($paginate_links, $this->query_result['current_page']);

                break;
        }

        $output .= '<span class="q-loading"></span>';

        return $this->render_element_wrapper('pagination', $output, $params);
    }

    function get_view_pagination_links($links, $current=1) {

        $output = '';

        $use_links = !$this->settings['pagination_ajax'] || $this->settings['pagination_seo'];

        foreach ($links as $link)
        {
            switch ($link['role'])
            {
                case 'prev':

                    if ($link['page'])
                        $output .= '<a class="q-page-nav" href="'.($use_links ? $link['href'] : '#').'" data-page="'.$link['page'].'">'.$link['text'].'</a>';
                    else
                        $output .= '<span class="q-page-nav q-disabled">'.$link['text'].'</span>';

                    break;

                case 'next':

                    if ($link['page'])
                        $output .= '<a class="q-page-nav" href="'.($use_links ? $link['href'] : '#').'" data-page="'.$link['page'].'">'.$link['text'].'</a>';
                    else
                        $output .= '<span class="q-page-nav q-disabled">'.$link['text'].'</span>';

                    break;

                case 'number':

                    if ($link['page']==$current)
                        $output .= '<span class="q-page-nav q-current-page" data-page="'.$link['page'].'">'.$link['text'].'</span>';
                    else
                        $output .= '<a class="q-page-nav" href="'.($use_links ? $link['href'] : '#').'" data-page="'.$link['page'].'">'.$link['text'].'</a>';

                    break;

                case 'dots':

                    $output .= '<span class="q-page-nav lae-dots">'.$link['text'].'</span>';

                    break;
            }
        }

        return $output;
    }


    function get_render_params()
    {
        return [

        ];
    }

    function get_region($region_id, $module_type, $entities = [], $params = [])
    {
        $output = '';

        $params += $this->get_render_params() + [
                'region_view'   => 'column',
                'region_id'     => $region_id,
                'region_params' => [],

                'modules_view'  => 'grid',
                'modules_attrs' => [],
                'module_type'   => $module_type,
            ];

        $params['attrs']['class'][] = 'q-region';

        $params['inner_attrs']['class'][] = 'q-region-modules-'.$region_id;

        if ($this->settings['modules_region_'.$region_id.'_scroll']==='yes')
        {
            $params['inner_attrs']['data-boot'] = 1;

            $params['inner_attrs']['data-sm-elementor-scrollbars'] = [
                'height_target'          => $this->settings['modules_region_'.$region_id.'_scroll_height_target'],
                'height_target_selector' => $this->settings['modules_region_'.$region_id.'_scroll_height_target_selector'],
                'height_target_value'    => $this->settings['modules_region_'.$region_id.'_scroll_height_target_value'],
            ];
        }

        $params['modules_attrs']['class'][] = 'modules';

        $output .= $this->{'get_region_view_'.$params['region_view']}($entities, $params);

        return $output;
    }

    function get_region_view_column($entities, $params = [])
    {
        $output = '';

        $params['attrs']['class'] = array_merge(
            $params['attrs']['class'],
            $this->get_col_width_classes_array('modules_region_'.$params['region_id'].'_width', [
                'desktop' => $params['column_width'],
                'tablet'  => $params['column_width_tablet'],
                'mobile'  => $params['column_width_mobile'],
            ])
        );

        $params['module_in_grid'] = true;

        $output .= '<div '.\SM\Util\Html::attributes($params['attrs']).'>';

        if (!empty($entities))
        {
            $output .= '<div '.\SM\Util\Html::attributes($params['inner_attrs']).'>';

            $output .= $this->{'get_modules_view_'.$params['modules_view']}($entities, $params);

            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    function get_region_view_block($entities, $params = [])
    {
        $output = '';

        $output .= '<div '.\SM\Util\Html::attributes($params['region_params']['attrs']).'>';

        if (!empty($entities))
        {
            $output .= '<div class="q-region-modules-'.$params['region_id'].'">';

            $output .= $this->{'get_modules_view_'.$params['modules_view']}($entities, $params);

            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    function get_modules_view_grid($entities, $params = [])
    {
        if (!empty($entities))
        {
            $params['modules_attrs']['class'][] = $this->get_grid_row_class();

            $params['module_params'] = \SM\Util\Base::sub_params($params, 'module_');

            $output = '<div '.\SM\Util\Html::attributes($params['modules_attrs']).'>';

            $i = 0;

            foreach ($entities as $entity)
            {
                if (($i+1)==count($entities)) $params['module_params']['is_last'] = true;


                $output .= $this->get_region_module($params['region_id'], $entity, $params['module_params']);

                $i++;
            }

            $output .= '</div>';

            return $output;
        }
    }

    function get_modules_view_list($entities, $params = [])
    {
        if (!empty($entities))
        {
            $params['module_params'] = \SM\Util\Base::sub_params($params, 'module_');

            $output = '<div '.\SM\Util\Html::attributes($params['modules_attrs']).'>';

            $i = 0;

            foreach ($entities as $entity)
            {
                if (($i+1)==count($entities)) $params['module_params']['is_last'] = true;

                $output .= $this->get_region_module($params['region_id'], $entity, $params['module_params']);

                $i++;
            }

            $output .= '</div>';

            return $output;
        }
    }


    function process_module_customizer_item(&$item, $region, &$module_entity, &$module_type, &$module_attrs, &$params)
    {
        if ($item['module_type'])
            $module_type = $item['module_type'];

        if ($params['modules_style']==='grid')
        {
            if ($item['width_in_columns'])
                $params['modules_columns_override']['desktop'] = $item['width_in_columns'];

            if ($item['width_in_columns_tablet'])
                $params['modules_columns_override']['tablet'] = $item['width_in_columns_tablet'];

            if ($item['width_in_columns_mobile'])
                $params['modules_columns_override']['mobile'] = $item['width_in_columns_mobile'];
        }
    }


    function get_module_render_params($module_entity, $region_id, $module_params=[])
    {

        if ($this->settings['module_'.$region_id.'_type'])
        {
            $module_params['type'] = $this->settings['module_'.$region_id.'_type'];
        }

        if (!empty($this->settings['modules_customizer']))
        {
            /*
            foreach ($this->settings['modules_customizer'] as $item)
            {
                if ($this->customizer_check_module_item($item, $module_entity, $params))
                {
                    $this->process_module_customizer_item($item, $region, $module_entity, $module_type, $module_attrs, $params);
                }
            }
            */
        }

        return $module_params;
    }

    function get_region_module($region_id, $module_entity, $module_params=[])
    {
        if (!isset($this->index[$region_id])) $this->index[$region_id] = 0;

        $this->index[$region_id]++;

        $module_params = $this->get_module_render_params($module_entity, $region_id, $module_params);

        if (empty($module_params['type']))
            $module_type = $module_params['type'];
        else
            $module_type = $this->settings['module_'.$region_id.'_type'];

        $module = \SM_Elementor\Module\Query\Module\Common\Base::create_object($module_type, [$this]);

        if ($module)
        {
            $module_params['index'] = $this->index[$region_id];

            if ($module_params['index']===1)
                $module_params['index_name'] = 'first';

            elseif (!empty($module_params['is_last']))
                $module_params['index_name'] = 'last';


            if (!isset($this->module_settings_common))
            {
                $this->module_settings_common = \SM\Util\Base::sub_params($this->settings, [
                    'lightbox_',
                    'modules_taxonomies',
                    'modules_customizer',
                    'query_avoid_duplicates',
                    'query_type'
                ], true);

            }

            if (!isset($this->module_settings_by_region[$region_id]))
            {
                if (!empty($this->settings['modules_region_'.$region_id.'_item_columns']))
                    $this->settings['module_'.$region_id.'_columns'] = $this->settings['modules_region_'.$region_id.'_item_columns'];

                if (!empty($this->settings['modules_region_'.$region_id.'_item_columns_tablet']))
                    $this->settings['module_'.$region_id.'_columns_tablet'] = $this->settings['modules_region_'.$region_id.'_item_columns_tablet'];

                if (!empty($this->settings['modules_region_'.$region_id.'_item_columns_mobile']))
                    $this->settings['module_'.$region_id.'_columns_mobile'] = $this->settings['modules_region_'.$region_id.'_item_columns_mobile'];

                $this->module_settings_by_region[$region_id] = \SM\Util\Base::sub_params($this->settings, 'module_'.$region_id.'_');

                $this->module_settings_by_region[$region_id] += $this->module_settings_common;
            }

            $module_settings = $module_params + $this->module_settings_by_region[$region_id];

            $module->init($module_entity, $module_settings, $region_id);

            if ($module->validate())
            {

                $module->index = $this->index[$region_id];
                $module->attrs = $module_params['attrs'];
                $module->view = $this;

                if (!empty($this->settings['query_avoid_duplicates']) && ($this->settings['query_avoid_duplicates']=='yes'))
                {
                    \SM\Query\Collector::i()->add_items([$module_entity->ID]);
                }

                return $module->render();
            }
        }
    }


    function get_paginate_links( $args = '' ) {

        return SM_Elementor\Util\PostQuery::get_paginate_links($args);

    }

    function get_widget_view_content()
    {
        $result = $this->render();

        if ($this->is_debug_mode())
        {
            $result .= $this->render_debug_panel();
        }

        self::enqueue_assets();

        return $result;
    }


    function render_debug_panel()
    {
        ob_start();

        ?>

        <nav class="navbar navbar-expand-lg navbar-light bg-light">

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav mr-auto">

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            SQL
                        </a>

                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                            <div>

                                <?php print $this->query_result['query']->request; ?>

                            </div>

                        </div>

                    </li>

                </ul>

            </div>

        </nav>

        <?

        return ob_get_clean();
    }
}