<?php

$posts_query_repeater = new \Elementor\Repeater();

$posts_query_repeater->add_control(
    'type',
    array(
        'label'   => esc_html__( 'Type', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'default' => '',
        'options' => array(
            'posts_params' => __( 'Posts Parameters', 'jet-engine' ),
            'order_offset' => __( 'Order & Offset', 'jet-engine' ),
            'tax_query'    => __( 'Tax Query', 'jet-engine' ),
            'meta_query'   => __( 'Meta Query', 'jet-engine' ),
            //'date_query'   => __( 'Date Query', 'jet-engine' ),
        ),
    )
);


$posts_query_repeater->add_control(
    'posts_in_selector',
    array(
        'label'       => esc_html__( 'Include posts', 'jet-engine' ),
        'type'        => 'query',
        'post_type'   => '',
        'options'     => [],
        'label_block' => true,
        'multiple'    => true,
        'filter_type' => 'by_id',
        'condition'   => array(
            'type' => 'posts_params'
        ),
    )
);

$posts_query_repeater->add_control(
    'posts_not_in_selector',
    array(
        'label'       => esc_html__( 'Exclude posts', 'jet-engine' ),
        'type'        => 'query',
        'post_type'   => '',
        'options'     => [],
        'label_block' => true,
        'multiple'    => true,
        'filter_type' => 'by_id',
        'condition'   => array(
            'type' => 'posts_params'
        ),
    )
);

$posts_query_repeater->add_control(
    'posts_in',
    array(
        'label'       => esc_html__( 'Include posts by IDs', 'jet-engine' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => '',
        'description' => __( 'Eg. 12, 24, 33', 'jet-engine' ),
        'condition'   => array(
            'type' => 'posts_params'
        ),
    )
);

$posts_query_repeater->add_control(
    'posts_not_in',
    array(
        'label'       => esc_html__( 'Exclude posts by IDs', 'jet-engine' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => '',
        'description' => __( 'Eg. 12, 24, 33. If this is used in the same query as Include posts by IDs, it will be ignored', 'jet-engine' ),
        'condition'   => array(
            'type' => 'posts_params'
        ),
    )
);

$posts_query_repeater->add_control(
    'posts_parent',
    array(
        'label'       => esc_html__( 'Get child of', 'jet-engine' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => '',
        'description' => __( 'Eg. 12, 24, 33', 'jet-engine' ),
        'condition'   => array(
            'type' => 'posts_params'
        ),
    )
);

$posts_query_repeater->add_control(
    'posts_status',
    array(
        'label'   => esc_html__( 'Get posts with status', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'default' => 'publish',
        'options' => array(
            'publish'    => __( 'Publish', 'jet-engine' ),
            'pending'    => __( 'Pending', 'jet-engine' ),
            'draft'      => __( 'Draft', 'jet-engine' ),
            'auto-draft' => __( 'Auto draft', 'jet-engine' ),
            'future'     => __( 'Future', 'jet-engine' ),
            'private'    => __( 'Private', 'jet-engine' ),
            'trash'      => __( 'Trash', 'jet-engine' ),
            'any'        => __( 'Any', 'jet-engine' ),
        ),
        'condition'   => array(
            'type' => 'posts_params'
        ),
    )
);

$posts_query_repeater->add_control(
    'offset',
    array(
        'label'     => esc_html__( 'Posts offset', 'jet-engine' ),
        'type'      => Controls_Manager::NUMBER,
        'default'   => '0',
        'min'       => 0,
        'max'       => 100,
        'step'      => 1,
        'condition' => array(
            'type' => 'order_offset'
        ),
    )
);

$posts_query_repeater->add_control(
    'order',
    array(
        'label'   => esc_html__( 'Order', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'default' => 'DESC',
        'options' => array(
            'ASC'  => __( 'ASC', 'jet-engine' ),
            'DESC' => __( 'DESC', 'jet-engine' ),
        ),
        'condition'   => array(
            'type' => 'order_offset'
        ),
    )
);

$posts_query_repeater->add_control(
    'order_by',
    array(
        'label'   => esc_html__( 'Order by', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'default' => 'date',
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
        ),
        'condition'   => array(
            'type' => 'order_offset'
        ),
    )
);

$posts_query_repeater->add_control(
    'meta_key',
    array(
        'label'       => esc_html__( 'Meta key to order', 'jet-engine' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => '',
        'label_block' => true,
        'description' => __( 'Set meta field name to order by', 'jet-engine' ),
        'condition'   => array(
            'type'     => 'order_offset',
            'order_by' => 'meta_value',
        ),
    )
);

$posts_query_repeater->add_control(
    'meta_type',
    array(
        'label'   => esc_html__( 'Meta type', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'default' => 'CHAR',
        'options' => array(
            'NUMERIC'  => __( 'NUMERIC', 'jet-engine' ),
            'CHAR'     => __( 'CHAR', 'jet-engine' ),
            'DATE'     => __( 'DATE', 'jet-engine' ),
            'DATETIME' => __( 'DATETIME', 'jet-engine' ),
            'DECIMAL'  => __( 'DECIMAL', 'jet-engine' ),
        ),
        'condition'   => array(
            'type'     => 'order_offset',
            'order_by' => 'meta_value',
        ),
    )
);

$posts_query_repeater->add_control(
    'tax_query_taxonomy',
    array(
        'label'   => esc_html__( 'Taxonomy', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'options' => $this->get_taxonomies_for_options(),
        'default' => '',
        'condition' => array(
            'type' => 'tax_query'
        ),
    )
);

$posts_query_repeater->add_control(
    'tax_query_compare',
    array(
        'label'   => esc_html__( 'Operator', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'options' => array(
            'IN'         => __( 'IN', 'jet-engine' ),
            'NOT IN'     => __( 'NOT IN', 'jet-engine' ),
            'AND'        => __( 'AND', 'jet-engine' ),
            'EXISTS'     => __( 'EXISTS', 'jet-engine' ),
            'NOT EXISTS' => __( 'NOT EXISTS', 'jet-engine' ),
        ),
        'default' => 'IN',
        'condition' => array(
            'type' => 'tax_query'
        ),
    )
);

$posts_query_repeater->add_control(
    'tax_query_field',
    array(
        'label'   => esc_html__( 'Field', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'options' => array(
            'term_id' => __( 'Term ID', 'jet-engine' ),
            'slug'    => __( 'Slug', 'jet-engine' ),
            'name'    => __( 'Name', 'jet-engine' ),
        ),
        'default' => 'term_id',
        'condition' => array(
            'type' => 'tax_query'
        ),
    )
);

$posts_query_repeater->add_control(
    'tax_query_terms',
    array(
        'label'       => esc_html__( 'Terms', 'jet-engine' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => '',
        'label_block' => true,
        'condition'   => array(
            'type' => 'tax_query'
        ),
    )
);



$posts_query_repeater->add_control(
    'tax_query_taxonomy_meta',
    array(
        'label'       => esc_html__( 'Taxonomy from meta field', 'jet-engine' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => '',
        'label_block' => true,
        'description' => __( 'Get taxonomy name from current page meta field', 'jet-engine' ),
        'condition'   => array(
            'type' => 'tax_query'
        ),
    )
);

$posts_query_repeater->add_control(
    'tax_query_terms_meta',
    array(
        'label'       => esc_html__( 'Terms from meta field', 'jet-engine' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => '',
        'label_block' => true,
        'description' => __( 'Get terms IDs from current page meta field', 'jet-engine' ),
        'condition'   => array(
            'type' => 'tax_query'
        ),
    )
);

$posts_query_repeater->add_control(
    'meta_query_key',
    array(
        'label'   => esc_html__( 'Key (name/ID)', 'jet-engine' ),
        'type'    => Controls_Manager::TEXT,
        'default' => '',
        'condition' => array(
            'type' => 'meta_query'
        ),
    )
);

$posts_query_repeater->add_control(
    'meta_query_compare',
    array(
        'label'   => esc_html__( 'Operator', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'default' => '=',
        'options' => array(
            '='           => __( 'Equal', 'jet-engine' ),
            '!='          => __( 'Not equal', 'jet-engine' ),
            '>'           => __( 'Greater than', 'jet-engine' ),
            '>='          => __( 'Greater or equal', 'jet-engine' ),
            '<'           => __( 'Less than', 'jet-engine' ),
            '<='          => __( 'Equal or less', 'jet-engine' ),
            'LIKE'        => __( 'Like', 'jet-engine' ),
            'NOT LIKE'    => __( 'Not like', 'jet-engine' ),
            'IN'          => __( 'In', 'jet-engine' ),
            'NOT IN'      => __( 'Not in', 'jet-engine' ),
            'BETWEEN'     => __( 'Between', 'jet-engine' ),
            'NOT BETWEEN' => __( 'Not between', 'jet-engine' ),
        ),
        'condition'   => array(
            'type' => 'meta_query',
        ),
    )
);

$posts_query_repeater->add_control(
    'meta_query_val_selector',
    array(
        'label'   => esc_html__( 'Value select', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT2,
        'multiple' => true,
        'default' => 'custom',
        'options' => array(
            'custom'           => __( 'Custom', 'jet-engine' ),
            'current_post_id'  => __( 'Current post id', 'jet-engine' ),
            'current_term_id'  => __( 'Current term id', 'jet-engine' ),
        ),
        'condition'   => array(
            'type' => 'meta_query',
        ),
    )
);

$posts_query_repeater->add_control(
    'meta_query_val',
    array(
        'label'       => esc_html__( 'Value', 'jet-engine' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => '',
        'label_block' => true,
        'description' => __( 'For <b>In</b>, <b>Not in</b>, <b>Between</b> and <b>Not between</b> compare separate multiple values with comma', 'jet-engine' ),
        'condition'   => array(
            'type' => 'meta_query',
            'meta_query_val_selector' => 'custom',
        ),
    )
);

$posts_query_repeater->add_control(
    'meta_query_type',
    array(
        'label'   => esc_html__( 'Type', 'jet-engine' ),
        'type'    => Controls_Manager::SELECT,
        'default' => 'CHAR',
        'options' => $this->meta_types(),
        'condition'   => array(
            'type' => 'meta_query',
        ),
    )
);


$fields['additional_header'] = [
    'label' => 'Настройки запроса',
    'type' => Controls_Manager::HEADING,
    'separator' => 'before',
    'condition'   => array(
        'advanced_mode' => 'yes'
    ),
];

$fields['posts_query'] = [
    'type'    => Controls_Manager::REPEATER,
    'fields'  => array_values( $posts_query_repeater->get_controls() ),
    'default' => array(),
    'title_field' => '{{{ type }}}',
    'prevent_empty' => false,
    'condition' => array(
        'type' => 'posts',
        'posts_query_mode' => 'query',
        'advanced_mode' => 'yes'
    ),
];

$fields['posts_meta_query_relation'] = array(
    'label'   => esc_html__( 'Meta query relation'),
    'type'    => Controls_Manager::SELECT,
    'default' => 'AND',
    'options' => array(
        'AND' => __( 'AND'),
        'OR'  => __( 'OR'),
    ),
    'condition'   => array(
        'type' => 'posts',
        'posts_query_mode' => 'query',
        'advanced_mode' => 'yes'
    ),
);

$fields['posts_tax_query_relation'] = array(
    'label'   => esc_html__( 'Tax query relation'),
    'type'    => Controls_Manager::SELECT,
    'default' => 'AND',
    'options' => array(
        'AND' => __( 'AND'),
        'OR'  => __( 'OR'),
    ),
    'condition' => array(
        'type' => 'posts',
        'posts_query_mode' => 'query',
        'advanced_mode' => 'yes'
    ),
);
