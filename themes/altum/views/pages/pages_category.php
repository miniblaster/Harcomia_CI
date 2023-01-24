<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url() ?>"><?= l('index.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li><a href="<?= url('pages') ?>"><?= l('pages.index.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('pages.pages_category.breadcrumb') ?></li>
        </ol>
    </nav>

    <h1 class="h3"><?= $data->pages_category->title ?></h1>

    <?php if($data->pages_result->num_rows): ?>
        <div class="mt-4">
            <div class="row">
                <?php while($row = $data->pages_result->fetch_object()): ?>

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
