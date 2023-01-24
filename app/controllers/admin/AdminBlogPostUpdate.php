<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;

class AdminBlogPostUpdate extends Controller {

    public function index() {

        $blog_post_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Check if resource exists */
        if(!$blog_post = db()->where('blog_post_id', $blog_post_id)->getOne('blog_posts')) {
            redirect('admin/blog-posts');
        }

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['url'] = get_slug($_POST['url']);
            $_POST['title'] = input_clean($_POST['title']);
            $_POST['description'] = input_clean($_POST['description']);
            $_POST['editor'] = in_array($_POST['editor'], ['wysiwyg', 'raw']) ? input_clean($_POST['editor']) : 'raw';
            $_POST['blog_posts_category_id'] = empty($_POST['blog_posts_category_id']) ? null : (int) $_POST['blog_posts_category_id'];
            $_POST['language'] = !empty($_POST['language']) ? input_clean($_POST['language']) : null;

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['title', 'url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(db()->where('blog_post_id', $blog_post->blog_post_id, '<>')->where('url', $_POST['url'])->where('language', $_POST['language'])->has('blog_posts')) {
                Alerts::add_field_error('url', l('admin_blog.error_message.url_exists'));
            }

            $blog_post->image = \Altum\Uploads::process_upload($blog_post->image, 'blog', 'image', 'image_remove', null);

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->where('blog_post_id', $blog_post->blog_post_id)->update('blog_posts', [
                    'blog_posts_category_id' => $_POST['blog_posts_category_id'],
                    'url' => $_POST['url'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'image' => $blog_post->image,
                    'editor' => $_POST['editor'],
                    'content' => $_POST['content'],
                    'language' => $_POST['language'],
                    'last_datetime' => \Altum\Date::$date,
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['title'] . '</strong>'));

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('blog_posts');

                redirect('admin/blog-post-update/' . $blog_post_id);
            }
        }

        /* Get the blog posts categories available */
        $blog_posts_categories = db()->get('blog_posts_categories', null, ['blog_posts_category_id', 'title']);

        /* Main View */
        $data = [
            'blog_posts_categories' => $blog_posts_categories,
            'blog_post' => $blog_post,
        ];

        $view = new \Altum\View('admin/blog-post-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
