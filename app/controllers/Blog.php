<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Language;
use Altum\Meta;
use Altum\Models\BlogPosts;
use Altum\Models\BlogPostsCategories;
use Altum\Title;

class Blog extends Controller {

    public function index() {

        if(!settings()->main->blog_is_enabled) {
            redirect('not-found');
        }

        $language = Language::$name;

        /* Blog post */
        if(isset($this->params[0]) && $this->params[0] != 'category') {
            $url = query_clean($this->params[0]);

            $blog_post = database()->query("
                SELECT * 
                FROM `blog_posts`
                WHERE (`url` = '{$url}' AND `language` = '{$language}') OR (`url` = '{$url}' AND `language` IS NULL)
                ORDER BY `language` DESC
            ")->fetch_object() ?? null;

            if(!$blog_post) {
                redirect('not-found');
            }

            /* Get the blog post category */
            $blog_posts_category = db()->where('blog_posts_category_id', $blog_post->blog_posts_category_id)->getOne('blog_posts_categories');

            /* Add a new view to the post */
            db()->where('blog_post_id', $blog_post->blog_post_id)->update('blog_posts', ['total_views' => db()->inc()]);

            /* Set a custom title */
            Title::set(sprintf(l('blog.blog_post.title'), $blog_post->title));

            /* Meta */
            Meta::set_description($blog_post->description);
            if($blog_post->image) {
                Meta::set_social_url(SITE_URL . \Altum\Router::$language_code . '/' . \Altum\Router::$original_request);
                Meta::set_social_image(\Altum\Uploads::get_full_url('blog') . $blog_post->image);
            }

            /* Get all the categories */
            $blog_posts_categories = (new BlogPostsCategories())->get_blog_posts_categories_by_language($language);

            /* Get popular posts */
            $blog_posts_popular = (new BlogPosts())->get_popular_blog_posts_by_language($language);

            /* Prepare the View */
            $data = [
                'blog_post' => $blog_post,
                'blog_posts_category' => $blog_posts_category,
                'blog_posts_categories' => $blog_posts_categories,
                'blog_posts_popular' => $blog_posts_popular,
            ];

            $view = new \Altum\View('blog/blog_post', (array) $this);

            $this->add_view_content('content', $view->run($data));
        }

        /* Blog category */
        else if(isset($this->params[0], $this->params[1]) && $this->params[0] == 'category') {
            $url = query_clean($this->params[1]);

            $blog_posts_category = database()->query("
                SELECT * 
                FROM `blog_posts_categories`
                WHERE (`url` = '{$url}' AND `language` = '{$language}') OR (`url` = '{$url}' AND `language` IS NULL)
                ORDER BY `language` DESC
            ")->fetch_object() ?? null;

            if(!$blog_posts_category) {
                redirect('not-found');
            }

            /* Get the posts */
            /* Prepare the filtering system */
            $filters = (new \Altum\Filters());
            $filters->set_default_order_by('datetime', settings()->main->default_order_type);
            $filters->set_default_results_per_page(settings()->main->default_results_per_page);

            /* Prepare the paginator */
            $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `blog_posts` WHERE `blog_posts_category_id` = {$blog_posts_category->blog_posts_category_id} AND `language` = '{$language}' OR `language` IS NULL {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
            $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('blog/category/' . $blog_posts_category->url . '?' . $filters->get_get() . '&page=%d')));

            /* Blog posts query */
            $blog_posts_result = database()->query("
                SELECT * 
                FROM `blog_posts`
                WHERE `blog_posts_category_id` = {$blog_posts_category->blog_posts_category_id} AND (`language` = '{$language}' OR `language` IS NULL) {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                {$paginator->get_sql_limit()}
            ");

            /* Iterate over the blog posts */
            $blog_posts = [];

            while($row = $blog_posts_result->fetch_object()) {
                $blog_posts[] = $row;
            }

            /* Prepare the pagination view */
            $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

            /* Get all the categories */
            $blog_posts_categories = (new BlogPostsCategories())->get_blog_posts_categories_by_language($language);

            /* Get popular posts */
            $blog_posts_popular = (new BlogPosts())->get_popular_blog_posts_by_language($language);

            /* Set a custom title */
            Title::set(sprintf(l('blog.blog_posts_category.title'), $blog_posts_category->title));

            /* Meta */
            Meta::set_description($blog_posts_category->description);

            /* Prepare the View */
            $data = [
                'blog_posts_category' => $blog_posts_category,
                'blog_posts' => $blog_posts,
                'pagination' => $pagination,
                'blog_posts_categories' => $blog_posts_categories,
                'blog_posts_popular' => $blog_posts_popular,
            ];

            $view = new \Altum\View('blog/blog_posts_category', (array) $this);

            $this->add_view_content('content', $view->run($data));
        }

        /* Blog index */
        else {

            /* Get the posts */
            /* Prepare the filtering system */
            $filters = (new \Altum\Filters());
            $filters->set_default_order_by('datetime', settings()->main->default_order_type);
            $filters->set_default_results_per_page(settings()->main->default_results_per_page);

            /* Prepare the paginator */
            $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `blog_posts` WHERE `language` = '{$language}' OR `language` IS NULL {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
            $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('blog?' . $filters->get_get() . '&page=%d')));

            /* Blog posts query */
            $blog_posts_result = database()->query("
                SELECT * 
                FROM `blog_posts`
                WHERE (`language` = '{$language}' OR `language` IS NULL) {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                {$paginator->get_sql_limit()}
            ");

            /* Iterate over the blog posts */
            $blog_posts = [];

            while($row = $blog_posts_result->fetch_object()) {
                $blog_posts[] = $row;
            }

            /* Prepare the pagination view */
            $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

            /* Get all the categories */
            $blog_posts_categories = (new BlogPostsCategories())->get_blog_posts_categories_by_language($language);

            /* Get popular posts */
            $blog_posts_popular = (new BlogPosts())->get_popular_blog_posts_by_language($language);

            /* Prepare the View */
            $data = [
                'blog_posts' => $blog_posts,
                'pagination' => $pagination,
                'filters' => $filters,
                'blog_posts_categories' => $blog_posts_categories,
                'blog_posts_popular' => $blog_posts_popular,
            ];

            $view = new \Altum\View('blog/index', (array) $this);

            $this->add_view_content('content', $view->run($data));
        }
    }

}
