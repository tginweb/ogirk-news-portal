<?php

namespace SM;


class Page extends Common\Component
{

    var $page_queried;
    var $page_conditional;

    var $page_fields;
    var $page_args;

    var $region_buffer;
    var $region_buffer_current;

    var $current_template_file;
    var $current_template_suggestions = [];

    /* @return Page */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {
        parent::init_events();

        $this->add_action('add_meta_boxes');
        $this->add_action('save_post_page');
        $this->add_filter('template_include');

        $this->add_filter([
                 'embed_template_hierarchy'
                ,'404_template_hierarchy'
                ,'search_template_hierarchy'
                ,'front_page_template_hierarchy'
                ,'home_template_hierarchy'
                ,'archive_template_hierarchy'
                ,'taxonomy_template_hierarchy'
                ,'attachment_template_hierarchy'
                ,'single_template_hierarchy'
                ,'page_template_hierarchy'
                ,'singular_template_hierarchy'
                ,'category_template_hierarchy'
                ,'tag_template_hierarchy'
                ,'author_template_hierarchy'
                ,'date_template_hierarchy'
                ,'archive_template_hierarchy'
                ,'index_template_hierarchy'
            ],
            '_filter_template_hierarchy', 10000
        );


        $this->add_filter('sm/page/fields');
        $this->add_filter('sm/compiler/vars/wp');


        foreach ( ['post', 'page'] as $post_type )
        {
            $this->add_filter( "theme_{$post_type}_templates", '_filter_theme_post_templates', 10, 4 );
        }

        $this->add_filter( 'template_include');

        $this->add_action('admin_bar_menu', null, 1000);
    }

    function _action_admin_bar_menu()
    {
        global $wp_admin_bar;

        if ( 1)
        {
            if (!is_admin_bar_showing()) return;

            $wp_admin_bar->add_menu( array(
                'id'     => 'sm-page',
                'title'  => 'Page',
                'parent' => 'top-secondary',
                'href'   => false,
            ));

            $regions = [
                'page-outer',
                'page-layout',
                'page',
            ];

            foreach ($regions as $region)
            {
                $region_data = $this->get_region_data($region);

                $region_data = $this->fill_region_info($region_data);

                if (is_array($region_data))
                {
                    $value = json_encode($region_data);
                }
                else
                {
                    $value = 'string';
                }

                $wp_admin_bar->add_menu( array(
                    'id'     => 'sm-page-region-'.$region,
                    'title'  => $region.': '.$value,
                    'parent' => 'sm-page',
                    'href'   => false,
                ));
            }


        }
    }

    function _filter_theme_post_templates($post_templates, $theme, $post, $post_type)
    {

        foreach ($this->get_region_variants_options('page', 'post') as $key => $name)
        {
            $post_templates[$key] = $name;
        }

        return $post_templates;
    }

    function _filter_template_hierarchy($templates)
    {
        $this->current_template_suggestions = array_merge($this->current_template_suggestions, $templates);

        return $templates;
    }

    function _filter_template_include($template)
    {
        $this->current_template_file = $template;

        if (is_singular())
        {
            $region_variant_id = get_post_meta( get_the_ID(), '_wp_page_template', true );

            $this->current_region_data['page'] = $this->get_region_variant('page/'.$region_variant_id);
        }

        return $template;
    }

    function get_current_template_file()
    {
        return $this->current_template_file;
    }

    function _filter_sm_compiler_vars_wp($vars)
    {
        if ($page_com = $this->get_current())
        {
            $vars['page']        = new sm_compiler_object($page_com);
            $vars['page_entity'] = new sm_compiler_object($page_com->get_page_entity_com());
        }

        return $vars;
    }

    function _filter_sm_page_fields($fields)
    {
        if ($page = $this->get_page())
        {
            $fields['page_com'] = get_post_meta($page->ID, 'sm_page_com', true);

            $fields['page_layout_com'] = get_post_meta($page->ID, 'sm_page_layout_com', true);
        }

        return $fields;
    }

    function _action_add_meta_boxes()
    {
        add_meta_box('sm-page-post-params', __('Page params'), array($this, 'render_metabox_page_post_params'), 'page', 'side', 'default');
    }

    function render_metabox_page_post_params($post)
    {
        $form = array(
            'sm_page_com' => array(
                '#label'         => 'Page component',
                '#type'          => 'com_select',
                '#class_bundle'  => 'page',
                '#default'       => $post->ID ? get_post_meta($post->ID, 'sm_page_com', true) : null
            ),
            'sm_page_layout_com' => array(
                '#label'         => 'Layout component',
                '#type'          => 'com_select',
                '#class_bundle'  => 'layout',
                '#default'       => $post->ID ? get_post_meta($post->ID, 'sm_page_layout_com', true) : null
            ),
            'sm_page_condition_code' => array(
                '#label'         => 'Condition code',
                '#type'          => 'condition',
                '#rows'          => 5,
                '#default'       => $post->ID ? get_post_meta($post->ID, 'sm_page_condition_code', true) : null
            ),
            'sm_page_condition_priority' => array(
                '#label'         => 'Condition priority',
                '#type'          => 'text',
                '#default'       => $post->ID ? get_post_meta($post->ID, 'sm_page_condition_priority', true) : 0
            ),
        );

        print sm_renderer::render_form_inner($form);
    }

    function _action_save_post_page($post_id, $post=null)
    {
        if (!$post) return;

        if (!isset($_POST['original_publish']) && !isset($_POST['save_post'])) return;

        if (!current_user_can('edit_post', $post_id)) return;

        if($post->post_status == 'auto-draft') return;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        update_post_meta($post_id, 'sm_page_com', $_POST['sm_page_com']);

        update_post_meta($post_id, 'sm_page_layout_com', $_POST['sm_page_layout_com']);

        update_post_meta($post_id, 'sm_page_condition_code', $_POST['sm_page_condition_code']);

        update_post_meta($post_id, 'sm_page_condition_priority', intval($_POST['sm_page_condition_priority']));
    }

    function get_page()
    {
        return $this->get_page_queried() ?: $this->get_page_conditional();
    }

    function get_pages_conditional()
    {
        global $wpdb;

        $pages = $wpdb->get_results("         
            SELECT 
                p.*, 
                
                pm_cond_code.meta_value as page_condition_code,
                
                pm_cond_priority.meta_value as page_condition_priority
            
            FROM $wpdb->posts as p 

            INNER JOIN $wpdb->postmeta as pm_cond_code on pm_cond_code.post_id=p.ID 

            LEFT JOIN $wpdb->postmeta as pm_cond_priority on pm_cond_priority.post_id=p.ID 
                        
            WHERE 
              pm_cond_code.meta_value IS NOT NULL AND 
              pm_cond_code.meta_value <> '' AND 
              pm_cond_code.meta_key = 'sm_page_condition_code' AND 
              pm_cond_priority.meta_key='sm_page_condition_priority' AND
              p.post_status = 'publish'     
                     
            ORDER BY pm_cond_priority.meta_value * 1 ASC  
               
            LIMIT 100     
        ");

        return $pages;
    }

    function get_page_conditional()
    {
        if (!isset($this->page_conditional))
        {
            $page = false;

            $pages_conditional = $this->get_pages_conditional();

            if (!empty($pages_conditional))
            {
                foreach ($pages_conditional as $page_item)
                {
                    if ($result = sm_renderer::element_value_exec(array('#type'=>'condition'), $page_item->page_condition_code))
                    {
                        $page = Entity::i()->load_entity('post', $page_item);

                        if ( defined( 'SM_DEBUG' ) && SM_DEBUG )
                        {
                            fb(array(
                                'ID'=>$page->ID,
                                'title'=>$page->post_title,
                                'code'=>$page->page_condition_code,
                                'priority'=>$page->page_condition_priority,
                                'post'=>$page
                            ), 'CONDITIONAL PAGE FOUND');
                        }

                        break;
                    }
                }
            }

            $this->page_conditional = $page;
        }

        return $this->page_conditional;
    }

    function get_page_queried()
    {
        if (is_admin()) return;

        if (!$this->page_queried)
        {
            $this->page_queried = ($post = Entity::i()->get_post()) && ($post->post_type == 'page') ? $post : null;
        }

        return $this->page_queried;
    }



    function _filter_sm_context_param_field($value, $param)
    {
        if (!$value)
        {
            $page_entity = $this->get_page();

            $queried_entity = Entity::i()->get_request_entity();

            if ($queried_entity)
            {
                $value = $queried_entity->field('sm_page_'.$param) ?: $queried_entity->field('entity_'.$param);
            }

            if (!$value && $page_entity)
            {
                $value = $page_entity->cfield('sm_page_'.$param);
            }

            if (!$value)
            {
                switch ($param)
                {
                    case 'title':
                    case 'meta_title':

                        if ($queried_entity && $queried_entity->get_title())
                        {
                            $value = $queried_entity->get_title();
                        }
                        else if ($page_entity && $page_entity->get_title())
                        {
                            $value = $page_entity->get_title();
                        }
                }
            }
        }

        return $value;
    }

    function _filter_sm_context_result_arg($info)
    {
        if ($page_com = $this->get_current())
        {
            Util\Base::extender($info, $page_com->get_page_args());
        }

        return $info;
    }

    function get_current()
    {
        $class = Context::i()->get_result('page_class', 'sm_com_page');

        if (!$class) return;

        //return sm()->components()->com_instance('page', $class, [], [], 'sm_com_page');
    }

    function get_region_data($name, $default=null)
    {

        if (isset($this->region_buffer[$name]))
        {
            $content = $this->region_buffer[$name];
        }
        else if (has_action('sm/region/'.$name))
        {
            ob_start();
            do_action('sm/region/'.$name);
            $content = ob_get_clean();
        }
        else if ($name=='page' && $this->current_region_data['page'])
        {
            $content = $this->current_region_data['page'];
        }
        else if ($data = Context::i()->get_result('region/'.$name))
        {
            $content = $data;
        }
        else if ($default)
        {
            $content = $default;
        }
        else
        {
            if ($name=='page')
            {

                $post_names = [];

                foreach ($this->current_template_suggestions as $file)
                {
                    $post_names[] = pathinfo($file, PATHINFO_FILENAME);
                }

                $post_names[] = pathinfo($this->get_current_template_file(), PATHINFO_FILENAME);

                $post_names = array_unique($post_names);


                $posts = get_posts(['post_type'=>'sm-tpl', 'post_name__in'=>$post_names, 'orderby'=>'post_name__in', 'numberposts'=>1]);

            }
            else
            {
                $posts = get_posts(['post_type'=>'sm-tpl', 'sm-tpl-role'=>$name, 'numberposts'=>1]);
            }

            if (!empty($posts))
            {
                $post = array_shift($posts);

                $content = [
                    'source' => 'tpl-post',
                    'value' => $post->ID,
                ];
            }
            else if (has_action('sm/region/default/'.$name))
            {
                ob_start();
                do_action('sm/region/default/'.$name);
                $content = ob_get_clean();
            }

        }

        if (is_string($content))
        {
            $content = [
                'source' => 'content',
                'value' => $content
            ];
        }

        return $content;
    }

    function render_region_data($data)
    {
        if (!empty($data) && is_array($data))
        {
            if ($data['uri'])
            {
                list($data['source'], $data['value']) = explode(':', $data['uri']);
            }

            switch ($data['source'])
            {
                case 'post':

                    if ($data['value'])
                    {
                        $post = Entity::i()->load_entity('post', $data['value']);

                        $content = apply_filters('the_content', $post->post_content);
                    }

                    break;

                case 'tpl-post':

                    if ($data['value'])
                    {
                        $content = ElementorPro\Plugin::elementor()->frontend->get_builder_content_for_display( $data['value'] );
                    }

                    break;

                case 'tpl-file':

                    ob_start();

                    locate_template('tpl/'.$data['value'], true);

                    $content = ob_get_clean();

                    break;

                case 'content':

                    $content = $data['value'];

                    break;
            }
        }
        else
        {
            $content = $data;
        }

        return $content;
    }

    function fill_region_info($data)
    {
        if (!empty($data) && is_array($data))
        {
            if ($data['uri']) list($data['source'], $data['value']) = explode(':', $data['uri']);

            switch ($data['source'])
            {
                case 'post':

                    if ($data['value'])
                    {
                        $post = Entity::i()->load_entity('post', $data['value']);

                        $data['post_name'] = $post->post_name;
                        $data['post_title'] = $post->post_title;
                    }

                    break;

                case 'tpl-post':

                    if ($data['value'])
                    {
                        $post = Entity::i()->load_entity('post', $data['value']);
                        $data['post_name'] = $post->post_name;
                        $data['post_title'] = $post->post_title;
                    }

                    break;

                case 'tpl-file':
                    break;

                case 'content':
                    break;
            }
        }

        return $data;
    }

    function region($name, $params=[])
    {
        return '';


        $params += [
            'container_tag' => 'div',
        ];

        if (!is_array($name))
        {
            $data = $this->get_region_data($name);
        }
        else
        {
            $data = $name;
            $name = $data['name'];
        }

        $content = trim($this->render_region_data($data));

        if (!$content) return;

        if (!empty($params))
        {
            $params = Util\Html::attributes_params_fetch($params);
        }

        if (!empty($params['container_tag']))
        {
            $params['container_attrs']['class'][] = 'sm-region-'.preg_replace('/[\.\s\:]/', '-', $name);
        }

        return Util\Html::tag($params['container_tag'], $params['container_attrs'], Util\Html::tag($params['tag'], $params['attrs'], $content));
    }

    function &get_region_variants($reset=false)
    {
        if (!isset($this->region_variants) || $reset) $this->region_variants = apply_filters('sm/region/variants', []);

        return $this->region_variants;
    }

    function get_region_variant($name, $default=null)
    {

        return Util\Base::get_nested_value($this->get_region_variants(), $name, $default);
    }


    function get_region_variants_options($name, $tpl_type=null)
    {
        $result = [];

        $variants = $this->get_region_variants();

        if (!empty($variants[$name]))
        {
            foreach ($variants[$name] as $key=>$variant)
            {
                if ($tpl_type && ($tpl_type!==$variant['tpl_type'])) continue;

                $result[$key] = $variant['label'] ?: $variant['source'].': '.$variant['value'];
            }
        }


        return $result;
    }

    function region_start($name)
    {
        ob_start();

        $this->region_buffer_current = $name;
    }

    function region_end()
    {
        if ($this->region_buffer_current)
        {
            $this->region_buffer[$this->region_buffer_current] .= ob_get_clean();

            $this->region_buffer_current = null;
        }
    }

}



