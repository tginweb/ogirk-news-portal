<?php


namespace SM_Elementor\Module\QueryControls\Group_Control;

use SM_Elementor\Common;

use Elementor\Controls_Manager;

class Entity_Query extends Common\Group_Control
{
	const INLINE_MAX_RESULTS = 17;

	protected static $fields;

	static function get_type()
    {
		return 'sm-entity-query';
	}

	protected function init_fields()
    {
		$fields = [];

        $taxonomy_filter_args = [

        ];

        $taxonomies_options = [];

        $taxonomies = get_taxonomies( $taxonomy_filter_args, 'objects' );

        foreach ( $taxonomies as $taxonomy => $object )
        {
            $taxonomies_options[$taxonomy] = $object->label;
        }

        $fields['advanced_mode'] = [
            'label'      => __('Продвинутый режим'),
            'type'       => Controls_Manager::SWITCHER,
            'default'    => 'no',
        ];


        $query_types_options = [];

        $query_types = apply_filters('sm_elementor/query/types', []);

        foreach ($query_types as $query_type_name => $query_type_info)
        {
            $query_types_options[$query_type_name] = $query_type_info['label'];
        }

        $fields['type'] = [
            'label'      => __('Тип запроса', 'sm-core'),
            'type'       => 'select',
            'default'    => 'posts',
            'options'    => $query_types_options
        ];


        $fields['terms_taxonomies'] = [
            'label'      => __('Таксономии', 'sm-core'),
            'type'       => 'select2',
            'multiple'   => true,
            'options'    => $taxonomies_options,
            'condition'  => [
                'type' => 'terms',
            ]
        ];

        $fields['terms_orderby'] = [
            'label'      => __('Сортировка', 'sm-core'),
            'type'       => 'select2',
            'options'    => [
                'name' => 'Name',
                'slug' => 'Slug',
                'term_id' => 'Term ID',
                'count' => 'Count',
                'last_post' => 'Last post',
            ],
            'condition'  => [
                'type' => 'terms',
            ]
        ];

        $fields['terms_num'] = array(
            'label'       => __( 'Количество' ),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 6,
            'min'         => 1,
            'condition'   => array(
                'type' => 'terms',
            ),
        );

        $fields['terms_in'] = [
            'label'       => 'Включить записи',
            'type'        => 'query',
            'post_type'   => '',
            'options'     => [],
            'label_block' => true,
            'multiple'    => true,
            'filter_type' => 'taxonomy',
            'condition'   => array(
                'type' => 'terms',
            ),
        ];

        $fields['terms_by_queried'] = [
            'label'       => 'Отношение к текущему объекту',
            'type'        => 'select2',
            'options'    => [
                'queried_post_author'  => 'Автор текущего поста',
                'queried_term'  => 'Текущий термин',
            ],
            'label_block' => true,
            'multiple' => true,
            'condition'   => array(
                'type' => 'terms',
            ),
        ];


        $fields['posts_query_mode'] = [
            'label'      => __('Источник запроса', 'sm-core'),
            'type'       => 'select',
            'default'    => 'query',
            'options'    => [
                'query'           => 'Выборка',
                'by_id'           => 'Несколько',
                'media'           => 'Медиафайлы',
                'current_query'   => 'Основной запрос',
                //'current_requery' => 'The ReQuery',
            ],
            'condition'  => [
                'type' => 'posts',
            ]
        ];


        $fields['posts_media_posts'] = [
            'label' => 'Изображения',
            'type' => Controls_Manager::GALLERY,
            'default' => [],
            'show_label' => false,
            'dynamic' => [
                'active' => true,
            ],
            'condition' => [
                'type' => 'posts',
                'posts_query_mode' => 'media',
            ],
        ];

        $fields['posts_type'] = [
            'label' => 'Типы записей',
            'type' => 'select',
            'multiple' => false,
            'options' => $this->get_post_type_options(),
            'default' => 'post',
            'condition' => [
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ],
        ];

        $fields['posts_num'] = array(
            'label'       => __( 'Количество' ),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 6,
            'min'         => 1,
            'max'         => 100,
            'step'        => 1,
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ),
        );

        $fields['posts_in'] = [
            'label'       => 'Включить записи',
            'type'        => 'query',
            'post_type'   => '',
            'options'     => [],
            'label_block' => true,
            'multiple'    => true,
            'filter_type' => 'by_id',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'by_id',
            ),
        ];

        $fields['taxonomy_header'] = [
            'label' => 'Выборка по категориям',
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ],
        ];



        foreach ( $taxonomies as $taxonomy => $object )
        {
            $taxonomy_args = [
                'label' => $object->label,
                'type' => 'query',
                'label_block' => true,
                'multiple' => true,
                'object_type' => $taxonomy,
                'options' => [],
                'condition' => [
                    'type' => 'posts',
                    'posts_query_mode' => 'query',
                    'posts_type' => $object->object_type,
                ],
            ];

            if (!$object->public)
            {
                $taxonomy_args['condition']['advanced_mode'] = 'yes';
            }

            $count = wp_count_terms( $taxonomy );

            $options = [];


            if ($taxonomy == 'post_format')
            {
                $taxonomy_args['type'] = Controls_Manager::SELECT2;
                $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);

                $options['none'] = 'Статья';

                foreach ( $terms as $term ) {
                    $options[ $term->term_id ] = $term->name;
                }

                $taxonomy_args['options'] = $options;
            }
            else
            {
                if ( $count > self::INLINE_MAX_RESULTS ) {
                    $taxonomy_args['type'] = 'query';
                    $taxonomy_args['filter_type'] = 'taxonomy';
                } else {
                    $taxonomy_args['type'] = Controls_Manager::SELECT2;
                    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);

                    foreach ( $terms as $term ) {
                        $options[ $term->term_id ] = $term->name;
                    }

                    $taxonomy_args['options'] = $options;
                }
            }



            $fields[ 'posts_tax_'.$taxonomy . '_ids' ] = $taxonomy_args;
        }


        $fields['misc_header'] = [
            'type' => Controls_Manager::HEADING,
            'label' => 'Дополнительно',
            'separator' => 'before',
            'condition' => [
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ],
        ];


        $fields['posts_parent'] = [
            'label'       => 'Родительские посты',
            'type'        => 'query',
            'post_type'   => '',
            'options'     => [],
            'label_block' => true,
            'multiple'    => true,
            'filter_type' => 'by_id',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ),
        ];

        $fields['posts_not_in'] = [
            'label'       => 'Исключить записи',
            'type'        => 'query',
            'post_type'   => '',
            'options'     => [],
            'label_block' => true,
            'multiple'    => true,
            'filter_type' => 'by_id',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ),
        ];

        $fields['posts_by_queried'] = [
            'label'       => 'Отношение к текущему объекту',
            'type'        => 'select2',
            'options'    => [
                'queried_post_gallery'         => 'Галерея текущего поста',
                'queried_post'                 => 'Текущий пост',
                'queried_post_hub'             => 'Принадлежит текущему хабу',
                'queried_post_related'         => 'Связные записи',
                'queried_post_parent'          => 'Родитель текущего поста',
                'queried_term'                 => 'Текущий термин',
                'queried_term_and_childs'      => 'Текущий термин и дочерние',
                'queried_post_id'              => 'Текущий пост ID'
            ],
            'label_block' => true,
            'multiple' => true,
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
                'advanced_mode' => 'yes'
            ),
        ];

        foreach ($this->get_taxonomies_for_options() as $tax_name => $tax_label)
        {
            $fields['posts_by_queried']['options']['queried_post_term_'.$tax_name] = 'Пост термин: '.$tax_label;
        }


        $fields['posts_by_queried_post_meta'] = [
            'label'       => 'Имя мета поля',
            'type'        => 'text',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
                'advanced_mode' => 'yes',
                'posts_by_queried' => 'queried_post'
            ),
        ];

        $fields['posts_by_queried_post_parent_meta'] = [
            'label'       => 'Имя мета поля',
            'type'        => 'text',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
                'advanced_mode' => 'yes',
                'posts_by_queried' => 'queried_post_parent'
            ),
        ];

        $fields['posts_by_queried_post_gallery_meta'] = [
            'label'       => 'Имя мета поля',
            'type'        => 'text',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
                'advanced_mode' => 'yes',
                'posts_by_queried' => 'queried_post_gallery'
            ),
        ];

        /*

        foreach ($this->get_taxonomies_for_options() as $tax_name => $tax_label)
        {
            $fields['posts_tax_queried']['options']['queried_post_term_and_childs.'.$tax_name] = 'Пост термин и дочерние: '.$tax_label;
        }

        */


        $fields['posts_age_days'] = [
            'label' => 'Не старее дней',
            'type' => Controls_Manager::NUMBER,
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ),
        ];

        $fields['posts_have_thumb'] = [
            'label' => 'Только с фото',
            'type' => Controls_Manager::SWITCHER,
            'default' => 'no',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ),
        ];

        $fields['avoid_duplicates'] = [
            'label' => 'Избежать повторов',
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ),
        ];

        $fields['posts_order'] = [
            'label'   => esc_html__( 'Сортировка', 'jet-engine' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'DESC',
            'options' => array(
                'ASC'  => __( 'ASC', 'jet-engine' ),
                'DESC' => __( 'DESC', 'jet-engine' ),
            ),
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ),
        ];

        $fields['posts_orderby'] = [
            'label'   => esc_html__( 'Поле сортировки', 'jet-engine' ),
            'type'    => Controls_Manager::SELECT,
            'options' => array(
                'none'          => __( 'None', 'jet-engine' ),
                'ID'            => __( 'ID', 'jet-engine' ),
                'author'        => __( 'Author', 'jet-engine' ),
                'title'         => __( 'Title', 'jet-engine' ),
                'name'          => __( 'Name', 'jet-engine' ),
                'type'          => __( 'Type', 'jet-engine' ),
                'date'          => __( 'Date', 'jet-engine' ),
                'modified'      => __( 'Modified', 'jet-engine' ),
                'parent'        => __( 'Parent', 'jet-engine' ),
                'rand'          => __( 'Rand', 'jet-engine' ),
                'comment_count' => __( 'Comment count', 'jet-engine' ),
                'relevance'     => __( 'Relevance', 'jet-engine' ),
                'menu_order'    => __( 'Menu order', 'jet-engine' ),
                'meta_value'    => __( 'Meta value', 'jet-engine' ),
                'post_views'    => __( 'Post views', 'jet-engine' ),
            ),
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
            ),
        ];

        $fields['query_id'] = [
            'label' => 'Query ID',
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
                'advanced_mode' => 'yes'
            ),
        ];

        $overrides_repeater = new \Elementor\Repeater();

        $overrides_repeater->add_control(
            'post_id',
            array(
                'label'       => esc_html__( 'Post', 'jet-engine' ),
                'type'        => 'query',
                'post_type'   => '',
                'options'     => [],
                'label_block' => true,
                'filter_type' => 'by_id',
                'condition'   => array(

                ),
            )
        );

        $overrides_repeater->add_control(
            'pos',
            array(
                'label'       => esc_html__( 'Position', 'jet-engine' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 1
            )
        );


        $fields['posts_processors_heading'] = [
            'type' => Controls_Manager::HEADING,
            'label' => 'Процессоры',
            'separator' => 'before',
            'condition' => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
                'advanced_mode' => 'yes'
            ),
        ];

        $fields['posts_processors'] = [
            'label'   => esc_html__( 'Процессоры' ),
            'type'    => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => apply_filters('sm/query/posts/processors', []),
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
                'advanced_mode' => 'yes'
            ),
        ];


        $fields['posts_overrides_header'] = [
            'type' => Controls_Manager::HEADING,
            'label' => 'Переопределения',
            'separator' => 'before',
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
                'advanced_mode' => 'yes'
            ),
        ];

        $fields['posts_overrides'] = [
            'type'    => Controls_Manager::REPEATER,
            'fields'  => array_values( $overrides_repeater->get_controls() ),
            'default' => array(),
            'title_field' => '{{{ post_id }}}',
            'prevent_empty' => false,
            'condition'   => array(
                'type' => 'posts',
                'posts_query_mode' => 'query',
                'advanced_mode' => 'yes'
            ),
        ];



        return $fields;
	}

    protected function prepare_fields( $fields )
    {
        $args = $this->get_args();

        //$fields['posts_ids']['object_type'] = array_keys($post_types);

        return parent::prepare_fields( $fields );
    }

    public function get_taxonomies_for_options() {

        $args = array(
            'public'   => true,
        );

        $taxonomies = get_taxonomies( $args, 'objects', 'and' );

        return wp_list_pluck( $taxonomies, 'label', 'name' );
    }

    public function meta_types() {
        return array(
            'NUMERIC'  => __( 'NUMERIC', 'jet-engine' ),
            'BINARY'   => __( 'BINARY', 'jet-engine' ),
            'CHAR'     => __( 'CHAR', 'jet-engine' ),
            'DATE'     => __( 'DATE', 'jet-engine' ),
            'DATETIME' => __( 'DATETIME', 'jet-engine' ),
            'DECIMAL'  => __( 'DECIMAL', 'jet-engine' ),
            'SIGNED'   => __( 'SIGNED', 'jet-engine' ),
            'UNSIGNED' => __( 'UNSIGNED', 'jet-engine' ),
        );
    }

    public function get_post_type_options() {

        $post_types = get_post_types(array('public' => true), 'objects');

        $options = [];

        foreach ($post_types as $post_type) {
            $options[$post_type->name] = $post_type->label;
        }

        return $options;
    }


    public static function build_query_args($control_id, $settings)
    {

        switch ($settings[$control_id.'_type'])
        {
            case 'posts':

                $query_args = [
                    'ignore_sticky_posts'   => 1,
                    'post_status'           => ['publish'],
                ];


                switch ($settings[$control_id.'_posts_query_mode'])
                {
                    case 'query':

                        $query_args['tax_query'] = array();

                        $query_args['posts_per_page'] = $settings[$control_id.'_posts_num'];

                        if (!empty($settings[$control_id.'_page'])) {
                            $query_args['paged'] = $settings[$control_id.'_page'];
                        }

                        if (!empty($settings[$control_id.'_posts_type'])) {
                            $query_args['post_type'] = $settings[$control_id.'_posts_type'];
                        }

                        $query_args['post_type'] = (array)$query_args['post_type'];

                        if (in_array('attachment', $query_args['post_type']))
                        {
                            $query_args['post_status'][] = 'inherit';
                        }

                        $tax_conditions = [];

                        foreach ($query_args['post_type'] as $post_type)
                        {
                            $taxonomies = get_object_taxonomies( $post_type, 'objects' );

                            foreach ( $taxonomies as $object )
                            {
                                $setting_key = $control_id . '_posts_tax_' . $object->name . '_ids';

                                if ( ! empty( $settings[ $setting_key ] ) ) {

                                    if ($object->name=='post_format' && in_array('none', $settings[ $setting_key ]))
                                    {
                                        $tax_conditions[] = [
                                            'taxonomy' => 'post_format',
                                            'operator' => 'NOT EXISTS'
                                        ];
                                    }
                                    else
                                    {
                                        $tax_conditions[] = [
                                            'taxonomy' => $object->name,
                                            'field' => 'term_id',
                                            'terms' => $settings[ $setting_key ],
                                        ];
                                    }
                                }
                            }
                        }

                        if (!empty($tax_conditions))
                        {
                            $tax_conditions['relation'] = 'AND';

                            $query_args['tax_query'][] = $tax_conditions;
                        }


                        if (!empty($settings[$control_id.'_posts_by_queried']))
                        {
                            $tax_conditions = [];
                            $meta_conditions = [];

                            $queried_options = $settings[$control_id.'_posts_by_queried'];

                            $queried_object = get_queried_object();



                            if (in_array('queried_post_related', $queried_options))
                            {
                                if ($queried_object instanceof \WP_Post)
                                {
                                    $related_tag_terms = wp_get_post_terms($queried_object->ID, ['post_tag'], ['fields'=>'id=>name']);


                                    if (!empty($related_tag_terms))
                                    {
                                        $tax_conditions[] = [
                                            'taxonomy' => 'post_tag',
                                            'field' => 'term_id',
                                            'terms' => array_keys($related_tag_terms),
                                        ];
                                    }
                                    else
                                    {
                                        $related_category_terms = wp_get_post_terms($queried_object->ID, ['category'], ['fields'=>'id=>name']);

                                        $tax_conditions[] = [
                                            'taxonomy' => 'category',
                                            'field' => 'term_id',
                                            'terms' => array_keys($related_category_terms),
                                        ];
                                    }
                                }
                            }

                            if (in_array('queried_post_hub', $queried_options))
                            {
                                if ($queried_object instanceof \WP_Post)
                                {
                                    $hub_related_terms = wp_get_post_terms($queried_object->ID, 'sm-hub-term');

                                    if (!empty($hub_related_terms))
                                    {
                                        $hub_related_term = current($hub_related_terms);

                                        $tax_conditions[] = [
                                            'taxonomy' => $hub_related_term->taxonomy,
                                            'field' => 'term_id',
                                            'terms' => [$hub_related_term->term_id],
                                        ];
                                    }
                                }
                            }

                            if (in_array('queried_post_gallery', $queried_options))
                            {
                                if ($queried_object instanceof \WP_Post)
                                {
                                    $meta_key_name = $settings[$control_id.'_posts_by_queried_post_gallery_meta'];

                                    if ($meta_key_name)
                                    {
                                        $value = get_field( $meta_key_name, $queried_object->ID );

                                        $images_ids = [];

                                        if ($thumb_id = get_post_thumbnail_id($queried_object->ID))
                                        {
                                            $images_ids[] = $thumb_id;
                                        }

                                        if ( is_array( $value ) && ! empty( $value ) )
                                        {
                                            foreach ( $value as $image )
                                            {
                                                $images_ids[] = $image['ID'];
                                            }
                                        }

                                        if (empty($images_ids))
                                        {
                                            $images_ids = [1111111111];
                                        }

                                        $query_args['orderby'] = 'post__in';
                                        $query_args['post__in'] = array_map('intval', $images_ids);
                                    }
                                }
                            }

                            if (in_array('queried_post_id', $queried_options))
                            {
                                if ($queried_object instanceof \WP_Post)
                                {

                                    $query_args['orderby'] = 'post__in';
                                    $query_args['post__in'][] = $queried_object->ID;
                                }
                            }

                            if (in_array('queried_post', $queried_options))
                            {
                                if ($queried_object instanceof \WP_Post)
                                {
                                    $meta_key_name = $settings[$control_id.'_posts_by_queried_post_meta'];

                                    if ($meta_key_name)
                                    {
                                        $meta_conditions[] = [
                                            'compare' => '=',
                                            'key' => $meta_key_name,
                                            'value' => $queried_object->ID,
                                        ];
                                    }
                                }
                            }

                            if (in_array('queried_post_parent', $queried_options))
                            {
                                if ($queried_object instanceof \WP_Post)
                                {
                                    $meta_key_name = $settings[$control_id.'_posts_by_queried_post_parent_meta'];

                                    if ($meta_key_name)
                                    {
                                        $queried_object_parent_id = get_field($meta_key_name, $queried_object->ID);

                                        $meta_conditions[] = [
                                            'compare' => '=',
                                            'key' => $meta_key_name,
                                            'value' => $queried_object_parent_id,
                                        ];
                                    }
                                }
                            }

                            if (in_array('queried_term', $queried_options) || in_array('queried_term_and_childs', $queried_options))
                            {
                                if ($queried_object instanceof \WP_Term)
                                {
                                    $queried_object_term_ids = [
                                        $queried_object->term_id
                                    ];

                                    if (in_array('queried_term_and_childs', $queried_options))
                                    {
                                        $queried_object_term_ids = array_merge($queried_object_term_ids, get_terms([
                                            'taxonomy' => $queried_object->taxonomy,
                                            'parent'   => $queried_object->term_id,
                                            'fields'   => 'ids'
                                        ]));
                                    }

                                    $queried_object_term_ids = array_unique($queried_object_term_ids);

                                    $tax_conditions[] = [
                                        'taxonomy' => $queried_object->taxonomy,
                                        'field' => 'term_id',
                                        'terms' => $queried_object_term_ids,
                                    ];
                                }
                            }


                            if ($queried_object instanceof \WP_Post)
                            {
                                $taxonomies = get_object_taxonomies( $queried_object->post_type, 'objects' );

                                foreach ( $taxonomies as $tax )
                                {
                                    if (in_array('queried_post_term_'.$tax->name, $queried_options))
                                    {
                                        $post_terms_ids = wp_get_post_terms($queried_object->ID, $tax->name, ['fields'=>'ids']);

                                        if (!empty($post_terms_ids))
                                        {
                                            $tax_conditions[] = [
                                                'taxonomy' => $tax->name,
                                                'field' => 'term_id',
                                                'terms' => $post_terms_ids,
                                            ];
                                        }

                                    }
                                }
                            }

                            if (!empty($tax_conditions))
                            {
                                $tax_conditions['relation'] = 'OR';

                                $query_args['tax_query'][] = $tax_conditions;
                            }

                            if (!empty($meta_conditions))
                            {
                                $meta_conditions['relation'] = 'OR';

                                $query_args['meta_query'][] = $meta_conditions;
                            }
                        }


                        if (!empty($query_args['tax_query']))
                        {
                            $query_args['tax_query']['relation'] = 'AND';
                        }

                        if (!empty($query_args['meta_query']))
                        {
                            $query_args['meta_query']['relation'] = 'AND';
                        }


                        if (!empty($settings[$control_id.'_avoid_duplicates']) && $settings[$control_id.'_avoid_duplicates']=='yes')
                        {
                            if ($used_ids = \SM\Query\Collector::i()->get_items())
                            {
                                if (empty($query_args['post__not_in'])) $query_args['post__not_in'] = [];

                                $query_args['post__not_in'] = array_merge($query_args['post__not_in'], $used_ids);
                            }
                        }

                        if (!empty($settings[$control_id.'_posts_not_in']))
                        {
                            if (empty($query_args['post__not_in'])) $query_args['post__not_in'] = [];

                            $query_args['post__not_in'] = array_merge($query_args['post__not_in'], $settings[$control_id.'_posts_not_in']);
                        }




                        if (!empty($settings[$control_id.'_posts_parent']))
                        {
                            $query_args['post_parent__in'] = $settings[$control_id.'_posts_parent'];
                        }

                        if ($settings[$control_id.'_posts_have_thumb'] === 'yes')
                        {
                            $query_args['have_thumb'] = 1;
                        }

                        if ($settings[$control_id.'_posts_age_days'] && intval($settings[$control_id.'_posts_age_days'])>0)
                        {
                            $query_args['age_days'] = intval($settings[$control_id.'_posts_age_days']);
                        }

                        if ($settings[$control_id.'_posts_overrides'])
                        {
                            $query_args['overrides'] = $settings[$control_id.'_posts_overrides'];
                        }

                        if ($settings[$control_id.'_posts_order'])
                        {
                            $query_args['order'] = $settings[$control_id.'_posts_order'];
                        }

                        if ($settings[$control_id.'_posts_orderby'])
                        {
                            $query_args['orderby'] = $settings[$control_id.'_posts_orderby'];

                            if ($query_args['orderby']=='post_views')
                            {
                                $query_args['suppress_filters'] = false;
                            }
                        }

                        if (!empty($settings[$control_id.'_posts_processors']))
                        {
                            $query_args['query_processors'] = $settings[$control_id.'_posts_processors'];
                        }


                        break;

                    case 'by_id':

                        $query_args['post_type'] = 'any';

                        if (!empty($settings[$control_id.'_posts_in']))
                            $posts_ids = $settings[$control_id.'_posts_in'];
                        else
                            $posts_ids = [0];

                        $query_args['post__in'] = array_map('intval', $posts_ids);


                        break;

                    case 'media':

                        $query_args['post_type'] = 'any';

                        if (!empty($settings[$control_id.'_posts_media_posts']))
                        {
                            foreach ($settings[$control_id.'_posts_media_posts'] as $item)
                            {
                                $posts_ids[] = $item['id'];
                            }
                        }
                        else
                            $posts_ids = [0];

                        $query_args['post_status'][] = 'inherit';

                        $query_args['post__in'] = array_map('intval', $posts_ids);
                        $query_args['orderby'] = 'post__in';

                        break;

                    case 'current_query':

                        if ($GLOBALS['wp_query']) {

                            $current_query_vars = [];

                            foreach (array_filter($GLOBALS['wp_query']->query_vars) as $qv_name=>$qv_value)
                            {
                                if (self::is_public_query_var($qv_name) || in_array($qv_name, ['posts_per_page']))
                                {
                                    $current_query_vars[$qv_name] = $qv_value;
                                }
                            }
                        }
                        else {
                            $current_query_vars = [];
                        }

                        $query_args = apply_filters( 'elementor_pro/query_control/get_query_args/current_query', $current_query_vars );

                        break;
                }


                break;

            case 'terms':

                $query_args = [

                ];

                $query_args['number'] = !empty($settings[$control_id.'_terms_num']) && intval($settings[$control_id.'_terms_num'])>0 ? $settings[$control_id.'_terms_num'] : 100;

                $query_args['taxonomy'] =  $settings[$control_id.'_terms_taxonomies'];


                if (!empty($settings[$control_id.'_terms_by_queried']))
                {
                    $queried_options = $settings[$control_id.'_terms_by_queried'];


                    if (in_array('queried_post_author', $queried_options))
                    {
	                    $queried_object = get_queried_object();

	                    $author_term_id = -1;

	                    if ($queried_object instanceof \WP_Post)
	                    {
		                    $author_term_ids = get_field('sm_author_spr', $queried_object->ID);

		                    if (!empty($author_term_ids)) {

			                    $author_term_id = current($author_term_ids);
		                    }
		                    /*
							$author_terms = wp_get_post_terms($queried_object->ID, 'sm-author');

							if (!empty($author_terms))
							{
								$author_term = current($author_terms);

								$author_term_id = $author_term->term_id;
							}
							*/
	                    }

	                    if (!$author_term_id)
		                    $author_term_id = -1;

	                    //file_put_contents(__DIR__.'/t.log', $queried_object->ID.'-'.$author_term_id."\n", FILE_APPEND);

	                    $query_args['term_taxonomy_id'][] = $author_term_id;
                    }

                    if (in_array('queried_term', $queried_options))
                    {
                        $queried_object = get_queried_object();

                        if ($queried_object instanceof \WP_Term)
                        {
                            $query_args['term_taxonomy_id'][] = $queried_object;
                        }
                    }
                }

                if ($settings[$control_id.'_terms_orderby'])
                {
                    $query_args['orderby'] =  $settings[$control_id.'_terms_orderby'];
                }

                if ($settings[$control_id.'_terms_in'])
                {
                    $query_args['term_taxonomy_id'] = $settings[$control_id.'_terms_in'];
                    $query_args['orderby'] =  '';
                }


                break;
        }

        $query_args['query_id'] = $settings[$control_id.'_query_id'];


        return $query_args;
    }

    public static function build_query_result($control_id, $settings, $query_args)
    {
        $result = [
            //'type' => $settings[$control_id.'_type'],
            'query_url' => $settings[$control_id.'_url']
        ];

        $query_type = $settings[$control_id.'_type'];

        switch ($query_type)
        {
            case 'posts':

                //$result['mode'] = $settings[$control_id.'_posts_query_mode'];

                switch ($settings[$control_id.'_posts_query_mode'])
                {
                    case 'query':
                    case 'by_id':
                    case 'media':

                        $query = new \WP_Query($query_args);

                        break;

                    case 'current_query':
                        $query = $GLOBALS['wp_query'];
                        break;
                }


                $result += [
                    'query'          => $query,
                    'entities'       => $query->posts,
                    'max_num_pages'  => $query->max_num_pages,
                    'count_all'      => $query->found_posts,
                    'count'          => $query->post_count,
                    'current_page'   => $query->get('paged', 1) ?: 1,
                ];


                break;

            case 'terms':

                //$result['mode'] = $settings[$control_id.'_terms_query_mode'];

                $query = new \WP_Term_Query($query_args);


                $result += [
                    'query'         => $query,
                    'entities'      => $query->terms,
                    'max_num_pages' => $query->number
                ];

                break;

            default:

                $result += apply_filters('sm_elementor/query/type/'.$query_type.'/result', [], $query_args);

        }

        return $result;
    }

    public static function alter_query_args($query_args, $control_id, $settings)
    {

        return $query_args;
    }


    public static function is_public_query_var($name)
    {
        static $public_vars;

        global $wp;

        if (!$public_vars) $public_vars = apply_filters( 'query_vars', $wp->public_query_vars );

        return in_array($name, $public_vars);
    }

    protected function get_default_options()
    {
		return [
			'popover' => false,
		];
	}
}
