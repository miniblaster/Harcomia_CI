<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url() ?>"><?= l('index.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li><a href="<?= url('blog') ?>"><?= l('blog.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <?php if($data->blog_posts_category): ?>
                <li><a href="<?= url('blog/category/' . $data->blog_posts_category->url) ?>"><?= $data->blog_posts_category->title ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <?php endif ?>
            <li class="active" aria-current="page"><?= $data->blog_post->title ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">

                    <h1 class="h3 mb-1"><?= $data->blog_post->title ?></h1>
                    <p class="small text-muted">
                        <span><?= sprintf(l('global.datetime_tooltip'), \Altum\Date::get($data->blog_post->datetime, 2)) ?></span> |

                        <?php if($data->blog_posts_category): ?>
                            <a href="<?= SITE_URL . ($data->blog_posts_category->language ? \Altum\Language::$active_languages[$data->blog_posts_category->language] . '/' : null) . 'blog/category/' . $data->blog_posts_category->url ?>" class="text-muted"><?= $data->blog_posts_category->title ?></a> |
                        <?php endif ?>

                        <span><?= sprintf(l('blog.total_views'), nr($data->blog_post->total_views)) ?></span>

                        <?php $estimated_reading_time = string_estimate_reading_time($data->blog_post->content) ?>
                        <?php if($estimated_reading_time->minutes > 0 || $estimated_reading_time->seconds > 0): ?>
                            <span>|
                                <?= $estimated_reading_time->minutes ? sprintf(l('blog.estimated_reading_time'), $estimated_reading_time->minutes . ' ' . l('global.date.minutes')) : null ?>
                                <?= $estimated_reading_time->minutes == 0 && $estimated_reading_time->seconds ? sprintf(l('blog.estimated_reading_time'), $estimated_reading_time->seconds . ' ' . l('global.date.seconds')) : null ?>
                            </span>
                        <?php endif ?>
                    </p>

                    <?php if($data->blog_post->image): ?>
                        <img src="<?= UPLOADS_FULL_URL . 'blog/' . $data->blog_post->image ?>" class="blog-post-image img-fluid w-100 rounded mb-3" />
                    <?php endif ?>

                    <p><?= $data->blog_post->description ?></p>

                    <?= nl2br($data->blog_post->content) ?>

                    <div class="mt-4">
                        <small class="text-muted"><?= sprintf(l('global.last_datetime_tooltip'), \Altum\Date::get($data->blog_post->last_datetime, 2)) ?></small>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-4">
                        <a href="mailto:?body=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                            <i class="fa fa-fw fa-envelope"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                            <i class="fab fa-fw fa-facebook"></i>
                        </a>
                        <a href="https://twitter.com/share?url=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                            <i class="fab fa-fw fa-twitter"></i>
                        </a>
                        <a href="https://pinterest.com/pin/create/link/?url=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                            <i class="fab fa-fw fa-pinterest"></i>
                        </a>
                        <a href="https://linkedin.com/shareArticle?url=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                            <i class="fab fa-fw fa-linkedin"></i>
                        </a>
                        <a href="https://www.reddit.com/submit?url=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                            <i class="fab fa-fw fa-reddit"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= url(\Altum\Router::$original_request) ?>" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                            <i class="fab fa-fw fa-whatsapp"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <?php if(count($data->blog_posts_categories)): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><?= l('blog.categories') ?></h3>

                        <ul class="list-style-none m-0">
                            <?php foreach($data->blog_posts_categories as $blog_post_category): ?>
                                <li class="mb-2">
                                    <a href="<?= SITE_URL . ($blog_post_category->language ? \Altum\Language::$active_languages[$blog_post_category->language] . '/' : null) . 'blog/category/' . $blog_post_category->url ?>"><?= $blog_post_category->title ?></a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            <?php endif ?>

            <?php if(count($data->blog_posts_popular)): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><?= l('blog.popular') ?></h3>

                        <ul class="list-style-none m-0">
                            <?php foreach($data->blog_posts_popular as $blog_post): ?>
                                <li class="mb-2">
                                    <a href="<?= SITE_URL . ($blog_post->language ? \Altum\Language::$active_languages[$blog_post->language] . '/' : null) . 'blog/' . $blog_post->url ?>"><?= $blog_post->title ?></a>
                                    <div class="small">
                                        <?php if($blog_post->blog_posts_category_id && isset($data->blog_posts_categories[$blog_post->blog_posts_category_id])): ?>
                                            <a href="<?= SITE_URL . ($data->blog_posts_categories[$blog_post->blog_posts_category_id]->language ? \Altum\Language::$active_languages[$data->blog_posts_categories[$blog_post->blog_posts_category_id]->language] . '/' : null) . 'blog/category/' . $data->blog_posts_categories[$blog_post->blog_posts_category_id]->url ?>" class="text-muted"><?= $data->blog_posts_categories[$blog_post->blog_posts_category_id]->title ?></a> |
                                        <?php endif ?>

                                        <span class="text-muted"><?= sprintf(l('blog.total_views'), nr($blog_post->total_views)) ?></span>
                                    </div>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
