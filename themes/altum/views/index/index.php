<?php defined('ALTUMCODE') || die() ?>

<div class="index-container">
    <?= $this->views['index_menu'] ?>

    <div class="container my-9">
        <?= \Altum\Alerts::output_alerts() ?>

        <div class="row">
            <div class="col">
                <div class="text-left">
                    <h1 class="index-header mb-4"><?= l('index.header') ?></h1>
                    <p class="index-subheader mb-5"><?= l('index.subheader') ?></p>

                    <div class="d-flex flex-column flex-lg-row">
                        <a href="<?= url('register') ?>" class="btn btn-primary index-button mb-3 mb-lg-0 mr-lg-3"><?= l('index.sign_up') ?></a>
                        <?php //ALTUMCODE:DEMO if(!DEMO): ?>
                        <a href="<?= url('example') ?>" target="_blank" class="btn btn-outline-dark index-button mb-3 mb-lg-0"><?= l('index.example') ?> <i class="fa fa-fw fa-xs fa-external-link-alt"></i></a>
                        <?php //ALTUMCODE:DEMO endif ?>
                    </div>
                </div>
            </div>

            <div class="d-none d-lg-block col">
                <img src="<?= ASSETS_FULL_URL . 'images/hero.png' ?>" class="index-image" loading="lazy" />
            </div>
        </div>
    </div>
</div>

<?php if(settings()->links->biolinks_is_enabled): ?>
<div class="container mt-8">
    <div class="row">
        <div class="col-lg-7 mb-5">
            <img src="<?= ASSETS_FULL_URL . 'images/index/bio-link.png' ?>" class="img-fluid shadow" loading="lazy" />
        </div>

        <div class="col-lg-5 mb-5 d-flex align-items-center">
            <div>
                <span class="fa-stack fa-2x">
                  <i class="fa fa-circle fa-stack-2x text-primary-100"></i>
                  <i class="fa fa-users fa-stack-1x text-primary"></i>
                </span>

                <h2 class="mt-3"><?= l('index.presentation1.header') ?></h2>
                <p class="mt-3"><?= l('index.presentation1.subheader') ?></p>

                <ul class="list-style-none mt-4">
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation1.feature1') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation1.feature2') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation1.feature3') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation1.feature4') ?></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endif ?>

<?php if(settings()->links->shortener_is_enabled): ?>
<div class="container mt-8">
    <div class="row">
        <div class="col-lg-5 mb-5 d-flex align-items-center order-1 order-lg-0">
            <div>
                <span class="fa-stack fa-2x">
                  <i class="fa fa-circle fa-stack-2x text-primary-100"></i>
                  <i class="fa fa-link fa-stack-1x text-primary"></i>
                </span>

                <h2 class="mt-3"><?= l('index.presentation2.header') ?></h2>
                <p class="mt-3"><?= l('index.presentation2.subheader') ?></p>

                <ul class="list-style-none mt-4">
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation2.feature1') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation2.feature2') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation2.feature3') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation2.feature4') ?></div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-7 mb-5 order-0 order-lg-1">
            <img src="<?= ASSETS_FULL_URL . 'images/index/short-link.png' ?>" class="img-fluid shadow" loading="lazy" />
        </div>
    </div>
</div>
<?php endif ?>

<?php if(settings()->links->qr_codes_is_enabled): ?>
<div class="container mt-8">
    <div class="row">
        <div class="col-lg-7 mb-5">
            <img src="<?= ASSETS_FULL_URL . 'images/index/qr-code.png' ?>" class="img-fluid shadow" loading="lazy" />
        </div>

        <div class="col-lg-5 mb-5 d-flex align-items-center">
            <div>
                <span class="fa-stack fa-2x">
                  <i class="fa fa-circle fa-stack-2x text-primary-100"></i>
                  <i class="fa fa-qrcode fa-stack-1x text-primary"></i>
                </span>

                <h2 class="mt-3"><?= l('index.presentation3.header') ?></h2>
                <p class="mt-3"><?= l('index.presentation3.subheader') ?></p>

                <ul class="list-style-none mt-4">
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation3.feature1') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation3.feature2') ?></div>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw fa-sm fa-check-circle text-primary mr-3"></i>
                        <div><?= l('index.presentation3.feature3') ?></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endif ?>

<div class="container mt-8">
    <div class="row">
        <div class="col-lg-5 mb-5 d-flex align-items-center order-1 order-lg-0">
            <div>
                <span class="fa-stack fa-2x">
                    <i class="fa fa-circle fa-stack-2x text-primary-100"></i>
                    <i class="fa fa-chart-bar fa-stack-1x text-primary"></i>
                </span>

                <h2 class="mt-3"><?= l('index.presentation4.header') ?></h2>

                <p class="mt-3"><?= l('index.presentation4.subheader') ?></p>
            </div>
        </div>

        <div class="col-lg-7 mb-5 order-0 order-lg-1">
            <img src="<?= ASSETS_FULL_URL . 'images/index/analytics.png' ?>" class="img-fluid shadow" loading="lazy" />
        </div>
    </div>
</div>

<div class="index-background-one py-7 mt-8">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-12 col-lg-3 mb-4 mb-lg-0">
                <div class="card border-0">
                    <div class="card-body text-center d-flex flex-column">
                        <span class="font-weight-bold text-muted mb-3"><?= l('index.stats.links') ?></span>
                        <span class="h2"><?= nr($data->total_links) . '+' ?></span>
                    </div>
                </div>
            </div>

            <?php if(settings()->links->qr_codes_is_enabled): ?>
            <div class="col-12 col-lg-3 mb-4 mb-lg-0">
                <div class="card border-0">
                    <div class="card-body text-center d-flex flex-column">
                        <span class="font-weight-bold text-muted mb-3"><?= l('index.stats.qr_codes') ?></span>
                        <span class="h2"><?= nr($data->total_qr_codes) . '+' ?></span>
                    </div>
                </div>
            </div>
            <?php endif ?>

            <div class="col-12 col-lg-3 mb-4 mb-lg-0">
                <div class="card border-0">
                    <div class="card-body text-center d-flex flex-column">
                        <span class="font-weight-bold text-muted mb-3"><?= l('index.stats.track_links') ?></span>
                        <span class="h2"><?= nr($data->total_track_links) . '+' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-8">
    <div class="row">
        <?php if(settings()->links->files_is_enabled): ?>
        <div class="col-12 col-lg-4 mb-4">
            <div class="card d-flex flex-column justify-content-between h-100">
                <div class="card-body">
                    <div class="mb-2 bg-gray-100 p-3 rounded">
                        <i class="fa fa-fw fa-lg fa-file text-gray mr-3"></i>
                        <span class="h5"><?= l('index.file_links.header') ?></span>
                    </div>

                    <span class="text-muted"><?= l('index.file_links.subheader') ?></span>
                </div>
            </div>
        </div>
        <?php endif ?>

        <?php if(settings()->links->vcards_is_enabled): ?>
            <div class="col-12 col-lg-4 mb-4">
                <div class="card d-flex flex-column justify-content-between h-100">
                    <div class="card-body">
                        <div class="mb-2 bg-gray-100 p-3 rounded">
                            <i class="fa fa-fw fa-lg fa-id-card text-gray mr-3"></i>
                            <span class="h5"><?= l('index.vcard_links.header') ?></span>
                        </div>

                        <span class="text-muted"><?= l('index.vcard_links.subheader') ?></span>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <?php if(settings()->tools->is_enabled): ?>
            <div class="col-12 col-lg-4 mb-4">
                <div class="card d-flex flex-column justify-content-between h-100">
                    <div class="card-body">
                        <div class="mb-2 bg-gray-100 p-3 rounded">
                            <i class="fa fa-fw fa-lg fa-id-card text-gray mr-3"></i>
                            <span class="h5"><?= l('index.tools.header') ?></span>
                        </div>

                        <span class="text-muted"><?= sprintf(l('index.tools.subheader'), count(array_filter((array) settings()->tools->available_tools))) ?></span>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <div class="col-12 col-lg-4 mb-4">
            <div class="card d-flex flex-column justify-content-between h-100">
                <div class="card-body">
                    <div class="mb-2 bg-gray-100 p-3 rounded">
                        <i class="fa fa-fw fa-lg fa-adjust text-gray mr-3"></i>
                        <span class="h5"><?= l('index.pixels.header') ?></span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <?php foreach(require APP_PATH . 'includes/pixels.php' as $item): ?>
                            <span data-toggle="tooltip" title="<?= $item['name'] ?>"><i class="<?= $item['icon'] ?> fa-fw mx-1" style="color: <?= $item['color'] ?>"></i></span>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if(settings()->links->domains_is_enabled): ?>
        <div class="col-12 col-lg-4 mb-4">
            <div class="card d-flex flex-column justify-content-between h-100">
                <div class="card-body">
                    <div class="mb-2 bg-gray-100 p-3 rounded">
                        <i class="fa fa-fw fa-lg fa-globe text-gray mr-3"></i>
                        <span class="h5"><?= l('index.domains.header') ?></span>
                    </div>

                    <span class="text-muted"><?= l('index.domains.subheader') ?></span>
                </div>
            </div>
        </div>
        <?php endif ?>

        <div class="col-12 col-lg-4 mb-4">
            <div class="card d-flex flex-column justify-content-between h-100">
                <div class="card-body">
                    <div class="mb-2 bg-gray-100 p-3 rounded">
                        <i class="fa fa-fw fa-lg fa-project-diagram text-gray mr-3"></i>
                        <span class="h5"><?= l('index.projects.header') ?></span>
                    </div>

                    <span class="text-muted"><?= l('index.projects.subheader') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-8">
    <div class="text-center mb-5">
        <h2><?= l('index.pricing.header') ?></h2>
        <p class="text-muted"><?= l('index.pricing.subheader') ?></p>
    </div>

    <?= $this->views['plans'] ?>
</div>
