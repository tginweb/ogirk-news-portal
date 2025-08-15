<?php

namespace SM_Site_Content\Module\Conf;

use SM\Common;


class Module extends Common\Module
{
    function init_events()
    {
        $this->add_filter('sm/context/info');
        $this->add_filter('sm/entity/bundles');

        add_action('add_meta_boxes', [$this, 'addCustomBox']);
        add_action('save_post', [$this, 'onPostSmNoteSave']);

        add_action('rest_api_init', function () {
            register_rest_route('conf', '/send-question', array(
                'methods' => 'POST',
                'callback' => [$this, 'apiSendQuestion']
            ));
        });


    }

    function apiSendQuestion()
    {

        require_once(ABSPATH . WPINC . '/PHPMailer/PHPMailer.php');
        require_once(ABSPATH . WPINC . '/PHPMailer/SMTP.php');

        $request = $_REQUEST;


        $to = 'site@ogirk.ru';
        $subject = 'Вопрос по конференции';


        $message = <<<EOF
Отправитель: {$request['name']}
E-mail: {$request['email']}
Телефон: {$request['phone']}

Сообщение: {$request['message']}
EOF;

        $headers = array(
            'From: OG <og.robot@yandex.ru>',
            // 'content-type: text/html',
            // 'Cc: John Q Codex <jqc@wordpress.org>',
            // 'Cc: iluvwp@wordpress.org', // тут можно использовать только простой email адрес
        );

        wp_mail($to, $subject, $message, $headers);

        wp_send_json([
            'sss' => 11
        ]);
    }

    function addCustomBox()
    {
        $screens = array('sm-conference');
        add_meta_box('sm_questions', 'Вопросы', [$this, 'metaBoxCallback'], $screens, 'normal');
    }

    function metaBoxCallback($parentPost, $meta)
    {
        $screens = $meta['args'];


        $query = [
            'post_type' => 'sm-qa-question',
            'post_parent' => $parentPost->ID,
            'numberposts' => 10000
        ];

        $posts = get_posts($query);

        ?>

        <table class="wp-list-table widefat fixed striped">
            <thead>
            <th>ID</th>
            <th>Дата</th>
            <th>Вопрос</th>
            <th>Ответ</th>
            <th></th>
            </thead>
            <tbody>
            <?php
            foreach ($posts as $post) {
                ?>
                <tr>
                    <td><?php print $post->ID; ?></td>
                    <td><?php print $post->post_date; ?></td>
                    <td><?php print get_field('question', $post); ?></td>
                    <td><?php print get_field('answer', $post); ?></td>
                    <td style="text-align: right;">
                        <a
                                href="/wp-admin/post.php?post=<?php print $post->ID; ?>&action=edit"
                                target="_blank"
                        >Редактировать</a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>

        <br>

        <a
                class="button-primary"
                href="/wp-admin/post-new.php?post_type=sm-qa-question&set_post_parent=<?php print $parentPost->ID; ?>"
                target="_blank"
        >Добавить</a>

        <?php
    }


    function onPostSmNoteSave($post_id)
    {
        if (wp_is_post_revision($post_id))
            return;

        if ('sm-qa-question' == get_post_type($post_id)) {

            if ($set_post_parent = $_REQUEST['set_post_parent']) {
                remove_action('save_post', [$this, 'onPostSmNoteSave']);

                wp_update_post(array(
                    'ID' => $post_id,
                    'post_parent' => $set_post_parent
                ));

                add_action('save_post', [$this, 'onPostSmNoteSave']);
            }
        }
    }

    function _filter_sm_context_info(&$contexts)
    {
        return $contexts + $this->sm_class_set([

            ]);
    }

    function _filter_sm_entity_bundles($bundles)
    {
        return $bundles + $this->sm_class_set([
                'post:sm-conference' => array(
                    'label' => 'Конференции',
                    'labels' => array('singular_name' => 'Конференция'),
                    'register' => true,
                    'public' => true,
                    'has_archive' => true,
                    'hierarchical' => false,
                    'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
                ),
            ]);
    }
}

