<?php


class sm_entity_object_post_Sm_law_doc extends sm_entity_object_post
{
    function get_index_links()
    {

        $links[] = sm::tpl('link', array('text'=>'просмотреть', 'path'=>$this->get_url()));

        if ($file = $this->get_file_by_field('sd_file_pdf'))
        {
            $links[] = sm::tpl('link', array('text'=>'скачать PDF', 'path'=>$file->get_file_url_abs()));
        }

        if ($file = $this->get_file_by_field('sd_file_text'))
        {
            $links[] = sm::tpl('link', array('text'=>'скачать текст', 'path'=>$file->get_file_url_abs()));
        }

        return $links;
    }

}


