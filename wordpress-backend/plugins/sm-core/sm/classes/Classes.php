<?php

namespace SM;

class Classes
{
    static $instance;

    var $classes_map = array();
    var $classes_loaded = array();

    var $cache;
    var $autoloader_inited = false;

    /* @return Classes */

    static function i()
    {
        $cls = get_called_class();

        if (!isset(self::$instance))
        {
            $object = new $cls;

            self::$instance = $object;
        }

        return self::$instance;
    }


    function register_namespace($ns_name, $ns_path)
    {
        $ns_path = untrailingslashit($ns_path);

        spl_autoload_register(function ( $class ) use ($ns_name, $ns_path) {

            if ( 0 !== strpos( $class, $ns_name ) ) return;

            if ( ! class_exists( $class ) )
            {
                $filename = preg_replace([ '/^' . $ns_name . '\\\/', '/\\\/' ], [ '', DIRECTORY_SEPARATOR ], $class);


                $filename = $ns_path . '/' . $filename . '.php';

                if ( is_readable( $filename ) ) {
                    include( $filename );
                }
            }

        });
    }

    function get_class_clean_name($class)
    {
        return strtolower($class);
    }

    function find_classes_info($criteria, $check_exist=false, $reset=false)
    {
        static $cache=array();

        $cid = md5(serialize(func_get_args()));

        if (!isset($cache[$cid]) || $reset)
        {
            $cache[$cid] = array();

            foreach ($this->get_classes_map() as $class => $class_info)
            {
                //if ($class[strlen($class)-1]=='_') continue;

                $match = true;

                foreach ($criteria as $cname=>$cvalue)
                {
                    if (is_numeric($cname))
                    {
                        if (empty($class_info[$cvalue])) $match = false;
                    }
                    else
                    {
                        if ($cname=='class')
                        {
                            if ($class != $cvalue)
                            {
                                $match = false; break;
                            }
                        }
                        else if (empty($class_info[$cname]))
                        {
                            $match = false; break;
                        }
                        else if (is_array($cvalue))
                        {
                            if (!in_array($class_info[$cname], $cvalue))
                            {
                                $match = false; break;
                            }
                        }
                        else if ($class_info[$cname] != $cvalue)
                        {
                            $match = false; break;
                        }
                    }
                }

                if ($match)
                {
                    //$class = $remove_sm ? preg_replace('/^sm_/', '', $class) : $class;

                    if ($check_exist)
                    {
                        $class_info = $this->info($class);

                        if (!$class_info['exist']) continue;
                    }

                    $cache[$cid][$class] = $class_info;
                }
            }
        }

        return $cache[$cid];
    }

    function find_classes_options($criteria, $prepend=[])
    {
        $classes = $this->find_classes_info($criteria);

        $options = $prepend;

        foreach ($classes as $class=>$info)
        {
            $options[$class] = $info['label'].' ['.$class.']';
        }

        return $options;
    }

    function find_classes_instances($criteria, $init=null)
    {
        static $cache=array();

        $cid = serialize($criteria);

        if (!isset($cache[$cid]))
        {
            $cache[$cid] = array();

            foreach ($this->find_classes_info($criteria, true) as $class=>$class_info)
            {
                $cache[$cid][$class] = sm()->instance($class, array(), $init);
            }
        }

        return $cache[$cid];
    }

    function find_class_info($criteria, $class, $param=null)
    {
        $class_clean = $this->get_class_clean_name($class);

        $classes = $this->find_classes_info($criteria);

        return $param ? (isset($classes[$class_clean][$param]) ? $classes[$class_clean][$param] : null) : $classes[$class_clean];
    }

    function add_classes_path($dirs, $override=true)
    {
        $dirs = (array)$dirs;

        foreach ($this->find_class_files($dirs) as $filepath)
        {
            $classname = strtolower(strtr(pathinfo($filepath, PATHINFO_FILENAME), ['-'=>'_', '.'=>'\\']));

            if (empty($this->classes_map[$classname]['filepath']) || $override)
            {
                $this->classes_map[$classname]['class'] = $classname;
                $this->classes_map[$classname]['filepath'] = $filepath;
            }
        }
    }

    function add_typed_classes_map($class_owner, $schema)
    {
        foreach ($schema as $class_type=>$classes)
        {
            foreach ($classes as $class=>$info)
            {
                $info['class_owner'] = $class_owner;
                $info['class_type'] = $class_type;

                $this->classes_map[$class] = isset($this->classes_map[$class]) ? $this->classes_map[$class] : array();

                $this->classes_map[$class] = $info + $this->classes_map[$class];
            }
        }
    }

    function &get_classes_map()
    {
        return $this->classes_map;
    }

    function get_class_map($class, $param=null)
    {
        $class = $this->get_class_clean_name($class);

        $classes = &$this->get_classes_map();

        if (isset($classes[$class]))
        {
            return $param ? (isset($classes[$class][$param]) ? $classes[$class][$param] : null) : $classes[$class];
        }
    }

    function info($class, $param=null)
    {
        if (!isset($this->classes_loaded[$class]))
        {
            if (isset($this->classes_map[$class]))
            {
                $map_info = $this->classes_map[$class];
            }
            else
            {
                $map_info = [];
            }

            if (class_exists($class))
            {
                $map_info['exist'] = true;

                if (method_exists($class, 'load_class_info'))
                {
                    $map_info = $class::load_class_info($map_info);
                }
            }
            else
            {
                $map_info['exist'] = false;
            }

            $this->classes_map[$class] = $map_info;
            $this->classes_loaded[$class] = $map_info['exist'];
        }

        if ($param)
        {
            return isset($this->classes_map[$class][$param]) ? $this->classes_map[$class][$param] : null;
        }
        else
        {
            return $this->classes_map[$class];
        }
    }

    function update_class_info($class, $info=null)
    {
        if (!isset($this->classes_map[$class]))
        {
            $this->classes_map[$class] = [];
        }

        $this->classes_map[$class] = $info + $this->classes_map[$class];
    }


}

