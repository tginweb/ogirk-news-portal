<?php

if (sm_class_can_load('sm_com_query_ad', __FILE__))
{
    class sm_com_query_ad extends sm_com_query_
    {
        static function params_info()
        {
            $params = parent::params_info();

            $params['query_type']['options'] = ['post'=> $params['query_type']['options']['post']];

            $params['query_post']['default']['post_type'] = 'sm-ad';

            $params['query_post']['params']['limit_taxonomies'] = ['sm-adzone'];
            $params['query_post']['params']['limit_post_types'] = ['sm-ad'];

            unset($params['query_term']);

            return $params;
        }

        static function params_defaults()
        {
            return [
                'layout_module_type'  => 'sm_com_module_ad',
            ] + parent::params_defaults();
        }

        function render_results()
        {
            $out = '';

            $results = $this->get_parent_com()->get_results();

            if (!empty($results))
            {
                foreach ($results as $entity)
                {
                    $out .= $this->get_module_output('module', $entity);
                }
            }

            return $out;
        }

    }
}

if (sm_class_can_load('sm_com_query_ad_', __FILE__))
{
    class sm_com_query_ad_ extends sm_com_query_ad {}
}

