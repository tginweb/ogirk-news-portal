<?php

namespace SM_Elementor\Module\Query\Filter_Controller;

class Taxonomy extends Common\Base {

    static function info()
    {
        return [
            'default_widget' => 'select'
        ] + parent::info();
    }

    var $source_taxonomy = [];
    var $source_terms = [];
    var $source_parent = null;

    function __construct($parent, $settings, $value=null)
    {
        parent::__construct($parent, $settings, $value);

        if ($this->settings['source_type']=='taxonomy')
        {
            foreach ((array)$settings['source_taxonomy'] as $tax_name)
            {
                if (taxonomy_exists($tax_name))
                    $this->source_taxonomy[] = $tax_name;
            }

	        $this->source_parent = $settings['source_parent'];
        }
        else
        {
            foreach ((array)$settings['source_terms'] as $term)
            {
                if (($term = get_term($term)) && !is_wp_error($term))
                    $this->source_terms[$term->term_id] = $term;
            }
        }
    }

    function validate()
    {
        return !empty($this->source_taxonomy) || !empty($this->source_terms);
    }

    function get_source_items($name_query=null)
    {
        $args = [
			'orderby' => 'name'
        ];

        if ($this->settings['source_type']=='taxonomy')
        {
            $args['taxonomy'] = $this->source_taxonomy;

            if ($this->source_parent)
	            $args['parent'] = $this->source_parent;
        }
        else
        {
            $args['term_taxonomy_id'] = array_keys($this->source_terms);
        }


        if ($name_query)
            $args['name'] = $name_query;

	    $term_query = new \WP_Term_Query();

	    $terms = $term_query->query( $args );

        $items = [];

        if ($this->settings['required']!=='yes')
        {
            $item = [
                'label' => '',
                'value' => '',
            ];

            if (in_array('', $this->value))
            {
                $item['selected'] = true;
            }

            $items[] = $item;
        }

        foreach ($terms as $term)
        {
            $item = [
                'label' => $term->name,
                'value' => $term->term_id,
            ];

            if (in_array($term->term_id, $this->value))
            {
                $item['selected'] = true;
            }

            if ($this->settings['linkable']=='yes')
            {
                $item['link'] = get_term_link($term);
            }

            $items[] = $item;
        }

        return $items;
    }

    function get_source_items_ajax()
    {
        return $this->get_source_items($_REQUEST['name']);
    }

    function set_query_vars_value(&$query_vars)
    {
        if (!empty($this->value))
        {
            $by_taxonomy = [];

            foreach ((array)$this->value as $term_id)
            {
                if ($term = get_term($term_id))
                {
                    $by_taxonomy[$term->taxonomy][] = $term_id;
                }
            }

            foreach ($by_taxonomy as $taxonomy=>$term_ids)
            {
                $query_vars['tax_query'][] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'id',
                    'terms' => $term_ids,
                );
            }
        }
    }
}
