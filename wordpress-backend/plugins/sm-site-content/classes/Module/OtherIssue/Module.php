<?php

namespace SM_Site_Content\Module\OtherIssue;

use SM\Common;

class Module extends Common\Module
{
    static function info()
    {
        return array(
            'title' => 'Other Issue',
            'classes_path' => __DIR__ . '/classes/*',
            'classes_map' => []
        );
    }

    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');

        add_action('wp_insert_post', array($this, 'on_wp_insert_post'), 10, 2);

        add_filter('sm/entity/apply/issue_print', array($this, '_filter_sm_entity_apply_issue_print'), 10, 3);

        add_filter( 'parse_query', function ( &$query ) use (&$ctrl)
        {
            if (is_admin()  && 'edit.php' === $GLOBALS['pagenow'])
            {
                /*
                $query->query_vars['tax_query'][] = array(
                    'taxonomy' => 'sm-other-issue',
                    'field' => 'id',
                    'terms' => [7857]
                );
                */
            }
        });
    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([
                'page_sm_other_issue_print' => ['condition' => function () {
                    return is_singular('sm-other-issue-print');
                }],
                'page_sm_other_issue_prints' => ['condition' => function () {
                    return is_post_type_archive('sm-other-issue-print');
                }]
            ]);
    }

    function _filter_sm_entity_bundles($bundles)
    {
        $postSlug = 'sm-other-issue-print';
        $postSlugPlural = 'sm-other-issue-prints';

        return $bundles + $this->sm_class_set([
                'term:sm-other-issue' => array(
                    'label' => 'Областные издания',
                    'labels' => array('singular_name' => 'Областное издание'),
                    'object_type' => array('sm-other-issue-print'),
                    'meta_box_cb' => false,
                    'hierarchical' => true,
                    'register' => true,
                ),
                'post:sm-other-issue-print' => array(
                    'label' => 'Выпуски районок',
                    'labels' => array('singular_name' => 'Выпуск районки'),
                    'register' => true,
                    'public' => true,
                    'hierarchical' => false,
                    'has_archive' => true,
                    'supports' => array('title', 'editor', 'thumbnail', 'current'),
                    'menu_position' => 4,
                    'map_meta_cap' => true,
                    'capability_type' => $postSlug,
                    'capabilities' => [
                        'create_posts' => 'create_' . $postSlugPlural,
                        'delete_others_posts' => 'delete_others_' . $postSlugPlural,
                        'delete_posts' => 'delete_' . $postSlugPlural,
                        'delete_private_posts' => 'delete_private_' . $postSlugPlural,
                        'delete_published_posts' => 'delete_published_' . $postSlugPlural,
                        'edit_posts' => 'edit_' . $postSlugPlural,
                        'edit_others_posts' => 'edit_others_' . $postSlugPlural,
                        'edit_private_posts' => 'edit_private_' . $postSlugPlural,
                        'edit_published_posts' => 'edit_published_' . $postSlugPlural,
                        'publish_posts' => 'publish_' . $postSlugPlural,
                        'read_private_posts' => 'read_private_' . $postSlugPlural,
                        'read' => 'read',
                    ]
                ),
            ]);
    }

    function _filter_sm_entity_apply_issue_print($value, $entity, $params = array())
    {
        if ($issue_id = $entity->field('sm_issue_print')) {
            $value = sm()->entity()->load('post', $issue_id, 'sm-issue-print');
        }

        return $value;
    }

    function _filter_sm_entity_load_post($entity, $params = array())
    {
        if ($issue_id = $entity->field('sm_issue_print')) {
            $entity->issue = sm()->entity()->load('post', $issue_id, 'sm-issue-print');
        }

        return $entity;
    }


    function on_wp_insert_post($post_id, $post)
    {
        return;

        if ($post->post_type != 'post') return;

        $ctrl = sm()->entity()->controller('post', 'sm-issue-print');

        if (!$ctrl->event_rules_validate($post, array('DISABLE_DOING_AUTOSAVE', 'DISABLE_AUTO_DRAFT'))) return;

        $entity = sm()->entity()->load_entity('post', $post);

        if ($issue_print = $ctrl->get_by_post($entity)) {
            remove_action('wp_insert_post', array($this, 'on_wp_insert_post'));

            $entity->update_entity_db(array(
                'post_date' => $issue_print->post_date,
                'post_date_gmt' => $issue_print->post_date_gmt,
                'post_status' => $issue_print->post_status,
            ));

            add_action('wp_insert_post', array($this, 'on_wp_insert_post'), 10, 2);
        }
    }


    function column_show($column_name, $entity)
    {
        if ($column_name == 'post_other_issue_print') {
            $issue = sm()->entity()->controller('post', 'sm-other-issue-print')->get_by_post($entity);

            if ($issue) print $issue->get_link('issue+date');
        }
    }
}



