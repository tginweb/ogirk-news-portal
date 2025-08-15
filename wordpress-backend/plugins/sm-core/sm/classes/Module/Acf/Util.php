<?php

namespace SM\Module\Acf;

use SM\Common;

class Util extends Common\Component
{
    static function add_local_field_groups($field_groups)
    {
        foreach ($field_groups as &$field_group)
        {
            foreach ($field_group['fields'] as &$field)
            {

                /*
                if ($field['type']=='text')
                {
                    $field += array(
                        'default_value' => '',
                        'placeholder'   => '',
                        'maxlength'		=> '',
                        'prepend'		=> '',
                        'append'		=> '',
                        'readonly'		=> 0,
                        'disabled'		=> 0,
                    );
                }
                else if ($field['type']=='text')
                {
                    $field += array(
                        'default_value' => '',
                        'placeholder'   => '',
                        'maxlength'		=> '',
                        'prepend'		=> '',
                        'append'		=> '',
                        'readonly'		=> 0,
                        'disabled'		=> 0,
                    );
                }
                */

            }

            acf_add_local_field_group($field_group);
        }
    }

    static function import_override($field_groups)
    {
        foreach( $field_groups as $field_group )
        {
            $field_group = (array)$field_group;

            // check if field group exists
            if ($found_group = _acf_get_field_group_by_key($field_group['key']))
            {
                wp_delete_post($found_group['ID']);
            }

            // remove fields
            $fields = acf_extract_var($field_group, 'fields');

            // format fields
            $fields = acf_prepare_fields_for_import($fields);

            // save field group
            $field_group = acf_update_field_group($field_group);

            // add to ref
            $ref[$field_group['key']] = $field_group['ID'];

            // add to order
            $order[$field_group['ID']] = 0;


            // add fields
            foreach ($fields as $field)
            {
                // add parent
                if (empty($field['parent']))
                {
                    $field['parent'] = $field_group['ID'];
                }
                elseif (isset($ref[$field['parent']]))
                {
                    $field['parent'] = $ref[$field['parent']];
                }


                // add field menu_order
                if (!isset($order[$field['parent']])) $order[$field['parent']] = 0;

                $field['menu_order'] = $order[$field['parent']];
                $order[$field['parent']]++;


                // save field
                $field = acf_update_field($field);


                // add to ref
                $ref[$field['key']] = $field['ID'];
            }
        }
    }

    static function export($groups)
    {
        $json = array();

        foreach ($groups as $key )
        {
            // load field group
            $field_group = acf_get_field_group($key);


            // validate field group
            if (empty($field_group)) continue;


            // load fields
            $field_group['fields'] = acf_get_fields($field_group);


            // prepare fields
            $field_group['fields'] = acf_prepare_fields_for_export($field_group['fields']);


            // extract field group ID
            $id = acf_extract_var($field_group, 'ID');


            // add to json array
            $json[] = $field_group;
        }

        return $json;
    }


    static function import_fields_from_json($json)
    {
        // decode json
        $json = json_decode($json, true);

        // validate json
        if( empty($json) ) {

            acf_add_admin_notice(__('Import file empty', 'acf'), 'error');
            return;

        }

        // if importing an auto-json, wrap field group in array
        if( isset($json['key']) ) {

            $json = array( $json );

        }


        // vars
        $added = array();
        $ignored = array();
        $ref = array();
        $order = array();

        foreach( $json as $field_group ) {

            // check if field group exists
            if( acf_get_field_group($field_group['key'], true) ) {

                // append to ignored
                $ignored[] = $field_group['title'];
                continue;

            }


            // remove fields
            $fields = acf_extract_var($field_group, 'fields');


            // format fields
            $fields = acf_prepare_fields_for_import( $fields );


            // save field group
            $field_group = acf_update_field_group( $field_group );


            // add to ref
            $ref[ $field_group['key'] ] = $field_group['ID'];


            // add to order
            $order[ $field_group['ID'] ] = 0;


            // add fields
            foreach( $fields as $field ) {

                // add parent
                if( empty($field['parent']) ) {

                    $field['parent'] = $field_group['ID'];

                } elseif( isset($ref[ $field['parent'] ]) ) {

                    $field['parent'] = $ref[ $field['parent'] ];

                }


                // add field menu_order
                if( !isset($order[ $field['parent'] ]) ) {

                    $order[ $field['parent'] ] = 0;

                }

                $field['menu_order'] = $order[ $field['parent'] ];
                $order[ $field['parent'] ]++;


                // save field
                $field = acf_update_field( $field );


                // add to ref
                $ref[ $field['key'] ] = $field['ID'];

            }

            // append to added
            $added[] = '<a href="' . admin_url("post.php?post={$field_group['ID']}&action=edit") . '" target="_blank">' . $field_group['title'] . '</a>';

        }

        // messages
        if( !empty($added) ) {

            $message = __('<b>Success</b>. Import tool added %s field groups: %s', 'acf');
            $message = sprintf( $message, count($added), implode(', ', $added) );

            acf_add_admin_notice( $message );
        }

        if( !empty($ignored) ) {

            $message = __('<b>Warning</b>. Import tool detected %s field groups already exist and have been ignored: %s', 'acf');
            $message = sprintf( $message, count($ignored), implode(', ', $ignored) );

            acf_add_admin_notice( $message, 'error' );

        }
    }
}

