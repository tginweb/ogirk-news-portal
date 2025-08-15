<?php

namespace SM;

use SM\Entity\Obj;
use SM\Util;

class Entity extends Common\Component
{
    var $bundles_schema = null;
    var $additional_fields_schema = null;

    /**
     * @return Entity
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function __construct()
    {
        define( 'F_ID', 'F_ID' );
        define( 'F_SLUG', 'F_SLUG' );
        define( 'F_ARRAY', 'F_ARRAY' );
        define( 'F_OBJECT_WP', 'F_OBJECT_WP' );
        define( 'F_OBJECT_SMART', 'F_OBJECT_SMART' );
    }


    static function params_info()
    {
        return array(
            'test1' => array('type'=>'text', 'form'=>true, 'label'=>'Test 1', ),
        );
    }

    function get_entity_types()
    {
        return array(
            'post',
            'term',
            'user',
            'comment'
        );
    }

    function create_object($object)
    {
        if (is_object($object))
        {
            if ($object instanceof \WP_Post)
            {
                $entity = new Obj\Post($object);
            }
            else if ($object instanceof \WP_Term)
            {
                $entity = new Obj\Term($object);
            }
        }

        return $entity;
    }

    function init_events()
    {
        $this->add_action('init');
        $this->add_action('admin_init');
        $this->add_action('admin_bar_menu');
        $this->add_action('after_setup_theme',  null, 1000);
        $this->add_action('wp_insert_post_data', null, 10, 2);

        $this->add_filter('sm/compiler/vars/wp');

        $this->add_action(['wp_ajax_fetch_model_collection_entity', 'wp_ajax_nopriv_fetch_model_collection_entity'], '_action_wp_ajax_fetch_model_collection_entity');

        $this->init_additional_fields();
    }

    function _action_wp_ajax_fetch_model_collection_entity()
    {
        $entity_type = $_REQUEST['entity_type'];
        $entity_bundle = $_REQUEST['entity_bundle'];
        $model_class = $_REQUEST['model_class'];

        //fb($_REQUEST);

        return;

        if (apply_filters('sm/entity/access', $entity_type, $entity_bundle, 'fetch_'.$model_class) || 1)
        {
            return $this->controller($entity_type)->select(['entity_ids'=>$ids]);
        }
    }

    function init_additional_fields()
    {

    }

    function _filter_sm_compiler_vars_wp($vars)
    {
        $vars['entity'] = new sm_compiler_object($this->get_request_entity());

        $vars['entity_id'] = sm_request_object_id();

        return $vars;
    }

    function _action_wp_insert_post_data($data, $postarr)
    {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return $data;

        if ($this->controller('post', $data['post_type'])->get_bundle_arg('post_name_form_id'))
        {
            $data['post_name']  = $data['ID'];
        }

        return $data;
    }

    function _action_after_setup_theme()
    {
        $this->init_entity_bundles();
    }

    function _action_admin_init()
    {
        //$this->init_admin_columns();
    }

    function _action_init()
    {

    }

    function _action_admin_bar_menu()
    {

    }


    function init_entity_bundles()
    {
        foreach (apply_filters('sm/entity/bundles', array()) as $key=>$info)
        {
            list($entity_type, $entity_bundle) = explode(':', $key);

            $ctrl = $this->controller($entity_type, $entity_bundle);

            if ($ctrl && $info['register'])
            {
                $ctrl->register_wp_bundle($info);
            }
        }
    }


    function init_admin_columns()
    {
        $schema_info = array();

        foreach (apply_filters('sm/entity/columns', []) as $info)
        {
            $bundle = isset($info['entity_bundle']) ? $info['entity_bundle'] : 'all';

            $schema_info[$info['entity_type']][$bundle][] = $info;

            if ($info['entity_type']=='post')
            {
                if ($info['entity_bundle']=='attachment')
                {
                    add_action('manage_media_custom_column', array(sm($info['sm_invoke_class']), 'column_show'), 10, 2);
                }
                else
                {
                    add_action(
                        $info['entity_bundle'] =='all' ? 'manage_posts_custom_column' : 'manage_'.$info['entity_bundle'] .'_posts_custom_column',
                        array(sm($info['sm_invoke_class']), 'column_show'),
                        10,
                        2
                    );
                }
            }
        }

        if (!empty($schema_info['post']))
        {
            foreach ($schema_info['post'] as $entity_bundle => $columns)
            {
                switch ($entity_bundle)
                {
                    case 'all':        $filter = 'manage_posts_columns'; break;
                    case 'attachment': $filter = 'manage_media_columns'; break;
                    default:           $filter = 'manage_' . $entity_bundle . '_posts_columns'; break;
                }

                add_filter(
                    $filter,
                    function ($info) use ($columns)
                    {
                        foreach ($columns as $column) $info[$column['name']] = $column['title'];
                        return $info;
                    },
                    10
                );
            }
        }
    }

    function get_bundles_schema()
    {
        if (!isset($this->bundles_schema))
        {
            $this->bundles_schema = array();

            foreach ($this->get_entity_types() as $entity_type)
            {
                $type_ctrl = $this->controller($entity_type);

                $this->bundles_schema[$entity_type] = array();

                if ($type_ctrl->support_bundles)
                {
                    foreach ($type_ctrl->get_bundles_options() as $entity_bundle => $entity_bundle_title)
                    {
                        $this->bundles_schema[$entity_type][$entity_bundle] = $entity_bundle_title;
                    }
                }
            }
        }

        return $this->bundles_schema;
    }

    function bundle_info($entity_type, $entity_bundle, $key=null)
    {
        if ($ctrl = $this->controller($entity_type, $entity_bundle)) return $key ? $ctrl->get_bundle_arg($key) : $ctrl->register_args;
    }

    function controller_by_screen()
    {
        if (!($screen = get_current_screen())) return;

        $ctrl = null;

        if ($screen->base=='edit' && $screen->post_type)
        {
            $ctrl = $this->controller('post', $screen->post_type);
        }
        else if ($screen->base=='edit-tags' && $screen->taxonomy)
        {
            $ctrl = $this->controller('term', $screen->taxonomy);
        }

        return $ctrl;
    }

    /* @return Entity\Controller\Base */

    function controller($entity_type, $entity_bundle = null)
    {
        $cls = Util\Base::class_suggestion_find([
            'SM\Entity\Controller\\'.ucfirst($entity_type).'_'.strtr($entity_bundle, '-', '_'),
            'SM\Entity\Controller\\'.ucfirst($entity_type)
        ]);

        if ($cls)
        {
            $ctrl = $cls::i();

            $ctrl->entity_bundle = $entity_bundle;
        }

        return $ctrl;
    }


    /* @return Entity\Object\Base */

    function load_entity($entity_type, $entity, $entity_bundle='', $id_format=null)
    {

        if (empty($entity))
        {
            return null;
        }
        else if (is_scalar($entity))
        {
            $entity = trim($entity);

            if (Util\Base::is_slug($entity) || $id_format==F_SLUG)
            {
                $id_format = F_SLUG;

                if (!$entity_bundle) die('entity::load by Slug without Bundle');

                $entity_cache_id = $entity_type.'-'.$entity_bundle.'-'.$entity;
            }
            else
            {
                $id_format = F_ID;

                $entity_type_ctrl = $this->controller($entity_type);

                if ($entity_type_ctrl->support_bundles)
                {
                    $entity_bundle = $entity_type_ctrl->get_bundle($entity);
                }

                $entity_cache_id = $entity_type.'-'.$entity;
            }

        }
        else if (is_object($entity))
        {
            if (is_subclass_of($entity, 'sm_entity_object')) return $entity;

            $id_format = F_OBJECT_WP;

            $entity_type_ctrl = $this->controller($entity_type);

            $entity_cache_id = $entity_type.'-'.$entity->{$entity_type_ctrl->field_name_id};

            if (!$entity_bundle && $entity_type_ctrl->support_bundles)
            {
                $entity_bundle = $entity->{$entity_type_ctrl->field_bundle};
            }

        }

        if (!isset($this->sm_cache['entity'][$entity_cache_id]))
        {
            if ($ctrl = $this->controller($entity_type, $entity_bundle))
            {
                if (is_scalar($entity))
                {
                    $entity = $ctrl->entity_wp_load_by($id_format, $entity);
                }

                $this->sm_cache['entity'][$entity_cache_id] = $entity ? $ctrl->entity_build($entity) : false;
            }
        }

        return $this->sm_cache['entity'][$entity_cache_id];
    }


    function create($entity_type, $data, $entity_bundle='')
    {
        $data = (object)$data;

        if (!$entity_bundle)
        {
            $ctrl = $this->controller($entity_type);

            if ($ctrl->support_bundles)
            {
                $entity_bundle = $data->{$ctrl->field_bundle};
            }
        }

        if ($ctrl = $this->controller($entity_type, $entity_bundle))
        {
            return $ctrl->entity_create($data);
        }
    }

    function get_default_entity()
    {
        static $entity_default;

        if (!$entity_default) $entity_default = new sm_entity_object_default(array());

        return $entity_default;
    }


    function get_request_entity($entity_type=null, $entity_bundle=null)
    {
        if (sm_request_object_id())
        {
            if (!$entity_type)
            {
                $obj = sm_request_object();

                if (isset($obj->ID)) $entity_type='post';

                else if (isset($obj->term_id)) $entity_type='term';
            }

            return $entity_type ? $this->load_entity($entity_type, sm_request_object_id()) : null;
        }
    }

    function load_multiple($entity_type, $ids, $entity_bundle='')
    {
        if (!is_array($ids))
        {
            $ids = explode(',', $ids);
        }

        $res = array();

        foreach ($ids as $id)
        {
            if ($obj = $this->load_entity($entity_type, $id, $entity_bundle))
            {
                $res[$obj->id()] = $obj;
            }
        }

        return $res;
    }


    function format($entity_type, $entity_bundle, $id, $dest_format=F_OBJECT_SMART)
    {
        if (!$id) return;

        $source_format = $this->detect_format($id);

        if ($source_format == $dest_format) return $id;

        switch ($dest_format)
        {
            case F_ID:

                if ($source_format == F_ID)
                    return $id;
                else if ($obj = $this->load_entity($entity_type, $id, $entity_bundle, $source_format))
                    return $obj->id();
                break;

            case F_SLUG:

                if ($source_format == F_SLUG)
                    return $id;
                else if ($obj = $this->load_entity($entity_type, $id, $entity_bundle, $source_format))
                    return $obj->get_slug();
                break;

            case F_ARRAY:

                if ($source_format == F_ARRAY)
                    return $id;

                else if ($source_format == F_OBJECT_WP || $source_format == F_OBJECT_SMART)
                    return (array)$id;

                else if ($obj = $this->load_entity($entity_type, $id, $entity_bundle, $source_format))
                    return (array)$obj;

                break;

            case F_OBJECT_WP:

                if ($source_format == F_OBJECT_WP)
                    return $id;

                else
                    return $this->load_entity($entity_type, $id, $entity_bundle, $source_format);

                break;

            case F_OBJECT_SMART:
                return $this->load_entity($entity_type, $id, $entity_bundle, $source_format);

        }
    }

    function detect_format(&$id)
    {
        if (is_numeric($id))                $format = F_ID;

        else if (Util\Base::is_slug($id))     $format = F_SLUG;

        else if (is_array($id))             $format = F_ARRAY;

        else if (is_object($id))            $format = is_subclass_of($id, 'sm_entity_object') ? F_OBJECT_SMART : F_OBJECT_WP;

        return $format;
    }

    function get_post($id=null, $entity_bundle=null)
    {
        if (!$id)
        {
            $id = get_post();

            if ($entity_bundle && $id->post_type!=$entity_bundle) return;
        };

        return $id ? $this->load_entity('post', $id, $entity_bundle) : null;
    }

    function get_posts($args=null)
    {
        $result = array();
        $entities = get_posts($args);
        foreach ($entities as $entity) $result[$entity->ID] = $this->load_entity('post', $entity);
        return $result;
    }

    function get_term($id=null, $entity_bundle=null)
    {
        if (!$id && (!($id = $GLOBALS['wp_the_query']->get_queried_object()) || empty($id->term_id) || ($entity_bundle && $id->taxonomy!=$entity_bundle)))  return;

        return $id ? $this->load_entity('term', $id, $entity_bundle) : null;
    }

    function get_terms($args=null)
    {
        $result = array();
        $entities = get_terms($args);

        foreach ($entities as $entity) $result[$entity->term_id] = $this->load_entity('term', $entity);
        return $result;
    }

    function get_comments($args=null)
    {
        $result = array();

        $entities = get_comments($args);

        foreach ($entities as $entity) $result[$entity->comment_ID] = $this->load_entity('comment', $entity);

        return $result;
    }


}


