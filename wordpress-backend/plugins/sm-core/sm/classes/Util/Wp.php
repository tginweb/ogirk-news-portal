<?php


namespace SM\Util;

use SM\Cache;

class Wp
{
    static function do_code( $content, $autop = false )
    {
        if ( $autop )
        {
            $content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
        }

        return do_shortcode( shortcode_unautop( $content ) );
    }

    static function shortcodes_fix_unclosed($content)
    {
        global $shortcode_tags;

        if ( false === strpos( $content, '[' ) ) {
            return $content;
        }

        if (empty($shortcode_tags) || !is_array($shortcode_tags))
            return $content;

        // Find all registered tag names in $content.

        preg_match_all( '@\[(\/)?([^\s\]]+)@', $content, $matches,  PREG_SET_ORDER);

        $sc_tags = array();

        foreach ($matches as $mt)
        {
            $sc_tags[$mt[2]] = 1;

            if ($mt[1])
            {
                unset($sc_tags[$mt[2]]);
            }
        }

        if (!empty($sc_tags))
        {
            $sc_tags = array_keys($sc_tags);
            $content = preg_replace("/(\[(".join('|',$sc_tags).")[^]]*)/",'$1 /',$content);
        }

        return $content;
    }

    static function get_plugin_wp_info($plugin_name=null)
    {
        static $plugins_wp_info = array();

        if (empty(self::$plugins_wp_info)) foreach ((array)get_option('active_plugins', array()) as $plugin) $plugins_wp_info[$plugin] = array('filepath' => $plugin, 'fullpath' => WP_CONTENT_DIR . '/plugins/' . dirname($plugin));

        return $plugin_name ? $plugins_wp_info[$plugin_name] : $plugins_wp_info;
    }

    static function themeurl()
    {
        if (is_child_theme()) return get_stylesheet_directory_uri();

        return get_template_directory_uri();
    }

    static function theme_child_name()
    {
        if (!is_child_theme()) return;

        $parts = explode('/', get_stylesheet_directory_uri());

        return array_pop($parts);
    }

    static function theme_root_name()
    {
        $parts = explode('/', get_template_directory_uri());

        return array_pop($parts);
    }


    static function params_to_atts_array($oparams=array(), $method='to_array')
    {
        foreach ((array)$oparams as $i=>$v)
        {
            if     (is_scalar($v))   $params[$i] = $v;
            elseif (is_array($v))    $params[$i] = self::params_to_atts_array($v);
            elseif (is_object($v))
            {
                if (is_subclass_of($v, 'sm_entity_object'))
                {
                    $params[$i] = $v->id();
                }
                else if (is_subclass_of($v, 'WP_Post'))
                {
                    $params[$i] = $v->ID;
                }
                else if (is_subclass_of($v, 'WP_Term'))
                {
                    $params[$i] = $v->term_id;
                }
                else if (is_subclass_of($v, 'WP_Comment'))
                {
                    $params[$i] = $v->comment_ID;
                }
                else if (is_subclass_of($v, 'WP_User'))
                {
                    $params[$i] = $v->ID;
                }
                else if (method_exists($v, $method))
                {
                    $params[$i] = $v->$method();
                }
                else $params[$i] = (array)$v;
            }
        }

        return $params;
    }

    static function abspath()
    {
        return strtr(ABSPATH, '\\', '/');
    }

    static function path_to_plugin_path($path)
    {
        $path = strtr($path, '\\', '/');

        return trim(strtr($path, array(self::abspath() => '')),'/');
    }

    static function path_to_url($path)
    {
        if (!$path) return '';

        $path = strtr($path, '\\', '/');

        return '/'.rtrim(strtr($path, array(self::abspath() => '')),'/');
    }

    static function url_to_path($url)
    {
        $url = wp_make_link_relative($url);
        return ABSPATH.ltrim($url, '/');
    }

    static function path_wp_relative($path)
    {
        $path = strtr($path, '\\', '/');

        return trim(strtr($path, array(self::abspath() => '')),'/');
    }

    static function dir_to_root_relative($dir)
    {
        $dir = strtr($dir, '\\', '/');

        return strtr($dir, array(self::abspath() => ''));
    }

    static function dir_to_abs_url($dir)
    {
        if (!$dir) return '';
        return '/'.strtr($dir, array(self::abspath() => ''));
    }

    static function url_relative_root($url)
    {
        return '/'.trim(strtr($url, array(WP_SITEURL => '', WP_HOME => '')),'/');
    }


    static function resolve_path($path, $context_path=null, $check_exists=false, $relative_to = null)
    {
        if (!$path) return;

        $path = strtr($path, '\\', '/');
        $context_path = strtr($context_path, '\\', '/');
        $relative_to = strtr($relative_to, '\\', '/');

        if ($path[0]=='/')  $result = ABSPATH.ltrim($path,'/');

        else if ($context_path)
        {
            $context_path = rtrim($context_path, '/');
            $result = ($context_path[0]=='/') ? $context_path.'/'.$path : ABSPATH.'/'.$context_path.'/'.$path;
        }
        if ($check_exists && !file_exists($result)) return;

        if ($relative_to)
        {
            $result = ltrim(str_replace($relative_to, '', $result),'/');
        }

        return $result;
    }

    static function resolve_url($file, $context_path=null, $check_exists=false)
    {

        /*
         *  /var/www/site/path
         *  wp-content/plugins/smart/assets/css
         *
         *  http://www.google.ru/js/tpl.js
         *  /wp-content/plugins/smart-tpl/assets/js/tpl.js
         *   view/tpl.js
         */

        if (preg_match('/^(http)/', $file))
        {
            return $file;
        }
        else if ($file[0]=='/')
        {
            $file_url = $file;
            $file_path = ABSPATH.ltrim($file,'/');
        }
        else if ($context_path)
        {
            $context_path = rtrim($context_path, '/');

            $file_url  = self::path_to_url($context_path).'/'.$file;
            $file_path = $context_path.'/'.$file;
        }

        if ($check_exists && !file_exists($file_path)) return;

        return $file_url;
    }

    static function add_action_multiple( $tags, $function_to_add, $priority = 10, $accepted_args = 1 )
    {
        foreach ((array)$tags as $tag)
        {
            add_action($tag, $function_to_add, $priority, $accepted_args);
        }
    }

    static function add_filter_multiple( $tags, $function_to_add, $priority = 10, $accepted_args = 1 )
    {
        foreach ((array)$tags as $tag)
        {
            add_filter($tag, $function_to_add, $priority, $accepted_args);
        }
    }

    static function plugin_require($variants)
    {
        foreach ((array)$variants as $variant)
        {
            $fpath = WP_CONTENT_DIR.'/plugins/'.$variant;

            if (file_exists($fpath))
            {
                wp_register_plugin_realpath($fpath);
                include_once($fpath);
                return true;
            }
        }

        // @todo proccess plugin requirements error
    }

    static function load_template_part($template_name, $part_name=null)
    {
        ob_start();
        get_template_part($template_name, $part_name);
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
    }

    static function shortcode_the_content($content, $autop = false)
    {
        if ( $autop )
        {
            // Possible to use !preg_match('('.WPBMap::getTagsRegexp().')', $content)
            $content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
        }
        return do_shortcode( shortcode_unautop( $content ) );
    }

    static function plugin_basename_from_slug( $slug )
    {
        if ( ! function_exists( 'get_plugins' ) )
        {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        foreach (  self::get_plugins() as $plugin_key=>$plugin_path )
        {
            if ( preg_match( '|^' . $slug . '/|', $plugin_path ) ) return $plugin_path;
        }
    }

    static function get_plugins()
    {
        return get_option( 'active_plugins', array() );
    }

    static function get_plugins_all()
    {
        static $plugins;

        if ((!$plugins && !($plugins = Cache::i()->get('plugins', 'system'))) || $_REQUEST['cache_reset'])
        {
            $plugins = get_plugins();

            Cache::i()->set('plugins', $plugins, 'system');
        }

        return $plugins;
    }

    static function wp_title($t_sep='')
    {
        global $wp_locale;

        $m        = get_query_var( 'm' );
        $year     = get_query_var( 'year' );
        $monthnum = get_query_var( 'monthnum' );
        $day      = get_query_var( 'day' );
        $search   = get_query_var( 's' );
        $title    = '';

        // If there is a post
        if ( is_single() || ( is_home() && ! is_front_page() ) || ( is_page() && ! is_front_page() ) ) {
            $title = single_post_title( '', false );
        }

        // If there's a post type archive
        if ( is_post_type_archive() ) {
            $post_type = get_query_var( 'post_type' );
            if ( is_array( $post_type ) ) {
                $post_type = reset( $post_type );
            }
            $post_type_object = get_post_type_object( $post_type );
            if ( ! $post_type_object->has_archive ) {
                $title = post_type_archive_title( '', false );
            }
        }

        // If there's a category or tag
        if ( is_category() || is_tag() ) {
            $title = single_term_title( '', false );
        }

        // If there's a taxonomy
        if ( is_tax() ) {
            $term = get_queried_object();
            if ( $term ) {
                $tax   = get_taxonomy( $term->taxonomy );
                $title = single_term_title( $tax->labels->name . $t_sep, false );
            }
        }

        // If there's an author
        if ( is_author() && ! is_post_type_archive() ) {
            $author = get_queried_object();
            if ( $author ) {
                $title = $author->display_name;
            }
        }

        // Post type archives with has_archive should override terms.
        if ( is_post_type_archive() && $post_type_object->has_archive ) {
            $title = post_type_archive_title( '', false );
        }

        // If there's a month
        if ( is_archive() && ! empty( $m ) ) {
            $my_year  = substr( $m, 0, 4 );
            $my_month = $wp_locale->get_month( substr( $m, 4, 2 ) );
            $my_day   = intval( substr( $m, 6, 2 ) );
            $title    = $my_year . ( $my_month ? $t_sep . $my_month : '' ) . ( $my_day ? $t_sep . $my_day : '' );
        }

        // If there's a year
        if ( is_archive() && ! empty( $year ) ) {
            $title = $year;
            if ( ! empty( $monthnum ) ) {
                $title .= $t_sep . $wp_locale->get_month( $monthnum );
            }
            if ( ! empty( $day ) ) {
                $title .= $t_sep . zeroise( $day, 2 );
            }
        }

        // If it's a search
        if ( is_search() ) {
            /* translators: 1: separator, 2: search phrase */
            $title = sprintf( __( 'Search Results %1$s %2$s' ), $t_sep, strip_tags( $search ) );
        }

        // If it's a 404 page
        if ( is_404() ) {
            $title = __( 'Page not found' );
        }

        return $title;
    }

    static function get_option($option, $default = false)
    {
        global $wpdb;
        static $cache = array();

        $cid = md5(serialize($option));

        if (isset($cache[$cid])) return $cache[$cid];

        if (is_array($option))
        {
            $value = array();
            $default = (array)$default;

            foreach ($option as $i=>$v)
            {
                if (is_numeric($i))
                {
                    $key = $v;
                    $def = isset($default[$key]) ? $default[$key] : null;
                }
                else
                {
                    $key = $i;
                    $def = $v;
                }

                $value[$key] = get_option($key, $def);
            }
        }
        else if (is_string($option))
        {
            if ($option[0]=='/')
            {
                $value = array();

                $regexp = '\''.trim($option, '/').'\'';

                $result = $wpdb->get_results('SELECT * FROM '.$wpdb->options.' WHERE option_name REGEXP '.$regexp);

                foreach ($result as $item)
                {
                    $value[$item->option_name] = maybe_unserialize($item->option_value);
                }
            }
            else
            {
                $value = get_option($option, $default);
            }
        }

        $cache[$cid] = $value;

        return $value;
    }


    static function locate_template($template_dir, $template_names, $load = false, $require_once = true )
    {
        $located = '';

        foreach ( (array) $template_names as $template_name )
        {
            if ( !$template_name )
                continue;

            if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
                $located = STYLESHEETPATH . '/' . $template_name;
                break;
            } elseif ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
                $located = TEMPLATEPATH . '/' . $template_name;
                break;
            } elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
                $located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
                break;
            } else if ( file_exists($template_dir . '/' . $template_name)) {
                $located = $template_dir . '/' . $template_name;
                break;
            }
        }

        if ( $load && '' != $located )
            load_template( $located, $require_once );

        return $located;
    }


    static function get_taxonomies($args = [], $output = 'names', $cache=true)
    {
        global $wp_taxonomies;

        static $cache = [];

        $cid = serialize(func_get_args());

        if (!isset($cache[$cid]))
        {
            $field = ('names' == $output) ? 'label' : false;

            $cache[$cid] = wp_filter_object_list($wp_taxonomies, $args, 'and', $field);
        }

        return $cache[$cid];
    }

    static function get_post_types($args = [], $output = 'names', $cache=true)
    {
        global $wp_post_types;

        static $cache = [];

        $cid = serialize(func_get_args());

        if (!isset($cache[$cid]))
        {
            $field = ('names' == $output) ? 'label' : false;

            $cache[$cid] = wp_filter_object_list($wp_post_types, $args, 'and', $field);
        }

        return $cache[$cid];
    }
}




