<?php

namespace SM;

class Assets extends Common\Component
{
    static $registered_assets  = array();
    static $queued_assets = array();

    static $client_assets;
    static $queued_classes = array();

    var $styles;
    var $scripts;
    var $used_handles = array();

    /**
     * @return Assets
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }

    function get_scripts() { if (!isset($this->scripts)) $this->scripts = wp_scripts(); return $this->scripts; }

    function get_styles()  { if (!isset($this->styles)) $this->styles = wp_styles(); return $this->styles; }

    function reset()
    {
        $this->get_scripts()->reset();
        $this->get_styles()->reset();
    }

    function init_events()
    {
        parent::init_events();

        //$this->add_action('wp_print_scripts', '_action_dequeue');
        //$this->add_action('wp_footer', '_action_dequeue');
    }

    function _action_dequeue()
    {
        if (!empty(self::$queued_assets['dequeue_js']))
        {
            foreach (self::$queued_assets['dequeue_js'] as $handle)
            {
                $deq = self::$registered_assets['dequeue_js'][$handle]['data'];

                wp_dequeue_script($deq);
            }
        }

        if (!empty(self::$queued_assets['dequeue_css']))
        {
            foreach (self::$queued_assets['dequeue_css'] as $handle)
            {
                $deq = self::$registered_assets['dequeue_css'][$handle]['data'];

                wp_dequeue_style($deq);
            }
        }
    }



    function register_assets($items=array())
    {
        foreach ($items as $handle=>$asset)
        {
            $this->register_asset($handle, $asset);
        }
    }

    function register_asset($asset_handle, $asset=null)
    {
        if (isset(self::$registered_assets[$asset_handle])) return;

        $used_handlers = [];

        $result = [];

        foreach ($asset as $key=>$item)
        {
            if (!is_array($item))
            {
                $item = ['data' => $item];
            }

            if (!isset($item['type']))
            {
                if (in_array($key, ['js', 'css', 'inline'], true))
                {
                    $item['type'] = $key;
                }
                else if (is_string($item['data']))
                {
                    $item['type'] = pathinfo($item['data'], PATHINFO_EXTENSION);
                }
            }

            $item['type'] = strtolower($item['type']);

            if (!isset($item['handle']))
            {
                if (is_numeric($key))
                {
                    $index = '';

                    while (!$index || isset($used_handlers[$item['handle']]))
                    {
                        $item['handle'] = $asset_handle.'.'.$item['type'].$index;


                        if ($index==='') $index = 0;

                        $index++;
                    }

                    $used_handlers[$item['handle'] ] = $item['handle'];
                }
                else
                {
                    $item['handle'] = $asset_handle.'.'.$key;
                }
            }


            if (!$item['type']) continue;


            switch ($item['type'])
            {
                case 'js':
                    $item += array('deps'=>null,'ver'=>null,'in_footer'=>null);
                    wp_register_script($item['handle'], $item['data'], $item['deps'], $item['ver'], $item['in_footer']);
                    break;

                case 'css':
                    $item += array('deps'=>null,'ver'=>null,'media'=>null);
                    wp_register_style($item['handle'], $item['data'], $item['deps'], $item['ver'], $item['media']);
                    break;
            }

            $result[$item['handle']] = $item;
        }


        self::$registered_assets[$asset_handle] = $result;
    }

    function maybe_add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {

        // if action has already run, execute it
        // - if currently doing action, allow $tag to be added as per usual to allow $priority ordering needed for 3rd party asset compatibility
        if( did_action($tag) && !doing_action($tag) ) {

            call_user_func( $function_to_add, true );

            // if action has not yet run, add it
        } else {

            add_action( $tag, $function_to_add, $priority, $accepted_args );

        }

    }

    function wp_enqueue($handlers, $args = array())
    {

        $handlers = (array)$handlers;

        $items = [];

        foreach ($handlers as $handle_id => $handle_data)
        {
            if (is_string($handle_data))
            {
                $handle_id = $handle_data;

                if (isset(self::$registered_assets[$handle_id]) && !isset(self::$queued_assets[$handle_id]))
                {
                    $items = array_merge($items, array_values(self::$registered_assets[$handle_id]));
                }

                self::$queued_assets[$handle_id] = $handle_id;
            }
            else if (is_array($handle_data))
            {
                $items = array_merge($items, array_values($handle_data));
            }
        }

        if (!empty($items))
        {
            $args += [
                'context'			=> is_admin() ? 'admin' : 'wp',
                'action_type'       => 'enqueue_scripts',
                'priority'			=> 11,
            ];

            if (!empty($args['action']))
                $action = $args['action'];
            else
                $action = $args['context'].'_'.$args['action_type'];

            $cb = function ($direct_call) use ($items, $args) {

                foreach ($items as $item)
                {
                    switch ($item['type'])
                    {
                        case 'js':         wp_enqueue_script($item['handle']); break;
                        case 'css':        wp_enqueue_style($item['handle']); break;
                        case 'inline_js':  wp_add_inline_script($item['handle'], $item['data']); break;
                        case 'inline_css': wp_add_inline_style($item['handle'], $item['data']); break;
                        case 'code':       print $item['data']; break;
                        case 'asset':      $this->wp_enqueue($item['data'], $args); break;
                    }
                }

            };

            $this->maybe_add_action($action, $cb, $args['priority']);
        }
    }

    function process_incode_assets($content)
    {

        $content = preg_replace_callback
        (
            '@<\!--assets-enqueue\s+ class="(.+?)"\s*-->@is',

            function($mt)
            {
                $class = $mt[1];

                if (!$this->is_class_sended($class))
                {
                    if ($obj = sm($class))
                    {
                        $obj->enqueue_assets();
                    }

                    self::$queued_classes = $class;
                }

                return;
            },

            $content
        );

        return $content;
    }

    function assets_process($assets)
    {
        $com = $this;


        if (is_admin())
        {
            $action = 'admin_enqueue_scripts';
        }
        else
        {
            $action = 'wp_enqueue_scripts';
        }

        if (did_action($action))
        {
            $this->do_assets_enqueue($assets);
        }
        else
        {
            add_action($action, function () use ($com, $assets) { $com->do_assets_enqueue($assets); }, 200);
        }

        self::$processed_assets = array_merge(self::$processed_assets, $assets);
    }
}


