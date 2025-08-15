<?php

class sm_entity_object_term_sm_issue extends sm_entity_object_term
{
    public function get_last_issue_print()
    {
        $posts = sm()->entity()->get_posts(array(
            'numberposts' => 1,
            'post_type'   => 'sm-issue-print',
            'tax_query'   => array(array('taxonomy' => 'sm-issue', 'field' => 'term_id', 'terms' => $this->id())),
        ));

        if (!empty($posts)) return current($posts);
    }
}

