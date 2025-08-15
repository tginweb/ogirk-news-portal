<?php

namespace SM;

use SM\Assets;
use SM\Util;
use SM\Cache;


class Plugin extends Common\Module
{
    /* @return Plugin */
    static function i($info=[], $context=null) { return parent::i($info, $context); }

    static function info()
    {
        return array(
            'label' => 'Smart Core',
            'modules' => [
                'acf' => ['label'=>'ACF', 'pluggable'=>true, 'class'=>'SM\Module\Acf\Module'],
                'wc'  => ['label'=>'WC', 'pluggable'=>true, 'class'=>'SM\Module\Wc\Module'],
            ]
        );
    }

    static function params_info()
    {
        return array(
            'sm_extensions_active' => array(
                'type'     => 'checkboxes',
                'form'     => true,
                'label'    => 'Extensions',
             //   'options'  => sm()->classes()->find_classes_options(array('class_type'=>'extension', 'class_owner'=>['sm_core','sm_site_company','sm_site_content'])),
                'page'     => 'extensions'
            ),
        );
    }

    function init_related()
    {
	    Cache::i();
        Assets::i();
        Context::i();
        Entity::i();
        Image::i();
        Menu::i();
        Query::i();
        Query\Advert::i();
        User::i();

        //Admin_Page\Options::i();

        parent::init_related();
    }

    function assets()
    {
        $path = $this->get_path_rel();

        return [
            'sm_core.common' => [
                ['type'=>'js',  'data'=> $path.'/assets/js/core.min.js', 'deps'=>['wp-backbone']],
                $path.'/assets/css/core.css',
            ],
            'sm_core.backend' => [
                ['data'=> $path.'/assets/css/backend.css', 'deps'=>['wp-jquery-ui-dialog']],
                ['data'=> $path.'/assets/js/backend.js', 'deps'=>['jquery-ui-dialog','jquery-ui-datepicker']],
            ],
            'sm_core.frontend' => [
                $path.'/assets/css/frontend.css',
                $path.'/assets/js/frontend.js',
            ],

            'matchHeight' => [
                $path.'/assets/lib/matchHeight/jquery.matchHeight.js',
            ],
        ];
    }


    function enqueue_assets()
    {
        Assets::i()->wp_enqueue('sm_core.common');

        if (is_admin())
        {
            Assets::i()->wp_enqueue('sm_core.backend');
        }
        else
        {
            Assets::i()->wp_enqueue('sm_core.frontend');
        }
    }

    function init_events()
    {
        parent::init_events();

        $this->add_filter('sm/compiler/vars');
        $this->add_filter('sm/client/settings');
        $this->add_filter('sm/asset/register/css', null, 10, 2);

        $this->add_filter('admin_body_class');

        $this->add_action('init');
        $this->add_action('template_redirect');

        //$this->add_action(['deactivated_plugin','activate_plugin', 'after_switch_theme'],  array(sm(), 'rebuild_queue'));

        $this->add_filter('cron_schedules');

        if (is_admin())
        {
            $this->add_action('admin_head', '_action_head_common');
        }
        else
        {
            $this->add_action('wp_head', '_action_head_common');
        }

        $this->add_filter('register_taxonomy_args', null, 10, 3);
        $this->add_filter('register_post_type_args', null, 10, 2);

        $this->add_action('after_setup_theme', null, 100);

    }


    function _filter_register_taxonomy_args($args, $name, $object_type)
    {
        $data = sm_apply_filters_cached('sm/rewrite_slugs', []);

        if (isset($data['taxonomy'][$name]))
            $args['rewrite']['slug'] = $data['taxonomy'][$name];

        return $args;
    }

    function _filter_register_post_type_args($args, $name)
    {
        $data = sm_apply_filters_cached('sm/rewrite_slugs', []);

        if (isset($data['post'][$name]))
            $args['rewrite']['slug'] = $data['post'][$name];

        return $args;
    }

    function init()
    {
        //sm()->plugins();

        parent::init();
    }

    function _filter_sm_compiler_vars($vars)
    {
        $vars += array(
            'theme_uri'       => Util\Wp::url_relative_root(is_child_theme() ? get_stylesheet_directory_uri() : get_template_directory_uri()),
            'theme_uri_abs'   => is_child_theme() ? get_stylesheet_directory_uri() : get_template_directory_uri(),
            'theme_child_uri' => Util\Wp::url_relative_root(get_stylesheet_directory_uri()),
            'theme_root_uri'  => Util\Wp::url_relative_root(get_template_directory_uri())
        );

        return $vars;
    }

    function _filter_admin_body_class( $classes )
    {
        if (!empty($_REQUEST['sm-popup-admin']))
        {
            $classes .= ' sm-popup-admin-page ';
        }

        return $classes;
    }

    function _filter_sm_asset_register_css($row)
    {
        if (preg_match('/\.less$/', $row['data']))
        {
            if (!sm('sm_preprocess') || !sm('sm_preprocess')->param('sm_preprocess_less'))
            {
                $row['data'] = preg_replace('/\.less$/', '.css', $row['data']);
            }
        }

        return $row;
    }

    function _action_init()
    {
        global $wp;

        $wp->add_query_var('sm-action');

        add_rewrite_rule('^sm-action/(.+?)/?$','index.php?sm-action=$matches[1]','top');

        if (preg_match('@^/action/([\w\_]+)@', $_SERVER["REQUEST_URI"], $mt)) do_action('sm/action/'.$mt[1]);

        add_rewrite_tag('%listing%', '([\d]+)');
    }

    function _action_template_redirect()
    {
        if ($sm_action = get_query_var('sm-action')) do_action('sm/action/'.$sm_action);


        if (!empty($_REQUEST['sm-service']))
        {
            $service_name = $_REQUEST['sm-service'];

            do_action('sm/service/'.$service_name);
        }
    }


    function _action_head_common()
    {
        $settings = array(
            'class_info'=>array()
        );

        $settings = apply_filters('sm/client/settings', $settings);

        ?>
        <script>
            jQuery(document).ready(function () {
                smart.settings = jQuery.extend(smart.settings || {}, <?php print json_encode($settings, JSON_PRETTY_PRINT); ?>);
            });
        </script>
        <?
    }

    function _filter_sm_client_settings($settings)
    {
        $settings['ajaxurl']   = admin_url('admin-ajax.php');
        $settings['is_admin']  = is_admin();

        return $settings;
    }

    function _filter_cron_schedules($schedules)
    {
        $schedules += array(
            'sm60s'       => array( 'interval' => 60, 'display' => '1 '.__('minute') ),
            'sm120s'      => array( 'interval' => 120, 'display' => '2 '.__('minutes') ),
            'sm180s'      => array( 'interval' => 180, 'display' => '3 '.__('minutes') ),
            'sm240s'      => array( 'interval' => 240, 'display' => '4 '.__('minutes') ),
            'sm300s'      => array( 'interval' => 300, 'display' => '5 '.__('minutes') ),
            'sm600s'      => array( 'interval' => 600, 'display' => '10 '.__('minutes') ),
            'sm900s'      => array( 'interval' => 900, 'display' => '15 '.__('minutes') ),
            'sm1800s'     => array( 'interval' => 1800, 'display' => '30 '.__('minutes') ),
        );

        return $schedules;
    }

    function _action_after_setup_theme()
    {
        foreach (apply_filters('sm/image/sizes', []) as $key => $info)
        {
            add_image_size($key, $info[0], $info[1], $info[2]);
        }

    }


    var $admin_ui_core;
    var $admin_ui_builder;

    function get_admin_ui_core() {

        /**
         * Fires before loads the plugin's core.
         *
         * @since 1.0.0
         */
        do_action( 'jet-menu/core_before' );

        global $chery_core_version;

        if ( null !== $this->admin_ui_core ) {
            return $this->admin_ui_core;
        }

        if ( 0 < sizeof( $chery_core_version ) ) {
            $core_paths = array_values( $chery_core_version );
            require_once( $core_paths[0] );
        } else {
            die( 'Class Cherry_Core not found' );
        }


        $this->admin_ui_core = new \Cherry_Core( array(
            'base_dir' => $this->get_path().'/vendor/cherry-framework',
            'base_url' => $this->get_url().'/vendor/cherry-framework',
            'modules'  => array(
                'cherry-js-core' => array(
                    'autoload' => true,
                ),
                'cherry-ui-elements' => array(
                    'autoload' => false,
                ),
                'cherry-handler' => array(
                    'autoload' => false,
                ),
                'cherry-interface-builder' => array(
                    'autoload' => false,
                ),
                'cherry-utility' => array(
                    'autoload' => true,
                    'args'     => array(
                        'meta_key' => array(
                            'term_thumb' => 'cherry_terms_thumbnails'
                        ),
                    )
                ),
                'cherry-widget-factory' => array(
                    'autoload' => true,
                ),
                'cherry-term-meta' => array(
                    'autoload' => false,
                ),
                'cherry-post-meta' => array(
                    'autoload' => false,
                ),
                'cherry-dynamic-css' => array(
                    'autoload' => false,
                ),
                'cherry-customizer' => array(
                    'autoload' => false,
                ),
                'cherry-google-fonts-loader' => array(
                    'autoload' => false,
                ),
                'cherry5-insert-shortcode' => array(
                    'autoload' => false,
                ),
                'cherry5-assets-loader' => array(
                    'autoload' => false,
                ),
            ),
        ) );

        return $this->admin_ui_core;
    }




}



add_action('plugins_loaded', function() {

    function yoast_breadcrumb( $before = '', $after = '', $display = true ) {

        $cls = apply_filters('WPSEO_Breadcrumbs_class', 'WPSEO_Breadcrumbs');

        $breadcrumbs_enabled = current_theme_supports( 'yoast-seo-breadcrumbs' );

        if ( ! $breadcrumbs_enabled ) {
            $breadcrumbs_enabled = WPSEO_Options::get( 'breadcrumbs-enable', false );
        }

        if ( $breadcrumbs_enabled ) {
            return $cls::breadcrumb( $before, $after, $display );
        }
    }

}, -100);


