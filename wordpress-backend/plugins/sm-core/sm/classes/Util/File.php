<?php

namespace SM\Util;

class File
{
    static function glob_recursive($base, $pattern, $flags = 0)
    {
        if (substr($base, -1) !== DIRECTORY_SEPARATOR) {
            $base .= DIRECTORY_SEPARATOR;
        }

        $files = glob($base.$pattern, $flags);

        foreach (glob($base.'*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_MARK) as $dir)
        {
            $dirFiles = self::glob_recursive($dir, $pattern, $flags);

            if ($dirFiles !== false) {
                $files = array_merge($files, $dirFiles);
            }
        }

        return $files;
    }

    static function include_dir($folder, $recursive=false, $include_pattern='*.{php,inc}', $exclude_pattern=null)
    {

        foreach (self::glob_recursive($folder, $include_pattern, GLOB_BRACE) as $filename)
        {
            if ($exclude_pattern && preg_match($exclude_pattern, $filename)) continue;

            include_once $filename;
        }
    }

    static function include_first($files, $basedir)
    {
        foreach ($files as $file)
        {
            $path = $basedir.'/'.$file;

            if (file_exists($path.'.inc')) { include_once($path.'.inc'); return; }
            if (file_exists($path.'.php')) { include_once($path.'.php'); return; }
        }
    }

    static function find($dir, $pattern)
    {
        $dir = escapeshellcmd($dir);
        $files = glob("$dir/$pattern");
        foreach (glob("$dir/{.[^.]*,*}", GLOB_BRACE|GLOB_ONLYDIR) as $sub_dir)
        {
            $arr   = self::find($sub_dir, $pattern);  // resursive call
            $files = array_merge($files, $arr); // merge array with files from subdirectory
        }
        return $files;
    }

    static function scandir($path, $pattern = null, $depth = 0, $relative_path = '')
    {
        if (!is_dir($path)) return array();

        $relative_path = trailingslashit( $relative_path );

        if ( '/' == $relative_path ) $relative_path = '';

        $results = scandir( $path );

        $files = array();

        foreach ( $results as $result )
        {
            if ('.'==$result[0]) continue;

            if (is_dir($path.'/'.$result))
            {
                if ( ! $depth || 'CVS' == $result ) continue;
                $found = self::scandir( $path . '/' . $result, $pattern, $depth - 1 , $relative_path . $result );
                $files = array_merge_recursive( $files, $found );
            }
            elseif ( ! $pattern || preg_match( '/' . $pattern . '/', $result ) )
            {
                $files[$relative_path.$result] = $path . '/' . $result;
            }
        }

        return $files;
    }

    static function find_relative_path($frompath, $topath)
    {
        $from = explode( DIRECTORY_SEPARATOR, $frompath ); // Folders/File
        $to = explode( DIRECTORY_SEPARATOR, $topath ); // Folders/File
        $relpath = '';

        $i = 0;
        // Find how far the path is the same
        while ( isset($from[$i]) && isset($to[$i]) ) {
            if ( $from[$i] != $to[$i] ) break;
            $i++;
        }
        $j = count( $from ) - 1;
        // Add '..' until the path is the same
        while ( $i <= $j ) {
            if ( !empty($from[$j]) ) $relpath .= '..'.DIRECTORY_SEPARATOR;
            $j--;
        }
        // Go to folder from where it starts differing
        while ( isset($to[$i]) ) {
            if ( !empty($to[$i]) ) $relpath .= $to[$i].DIRECTORY_SEPARATOR;
            $i++;
        }

        // Strip last separator
        return substr($relpath, 0, -1);
    }

    static function parse_info($path, $format='css', $trigger_param='File variant')
    {
        $content = file_get_contents($path);

        $params = array();

        $params_text = '';

        if (in_array($format,array('css','php','inc')))
        {
            if (preg_match('|\/\*(.*?'.$trigger_param.'\:.*?)\*\/|smi', $content, $mt)) $params_text = trim($mt[1]);
        }

        if ($params_text)
        {
            foreach (explode("\n", $params_text) as $line)
            {
                if (strpos($line,':')!==false)
                {
                    @list($pname, $pvalue) = explode(':', $line);
                    $params[trim($pname)] = trim($pvalue);
                }
            }
        }
        return $params;
    }

    static function template_render($filepath, $vars=array())
    {
        if (!file_exists($filepath)) return;

        extract($vars, EXTR_SKIP);
        ob_start();
        include $filepath;
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    static function mkdir($dirName, $rights=0775)
    {
        $dirs = explode('/', $dirName);
        $dir='';

        foreach ($dirs as $part)
        {
            $dir.=$part.'/';
            if (!is_dir($dir) && strlen($dir)>0) mkdir($dir, $rights);
        }
    }

}

