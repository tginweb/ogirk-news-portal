<?php


namespace SM_Elementor\Module\Form;

use SM_Elementor;
use Elementor\Controls_Stack;
use ElementorPro\Plugin;
use Elementor\Controls_Manager;


class Module extends \SM_Elementor\Common\Plugin_Module
{
    var $current_form;
    var $current_form_fields;

    /* @return Module */
    static function i($info=[], $context=null) { return parent::i($info, $context); }

    static function info()
    {
        return [
            'classmap' => [
                'widget' => [
                    'SM_Elementor\Module\Form\Widget\Form' => array('init'=>true),
                ],
            ]
        ];

    }

    function init_events()
    {
        parent::init_events();

        $this->add_shortcode('sm_elementor_form_field');
    }


    function set_current_form($form, $form_fields=null){
        $this->current_form = $form;
        $this->current_form_fields = $form_fields;
    }

    function unset_current_form(){

        $this->current_form = null;
        $this->current_form_fields = null;
    }

    function _shortcode_sm_elementor_form_field($attrs){

        $is_editor = SM_Elementor\Plugin::i()->is_editor_mode();

        $result = '';

        if (!empty($attrs['id']))
        {
            $field_ids = (array)$attrs['id'];

            foreach ($field_ids as $field_id)
            {
                if (!empty($this->current_form_fields) && isset($this->current_form_fields[$field_id]))
                {
                    $result .= $this->current_form_fields[$field_id];
                }
                else if ($is_editor)
                {
                    $result .= $field_id;
                }
            }
        }

        return $result;
    }

}



