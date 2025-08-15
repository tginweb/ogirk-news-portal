<?php

add_filter('smart/compiler/eval/regexp_issues_slugs', function($value)
{
    $slugs = array();

    foreach (sm()->entity()->controller('term', 'sm-issue')->select() as $term)
    {
        $slugs[] = $term->slug;
    }

    return join('|',  $slugs);
});

