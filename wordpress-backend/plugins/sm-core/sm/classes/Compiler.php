<?php

namespace SM;

class Compiler extends Common\Component
{
    var $global_vars;

    /* @return Compiler */
    static function i($info=[], $context=null) { return parent::i($info, $context); }

    static function info()
    {
        return array(

        );
    }

    function init_events()
    {
        add_filter('sm/compiler/functions', array($this, '_filter_sm_compiler_functions'));

        $this->add_filter('sm/content', null, 10, 3);

        $this->add_action('wp');
    }

    function _action_wp(&$wp)
    {
        $this->get_global_vars();

        $this->global_vars += apply_filters('sm/compiler/vars/wp', array());
    }

    function _filter_sm_content($uri, $vars=array(), $scope=false)
    {
        return $this->compile($uri, $vars, $scope);
    }

    function _filter_sm_compiler_functions($funcs)
    {
        $funcs['sm_is_context']         = 'sm_is_context';

        $funcs['func']                  = function ($p) { return 'FUNC:'.$p; };

        $funcs['is_archive']            = 'is_archive';

        $funcs['is_admin']              = 'is_admin';

        $funcs['is_category']           = 'is_category';

        $funcs['is_front_page']         = 'is_front_page';

        $funcs['is_singular']           = 'is_singular';

        $funcs['is_single']             = 'is_single';

        $funcs['is_tax']                = 'is_tax';

        $funcs['is_tag']                = 'is_tag';


        $funcs['has_term']              = 'has_term';

        $funcs['is_search']             = 'is_search';

        $funcs['is_context']            = 'is_context';

        $funcs['is_post_type_archive']  = 'is_post_type_archive';

        $funcs['is_paged']              = 'is_paged';

        $funcs['is_category_root']      = function ($category= '') { if (is_category($category)) { $term = get_queried_object(); return (!$term->parent); }};

        $funcs['is_query_enter_page']   = function () { return !is_paged() && !get_query_var('mode') && empty($_GET); };

        $funcs['is_request_archive']    = function () { return get_query_var('sm_request_archive') ? true : false; };

        $funcs['is_taxterm']            = function () { global $wp_query; return $wp_query->is_category || $wp_query->is_tag || $wp_query->is_tax ? true : false; };

        $funcs['com']                   = function () { return sm()->compiler_context()->get_var('com'); };

        $funcs['the_excerpt']           = function () { return apply_filters('sm/theme/the_excerpt', apply_filters( 'the_excerpt', get_the_excerpt() )); };

        $funcs['the_title']             = function ()
                                          {
                                              ob_start();
                                              the_title();
                                              $t = ob_get_clean();

                                              return $t;
                                              //return apply_filters_ref_array('sm/theme/the_title', []);
                                          };

        $funcs['the_archive_title']     = function ()
                                          {
                                              return apply_filters_ref_array('sm/theme/the_archive_title', []);
                                          };

        $funcs['the_archive_description'] = function ()
                                          {
                                              return apply_filters_ref_array('sm/theme/the_archive_description', []);
                                          };


        $funcs['the_post_thumbnail']    = function ()
                                          {
                                              return apply_filters_ref_array('sm/theme/the_post_thumbnail', []);
                                          };

        $funcs['the_post_media']        = function ()
                                          {
                                              return apply_filters_ref_array('sm/theme/the_post_media', []);
                                          };

        $funcs['the_content']           = function ()
                                          {
                                              return apply_filters_ref_array('sm/theme/the_content', []);
                                          };

        $funcs['the_post']              = function ()
                                          {
                                              the_post();
                                          };

        $funcs['action']           =      function ($tag, $arg = '')
                                          {
                                              $args = func_get_args();

                                              $args[0] = 'sm/compiler/action/'.$tag;

                                              ob_start();

                                              call_user_func_array('do_action', $args);

                                              return ob_get_clean();
                                          };

        $funcs['region']           =      function ($name, $params=[])
                                          {
                                              //return sm()->page()->region($name);
                                          };

        $funcs['wrap']                  = function ($content, $before='', $after='') { return $content ? $before.$content.$after : ''; };


        return $funcs;
    }

    function get_processor($name=null)
    {
        if (!isset($this->processor))
        {
            $name = $name ?: 'fenom';

            $this->processor = sm()->implement('sm_compiler_processor_'.$name, 'sm_compiler_processor');
        }

        return $this->processor;
    }

    /* @return sm_compiler_context  */
    function get_compiler_context()
    {
        return sm('sm_compiler_context');
    }

    function get_code_cache_dir()
    {
        if (!isset($this->cache_dir))
        {
            $dir = $this->get_class_info('path').'/cache';

            $this->cache_dir = apply_filters('sm/compiler/code_cache_dir', $dir);
        }

        return $this->cache_dir;
    }

    function get_template_dirs()
    {
        $dirs = [];

        if (is_child_theme() && file_exists(get_stylesheet_directory().'/ftpl'))
        {
            $dirs[] = get_stylesheet_directory().'/ftpl';
        }

        if (file_exists(get_template_directory().'/ftpl'))
        {
            $dirs[] = get_template_directory().'/ftpl';
        }

        return $dirs;
    }

    function get_global_vars()
    {
        if (!isset($this->global_vars))
        {
            $this->global_vars = array();

            $this->global_vars += apply_filters('sm/compiler/vars', array());

            $this->global_vars['e'] = sm()->create('sm_compiler_object_eval');

            $this->global_vars['c'] = $this->tpl_var_prepare(sm()->compiler_context());
        }

        return $this->global_vars;
    }

    function get_global_var($name, $def=null)
    {
        $this->get_global_vars();

        return !empty($this->global_vars[$name]) ? $this->global_vars[$name] : $def;
    }

    function set_global_var($name, $value)
    {
        $this->get_global_vars();

        $this->global_vars[$name] = $value;
    }


    function compile_safe($uri, $vars=array(), $scope=false, $processor=null)
    {
        $vars += $this->get_global_vars();

        if (isset($scope) && $scope!==false)
        {
            $context = $this->get_compiler_context();

            $context->scope_open(is_array($scope) ? $scope : $vars);
        }

        $result = $this->get_processor($processor)->setup()->compile($uri, $vars);

        if (isset($scope) && $scope!==false)
        {
            $context->scope_close();
        }

        return $result;
    }

    function compile($uri, $vars=array(), $scope=false)
    {
        $vars += $this->get_global_vars();

        if (isset($scope) && $scope!==false)
        {
            $context = $this->get_compiler_context();

            $context->scope_push(is_array($scope) ? $scope : $vars);
        }

        $result = $this->get_processor()->setup()->compile($uri, $vars);

        if (isset($scope) && $scope!==false)
        {
            $context->scope_pop();
        }

        return $result;
    }

    function compile_condition($uri, $vars=array(), $processor=null)
    {
        return $this->get_processor($processor)->setup()->compile_condition($uri, $vars);
    }

    function compile_fast_vars($value)
    {
        $this->get_global_vars();

        if (is_array($value))
        {
           foreach ($value as $key=>&$item)
           {
               $item = $this->compile_fast_vars($item);
           }
        }
        else if (is_string($value))
        {
            $value = preg_replace_callback('/\{\$([\w\d\_]+)\}/', function($mt)
            {
                $vname = $mt[1];

                return @$this->global_vars[$vname];

            }, $value);
        }

        return $value;
    }

    function tpl_var_prepare($var)
    {
        if (is_scalar($var))
        {
            return $var;
        }
        else if (is_array($var))
        {
            $result = array();

            foreach ($var as $i => $v) $result[$i] = $this->tpl_var_prepare($v);

            return $result;
        }
        else if (is_object($var) && !is_subclass_of($var, 'sm_compiler_object'))
        {
            if (method_exists($var, 'sm_compiler_object'))
            {
                return $var->sm_compiler_object();
            }
            else
            {
                return new sm_compiler_object($var);
            }
        }
        else
        {
            return $var;
        }
    }



}


$item = \SM\Cache::i()->item([
    'name'=> 'header',
    'contexts'=> [
        \SM\Cache\Context\MenuActiveItems('main'),
        \SM\Cache\Context\UserRoles('main'),
    ],
])->load();

if (!$item->need_rebuild())
{
    print $item->data;
}
else
{
    $data = 'dddff';

    $item->save_data();
}
