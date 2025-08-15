<?php

class sm_entity_object_post_sm_issue_print extends sm_entity_object_post
{

    function get_issue()
    {
        return $this->get_term('sm-issue');
    }

    function get_issue_name()
    {
        if ($term_issue = $this->get_issue())
        {
            return $term_issue->label();
        }
    }

    function get_issue_posts($args=array())
    {
        $args += array(
            'numberposts' => -1,
            'meta_key'    => 'sm_issue_print',
            'meta_value'  => $this->id()
        );

        return _get_posts($args);
    }


}
