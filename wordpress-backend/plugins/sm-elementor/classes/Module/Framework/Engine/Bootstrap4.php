<?php


namespace SM_Elementor\Module\Framework\Engine;

use SM_Elementor\Module\Framework;
use SM\Assets;

class Bootstrap4 extends Base
{
    var $class_prefix = '';

    function assets()
    {
        return [

        ];
    }

    function enqueue_grid($enqueue_style)
    {
        if ($enqueue_style==='inline')
        {
            Assets::i()->wp_enqueue(['sm_elementor.framework.bootstrap4.grid'=>['code'=>'<style>'.$this->get_style_css().'</style>']], ['action_type'=>'head']);
        }
        else if ($enqueue_style=='css')
        {
            Assets::i()->wp_enqueue(['sm_elementor.framework.bootstrap4.grid']);
        }
    }

    function enqueue_engine($enqueue_style, $enqueue_script)
    {
        if ($enqueue_style && $enqueue_style!=='none')
        {
            Assets::i()->wp_enqueue(['sm_elementor.framework.bootstrap4.engine_css']);
        }

        if ($enqueue_script && $enqueue_script!=='none')
        {
            Assets::i()->wp_enqueue(['sm_elementor.framework.bootstrap4.engine_js']);
        }
    }

    function get_class_prefix()
    {
        return $this->class_prefix;
    }

    function get_grid_row_class()
    {
        return $this->class_prefix.'row';
    }

    function get_grid_col_width_classes($settings_key, $settings=[], $defaults=[])
    {
        $classes = [];

        if (!empty($settings[$settings_key]))
            $classes[] = $this->class_prefix.'col-lg-'.$settings[$settings_key];
        else if (!empty($defaults['desktop']))
            $classes[] = $this->class_prefix.'col-lg-'.$defaults['desktop'];

        if (!empty($settings[$settings_key.'_tablet']))
            $classes[] = $this->class_prefix.'col-md-'.$settings[$settings_key.'_tablet'];
        else if (!empty($defaults['tablet']))
            $classes[] = $this->class_prefix.'col-md-'.$defaults['tablet'];

        if (!empty($settings[$settings_key.'_mobile']))
            $classes[] = $this->class_prefix.'col-'.$settings[$settings_key.'_mobile'];
        else if (!empty($defaults['mobile']))
            $classes[] = $this->class_prefix.'col-'.$defaults['mobile'];

        if (empty($classes))
            $classes[] = $this->class_prefix.'col-12';

        return $classes;
    }

    function get_grid_col_classes($settings_key, $settings=[], $defaults=[], $override_width=[])
    {
        $classes = [];

        if (!empty($override_width['desktop']))
            $classes[] = $this->class_prefix.'col-lg-'.$override_width['desktop'];

        else if (!empty($settings[$settings_key]))
            $classes[] = $this->class_prefix.'col-lg-'.round(12/$settings[$settings_key]);

        else if (!empty($defaults['desktop']))
            $classes[] = $this->class_prefix.'col-lg-'.round(12/$defaults['desktop']);


        if (!empty($override_width['tablet']))
            $classes[] = $this->class_prefix.'col-md-'.$override_width['tablet'];

        else if (!empty($settings[$settings_key.'_tablet']))
            $classes[] = $this->class_prefix.'col-md-'.round(12/$settings[$settings_key.'_tablet']);

        else if (!empty($defaults['desktop']))
            $classes[] = $this->class_prefix.'col-md-'.round(12/$defaults['tablet']);


        if (!empty($override_width['mobile']))
            $classes[] = $this->class_prefix.'col-'.$override_width['mobile'];

        else if (!empty($settings[$settings_key.'_mobile']))
            $classes[] = $this->class_prefix.'col-'.round(12/$settings[$settings_key.'_mobile']);

        else if (!empty($defaults['desktop']))
            $classes[] = $this->class_prefix.'col-'.round(12/$defaults['mobile']);


        if (empty($classes))
            $classes[] = $this->class_prefix.'col-12';

        return $classes;
    }

    function get_style_css()
    {
        $css_lines = [];

        $breakpoints    = $this->get_grid_breakpoints();
        $columns_count  = $this->get_grid_columns_count();
        $gutter_width   = $this->get_grid_gutter_width();

        $gutter_width_half = round($gutter_width/2);

        $cprefix = $this->get_class_prefix();

        $css_lines[] = <<<EOT
.{$cprefix}row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -{$gutter_width_half}px;
    margin-left: -{$gutter_width_half}px; 
}

.{$cprefix}no-gutters {
    margin-right: 0;
    margin-left: 0; 
}

.{$cprefix}no-gutters > .{$cprefix}col,
.{$cprefix}no-gutters > [class*="{$cprefix}col-"] {
    padding-right: 0;
    padding-left: 0; 
}
EOT;

        $col_classes = [
            '.'.$cprefix.'col'
        ];

        foreach ([''=>''] + $breakpoints as $breakepoint=>$breakepoint_value)
        {
            if ($breakepoint=='xs') continue;

            $infix = $breakepoint ? $breakepoint.'-' : '';

            for ($column=1; $column <= $columns_count; $column++)
            {
                $col_classes[] = '.'.$cprefix.'col-'.$infix.$column;
            }

            $col_classes[] = '.'.$cprefix.'col-'.$infix.'auto';
        }

        $css_lines[] = join(', ', $col_classes).' { position: relative; width: 100%; min-height: 1px; padding-right: '.$gutter_width_half.'px; padding-left: '.$gutter_width_half.'px; }';

        $css_lines[] = <<<EOT
.{$cprefix}col {
  flex-basis: 0;
  flex-grow: 1;
  max-width: 100%; }

.{$cprefix}col-auto {
  flex: 0 0 auto;
  width: auto;
  max-width: none; }
EOT;

        for ($column=1; $column <= $columns_count; $column++)
        {
            $width = $this->get_procentage($column, $columns_count);

            $css_lines[] = '.'.$cprefix.'col-'.$column.' { flex: 0 0 '.$width.'; max-width: '.$width.'; }';
        }

        $css_lines[] = '.'.$cprefix.'order-first { order: -1; }';
        $css_lines[] = '.'.$cprefix.'order-last { order: '.($columns_count+1).'; }';
        $css_lines[] = '.'.$cprefix.'order-0 { order: 0; }';


        for ($column=1; $column <= $columns_count; $column++)
        {
            $css_lines[] = '.'.$cprefix.'order-'.$column.' { order: '.$column.'; }';
            $css_lines[] = '.'.$cprefix.'offset-'.$column.' { margin-left: '.$this->get_procentage($column, $columns_count).'; }';
        }


        foreach ($breakpoints as $breakepoint=>$breakepoint_value)
        {
            if ($breakepoint=='xs') continue;

            $css_lines[] = '@media (min-width: '.$breakepoint_value.'px) {';

            $css_lines[] = '.'.$cprefix.'col-'.$breakepoint.' { flex-basis: 0; flex-grow: 1; max-width: 100%; }';

            $css_lines[] = '.'.$cprefix.'col-'.$breakepoint.'-auto { flex: 0 0 auto; width: auto; max-width: none; }';

            $css_lines[] = '.'.$cprefix.'order-'.$breakepoint.'-first { order: -1; }';

            $css_lines[] = '.'.$cprefix.'order-'.$breakepoint.'-last { order: 13; }';

            for ($column=0; $column <= $columns_count; $column++)
            {
                $width = $this->get_procentage($column, $columns_count);

                if ($column>0)
                {
                    $css_lines[] = '.'.$cprefix.'col-'.$breakepoint.'-'.$column.' { flex: 0 0 '.$width.'; max-width: '.$width.'; }';
                }
                else
                {
                    $css_lines[] = '.'.$cprefix.'order-'.$breakepoint.'-'.$column.' { order: '.$column.'; }';

                    $css_lines[] = '.'.$cprefix.'offset-'.$breakepoint.'-'.$column.' { margin-left: '.$width.'; }';
                }

            }

            $css_lines[] = '}';
        }

        return join("\n", $css_lines);
    }

    function create_dropdown()
    {
        $widget->add_render_attribute( 'dropdown_content', 'class', 'sm-dropdown-content' );

        $widget->add_render_attribute( 'dropdown_content_wrapper', 'class', 'sm-dropdown-content-wrapper' );


        $schema = [
            'down-left'  => ['wrapper_class'=>'dropdown'],
            'down-right' => ['wrapper_class'=>'dropdown', 'content_class'=>'dropdown-menu-right'],
            'up-left'    => ['wrapper_class'=>'dropdown'],
            'up-right'   => ['wrapper_class'=>'dropdown'],
        ];

        $dir = $settings['sm_dropdown_direction'] ?: 'down-left';

        if (!isset($schema[$dir]))
            $dir = 'down-left';


        $schema_item = $schema[$dir];

        $widget->add_render_attribute( 'dropdown_content_wrapper', 'class', $schema_item['wrapper_class'] );

        if ($schema_item['content_class'])
            $widget->add_render_attribute( 'dropdown_content', 'class', $schema_item['content_class'] );

        //$trigger_content = sprintf( '<div %1$s>%2$s</div>', $widget->get_render_attribute_string( 'dropdown_trigger' ), $content );
        //$dropdown_content = sprintf( '<div %1$s>%2$s</div>', $widget->get_render_attribute_string( 'dropdown_content' ), $dropdown_content );

        $content =   sprintf( '<div %1$s>%2$s</div>', $widget->get_render_attribute_string( 'dropdown_content_wrapper' ), $dd_tigger_element.$dd_content_element );
    }


}
