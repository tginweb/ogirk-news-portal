<?php

class com_admin_sm_filter_issue_print extends _sm_class
{
    static function init()
    {
        add_action('restrict_manage_posts', function() use (&$ctrl)
        {
            global $typenow;

            if( $typenow == 'post' )
            {
                print '<div class="manage-posts-filter filter-issue-print">'.sm_renderer::render(['filter_acf[sm-issue-print]'=>array(
                    '#type'        => 'post_select',
                    '#post_type'   => 'sm-issue-print',
                    '#placeholder' => 'выпуск',
                    '#value'       => $_REQUEST['filter_acf']['sm-issue-print'],
                    '#container'   => 'div',
                    '#size'        => 30
                )]).'</div>';
            }
        });

        add_filter( 'parse_query', function ( &$query ) use (&$ctrl)
        {
            if (is_admin()  && 'edit.php' === $GLOBALS['pagenow'])
            {
                //fb($_REQUEST['filter_acf']);
               // die('s');
                if ($query->query_vars['post_type']=='post' && $_REQUEST['filter_acf']['sm-issue-print'])
                {
                    $query->query_vars['meta_query'][] = array(
                        'key'   => 'sm_issue_print',
                        'value' => $_REQUEST['filter_acf']['sm-issue-print'],
                    );
                }
            }

        });
    }
}
