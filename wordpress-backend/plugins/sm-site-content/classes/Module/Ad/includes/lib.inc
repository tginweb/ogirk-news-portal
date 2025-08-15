<?php

function sm_content_ad_zone($slug)
{
    $zone = _get_term($slug, 'sm-adzone');

    return $zone;
}

function sm_content_ad_zone_render($slug, $vars=array())
{
    if (!($zone = sm_content_ad_zone($slug))) return;

    return $zone->render($vars);
}
