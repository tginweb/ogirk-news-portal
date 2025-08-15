<?php


namespace SM;

class Lib extends Common\Component
{
    var $libs_info;

    /* @return Lib */
    static function i($info=[], $context=null) { return parent::i($info, $context); }

    function libs_info()
    {
        if (!isset($this->libs_info))
        {
            $this->libs_info = apply_filters('sm/libs', array());
        }

        return $this->libs_info;
    }

    function get_lib_root_name($libname)
    {
        $lib_path = explode('.', $libname);

        return $lib_path[0];
    }

    function get_lib_info($libname)
    {
        $this->libs_info();

        if (!($info = $this->libs_info[$libname])) return;

        $info += array(
            'namespace' => $libname,
            'folder'    => $this->get_lib_root_name($libname)
        );

        if (!isset($info['path']) && !empty($info['sm_invoke_class']) && ($assets_obj = sm($info['sm_invoke_class'])))
        {
            $info['path'] = $assets_obj->sm_class_info('libs_path').'/'.$info['folder'];
        }

        if (!isset($info['assets_path']))
        {
            $info['assets_path'] = $info['path'];
        }

        return $info;
    }
}


