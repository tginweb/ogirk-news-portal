<?php


namespace SM;

use SM\Util;

define('CACHE_PERMANENT', -1);
define('CACHE_TEMPORARY', 0);
define('CACHE_STATUS_OK', 0);
define('CACHE_STATUS_INVALIDATE', 1);

class Cache extends Common\Component
{
    /* @return Cache */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {
        parent::init_events();

        $this->add_action('sm/cache/query');

        //$this->add_action('admin_action_sm_cache_query');

        //$this->add_action('admin_bar_menu', null, 100);

	    $this->add_action('admin_bar_menu', null, 1000);

        $this->add_action('admin_action_sm_core_bg_tasks_process');
	    $this->add_action('admin_action_sm_core_cache_clear_temporary');
        $this->add_action('admin_action_sm_core_cache_clear_permanent');
    }

    function admin_action_sm_core_bg_tasks_process()
    {
        do_action('porter_process_bg');

        wp_redirect($_SERVER['HTTP_REFERER']);
    }

	function _action_admin_action_sm_core_cache_clear_temporary()
	{
        do_action('sm_clear_cache_temporary');

        Cache::i()->get_storage()->query('delete');

        /*

        if ( function_exists( 'w3tc_flush_all' ) ) {
            w3tc_flush_all();
        }
        */

		wp_redirect($_SERVER['HTTP_REFERER']);
	}

    function _action_admin_action_sm_core_cache_clear_permanent()
    {
        do_action('sm_clear_cache_permanent');

        Cache::i()->get_storage()->query('delete');

        // native
        /*

        if ( function_exists( 'w3tc_flush_all' ) ) {
            w3tc_flush_all();
        }
        */

        wp_redirect($_SERVER['HTTP_REFERER']);
    }


	/* @return Cache\Storage\Db */
    function get_storage()
    {
        if (!isset($this->storage))
        {
            $this->storage = \SM\Cache\Storage\Db::i();
        }

        return $this->storage;
    }

    function query($op, $cid=null, $bin=null, $expire=null)
    {
        $this->get_storage()->query($op, $cid, $bin, $expire);


    }

    /* @return Cache\Item */
    function item($info)
    {
        $item = $this->get_storage()->create_item($info);

        return $item;
    }

    /* @return Cache\Item */
    function load_item($info)
    {
        $item = $this->get_storage()->create_item($info);

        return $item->load();
    }

    function get($cid, $bin='cache')
    {
        $this->prepare($cid);

        return $this->get_storage()->cache_get($cid, $bin);
    }

    function set($cid, $data, $bin='cache', $expire=null)
    {
        if (!$expire)
        {
            if ($bin=='permanent' || $bin=='system')
                $expire = CACHE_PERMANENT;
            else
                $expire = CACHE_TEMPORARY;
        }

        return $this->get_storage()->cache_set($cid, $data, $bin, $expire);
    }

    function delete($cid, $bin='cache')
    {
        return $this->get_storage()->cache_row_delete($cid, $bin);
    }


    function get_shortcode_item($shortcode)
    {
        if (is_array($shortcode)) {

            $shortcode_tag = $shortcode[0];
            $shortcode_params = isset($shortcode[1]) ? $shortcode[1] : array();
        }
        else {

            list($shortcode_tag, $shortcode_params) = func_get_args();
        }

        if (!($cid = $shortcode_params['cache_cid'])) {

            if (!empty($shortcode_params['cache_per_url'])) {

                $shortcode_params['cache_salt_url'] = $_SERVER["REQUEST_URI"];
            }

            $cid = $shortcode_tag. '-' . md5(serialize(Util\Wp::params_to_atts_array($shortcode_params)));
        }

        return $this->get_storage()->cache_item_load($cid, 'content');
    }

    function get_callback($cb, $cid, $bin='cache', $expire=null)
    {
        if (!$cid) {
            if (is_string($cb))
                $cid = $cb;
            else if (is_array($cb))
                $cid = get_class($cb[0]) . $cb[1];
        }

        $item = $this->get_storage()->cache_item_load($cid, $bin);

        if (!$item->need_rebuild()) return $item->output();

        $item->start_build();

        if (is_callable($cb)) {
            ob_start();
            call_user_func($cb);
            $content = ob_get_clean();
        }

        $item->end_build($content, $expire);

        return $content;
    }

    function _action_sm_cache_query($params)
    {
        $this->query($params['op'], $params['cid'], $params['bin'], $params['expire']);
    }

    function page_admin()
    {
        print sm()->components()->com_load('backpage', array('content_main'=>sm()->form()->build('form_sm_cache_admin')));
    }


	function _action_admin_bar_menu()
	{
		global $wp_admin_bar;

		if (current_user_can('edit_posts'))
		{


			$wp_admin_bar->add_menu(array(
				'id'     => 'smart-cache',
				'title'  => 'КЭШ',
				'href'   => false,
			));

			$wp_admin_bar->add_menu(array(
				'title'  => 'Очистка временного кеша',
				'href'   => admin_url('admin.php?action=sm_core_cache_clear_temporary'),
				'parent' => 'smart-cache'
			));

            $wp_admin_bar->add_menu(array(
                'title'  => 'Очистка перманентного кеша',
                'href'   => admin_url('admin.php?action=sm_core_cache_clear_permanent'),
                'parent' => 'smart-cache'
            ));
		}
	}

}



