<?php

namespace SM_Elementor\Module\Query\Filter_Widget;

class Links extends Common\Base {

    static $type_id = 'links';

    function render_control()
    {
        $items = $this->controller->get_source_items();

        $base_item_attrs = $this->settings['control_item_attrs'];

        $base_item_attrs['class'][] = 'control-item';

        $base_item_attrs['href'] = '#';

        foreach ($items as $item)
        {
            $item_attrs = $base_item_attrs;

            if (!$item['value']) continue;

            $item_attrs['data-value'] = $item['value'];

            if ($item['selected'])
                $item_attrs['data-selected'] = '';

            if (!empty($item['link']))
            {
                $item_attrs['href'] = $item['link'];
            }
            else
            {
                $item_attrs['href'] = '#';
            }

            $options[] = '<a '.\SM\Util\Html::attributes($item_attrs).'>'.$item['label'].'</a>';
        }

        if ($this->settings['multiple'])
        {
            $this->settings['control_attrs']['data-multiple'] = true;
        }


        return '<div '.\SM\Util\Html::attributes($this->settings['control_attrs']).'>'.join('', $options).'</div>';
    }
}