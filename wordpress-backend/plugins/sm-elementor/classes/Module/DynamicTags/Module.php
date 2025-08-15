<?php


namespace SM_Elementor\Module\DynamicTags;



class Module extends \SM_Elementor\Common\Plugin_Module
{
    function init_events()
    {
        $this->add_action('elementor/dynamic_tags/register_tags');
    }

    function _action_elementor_dynamic_tags_register_tags($dynamic_tags)
    {

        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Acf_Repeater_Text' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Acf_Repeater_Image' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Acf_Post_Content' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Custom' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\CustomData' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Queried_Object_Id' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Post_Term_Id' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Post_Term_Title' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Archive_Title' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Archive_Url_Listing' );

        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Post_Tax_Post_Title' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Post_Tax_Post_Url' );

        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Term_Title');
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Term_Archive_Url');
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Term_Url');
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Post_Url');
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Post_Type_Url');
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Post_Hub_Post_Id' );
        $dynamic_tags->register_tag( 'SM_Elementor\Module\DynamicTags\Tag\Evaluate' );
    }

}



