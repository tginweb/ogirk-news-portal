<?php

class sm_entity_controller_post_sm_gallery extends sm_entity_controller_post
{
    public function bundle_link1($url, $entity)
    {
        if ( 'sm-gallery' == get_post_type( $entity ) )
        {
            return  '/gallery?gallery_id='.$entity->ID;
        }

        return $url;
    }
}


class sm_entity_object_post_sm_gallery extends sm_entity_object_post
{

}