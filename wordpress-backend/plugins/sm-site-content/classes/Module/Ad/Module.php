<?php

namespace SM_Site_Content\Module\Ad;

use SM\Common;

class Module extends Common\Module
{

    static function info()
    {
        return array(

        );
    }

    function assets()
    {
        $path = $this->get_path_rel();

        return [
            'sm_content_ad.common' => [
                'css' => $path.'/assets/css/common.css',
            ],
        ];
    }

    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm/entity/bundles');
        $this->add_action('sm/action/sm-ad-redirect', array($this,'on_action_sm_ad_redirect'));

        $this->add_filter('sm/query/posts/processors');

        $this->add_action('sm/query/posts/processor/ad_query');

        add_shortcode('sm_ad_query', array($this, 'shortcode_sm_ad_query'));
    }

    function _filter_sm_query_posts_processors($info)
    {
        $info += [
            'ad_query' => 'Ad query'
        ];

        return $info;
    }

    function _action_sm_query_posts_processor_ad_query($query)
    {
	    $is_demo_query = false;

	    if ($demo_ad_zone = $_REQUEST['demo_ad_zone'])
	    {

		    $demo_zone_ids = explode(',', $demo_ad_zone);

		    $demo_zone_ids = array_map('intval', $demo_zone_ids);

		    foreach ($query->query_vars['tax_query'] as $t_items)
		    {
			    if (is_array($t_items))
			    {
				    foreach ($t_items as $t_item)
				    {
					    if (is_array($t_item))
					    {
						    if ($t_item['taxonomy'] == 'sm-ad-zone')
						    {
							    $query_zone_term_id = current($t_item['terms']);
							    break;
						    }
					    }
				    }
			    }
		    }


		    if (($query_zone_term_id && ($demo_ad_zone==='all' || in_array($query_zone_term_id, $demo_zone_ids, true))))
		    {

			    //unset($query->query_vars['tax_query']);

			    $query->query_vars['posts_status'] = ['publish', 'trash'];

			    $query->query_vars['meta_query']['relation'] = 'AND';

			    $query->query_vars['meta_query'][] = array('key' => 'sm_ad_demo', 'value'=> 1);

			    $is_demo_query = true;
		    }
	    }

	    if (!$is_demo_query)
	    {
		    $now = date_i18n('Y-m-d H:i:s');

		    $query->query_vars['meta_query']['relation'] = 'AND';

		    $query->query_vars['meta_query'][] = array('key' => 'sm_ad_demo', 'value'=> 1, 'compare' => '!=');

		    $query->query_vars['meta_query'][] = [
			    'key'		=> 'sm_ad_start',
			    'compare'	=> '<=',
			    'value'		=> $now,
		    ];

		    $query->query_vars['meta_query'][] = [
			    'key'		=> 'sm_ad_end',
			    'compare'	=> '>=',
			    'value'		=> $now,
		    ];
	    }

    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
            'post:sm-repost' => array(
                'label'             => 'Репосты',
                'labels'            => array('singular_name'=>'Репост'),
                'register'          => true,
                'public'            => true,
                'hierarchical'      => false,
                'has_archive'       => false,
                'show_in_menu'      => true,
                'supports'          => array('title','current'),
            ),
            'post:sm-ad-item' => array(
                'label'             => 'Реклама',
                'labels'            => array('singular_name'=>'Реклама'),
                'register'          => true,
                'public'            => true,
                'hierarchical'      => false,
                'has_archive'       => false,
                'show_in_menu'      => true,
                'supports'          => array('title','current'),
            ),
            'term:sm-ad-zone' => array(
                'label'             => 'Зоны рекламы',
                'labels'            => array('singular_name'=>'Зона рекламы'),
                'object_type'       => array('sm-ad-item'),
                'register'          => true,
                'show_admin_column' => true,
                'hierarchical'      => true,
            ),
            'term:sm-ad-teaser-zone' => array(
                'label'             => 'Зоны тизерной рекламы',
                'labels'            => array('singular_name'=>'Зоны тизерной рекламы'),
                'object_type'       => array('sm-ad-item'),
                'register'          => true,
                'show_admin_column' => true,
                'hierarchical'      => true,
            ),
        ]);
    }

    function _action_sm_action_sm_ad_redirect()
    {
        if ($_REQUEST['url'])
        {
            wp_redirect($_REQUEST['url']);
        }
    }
}


