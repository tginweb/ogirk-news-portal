<?php

namespace SM;

class Form extends Common\Component
{
    var $forms_builded = array();

    /**
     * @return Form
     */
    static function i($info=[], $context=null) { return parent::i($info, $context); }


    function init_events()
    {
        add_action( 'init', array( $this, 'action_init' ) );
    }

    function action_init()
    {

    }

    function forms()
    {
        //return sm()->invoke_all('forms');
    }

    function build($form_id, $params=array(), $context=array())
    {
        $args = $params['args'];

        $form_aid   = $this->get_build_aid($form_id, $args);
        $build_hash = $this->get_build_hash($form_id, $args);

        if ($form_aid == $this->sumbitted_form_aid())
        {
            if (!$this->get_build_hash_verify($form_id, $args, $this->sumbitted_form_build_hash()))
            {
                return false;
            }
            else
            {
                $process_form = true;
            }
        }

        if (!isset($this->forms_builded[$build_hash]))
        {
            $params['form_id'] = $form_id;
            $params['form_aid'] = $form_aid;
            $params['form_build_hash'] = $build_hash;

            //$form = sm()->components()->com_load($form_id, $params, $context);



            if ($form)
            {
                $form->build();

                if ($process_form)
                {
                    $form->process_form();
                }
            }

            $this->forms_builded[$build_hash] = $form;
        }

        return $this->forms_builded[$build_hash];
    }


    function get_build_aid($form_id, $args)
    {
        return $form_id.'_'.md5(serialize($args));
    }

    function get_build_hash($form_id, $args)
    {
        return wp_create_nonce($this->get_build_aid($form_id, $args));
    }

    function get_build_hash_verify($form_id, $args, $build_hash)
    {
        return wp_verify_nonce($build_hash, $this->get_build_aid($form_id, $args));
    }


    function is_sumbitted_sm_form()      { if ($_REQUEST && $_REQUEST['sm_form_id']) return true; }

    function sumbitted_form_id()         { if ($this->is_sumbitted_sm_form()) return $_REQUEST['sm_form_id']; }

    function sumbitted_form_aid()        { if ($this->is_sumbitted_sm_form()) return $_REQUEST['sm_form_aid']; }

    function sumbitted_form_build_hash() { if ($this->is_sumbitted_sm_form()) return $_REQUEST['sm_form_build_hash']; }

    function sumbitted_form_args()       { if ($this->is_sumbitted_sm_form()) return json_decode(base64_decode($_REQUEST['sm_form_args']), true); }

}


