<?php

namespace SM_Elementor\Module\Query\Filter_Widget;

class Select extends Common\Base {

    static $type_id = 'select';

    function render_control()
    {
        $items = $this->controller->get_source_items();

        foreach ($items as $item)
        {
            if ($item['selected'])
                $options[] = '<option value="'.$item['value'].'" selected>'.$item['label'].'</option>';
            else
                $options[] = '<option value="'.$item['value'].'" >'.$item['label'].'</option>';
        }

        if ($this->settings['multiple'])
        {
            $this->settings['control_attrs']['size'] = $this->settings['select_size'] ?: 10;
        }

        $this->settings['control_attrs']['class'][] = 'filter-input';
        $this->settings['control_attrs']['name'] = $this->controller->name;

        return '<select '.\SM\Util\Html::attributes($this->settings['control_attrs']).'>'.join('', $options).'</select>';
    }
}