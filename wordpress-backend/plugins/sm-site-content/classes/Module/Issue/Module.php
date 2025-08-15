<?php

namespace SM_Site_Content\Module\Issue;

use SM\Common;

class Module extends Common\Module
{
    static function info()
    {
        return array(
            'title'        => 'Issue',
            'classes_path' => __DIR__.'/classes/*',
            'classes_map'  => []
        );
    }

    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');

        add_action('wp_insert_post', array($this,'on_wp_insert_post'), 10, 2);

        add_filter('sm/entity/apply/issue_print', array($this,'_filter_sm_entity_apply_issue_print'), 10, 3);
    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([
            'page_sm_issue_print'  => ['condition'  => function() { return is_singular('sm-issue-print');  }],
            'page_sm_issue_prints' => ['condition'  => function() { return is_post_type_archive('sm-issue-print'); }]
        ]);
    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
            'term:sm-issue' => array(
                'label'             => 'Издания',
                'labels'            => array('singular_name'=>'Изданиe'),
                'object_type'       => array('sm-issue-print'),
                'meta_box_cb'       => false,
                'hierarchical'      => true,
                'register'          => true,
            ),
            'post:sm-issue-print' => array(
                'label'             => 'Выпуски',
                'labels'            => array('singular_name'=>'Выпуск'),
                'register'          => true,
                'public'            => true,
                'hierarchical'      => false,
                'has_archive'       => true,
                'supports'          => array('title', 'editor', 'thumbnail','current'),
                'menu_position'     => 4
            ),
        ]);
    }

    function _filter_sm_entity_apply_issue_print($value, $entity, $params=array())
    {
        if ($issue_id = $entity->field('sm_issue_print'))
        {
            $value = sm()->entity()->load('post', $issue_id, 'sm-issue-print');
        }

        return $value;
    }

    function _filter_sm_entity_load_post($entity, $params=array())
    {
        if ($issue_id = $entity->field('sm_issue_print'))
        {
            $entity->issue = sm()->entity()->load('post', $issue_id, 'sm-issue-print');
        }

        return $entity;
    }


    function on_wp_insert_post($post_id, $post)
    {
        return;

        if ($post->post_type!='post') return;

        $ctrl = sm()->entity()->controller('post', 'sm-issue-print');

        if (!$ctrl->event_rules_validate($post, array('DISABLE_DOING_AUTOSAVE','DISABLE_AUTO_DRAFT'))) return;

        $entity = sm()->entity()->load_entity('post', $post);

        if ($issue_print = $ctrl->get_by_post($entity))
        {
            remove_action('wp_insert_post', array($this, 'on_wp_insert_post'));

            $entity->update_entity_db(array(
                'post_date'     => $issue_print->post_date,
                'post_date_gmt' => $issue_print->post_date_gmt,
                'post_status'   => $issue_print->post_status,
            ));

            add_action('wp_insert_post', array($this, 'on_wp_insert_post'), 10, 2);
        }
    }



    function column_show($column_name, $entity)
    {
        if ($column_name=='post_issue_print')
        {
            $issue = sm()->entity()->controller('post', 'sm-issue-print')->get_by_post($entity);

            if ($issue) print $issue->get_link('issue+date');
        }
    }
}



