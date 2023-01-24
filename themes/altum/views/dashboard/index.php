<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="mb-6">
        <div class="row justify-content-between">
            <?php if(settings()->links->biolinks_is_enabled): ?>
            <div class="col-12 col-lg-4 mb-3">
                <div class="card h-100 position-relative">
                    <div class="card-body d-flex">
                        <div>
                            <div class="card border-0 bg-primary-100 mr-3 position-static">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <a href="<?= url('links?type=biolink') ?>" class="stretched-link text-primary-600">
                                        <i class="fa fa-fw fa-hashtag fa-lg"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= nr($data->biolinks_total) ?></div>
                            <span class="text-muted"><?= l('dashboard.biolinks') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif ?>

            <?php if(settings()->links->shortener_is_enabled): ?>
                <div class="col-12 col-lg-4 mb-3">
                    <div class="card h-100 position-relative">
                        <div class="card-body d-flex">
                            <div>
                                <div class="card border-0 bg-primary-100 mr-3 position-static">
                                    <div class="p-3 d-flex align-items-center justify-content-between">
                                        <a href="<?= url('links?type=link') ?>" class="stretched-link text-primary-600">
                                            <i class="fa fa-fw fa-link fa-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="card-title h4 m-0"><?= nr($data->links_total) ?></div>
                                <span class="text-muted"><?= l('dashboard.links') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <?php if(settings()->links->files_is_enabled): ?>
                <div class="col-12 col-lg-4 mb-3">
                    <div class="card h-100 position-relative">
                        <div class="card-body d-flex">
                            <div>
                                <div class="card border-0 bg-primary-100 mr-3 position-static">
                                    <div class="p-3 d-flex align-items-center justify-content-between">
                                        <a href="<?= url('links?type=file') ?>" class="stretched-link text-primary-600">
                                            <i class="fa fa-fw fa-file fa-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="card-title h4 m-0"><?= nr($data->file_links_total) ?></div>
                                <span class="text-muted"><?= l('dashboard.file_links') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <?php if(settings()->links->vcards_is_enabled): ?>
                <div class="col-12 col-lg-4 mb-3">
                    <div class="card h-100 position-relative">
                        <div class="card-body d-flex">
                            <div>
                                <div class="card border-0 bg-primary-100 mr-3 position-static">
                                    <div class="p-3 d-flex align-items-center justify-content-between">
                                        <a href="<?= url('links?type=vcard') ?>" class="stretched-link text-primary-600">
                                            <i class="fa fa-fw fa-id-card fa-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="card-title h4 m-0"><?= nr($data->vcard_links_total) ?></div>
                                <span class="text-muted"><?= l('dashboard.vcard_links') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <?php if(settings()->links->qr_codes_is_enabled): ?>
            <div class="col-12 col-lg-4 mb-3">
                <div class="card h-100 position-relative">
                    <div class="card-body d-flex">
                        <div>
                            <div class="card border-0 bg-primary-100 mr-3 position-static">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <a href="<?= url('qr-codes') ?>" class="stretched-link text-primary-600">
                                        <i class="fa fa-fw fa-qrcode fa-lg text-primary-600"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= nr($data->qr_codes_total) ?></div>
                            <span class="text-muted"><?= l('dashboard.qr_codes') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif ?>

            <?php if(settings()->links->domains_is_enabled): ?>
                <div class="col-12 col-lg-4 mb-3">
                    <div class="card h-100 position-relative">
                        <div class="card-body d-flex">
                            <div>
                                <div class="card border-0 bg-primary-100 mr-3 position-static">
                                    <div class="p-3 d-flex align-items-center justify-content-between">
                                        <a href="<?= url('domains') ?>" class="stretched-link text-primary-600">
                                            <i class="fa fa-fw fa-globe fa-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="card-title h4 m-0"><?= nr($data->domains_total) ?></div>
                                <span class="text-muted"><?= l('dashboard.domains') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>

        <?php if($data->links_chart): ?>
            <div class="card mt-4">
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="clicks_chart"></canvas>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>

    <?= $this->views['links_content'] ?>
</div>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/Chart.bundle.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/chartjs_defaults.js' ?>"></script>

<script>
    if(document.getElementById('clicks_chart')) {
        let clicks_chart = document.getElementById('clicks_chart').getContext('2d');

        let gradient = clicks_chart.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(56, 178, 172, 0.6)');
        gradient.addColorStop(1, 'rgba(56, 178, 172, 0.05)');

        let gradient_white = clicks_chart.createLinearGradient(0, 0, 0, 250);
        gradient_white.addColorStop(0, 'rgba(56,62,178,0.6)');
        gradient_white.addColorStop(1, 'rgba(56, 62, 178, 0.05)');

        new Chart(clicks_chart, {
            type: 'line',
            data: {
                labels: <?= $data->links_chart['labels'] ?? '[]' ?>,
                datasets: [
                    {
                        label: <?= json_encode(l('link.statistics.pageviews')) ?>,
                        data: <?= $data->links_chart['pageviews'] ?? '[]' ?>,
                        backgroundColor: gradient,
                        borderColor: '#38B2AC',
                        fill: true
                    },
                    {
                        label: <?= json_encode(l('link.statistics.visitors')) ?>,
                        data: <?= $data->links_chart['visitors'] ?? '[]' ?>,
                        backgroundColor: gradient_white,
                        borderColor: '#383eb2',
                        fill: true
                    }
                ]
            },
            options: chart_options
        });
    }
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

