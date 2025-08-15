<?php


namespace SM_Elementor\Common;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Template extends Customizable
{
    var $default_render_callback;

    function init($settings) {

        $this->settings = $this->prepare_settings($settings);

        if ($this->settings['customizer_items'])
        {
            $this->customizer_items = $this->settings['customizer_items'];
        }

        if (isset($this->settings['customizer_preffix']))
        {
            $this->customizer_element_class_preffix = $this->settings['customizer_preffix'];
        }

        return $this;
    }

    function set_default_render($cb)
    {
        $this->default_render_callback = $cb;

        return $this;
    }

    function set_renderers($renderers)
    {
        $this->renderers = $renderers;

        return $this;
    }

    function render()
    {
        if ($this->settings['template_source']=='settings')
        {
            $tpl = $this->settings['template_code'];
        }
        else if ($this->settings['template_source']=='registry')
        {

        }
        else
        {
            $tpl = $this->get_template();
        }

        if (isset($tpl))
        {
            return preg_replace_callback('/(\{\{([^\}]+?)(\s+[^\}]+?)?\}\})/', array($this, 'cb_replace_element'), $tpl);
        }
        else if (isset($this->default_render_callback))
        {
            return call_user_func($this->default_render_callback, $this);
        }
    }

    function get_template()
    {
        return null;
    }

}


