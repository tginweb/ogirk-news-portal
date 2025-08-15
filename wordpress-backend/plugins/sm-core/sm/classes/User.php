<?php

namespace SM;

class User extends Common\Component
{
    var $role_default_caps = array();

    /* @return User */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function can($cap)
    {
        return is_super_admin() || current_user_can($cap);
    }

    function init_events()
    {
        parent::init_events();

        $this->add_action('init');
        //$this->add_filter('user_has_cap', null, 10, 4);
        //$this->add_filter('role_has_cap', null, 10, 3);
        //$this->add_filter('register_post_type_args', null, 1000, 2);
    }

    function _filter_register_post_type_args($args, $bundle)
    {
        if (!empty($args['access_restricted']))
        {
            $args['capability_type'] = $bundle;
            $args['map_meta_cap'] = true;
        }

        if (!empty($args['access_grant_roles']))
        {
            $caps = (array)get_post_type_capabilities((object)($args+(array('capabilities'=>array()))));

            foreach ($args['access_grant_roles'] as $role_id)
            {
                foreach ($caps as $cap_type=>$cap) $this->role_default_caps[$role_id][$cap] = true;
            }
        }

        return $args;
    }

    function _filter_user_has_cap($allcaps, $caps, $args,  $user)
    {
        foreach ($user->roles as $role_id)
        {
            if (!empty($this->role_default_caps[$role_id]))
            {
                foreach ($this->role_default_caps[$role_id] as $cap=>$grant)
                {
                    if (!isset($allcaps[$cap]))
                    {
                        $allcaps[$cap] = $grant;
                    }
                }
            }
        }

        return $allcaps;
    }

    function _filter_role_has_cap($role_capabilities, $cap, $role_id)
    {
        return $role_capabilities;
    }

    function _action_init()
    {
        //$this->register_caps();
    }

    function check_roles($roles, $logic='OR')
    {
        $roles = (array)$roles;

        if ($current_user = wp_get_current_user())
        {

            $user_roles = $current_user->roles;

            foreach ($roles as $role)
            {
                if ($logic=='AND')
                {
                    if (($role!='all') && !in_array($role, $user_roles)) return false;
                }
                else
                {
                    if (($role=='all') || in_array($role, $user_roles)) return true;
                }
            }

            return $logic=='OR' ? false : true;
        }
        else
        {
            return false;
        }
    }

    function register_caps()
    {
        $caps = apply_filters('sm/caps');

        $caps_groups = array(
            'common' => array('label'=>'Common')
        );

        foreach ($caps as $cap_key => &$cap_info)
        {
            if (!$cap_info['label']) $cap_info['label'] = $cap_key;


            if (!$cap_info['group'] && ($group_key = $cap_info['sm_invoke_class']))
            {
                $cap_info['group'] = $group_key;

                if (!$caps_groups[$group_key])
                {
                    $caps_groups[$group_key] = array('label'=>sm($group_key)->sm_class_info('title'));
                }
            }
            else
            {
                $group_key = 'common';
            }

            $caps_groups[$group_key]['caps'][] = $cap_key;

        }

        if (function_exists('members_register_cap_group'))
        {

            foreach ($caps_groups as $group_key=>$group_info)
            {
                $group_info += array(
                    'icon'       => 'dashicons-admin-generic',
                    'diff_added' => false
                );
                members_register_cap_group( $group_key, $group_info);
            }
        }
        else
        {

        }
    }

}




