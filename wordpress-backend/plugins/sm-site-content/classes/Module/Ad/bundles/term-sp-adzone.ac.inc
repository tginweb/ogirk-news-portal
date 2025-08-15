<?php


class econtroller_term_sm_adzone extends _econtroller_term
{

}

class entity_term_sm_adzone extends _entity_term
{
    function get_style_vars()
    {
        $result = array();

        $vars = array(
            'ad_width',
            'ad_height',
            'ad_image_size',
            'ad_style_bg',
            'ad_style_center',
            'ad_style_border',
            'ad_style_padding',
        );

        foreach ($vars as $vname)
        {
            $val = $this->field($vname);

            if (strlen($val) && $val!='_')
            {
                $result[$vname] = $val;
            }
        }

        return $result;
    }

    function render($vars)
    {
        $ads = $this->get_zone_ads();

        $vars += $this->get_style_vars();

        foreach ($ads as $ad)
        {
            $items[] = $ad->render($vars);
        }


        if (count($items))
        {
            $attrs = array();
            $attrs['class'][] = 'view-ad-zone';
            $attrs['class'][] = 'ad-zone-'.$this->slug();

            if ($this->field('adzone_width'))  $attrs['style']['with']   = $this->field('adzone_width');
            if ($this->field('adzone_height')) $attrs['style']['height'] = $this->field('adzone_height');

            return SM\Util\html::tag('div', $attrs, join('', $items));
        }
        else
        {
            return '';
        }
    }

    function get_zone_ads()
    {
        return _get_posts(array(
            'numberposts' => 10,
            'post_type'   => 'sm-ad',
            'tax_query'   => array(array('taxonomy' => 'sm-adzone', 'field' => 'term_id', 'terms' => $this->id())),
        ));
    }

}

if (smart_class_can_load('_econtroller_term_sm_adzone', __FILE__)) { class _econtroller_term_sm_adzone extends econtroller_term_sm_adzone { } }
if (smart_class_can_load('_entity_term_sm_adzone', __FILE__)) { class _entity_term_sm_adzone extends entity_term_sm_adzone { } }
