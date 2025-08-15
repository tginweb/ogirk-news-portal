<?php

namespace SM;

use SM;
use SM\Util;

class Context extends Common\Component
{
    public $contexts = [];
    public $tested_contexts = [];

    public $result = array();
    public $result_filtered = array();
    public $result_fields = array();

    /**
     * @return Context
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {
        add_action('sm/context/activate', array($this, '_action_context_activate'), 10, 2);
        add_filter('sm/context/info', array($this, '_filter_sm_context_info'));
        add_filter('sm/page/fields', array($this, '_filter_sm_page_fields'));
        add_filter('sm/page/args', array($this, '_filter_sm_page_args'));
        add_filter('sm/compiler/functions', array($this, '_filter_sm_compiler_functions'));
    }

    function _filter_sm_compiler_functions($funcs)
    {
        $funcs['is_context'] = array($this, 'is_active');

        return $funcs;
    }

    function _filter_sm_context_info(&$contexts)
    {
        $contexts['test'] = array(
            'condition'      => function() { return 1; },
        );

        $contexts['admin'] = array(
            'condition'      => function() { return is_admin(); },
        );

        $contexts['page_front'] = array(
            'condition'      => function() { return is_front_page(); },
        );

        $contexts['page_post'] = array(
            'condition'      => function() { return is_singular('post'); },
        );

        $contexts['page_posts_gallery'] = array(
            'condition'      => function() { return is_archive() &&  is_tax( 'post_format', 'post-format-gallery' ); },
        );

        $contexts['page_posts_video'] = array(
            'condition'      => function() { return is_archive() &&  has_term('video', 'post_format'); },
        );

        $contexts['page_category'] = array(
            'condition'      => function() { return is_category(); },
        );

        $contexts['page_tag'] = array(
            'condition'      => function() { return is_tag(); },
        );

        $contexts['page_search'] = array(
            'condition'      => function() { return is_search(); },
        );



        return $contexts;
    }

    function get_result($name=null, $default=null)
    {
        $this->build();

        if ($name)
        {
            return apply_filters('sm/context/result/'.$name, Util\Base::get_nested_value($this->result, $name, $default));
        }
        else
        {
            return $this->result;
        }
    }

    function get_active()
    {
        $this->build();

        return $this->active_contexts;
    }

    function is_active($check_ids, $logic='AND')
    {
        $this->build();

        $check_ids = (array)$check_ids;

        foreach ($check_ids as $check_id)
        {
            if ($logic=='AND')
            {
                if (!$this->test_context($check_id)) return false;
            }
            else
            {
                if ($this->test_context($check_id)) return true;
            }
        }

        return $logic=='AND' ? true : false;
    }

    function find_active($check_ids, $default=null)
    {
        foreach ($check_ids as $id)
        {
            if ($this->is_active($id))
            {
                return $id;
            }
        }

        return $default;
    }

    function context_activate($context_id, $context=array())
    {
        $context['context_id'] = $context_id;

        $this->tested_contexts[$context_id] = true;

        do_action_ref_array('sm/context/activate', array($context, &$this->result));
    }

    function context_deactivate($context_id, $context=array())
    {
        $context['context_id'] = $context_id;

        $this->tested_contexts[$context_id] = false;

        do_action_ref_array('sm/context/deactivate', array($context, &$this->result));
    }

    function _action_context_activate($context, &$data)
    {
        $data = Util\Base::extend_resolved_safe($data, $context);
    }

    function get_contexts()
    {
        if (isset($this->contexts))
        {
            $this->contexts = array();

            $this->contexts = apply_filters_ref_array('sm/context/info', array(&$this->contexts));
        }

        return $this->contexts;
    }

    function get_contexts_options($context_options=[])
    {
        foreach ($this->get_contexts() as $context_id => $context_info)
        {
            $context_options[$context_id] = $context_info['label'] ?: $context_id;
        }

        return $context_options;
    }

    function test_context($context_id)
    {
        $this->get_contexts();

        if ($context = $this->contexts[$context_id])
        {
            if (isset($this->tested_contexts[$context_id]))
                return $this->tested_contexts[$context_id];

            if (isset($context['condition']) && is_callable($context['condition']))
            {
                if (call_user_func($context['condition']))
                {
                    $this->context_activate($context_id, $context);
                    return true;
                }
                else
                {
                    $this->context_deactivate($context_id, $context);
                    return false;
                }
            }
        }

        return false;
    }

    function build()
    {
        if ($this->builded) return $this;

        foreach ($this->get_contexts() as $context_id=>$context)
        {
            if (!empty($context['autoload']))
                $this->test_context($context_id);
        }

        $this->result = apply_filters('sm/context/result', $this->result);

        $this->result = array_filter($this->result);

        $this->builded = true;

        return $this;
    }


}


