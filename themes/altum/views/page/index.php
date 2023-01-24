<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url() ?>"><?= l('index.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li><a href="<?= url('pages') ?>"><?= l('pages.index.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <?php if($data->pages_category): ?>
                <li><a href="<?= url('pages/' . $data->pages_category->url) ?>"><?= $data->pages_category->title ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <?php endif ?>
            <li class="active" aria-current="page"><?= l('page.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
        <h1 class="h3 mb-1"><?= $data->page->title ?></h1>

        <div class="d-print-none col-auto p-0 d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="fa fa-fw fa-sm fa-print"></i> <?= l('page.print') ?></button>
        </div>
    </div>

    <p class="small text-muted">
        <span><?= sprintf(l('global.datetime_tooltip'), \Altum\Date::get($data->page->datetime, 2)) ?></span> |

        <?php if($data->pages_category): ?>
            <a href="<?= SITE_URL . ($data->pages_category->language ? \Altum\Language::$active_languages[$data->pages_category->language] . '/' : null) . 'pages/' . $data->pages_category->url ?>" class="text-muted"><?= $data->pages_category->title ?></a> |
        <?php endif ?>

        <span><?= sprintf(l('page.total_views'), nr($data->page->total_views)) ?></span>

        <?php $estimated_reading_time = string_estimate_reading_time($data->page->content) ?>
        <?php if($estimated_reading_time->minutes > 0 || $estimated_reading_time->seconds > 0): ?>
            <span>|
                <?= $estimated_reading_time->minutes ? sprintf(l('page.estimated_reading_time'), $estimated_reading_time->minutes . ' ' . l('global.date.minutes')) : null ?>
                <?= $estimated_reading_time->minutes == 0 && $estimated_reading_time->seconds ? sprintf(l('page.estimated_reading_time'), $estimated_reading_time->seconds . ' ' . l('global.date.seconds')) : null ?>
            </span>
        <?php endif ?>
    </p>

    <p><?= $data->page->description ?></p>

    <?= nl2br($data->page->content) ?>

    <div class="mt-4">
        <small class="text-muted"><?= sprintf(l('global.last_datetime_tooltip'), \Altum\Date::get($data->page->last_datetime, 2)) ?></small>
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
