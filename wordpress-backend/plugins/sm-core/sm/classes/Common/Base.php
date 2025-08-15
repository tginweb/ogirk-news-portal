<?php

namespace SM\Common;

use SM\Classes;

class Base extends \stdClass
{
    var $sm_cache = [];
    var $sm_compiler_object = null;
    var $sm_load_context = [];


    static $instances = [];

    static function load()
    {
        $cls = get_called_class();

        return new $cls;
    }

    static function i($info=[], $context=null)
    {
        $cls = get_called_class();

        if (!isset(self::$instances[$cls]))
        {
            if ($context)
            {
                $info['init_context'] = $context;
            }

            if ($info) Classes::i()->update_class_info($cls, $info);

            $object = new $cls;

            self::$instances[$cls] = $object;

            if (method_exists($object, 'init'))
                $object->init();
        }

        return self::$instances[$cls];
    }

    static function info()
    {
        return [];
    }

    static function load_class_info($info)
    {
        $info += static::info();

        $cls = get_called_class();

        $info += array(
            'path'       => null,
            'namecode'   => strtr(rtrim($cls, '_'), '_', '-'),
        );

        if (empty($info['path']))
        {
            $reflector = new \ReflectionClass($cls);

            $info['path'] = dirname($reflector->getFileName());
        }


        $info['path']     = untrailingslashit(strtr($info['path'], '\\', '/'));
        $info['path_rel'] = '/'.strtr($info['path'], array(strtr(ABSPATH, '\\', '/') => ''));
        $info['url']      = rtrim(site_url(),'/').$info['path_rel'];

        $info += array(
            'assets_path'  => $info['path'].'/assets',
            'assets_url'   => $info['url'].'/assets',
            'libs_path'    => $info['path'].'/libs',
            'libs_url'     => $info['url'].'/libs',
        );

        if (!empty($info['classmap']))
        {
            Classes::i()->add_typed_classes_map($cls, $info['classmap']);
        }

        return $info;
    }

    static function class_info($param=null, $def=null)
    {
        $res = Classes::i()->info(get_called_class(), $param);

        return isset($res) ? $res : $def;
    }

    static function class_owner()
    {
        $res = static::class_info('class_owner');

        return $res ? sm($res) : sm('sm_core');
    }

    static function class_attr()
    {
        return strtr(rtrim(get_called_class(), '_'), ['_'=>'-']);
    }

    static function class_name()
    {
        return rtrim(get_called_class(), '_');
    }

    static function class_label()
    {
        return self::get_class_info('label', ucfirst(trim(strtr(get_called_class(), array('_'=>' ')))));
    }


    function get_class_info($name, $def=null)
    {
        return static::class_info($name, $def);
    }

    function get_class_owner()
    {
        return static::class_owner();
    }

    function get_path()
    {
        return static::class_info('path');
    }

    function get_path_rel()
    {
        return static::class_info('path_rel');
    }

    function get_url()
    {
        return static::class_info('url');
    }

}


