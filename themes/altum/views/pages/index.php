<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url() ?>"><?= l('index.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('pages.index.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="d-flex align-items-center">
        <h1 class="h3 m-0"><?= l('pages.header') ?></h1>

        <div class="ml-2">
            <span data-toggle="tooltip" title="<?= l('pages.subheader') ?>">
                <i class="fa fa-fw fa-info-circle text-muted"></i>
            </span>
        </div>
    </div>

    <?php if($data->pages_categories_result->num_rows): ?>
        <div class="mt-4">
            <h2 class="h4 mb-4"><?= l('pages.index.pages_categories.header') ?></h2>

            <div class="row">
                <?php while($row = $data->pages_categories_result->fetch_object()): ?>

                    <div class="col-12 col-md-4 mb-4">
                        <a href="<?= SITE_URL . ($row->language ? \Altum\Language::$active_languages[$row->language] . '/' : null) . 'pages/' . $row->url ?>" class="text-decoration-none">
                            <div class="card bg-gray-50 border-0 h-100 p-3">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <?php if(!empty($row->icon)): ?>
                                        <span class="round-circle-lg bg-primary-100 text-primary p-3 mb-4"><i class="<?= $row->icon ?> fa-2x"></i></span>
                                    <?php endif ?>

                                    <div class="h5"><?= $row->title ?></div>
                                </div>
                            </div>
                        </a>
                    </div>

                <?php endwhile ?>
            </div>
        </div>
    <?php endif ?>

    <?php if($data->popular_pages_result->num_rows): ?>
        <div class="mt-4">
            <h2 class="h4 mb-4"><?= l('pages.index.popular_pages') ?></h2>

            <div class="row">
                <?php while($row = $data->popular_pages_result->fetch_object()): ?>

                    <div class="col-12 col-md-6 mb-4">
                        <a href="<?= $row->type == 'internal' ? SITE_URL . ($row->language ? \Altum\Language::$active_languages[$row->language] . '/' : null) . 'page/' . $row->url : $row->url ?>" target="<?= $row->type == 'internal' ? '_self' : '_blank' ?>" class="text-decoration-none">
                            <div class="card bg-gray-50 border-0 h-100 p-3">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <div class="h5"><?= $row->title ?></div>

                                    <span class="text-muted text-center"><?= $row->description ?></span>
                                </div>
                            </div>
                        </a>
                    </div>

                <?php endwhile ?>
            </div>
        </div>
    <?php endif ?>
</div>



