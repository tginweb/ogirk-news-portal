<?php


namespace SM_Elementor\Module\Porting;



class Module extends \SM_Elementor\Common\Plugin_Module
{

    function init_events()
    {
        $this->add_action('elementor/template-library/after_save_template', null, 10, 2);
        $this->add_filter('elementor/template-library/prepare_template_export', null, 10, 2);
    }

    function _action_elementor_template_library_after_save_template($template_id, $template_data)
    {
        if (!empty($template_data['sm_conditions']))
            update_post_meta( $template_id, '_elementor_conditions', $template_data['sm_conditions'] );
    }

    function _filter_elementor_template_library_prepare_template_export($template_data, $template_id)
    {
        $template_data['sm_conditions'] = get_post_meta( $template_id, '_elementor_conditions', true );

        return $template_data;
    }

}



