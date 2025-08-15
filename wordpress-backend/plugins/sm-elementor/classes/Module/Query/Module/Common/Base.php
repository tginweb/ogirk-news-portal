<?php

namespace SM_Elementor\Module\Query\Module\Common;


use SM_Elementor;
use Elementor\Controls_Manager;
use SM\Module\Wc\Util;

class Base extends \SM_Elementor\Common\Customizable
{

    static $customizer_class_name = 'query_module';
    static $customizer_class_types = null;

    /* @var \SM\Entity\Obj\Base */
    public $entity;

    public $settings;

    public $region;

    public $index;

    public $attrs = [];

    public $customizer_element_class_preffix = 'm-';


    static function get_elements_info()
    {
        return
            [
                'container'        => ['label'=>'Container'],
                'content'          => ['label'=>'Container: Content', 'is_container'=>true],
                'media'            => ['label'=>'Container: Media', 'is_container'=>true],
                'info'             => ['label'=>'Container: Info', 'is_container'=>true],
                'meta'             => ['label'=>'Container: Meta', 'is_container'=>true],

                'thumb'            => ['label'=>'Thumbnail', 'parent'=>'content'],
                'thumb_media'      => ['label'=>'Thumbnail Image'],

                'lightbox_link'    => ['label'=>'Lightbox link', 'parent'=>'content'],
                'lightbox_info'    => ['label'=>'Container: Lightbox Info', 'is_container'=>true],
                'lightbox_title'   => ['label'=>'Lightbox Title', 'parent'=>'lightbox_info'],
                'lightbox_excerpt' => ['label'=>'Lightbox Excerpt', 'parent'=>'lightbox_info'],

                'terms'            => ['label'=>'Terms', 'parent'=>'content'],
                'title'            => ['label'=>'Title', 'parent'=>'content'],
                'excerpt'          => ['label'=>'Excerpt', 'parent'=>'content'],
                'author'           => ['label'=>'Author', 'parent'=>'content'],
                'date'             => ['label'=>'Date', 'parent'=>'content'],
                'comments'         => ['label'=>'Comments', 'parent'=>'content'],
                'readmore'         => ['label'=>'Readmore', 'parent'=>'content'],
                'index'            => ['label'=>'Index', 'parent'=>'content'],

                'mediatype'        => ['label'=>'Media type', 'parent'=>'media'],
                'player'           => ['label'=>'Player', 'parent'=>'media'],

                'links'            => ['label' => 'Links'],
                'links_item'       => ['label' => 'Links item'],

                'flags'            => ['label' => 'Flags'],
            ];
    }

    function init($entity, $settings, $region_id=1) {

        $this->entity = \SM\Entity::i()->create_object($entity);


        $this->settings = $this->prepare_settings($settings);

        $this->region = $region_id;

        if ($settings['modules_customizer'])
        {
            $this->customizer_init($settings['modules_customizer']);
        }
    }


    public function get_title()
    {
        return $this->object_type_info ? $this->object_type_info['label'] : $this->get_object_type_id();
    }

    function get_template()
    {
        return '';
    }


    function render()
    {

        if ($this->settings['template_source']=='settings')
        {
            $tpl = $this->settings['template_code'];
        }
        else if ($this->settings['template_source']=='registry')
        {
            if (!empty($this->settings['template_id']))
            {
                $tpl_id = $this->settings['template_id'];

                if ($tpl_info = \SM_Elementor\Module\Query\Module::i()->get_query_module_templates($tpl_id))
                {
                    ob_start();

                    if ($tpl_info['file'])
                    {
                        include $tpl_info['file'];
                    }

                    return ob_get_clean();
                }
            }
        }
        else
        {
            $tpl = $this->get_template();
        }

        return $this->compile_template($tpl);
    }


    function get_container_attrs($attrs)
    {
        $attrs['class'] = $this->get_container_classes();

        if (!empty($this->attrs))
        {
            if (!empty($this->attrs['class']))
                $attrs['class'] = array_merge($attrs['class'], $this->attrs['class']);

            $attrs += $this->attrs;
        }

        if (!empty($this->settings['full_from']))
        {
            $attrs['class'][] = 's-full-'.$this->settings['full_from'];
        }
        else
        {
            $attrs['class'][] = 's-full-always';
        }

        $attrs['data-media-info'] = $this->get_media_info();

        return $attrs;
    }

    function get_container_classes() {

        $module_class_name = strtr($this->get_object_type_id(), '_','-');

        $classes = [
            'sm-query-module',
            'module',
            $module_class_name,
            'module-region-'.$this->region
        ];

        return $classes;
    }

    function wrap_link($element_id, $content, $params=[])
    {
        if (($this->settings[$element_id.'_link']=='yes') || !empty($params['wrap_link']))
        {
            $target = '';

            if (!empty($params['url']))
                $url = $params['url'];
            else
                $url = $this->get_link();

            $content = '<a href="'.$url.'" '.$target.'>'.$content.'</a>';
        }

        return $content;
    }

    function get_link()
    {
        return $this->entity->get_url();
    }

    function get_title_value()
    {
        return $this->entity->get_title();
    }

    function get_excerpt_value()
    {
        $excerpt_count = $this->settings['excerpt_length'];

        if (empty($this->entity->get_excerpt()))
            $excerpt = $this->entity->get_content();
        else
            $excerpt = $this->entity->get_excerpt();

        if ($this->settings['rich_text_excerpt'])
            $output = do_shortcode(force_balance_tags(html_entity_decode(wp_trim_words(htmlentities($excerpt), $excerpt_count, '…'))));
        else
            $output = wp_trim_words(wp_strip_all_tags(strip_shortcodes($excerpt)), $excerpt_count, '…');

        return $output;
    }

    function get_thumb_image_id() {

        if ($this->entity->type=='post' && $this->entity->bundle=='attachment')
            $image_id = $this->entity->id;
        else
            $image_id = $this->entity->get_thumb('id');

        if ($image_id)
        {

            if (!file_exists(get_attached_file($image_id)))
            {
                $image_id = null;
            }
        }

        if (!$image_id &&  ($this->settings['thumb_empty_hook']=='yes'))
        {
            $image_id = $this->get_thumb_hook_id();
        }

        return $image_id;
    }

    function get_thumb_hook_id()
    {
        return 289763;
    }

    function get_lightbox_title_value()
    {
        return $this->get_title_value();
    }

    function get_lightbox_excerpt_value()
    {

        $excerpt_count = $this->settings['lightbox_display_excerpt_length'];

        if (empty($this->entity->get_excerpt()))
            $excerpt = $this->entity->get_content();
        else
            $excerpt = $this->entity->get_excerpt();

        if ($this->settings['lightbox_display_excerpt_rich']=='yes')
            $output = do_shortcode(force_balance_tags(html_entity_decode(wp_trim_words(htmlentities($excerpt), $excerpt_count, '…'))));
        else
            $output = wp_trim_words(wp_strip_all_tags(strip_shortcodes($excerpt)), $excerpt_count, '…');

        return $output;
    }

    function get_lightbox_href()
    {
        $image_id = $this->get_thumb_image_id();

        if ($image_id)
        {
            $image_data = wp_get_attachment_image_src($image_id, $this->settings['lightbox_image_size_size'] ?: 'full');

            if ($image_data)
            {
                return $image_data[0];
            }
        }
    }

    function get_lightbox_params($params=[]) {

        $href = $this->get_lightbox_href();

        if ($href)
        {
            $params = $this->get_lightbox_base_params($params);

            $params['attrs'] += [
                'href'           => $href,
                'data-caption'   => $this->_render_element('lightbox_info'),
            ];

            return $params;
        }
    }

    function get_lightbox_base_params($params) {

        $params['attrs'] += [
            'data-fancybox'  =>  $this->host->cid,
            'data-elementor-open-lightbox' => 'no'

        ];

        return $params;
    }


    function get_media_info() {

        $info = [];

        $type = $this->entity->get_format();

        switch ($type)
        {
            case 'video':

                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->entity->get_content(), $match))
                {
                    $info['provider'] = 'youtube';
                    $info['src'] = $match[1];
                }

                break;
        }

        $info['type'] = $type;

        return $info;
    }

    function get_media_player($params=[])
    {
        $media_info = $this->get_media_info();

        $player_params = [

        ];

        if (!empty($params['lazyload']))
        {
            $player_params['lazyload'] = '1';
        }

        $control_attrs = [
            'data-boot' => 1,
            'data-sm-elementor-player' => $player_params,
            'class' => ['player-control'],
        ];

        switch ($media_info['type'])
        {
            case 'video':
            case 'audio':

                $output = '<div '.\SM\Util\Html::attributes($control_attrs).'><div class="player player-handle" data-plyr-provider="'.$media_info['provider'].'" data-plyr-embed-id="'.$media_info['src'].'" ></div></div>';

                break;
        }

        return $output;
    }

    function get_format_info()
    {
        $formats = [
            'gallery' => ['icon'=>'fa fa-camera', 'format'=>'gallery', 'title'=>'Фото'],
            'video'   => ['icon'=>'fa fa-play', 'format'=>'video' , 'title'=>'Видео']
        ];

        $format = $this->entity->get_format();

        if ($formats[$format])
        {
            return $formats[$format];
        }
    }

    function render_flags($params=[]) {

        $format_info = $this->get_format_info();

        $flags = [];

        if (!empty($format_info))
        {
            $flag = [];

            $flag['title'] = $format_info['title'];
            $flag['item_class'] = 'flag-type-'.$format_info['format'];
            $flag['icon_class'] = $format_info['icon'];

            $flags[] = $flag;
        }

        if (has_term('is-exclusive', 'sm-role', $this->entity->host))
        {
            $flags[] = [
                'title'      => 'Эксклюзив',
                'item_class' => 'flag-type-exclusive',
                'icon_class' => 'fa fa-star'
            ];
        }

        if (!empty($flags))
        {
            $params = $this->customizer_element_params('flags', $params) + [
                    'tag' => 'span',
                ];

            $output = '<' . $params['tag'] . ' ' . \SM\Util\Html::attributes($params['attrs']) . '>';

            foreach ($flags as $flag)
            {
                $output .= '<span title="'.$flag['title'].'" alt="'.$flag['title'].'" class="flag-item '.$flag['item_class'].'"><i class="'.$flag['icon_class'].'"></i></span>';
            }

            $output .= '</' . $params['tag'] . '>';

        }

        return $output;
    }


    function render_mediatype($params=[]) {

        $format_info = $this->get_format_info();

        if (!empty($format_info))
        {
            $params = $this->customizer_element_params('mediatype', $params) + [
                    'tag' => 'div',
                ];

            $output = '<' . $params['tag'] . ' ' . \SM\Util\Html::attributes($params['attrs']) . '>';

            $output .= '<i class="'.$format_info['icon'].'"></i> <span class="length">'.$this->get_mediatype_length().'</span>';

            $output .= '</' . $params['tag'] . '>';
        }

        return $output;
    }

    function get_mediatype_length() {

        $format = $this->entity->get_format();

        if ($format=='video')
        {
            return '7:30';
        }
        else if ($format=='gallery')
        {
            $field_value = $this->entity->get_gallery('id');

            return $field_value ? count($field_value) : 0;
        }
    }

    function render_thumb($params=[]) {

        $output = '';

        if ($this->get_thumb_image_id()) {

            $params = $this->customizer_element_params('thumb', $params) + [
                'tag' => 'div',
                'size' => 'default'
            ];

            $output .= '<' . $params['tag'] . ' ' . \SM\Util\Html::attributes($params['attrs']) . '>';

            $output .= $this->render_thumb_overlay();

            $output .= $this->render_thumb_media();

            $output .= '</' . $params['tag'] . '>';

        }

        return $output;
    }

    function render_thumb_media($params=[]) {

        $params = $this->customizer_element_params('thumb_media', $params) + [

            ];


        $image_size_key = 'image_size';

        $image_setting = ['id' => $this->get_thumb_image_id()];

        $thumbnail_html = SM_Elementor\Util\Image::get_image_html($image_setting, $image_size_key, $this->settings, $params['attrs']);


        return $this->wrap_link('thumb', $thumbnail_html);
    }

    function render_thumb_overlay($params=[]) {

        $params += [
            'tag' => 'div',
        ];

        $params['attrs']['class'][] = 'm-overlay';

        $output = '<' . $params['tag'] . ' '. \SM\Util\Html::attributes($params['attrs']). '>';

        $output .= '</' . $params['tag'] . '>';

        return $output;
    }

    function render_lightbox_link($params=[]) {

        $output = '';

        if ($this->settings['display_lightbox']=='yes')
        {
            $params = $this->customizer_element_params('lightbox_link', $params) + [
                    'tag' => 'a',
                    'attrs' => []
                ];

            if ($params = $this->get_lightbox_params($params))
            {
                $output .= '<a '. \SM\Util\Html::attributes($params['attrs']). '><i class="fa fa-expand"></i></a>';
            }
        }

        return $output;
    }

    function render_lightbox_title($params=[]) {

        $output = '';

        if ($this->settings['lightbox_display_title']==='yes') {

            $params = $this->customizer_element_params('lightbox_title', $params) + [
                    'tag' => 'h3'
                ];

            $output .= '<' . $params['tag'] . ' ' . \SM\Util\Html::attributes($params['attrs']) . '>';

            $output .=  $this->get_lightbox_title_value();

            $output .= '</' . $params['tag'] . '>';

        }

        return $output;
    }

    function render_lightbox_excerpt($params=[]) {

        $output = '';

        if ($this->settings['lightbox_display_excerpt']==='yes') {

            $params = $this->customizer_element_params('lightbox_excerpt', $params) + [
                    'tag' => 'p'
                ];

            $output = '<' . $params['tag'] . ' ' . \SM\Util\Html::attributes($params['attrs']) . '>';

            $output .=  $this->get_lightbox_excerpt_value();

            $output .= '</' . $params['tag'] . '>';

        }

        return $output;
    }

    function render_title($params=[]) {

        $output = '';

        if ($this->settings['display_title']=='yes') :

            $params = $this->customizer_element_params('title', $params) + [
                    'tag' => 'h3'
                ];

            $output = '<' . $params['tag'] . ' '. \SM\Util\Html::attributes($params['attrs']). '>';

            $output .= $this->wrap_link('title', $this->get_title_value());

            $output .= '</' . $params['tag'] . '>';

        endif;

        return $output;
    }

    function render_excerpt($params=[]) {

        $output = '';

        if ($this->settings['display_summary']=='yes') {

            $params = $this->customizer_element_params('excerpt', $params) + [
                    'tag' => 'div'
                ];

            $output .= '<' . $params['tag'] . ' ' . \SM\Util\Html::attributes($params['attrs']) . '>';

            $output .= $this->wrap_link('excerpt', $this->get_excerpt_value());

            $output .= '</' . $params['tag'] . '>';

        }

        return $output;
    }


    function render_readmore($params=[]) {

        $output = '';

        if ($this->settings['display_read_more']=='yes') {

            $params = $this->customizer_element_params('readmore', $params) + [
                    'tag' => 'div'
                ];

            $read_more_text = $this->settings['read_more_text'];

            $output .= '<' . $params['tag'] . ' '. \SM\Util\Html::attributes($params['attrs']). '>';

            $output .= '<a href="' . $this->entity->get_url() . '">' . $read_more_text . '</a>';

            $output .= '</' . $params['tag'] . '>';
        }

        return $output;
    }


    function render_terms($params=[]) {

        $output = '';

        if ($this->settings['display_taxonomy'])
        {
            $params = $this->customizer_element_params('terms', $params) + [
                    'tag' => 'div',
                    'taxonomies' => !empty($this->settings['taxonomies']) ? $this->settings['taxonomies'] : $this->settings['modules_taxonomies']
                ];

            if (!empty($params['taxonomies']))
            {
                $params['taxonomies'] = (array)$params['taxonomies'];

                $groups = [];

                foreach ($params['taxonomies'] as $taxonomy)
                {
                    if ($group = $this->get_taxonomy_info($taxonomy))
                        $groups[] = $group;
                }

                if (!empty($groups)) {

                    $output .= '<' . $params['tag'] . ' ' . \SM\Util\Html::attributes($params['attrs']) . '>';

                    $output .= join(', ', $groups);

                    $output .= '</' . $params['tag'] . '>';
                }
            }
        }

        return $output;
    }

    function get_default_field_source()
    {

    }

    function get_field_value($name, $source=null, $default=null) {

        if (!$source)
        {
            $source = $this->get_default_field_source();
        }

        switch ($source)
        {
            case 'acf':

                $value = get_field($name, $this->entity->host);
                break;
        }

        return $value;
    }


    function render_field($params=[]) {

        $output = '';

        if (!empty($params['name']))
        {
            list($field_source, $field_name) = explode(':', $params['name']);

            if (!$field_name)
            {
                $field_name = $field_source;
                $field_source = null;
            }

            $value = $this->get_field_value($field_name, $field_source);


            if ($value) {

                $element_id = 'field_'.$field_name;

                $params = $this->customizer_element_params(['field', 'field_'.$field_name], $params) + [

                    ];

                if ($params['raw']) {
                    $output = $value;
                } else {
                    $output = $this->render_element_wrapper($element_id, $this->wrap_link($element_id, $value, $params), $params);
                }

            }
        }

        return $output;
    }

    function render_url($params=[]) {
       return $this->get_link();
    }

    function get_taxonomy_info($taxonomy) {

        $output = '';

        $terms = $this->entity->get_the_terms($taxonomy);

        if (!empty($terms) && !is_wp_error($terms)) {

            $output .= '<span class="lae-terms-tax">';

            $term_count = 0;

            foreach ($terms as $term) {

                if ($term_count != 0)
                    $output .= ', ';

                $output .= '<a href="' . get_term_link($term, $taxonomy) . '">' . $term->name . '</a>';

                $term_count = $term_count + 1;
            }

            $output .= '</span>';
        }

        return $output;
    }


    function get_date($params)
    {
        return $this->entity->get_the_time($params['format']);
    }

    function render_date($params=[]) {

        $output = '';

        if ($this->settings['display_date']=='yes') {

            $params = $this->customizer_element_params('date', $params) + [
                    'tag'    => 'div',
                    'format' => !empty($this->settings['date_format']) ? $this->settings['date_format'] : 'd.m.Y'
                ];

            if (empty($params['format']))
                $params['format'] = get_option('date_format');

            $output .= '<' . $params['tag'] . ' ' . \SM\Util\Html::attributes($params['attrs']) . '>';

            if (!empty($params['prefix']))
                $output .= '<span class="value-prefix">' . $params['prefix'] . '</span>';

                $output .= '<span>' . $this->entity->get_the_time($params['format']) . '</span>';

            if (!empty($params['suffix']))
                $output .= '<span class="value-suffix">' . $params['suffix'] . '</span>';

            $output .= '</' . $params['tag'] . '>';

        }

        return $output;
    }

    function render_comments($params=[])
    {

        $output = '';

        if ($this->settings['display_comments']=='yes') {

            $params = $this->customizer_element_params('comments', $params) + [
                    'tag' => 'div',
                    'caption' => !empty($this->settings['module_' . $this->region . '_comments_caption']) ? $this->settings['module_' . $this->region . '_comments_caption'] : 'Комментариев'
                ];

            $number = $this->entity->get_comments_count();

            $output .= '<' . $params['tag'] . ' ' . \SM\Util\Html::attributes($params['attrs']) . '>';

            if (!empty($params['prefix'])) $output .= '<span class="value-prefix">' . $params['prefix'] . '</span>';

            $output .= '<span class="value">
                                    <span class="value-caption">' . $params['caption'] . '</span>
                                    <span class="value-number">' . $number . '</span>                                
                                </span>';

            if (!empty($params['suffix'])) $output .= '<span class="value-suffix">' . $params['suffix'] . '</span>';

            $output .= '</' . $params['tag'] . '>';

        }

        return $output;
    }


    function customizer_init_item($item)
    {
        $item = parent::customizer_init_item($item);

        $item['index'] = $item['index'] ? preg_split('/\s*,\s*/', trim($item['index'])) : [];

        if (!empty($item['regions']))
            $item['regions'] = is_array($item['regions']) ? $item['regions'] : (array)$item['regions'];
        else
            $item['regions'] = [];

        return $item;
    }

    function customizer_check_item($item, $target=null, $args=[]) {

        if (!parent::customizer_check_item($item, $target, $args))
            return false;

        if (
            (empty($item['regions']) || in_array('all', $item['regions']) || in_array($this->region, $item['regions']))  &&
            (empty($item['index']) || in_array($this->index, $item['index']))
        )
        {
            return true;
        }

        return false;
    }

    function customizer_element_params($target, $params = [], $key=null) {

        $params = parent::customizer_element_params($target, $params);

        if ($this->settings['lightbox_link_wrap']=='yes')
        {
            if ($target=='content')
            {
                $params['attrs']['class'][] = 'lightbox-link-wrapper';
            }
        }

        return $key ? $params[$key] : $params;
    }

    static function customizer_fill_repeater_conditions(&$repeater, $targets=[])
    {

        parent::customizer_fill_repeater_conditions($repeater, $targets);

        $repeater->add_control(
            'regions',
            array(
                'label' => __('Регионы модуля'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => [
                    1 => '1',
                    2 => '2',
                    3 => '3'
                ]
            )
        );

        $repeater->add_control(
            'index',
            array(
                'label' => __('Целевые индексы'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            )
        );

        $repeater->add_control(
            'condition_entity_taxonomy',
            array(
                'label' => __('Целевая таксономия'),
                'type' => 'query',
                'label_block' => true,
                'filter_type' => 'taxonomy',
                'include_type' => true,
                'multiple' => true,
            )
        );



    }

    static function customizer_fill_repeater_style(&$repeater, $targets)
    {


        $repeater->add_responsive_control(
            'width_in_columns',
            array(
                'label' => __('Ширина в колонках'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => '',
                    1 => '1/12',
                    2 => '2/12',
                    3 => '3/12',
                    4 => '4/12',
                    5 => '5/12',
                    6 => '6/12',
                    7 => '7/12',
                    8 => '8/12',
                    9 => '9/12',
                    10 => '10/12',
                    11 => '11/12',
                    12 => '12/12',
                ],
            )
        );

        parent::customizer_fill_repeater_style($repeater, $targets);
    }
}