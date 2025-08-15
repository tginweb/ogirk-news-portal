<?php


namespace SM\Query;

use SM\Common;

class Advert extends Common\Component
{

    var $query_advert_rules = null;

    /**
     * @return Advert
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {
        if (!is_admin())
        {
            $this->add_action('sm/query/pre_get_posts', null, -1000, 2);
        }
    }

    function _action_sm_query_pre_get_posts($query, $query_id)
    {
    	return;

        $this->get_query_advert_rules();

        if (isset($this->query_advert_rules[$query_id]))
        {

            $query->set('overrides', $this->query_advert_rules[$query_id]);
        }
    }

    function get_query_advert_rules()
    {
        if (!isset($this->query_advert_rules))
        {
            $this->query_advert_rules = [];

            $adverted_posts = get_posts([
                'numberposts' => -1,
                'meta_query' => array(
                    array(
                        'key'     => 'sm_query_advert',
                        'compare' => 'EXISTS',
                    ),
                )
            ]);

            $ts = time();

            foreach ($adverted_posts as $adverted_post)
            {
                $rules = get_field('sm_query_advert', $adverted_post);

                if (!empty($rules))
                {
                    foreach ($rules as $rule)
                    {

                        if (
                            $rule['query_ids'] &&
                            (!$rule['date_start'] || ($ts > $rule['date_start'])) &&
                            (!$rule['date_end'] || ($ts < $rule['date_end']))
                        )
                        {
                            $rule['post_id'] = $adverted_post->ID;
                            $rule['pos'] = $rule['position'];

                            foreach ($rule['query_ids'] as $query_id)
                            {
                                $this->query_advert_rules[$query_id][] = $rule;
                            }
                        }

                    }
                }
            }
        }

        return $this->query_advert_rules;
    }
}


