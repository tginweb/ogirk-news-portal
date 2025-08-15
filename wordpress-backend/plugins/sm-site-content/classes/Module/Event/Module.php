<?php


namespace SM_Site_Content\Module\Event;

use SM\Common;

class Module extends Common\Module
{
    function table_schema()
    {
        $schema['sm_event_occurrence'] = array(
            'fields' =>  "
                     id                  INT(9)       NOT NULL AUTO_INCREMENT,
                     event_id            INT(11)      NOT NULL default 0,
                     place_id            INT(11)      NOT NULL default 0,                     
                     day_date            INT(11)      NOT NULL default 0,
                     day_first_showtime  INT(11)      NOT NULL default 0,
                     day_last_showtime   INT(11)      NOT NULL default 0,                     
                     showtimes           LONGTEXT     NOT NULL
                     ",
            'indexes' => "
                     PRIMARY KEY (id),
                     INDEX day_date_last_showtime(day_date, day_last_showtime),
                     INDEX place_id(place_id)
                     "
        );

        return $schema;
    }

    function init_events()
    {
        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');
        $this->add_action('admin_menu');
        $this->add_action('admin_head');
        $this->add_action('add_meta_boxes', '_action_metaboxes_sm_event_occurrence');

        $this->add_action('save_post_sm-event', null, 10, 2);

        $this->add_filter('posts_join', null, 10, 2);
    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([
            'page_sm_event'        => ['condition'  => function() { return is_singular('sm-event'); }],
            'page_sm_events'       => ['condition'  => function() { return is_post_type_archive('sm-event'); }],
            'page_sm_event_place'  => ['condition'  => function() { return is_singular('sm-event-place'); }],
            'page_sm_event_places' => ['condition'  => function() { return is_post_type_archive('sm-event-places'); }]
        ]);
    }


    function _filter_sm_entity_bundles($bundles)
    {

        return $bundles + $this->sm_class_set([
            'post:sm-event' => array(
                'label'             => 'События',
                'labels'            => array('singular_name'=>'Событие'),
                'register'          => true,
                'public'            => true,
                'hierarchical'      => false,
                'supports'          => array('title','editor','thumbnail','current'),
                'menu_position'     => 4
            ),
            'post:sm-event-place' => array(
                'label'             => 'Места событий',
                'labels'            => array('singular_name'=>'Место событий'),
                'show_in_menu'      => false,
                'register'          => true,
                'public'            => true,
                'hierarchical'      => false,
                'supports'          => array('title','editor','thumbnail','current'),
                'menu_position'     => 4
            ),
        ]);
    }

    function _action_admin_menu()
    {
        $td = $this->get_text_domain();

        $this->add_submenu_page('edit.php?post_type=sm-event', __('Места событий', $td), __('Места событий', $td), 'manage_options', 'edit.php?post_type='.rawurlencode('sm-event-place'));

    }

    function _action_admin_head($bundles)
    {
        global $parent_file, $submenu_file, $post_type;

        if ( $post_type === 'sm-event-place' )
        {
            $parent_file = 'edit.php?post_type=sm-event';

            $submenu_file = 'edit.php?post_type=sm-event-place';
        }
    }

    function _action_metaboxes_sm_event_occurrence()
    {
        add_meta_box('sm-event-post-occurrence', __('Event occurrence'), array($this, 'metabox_event_post_occurrence'), 'sm-event', 'normal', 'default');
    }



    function metabox_event_post_occurrence($post)
    {
        $form = $values = [];

        $form = $this->form_event_post_occurrence();

        $values['sm_event_occurrence'] = $this->load_event_post_occurrence($post->ID, true);

        print sm_renderer::render_form_inner($form, $values);
    }


    function _action_save_post_sm_event($post_id, $post)
    {
        if (sm()->entity()->controller('post', 'sm-event')->check_save_entity_form($post))
        {
            if (isset($_POST['sm_event_occurrence']))
            {
                sm_renderer::process_form($this->form_event_post_occurrence(), $values, $_POST);

                $this->save_event_occurrence($post_id, stripslashes_deep($values['sm_event_occurrence']));
            }
        }

    }

    function form_event_post_occurrence()
    {
        return array(
            'sm_event_occurrence' => array(
                '#type'     => 'multiple',
                '#multiple_style' => 'table',
                '#multiple' => [
                    'day_date' => [
                        '#type' => 'date',
                        '#label' => 'Date',
                        '#value_format' => 'timestamp',
                        '#date_format' => 'd.m.Y',
                        '#picker_format' => 'dd.mm.yy',
                        '#width' => '20%'
                    ],
                    'place_id' => [
                        '#type' => 'select_entity',
                        '#label' => 'Place',
                        '#query' => ['type'=>'post', 'bundle'=>'sm-event-place']
                    ],
                    'showtimes' => [
                        '#type' => 'textarea',
                        '#label' => 'Сеансы',
                        '#rows' => 6
                    ],
                ]
            )
        );
    }

    function load_event_post_occurrence($post_id, $raw=false)
    {
        global $wpdb;

        $table = $this->get_table_name('sm_event_occurrence');

        $items = $wpdb->get_results("SELECT * FROM `".$table."` WHERE event_id=".$post_id, ARRAY_A);

        if (!$raw)
        {
            foreach ($items as &$item)
            {
                if (!empty($item['showtimes']))
                {
                    $showtimes = [];

                    foreach (preg_split('/\n/', $item['showtimes']) as $showtimes_line)
                    {
                        $showtimes[] = shortcode_parse_atts($showtimes_line);
                    }

                    $item['showtimes'] = $showtimes;
                }
            }
        }

        return $items;
    }

    function save_event_occurrence($post_id, $data)
    {
        global $wpdb;

        $table = $this->get_table_name('sm_event_occurrence');

        $wpdb->query("DELETE FROM `".$table."` WHERE event_id=".$post_id);

        foreach ($data as $item)
        {
            $item  = (array)$item;

            if (is_array($item['showtimes']))
            {
                $showtimes_code = '';

                foreach ($item['showtimes'] as $show)
                {
                    if (is_array($show))
                    {
                        $showtimes_code .= SM\Util\html::attributes($show)."\n";
                    }
                    else
                    {
                        $showtimes_code .= ['time'=>$show]."\n";
                    }
                }

                $item['showtimes'] = $showtimes_code;
            }

            $fields = array(
                'event_id'   => $post_id,
                'place_id'   => $item['place_id'],
                'day_date'   => $item['day_date'],
                'showtimes'  => $item['showtimes'],
            );

            $wpdb->insert($table, $fields);
        }
    }


    function _filter_pre_get_posts(&$query)
    {

        if (!empty($query->query_vars['sm_event_occurrence_query']))
        {
            $query->query_vars['meta_query'][] = array(
                'key' => '_thumbnail_id',
                'compare' => 'EXISTS'
            );
        }


        return $query;
    }

    function _filter_posts_fields( $fields, $query )
    {
        if (!empty( $query->sm_event_occurrence_query))
        {
            $fields = $fields . ', COUNT(sm_eo.id) AS sm_event_occurrences_count';
        }
        return $fields;
    }

    function _filter_posts_groupby( $groupby, $query )
    {
        if (!empty( $query->sm_event_occurrence_query))
        {
            global $wpdb;

            $groupby = trim( $groupby );

            if ( strpos( $groupby, $wpdb->posts . '.ID' ) === false )
                $groupby = ( $groupby !== '' ? $groupby . ', ' : '') . $wpdb->prefix . 'posts.ID';

                $groupby .= ' HAVING sm_event_occurrences_count > 0';
        }

        return $groupby;
    }

    function _filter_posts_join( $join, $query )
    {
        global  $wpdb;

        $join_sql = '';

        if ( ! empty( $query->query['sm_event_occurrence_date'] ) )
        {
            $m = $query->query['sm_event_occurrence_date'];

            if ( $m )
            {
                $join_sql .= " AND YEAR(sm_event_occurrence.day_date)=" . substr($m, 0, 4);

                if ( strlen($m) > 5 ) $join_sql .= " AND MONTH(sm_event_occurrence.day_date)=" . substr($m, 4, 2);

                if ( strlen($m) > 7 ) $join_sql .= " AND DAYOFMONTH(sm_event_occurrence.day_date)=" . substr($m, 6, 2);
            }
        }

        if ( ! empty( $query->query['sm_event_occurrence_place_id'] ) )
        {
            $join_sql .= " AND sm_event_occurrence.place_id IN (".implode( ',', wp_parse_id_list( $query->query['sm_event_occurrence_place_id'] ) ).')';
        }

        if ($join_sql)
        {
            $query->sm_event_occurrence_query = true;

            $table = $this->get_table_name('sm_event_occurrence');

            $join .= " LEFT JOIN " . $table. " sm_event_occurrence ON sm_event_occurrence.event_id = " . $wpdb->posts . ".ID AND " . $join_sql;
        }

        return $join;
    }

}









