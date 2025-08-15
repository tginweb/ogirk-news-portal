<?php


namespace SM_Elementor\Module\Document;

use Elementor\Core\Base\Document;
use SM\Common;
use SM\Context;
use Elementor\Controls_Stack;
use Elementor\Controls_Manager;
use ElementorPro\Plugin;

class Module extends \SM_Elementor\Common\Plugin_Module
{
    function init_events()
    {
        // $this->add_action('elementor/documents/register');
       // $this->add_action('elementor/element/before_section_end', null, 10, 3);
       // $this->add_action('elementor/editor/after_save_post', null, 10, 3 );
    }


    function _action_elementor_documents_register( $manager ) {

        $manager->register_document_type( 'post', '\SM_Elementor\Module\Document\DocumentTypes\Post' );
    }

    function _action_elementor_editor_after_save_post( $post_id, $editor_data ) {


        $global_widget_ids = [];

        $data = json_decode( stripslashes( $_REQUEST['actions'] ), true );

        $settings = $data['save_builder']['data']['settings'];


        $cats = $settings['ns_post_category'] ?: [];


        wp_set_post_terms( $post_id, $cats, 'category');
    }


    function _action_elementor_element_before_section_end(Controls_Stack $element, $section_id, $args)
    {

        if ($section_id=='document_settings')
        {
            /*
            $element->add_control(
                'sm_hide_header',
                [
                    'label' => __( 'Спрятать шапку'),
                    'type' => Controls_Manager::SWITCHER,
                    'export' => '__return_true',
                    'selectors' => [
                        '{{WRAPPER}} .page-title' => 'display: none',
                    ],
                ]
            );
            */

            $post = get_post();

            $element->add_control(
                'ns_post_category',
                [
                    'label' => __( 'Category'),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => \SM\Entity\Controller\Term::i()->get_select_options(['taxonomy'=>'category'], ['0'=>'']),
                    'default' => wp_get_post_categories($post->ID)
                ]
            );

            $element->add_control(
                'ns_post_images',
                [
                    'label' => __( 'Gallery'),
                    'type' => Controls_Manager::GALLERY,
                    'default' => [['id'=>282801]]
                ]
            );
        }


    }
}



