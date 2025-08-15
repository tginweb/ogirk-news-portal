<?php

namespace SM_Site_Content\Module\Card;

use SM\Common;

class Module extends Common\Module
{
    function init_events()
    {
        $this->add_filter('sm/context/info');

        $this->add_action('init');

        $this->add_filter('sm_elementor/query/types', null, 20);

        $this->add_filter('sm_elementor/query/type/cards/result', null, 20);

        $this->add_filter('sm_elementor/query_module/types');

        $this->add_filter('acf/settings/load_json');
    }

    function _filter_acf_settings_load_json( $paths ) {


        // remove original path (optional)
        unset($paths[0]);

        // append path
        $paths[] = __DIR__ . '/Acf/json';

        // return
        return $paths;

    }

    function _action_init()
    {
        $term = get_term_by('slug', 'card', 'sm-role');


        if (!$term)
        {
            wp_insert_term(
                'Карточки',
                'sm-role',
                array(
                    'description'=> '',
                    'slug' => 'card',
                )
            );
        }
    }

    function _filter_sm_elementor_query_types($types) {

        $types += [
            'cards' => ['label'=>'Карточки', 'class'=>'\SM_Site_Content\Module\Card\Query\Entity'],
        ];

        return $types;
    }


    function _filter_sm_elementor_query_type_cards_result($result) {

        $entities = [];


        if (is_single())
        {
            $items = get_field('sm_cards');

            foreach ($items as $index=>$item)
            {
                $item['id'] = get_the_ID().'-'.$index;

                $entities[] = (object)$item;
            }
        }

        $result += [
            'entities'      => $entities,
            'max_num_pages' => count($entities)
        ];

        return $result;
    }

    function _filter_sm_elementor_query_module_types($types)
    {
        $types += [
            'module_cards'        =>  ['label'=>'Module Cards',  'class'=> '\SM_Site_Content\Module\Card\Query\module_cards'],
        ];


        return $types;
    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([

        ]);
    }


}

