<?php


namespace SM;

class Query extends Common\Component
{
	/* @return Query */
	static function i($info=[], $context=null) { return parent::i($info, $context); }


	/* @return sm_query_type_post */
	function create($entity_type=null, $params=null, $context=[])
	{
		if (!$entity_type)
		{
			if (is_array($params))
			{
				if (!empty($params['entity_type'])) $entity_type = $params['entity_type'];

				else if (!empty($params['type'])) $entity_type = $params['type'];
			}

			if (!$entity_type) $entity_type = 'post';
		}

		if ($entity_type)
		{
			if ($query = sm()->create('sm_query_type_'.$entity_type, [], false, 'sm_query_type'))
			{
				if (isset($params))
				{
					$query->load($params, $context);
				}

				return $query;
			}
		}
	}

	function init_events()
	{
		$this->add_action('init');

		$this->add_action('pre_get_terms');
		$this->add_filter('request');
		$this->add_action('parse_tax_query');
		$this->add_action('pre_get_posts', null, 0);
		$this->add_filter('posts_pre_query', null, 100, 2);
		$this->add_filter('get_terms_fields', null, 100, 3);
		$this->add_filter('terms_clauses', null, 100, 3);
		$this->add_filter('the_posts', null, 100, 2);
		$this->add_filter('query_vars');
	}

	function _filter_query_vars($vars)
	{
		$vars[] = 'ruleid';
		return $vars;
	}

	function _action_parse_tax_query($query)
	{
		if ($query->is_main_query())
		{
			if ($qtax = $query->get('qtax'))
			{
				foreach ($query->tax_query->queried_terms as $tax => $qterm)
				{
					if ($qtax==$tax)
					{
						unset($query->tax_query->queried_terms[$tax]);

						$query->tax_query->queried_terms = array_merge([
							$tax => $qterm
						], $query->tax_query->queried_terms);


						break;
					}
				}
			}
		}
	}

	function _action_init()
	{
		global $wp;

		add_rewrite_tag('%age_limit_day%', '([\d]+)');
		add_rewrite_tag('%have_thumb%', '([\d]+)');
		add_rewrite_tag('%filter_tax%', '([\d]+)');
		add_rewrite_tag('%filter_meta%', '([\w\d\s]+)');
		add_rewrite_tag('%filter_meta_like%', '([\w\d\s]+)');
		add_rewrite_tag('%filter_date_from%', '([\d\.\-\s]+)');
		add_rewrite_tag('%filter_date_to%', '([\d\.\-\s]+)');
		add_rewrite_tag('%sm_request_term_id%', '([\d]+)');
		add_rewrite_tag('%qtax%', '([\d\w\-\_]+)');
		add_rewrite_tag('%sm_tax_post_format_none%', '([\d]+)');

		add_rewrite_tag('%sm-subpage%', '([\d\s]+)');
		add_rewrite_tag('%sm_term_id%', '([\d\s]+)');

		add_rewrite_tag('%date_filter%', '([\d\s\-]+)');

	}

	function _action_pre_get_terms($query)
	{

	}

	function _filter_terms_clauses($pieces, $taxonomies, $args)
	{

		if (!empty($args['orderby']) && $args['orderby']=='last_post')
		{
			$pieces['orderby'] = 'ORDER BY last_post_date';
			$pieces['order'] = 'DESC';
		}

		if (!empty($args['having_posts']))
		{
			$arg = $args['having_posts'];

			if (!empty($arg['min']))
			{
				$sql_where = 'sm_hp.post_type="'.$arg['type'].'" AND ';
			}

			$having = array();

			if (!empty($arg['min'])) $having[] = 'having_posts_count >= '.intval($arg['min']);

			if (!empty($arg['max'])) $having[] = 'having_posts_count <= '.intval($arg['max']);

			$pieces['having'] .= (!empty($pieces['having']) ? ' AND ' : '').join(' AND ', $having);
		}

		if (!empty($args['query_id']))
		{
			$pieces = apply_filters('terms_clauses_'.$args['query_id'], $pieces, $taxonomies, $args);
		}

		if (!empty($pieces['having']))
		{
			$pieces['where'] .= ' HAVING '.$pieces['having'];
		}

		return $pieces;
	}

	function _filter_get_terms_fields($selects, $args, $taxonomies)
	{
		if (!empty($args['orderby']) && $args['orderby']=='last_post')
		{
			if (!empty($args['last_post_type']) && post_type_exists($args['last_post_type']))
			{
				$sql_where = 'sm_lp.post_type="'.$args['last_post_type'].'" AND ';
			}

			$selects[] = '(SELECT MAX(post_date) FROM wp_posts as sm_lp LEFT JOIN wp_term_relationships as sm_tr ON sm_tr.object_id = sm_lp.ID WHERE '.$sql_where.' sm_tr.term_taxonomy_id=t.term_id) as last_post_date';
		}

		if (!empty($args['having_posts']))
		{
			$arg = $args['having_posts'];

			$join_where = array();

			$join_where[] = 'sm_tr.term_taxonomy_id=t.term_id';

			if (!empty($arg['type']) && post_type_exists($arg['type']))
			{
				$join_where[] = 'sm_hp.post_type="'.$arg['type'].'"';
			}

			if (!empty($arg['age_days']))
			{


				$join_where[] = 'date(sm_hp.post_date_gmt) >= "'.date('Y-m-d', strtotime('-'.intval($arg['age_days']).' days')).'"';
			}

			$selects[] = '(SELECT count(sm_hp.ID) FROM wp_posts AS sm_hp LEFT JOIN wp_term_relationships as sm_tr ON sm_tr.object_id = sm_hp.ID WHERE '.join(' AND ', $join_where).' ) as having_posts_count';
		}

		return $selects;
	}


	function _filter_request($vars)
	{

		if (isset($vars['sm_request_term_id']))
		{
			$sm_request_term_id = $vars['sm_request_term_id'];

			if ($term = get_term($sm_request_term_id))
			{
				$vars['tax_query'][] = array('taxonomy'=>$term->taxonomy, 'terms'=> [$term->term_id], 'field'=>'term_id');
			}
		}

		return $vars;
	}

	function _filter_the_posts($posts, $query)
	{

		if (!empty($query->query_vars['query_id']))
		{

		}

		if (!empty($query->query_vars['overrides']))
		{

			$limit = $query->query_vars['posts_per_page'];

			$overrides = $query->query_vars['overrides'];


			$posts_library = $overrides_ids = $overrides_by_pos = $overrides_valid = [];

			foreach ($posts as $post)
			{
				$posts_ids[] = $post->ID;
				$posts_by_id[$post->ID] = $post;
				$posts_library[$post->ID] = $post;
			}

			$posts_without_overrides = $posts_by_id;

			$overrides_ids = [];

			foreach ($overrides as $i=>$item)
			{
				if ($post_id = $item['post_id'])
				{
					if ($item['pos']>0)
					{
						$item['pos']--;
						$overrides[$i] = $item;
					}

					$post = get_post($post_id);

					if ($post && ((is_numeric($item['pos']) && $item['pos']<$limit) || ($item['pos']==='append')))
					{
						$overrides_ids[] = $post_id;

						$overrides_by_pos[$item['pos']][] = $item;

						if ($posts_without_overrides[$post_id])
						{
							unset($posts_without_overrides[$post_id]);
						}
					}
				}
			}


			if (!empty($overrides_ids))
			{
				ksort($overrides_by_pos);

				$to_load_ids = array_diff($overrides_ids, $posts_ids);


				if (!empty($to_load_ids))
				{
					if (count($to_load_ids)>$limit)
					{
						$to_load_ids = array_slice($to_load_ids, 0, $limit);
					}

					$loaded_posts = get_posts( array(
						'post__in' => $to_load_ids,
						'post_status' => 'publish',
						'nopaging' => true
					));


					foreach ($loaded_posts as $p) $posts_library[$p->ID] = $p;
				}

				$posts = array_values($posts_without_overrides);

				$total_posts = [];

				for ($pos=0; $pos<$limit; $pos++)
				{

					if (!empty($overrides_by_pos) && (isset($overrides_by_pos[$pos]) || empty($posts)))
					{
						$pos_posts = reset($overrides_by_pos);

						unset($overrides_by_pos[key($overrides_by_pos)]);

						foreach ($pos_posts as $item)
						{
							$total_posts[] = $posts_library[$item['post_id']];

							if (count($total_posts)==$limit) break;
						}
					}
					else
					{
						$total_posts[] = array_shift($posts);
					}

					if (count($total_posts)==$limit) break;
				}

				$posts = $total_posts;
			}

		}

		return $posts;
	}

	function _action_pre_get_posts(&$query)
	{

		if (!empty($query->query_vars['query_id']))
		{
			$query_id = $query->query_vars['query_id'];

			do_action_ref_array( 'sm/query/pre_get_posts', array( &$query, $query_id ) );

			do_action_ref_array( 'sm/query/pre_get_posts/'.$query_id, array( &$query ) );

		}

		if (!empty($query->query_vars['query_processors']))
		{
			foreach ($query->query_vars['query_processors'] as $processor)
			{
				do_action_ref_array( 'sm/query/posts/processor/'.$processor, array( &$query ) );
			}
		}


		if (!empty($query->query_vars['sm_tax_post_format_none']))
		{
			$query->query_vars['tax_query']['relation'] = 'AND';

			$query->query_vars['tax_query'][] = array(
				'taxonomy' => 'post_format',
				'NOT EXISTS'
			);
		}

		if (!empty($query->query_vars['have_thumb']))
		{
			$query->query_vars['meta_query'][] = array(
				'key' => '_thumbnail_id',
				'compare' => 'EXISTS'
			);
		}

		if (!empty($query->query_vars['age_days']))
		{
			$query->query_vars['date_query'][] = array(
				'column' => 'post_date_gmt',
				'after' => $query->query_vars['age_days'] . ' days ago',
			);
		}


		if ($term_id = $query->query_vars['sm_term_id'])
		{
			if ($term = get_term($term_id))
			{
				$query->query_vars['tax_query'][] = array(
					'taxonomy'=>$term->taxonomy,
					'terms'=> [$term->term_id],
					'field'=>'term_id',
					'operator'=> 'IN'
				);
			}
		}

		if (!empty($query->query_vars['date_filter']))
		{
			list($year, $month, $day) = explode('-', $query->query_vars['date_filter']);

			if ($year)
				$query->query_vars['year'] = $year;

			if ($month)
				$query->query_vars['monthnum'] = $month;

			if ($day)
				$query->query_vars['day'] = $day;
		}

		if ($filter_date_from = $query->query_vars['filter_date_from'])
		{
			list($after['day'],$after['month'],$after['year']) = explode('.', $filter_date_from);

			$after['day']   = ltrim($after['day'],'0');
			$after['month'] = ltrim($after['month'],'0');
			$after['year']  = ltrim($after['year'],'0');

			$query->query_vars['date_query']['after'] = $after;
			$query->query_vars['date_query']['inclusive'] = true;
			$query->query_vars['date_query']['column'] = 'post_date_gmt';
		}

		if ($filter_date_to = $query->query_vars['filter_date_to'])
		{
			list($before['day'],$before['month'],$before['year']) = explode('.', $filter_date_to);

			$before['day']   = ltrim($before['day'],'0');
			$before['month'] = ltrim($before['month'],'0');
			$before['year']  = ltrim($before['year'],'0');


			$query->query_vars['date_query']['before'] = $before;
			$query->query_vars['date_query']['inclusive'] = true;
			$query->query_vars['date_query']['column'] = 'post_date_gmt';
		}

		if ($filter_tax = $query->query_vars['filter_tax'])
		{
			foreach ($filter_tax as $tax=>$terms)
			{
				if ($terms)
				{
					$terms = (array)$terms;

					$terms_ids = [];

					foreach ($terms as $term)
					{
						$terms_ids[] = intval($term);
					}

					$query->query_vars['tax_query']['relation'] = 'AND';

					$query->query_vars['tax_query'][] = array(
						'taxonomy' => $tax,
						'field' => 'term_id',
						'terms'=> $terms_ids
					);
				}
			}
		}

		if (!empty($query->query_vars['filter_meta_like']))
		{
			$query->query_vars['meta_query']['relation'] = 'AND';

			foreach ($query->query_vars['filter_meta_like'] as $meta_key=>$meta_value)
			{
				if ($meta_value) $query->query_vars['meta_query'][] = array('compare' => 'LIKE', 'key' => $meta_key, 'value'=> $meta_value);
			}
		}

		if (!empty($query->query_vars['filter_meta']))
		{
			$query->query_vars['meta_query']['relation'] = 'AND';

			foreach ($query->query_vars['filter_meta_value'] as $meta_key=>$meta_value)
			{
				if ($meta_value)  $query->query_vars['meta_query'][] = array('key' => $meta_key, 'value'=> $meta_value);
			}
		}


		return $query;
	}


	function _filter_posts_pre_query($result, $query)
	{
		/*
		if (!$query->query_params['force_enable'] && $query->is_main_query() && sm_page_field('main_query_disable'))
		{
			$result = array();
		}
		*/


		return $result;
	}

	function is_public_query_var($name)
	{
		static $public_vars;

		global $wp;

		if (!$public_vars) $public_vars = apply_filters( 'query_vars', $wp->public_query_vars );

		return in_array($name, $public_vars);
	}


	function extract_public_query_vars($query)
	{
		$result = [];

		foreach ($query->query_vars as $name=>$value)
		{
			if ($this->is_public_query_var($name))
			{
				$result[$name] = $value;
			}
		}

		return $result;
	}


}



