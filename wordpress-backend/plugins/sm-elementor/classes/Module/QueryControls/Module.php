<?php


namespace SM_Elementor\Module\QueryControls;


class Module extends \SM_Elementor\Common\Plugin_Module
{
    static function info()
    {
        return [
            'classmap' => [
                'group_control' => [
                    'SM_Elementor\Module\QueryControls\Group_Control\Entity_Query' => array('control_id' => 'sm-entity-query', 'label' => 'Entity Query', 'init'=>true),
                ],
            ]
        ];
    }

    function init_events()
    {
        parent::init_events();


        $this->add_filter('sm_elementor/query/types');
    }

    function _filter_sm_elementor_query_types($types) {

        $types += [
            'posts'   => ['label'=>'Записи',  'class'=>'\SM\Entity\Obj\Post'],
            'terms'   => ['label'=>'Термины', 'class'=>'\SM\Entity\Obj\Term'],
        ];

        return $types;
    }
}



