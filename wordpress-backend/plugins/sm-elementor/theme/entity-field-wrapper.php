<?php

add_action('sm/theme/entity/field/wrapper', function($value, &$entity, &$settings, &$rendered=0) {

    if ($rendered++>0) return $value;

    if ($settings['label'])
    {
        $value = '<span class="entity-field-value">'.$value.'</span>';

        $value = '<label class="entity-field-label">'.$settings['label'].'</label>'.$value;
    }


    if ($settings['link_url'] && $settings['link_intag']==='yes') $value = '<a href="'.$settings['link_url'].'">'.$value.'</a>';

    if (!$settings['nowrapper'])
    {
        $value = \SM\Util\Html::tag($settings['field_tag'], $settings['field_attrs'], $value);
    }

    if ($settings['link_url'] && $settings['link_intag']!=='yes') $value = '<a href="'.$settings['link_url'].'">'.$value.'</a>';

    return $value;

}, 10, 4);