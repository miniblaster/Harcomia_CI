<?php defined('ALTUMCODE') || die() ?>

<div class="row mb-4">
    <div class="col d-flex align-items-center">
        <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-chart-bar text-primary-900 mr-2"></i> <?= sprintf(l('admin_statistics.header')) ?></h1>

        <div class="ml-2">
            <span data-toggle="tooltip" title="<?= l('admin_statistics.subheader') ?>">
                <i class="fa fa-fw fa-info-circle text-muted"></i>
            </span>
        </div>
    </div>

    <div class="col-auto d-flex align-items-center">
        <button
                id="daterangepicker"
                type="button"
                class="btn btn-sm btn-outline-secondary"
                data-max-date="<?= \Altum\Date::get('', 4) ?>"
        >
            <i class="fa fa-fw fa-calendar mr-lg-1"></i>
            <span class="d-none d-lg-inline-block">
                <?php if($data->datetime['start_date'] == $data->datetime['end_date']): ?>
                    <?= \Altum\Date::get($data->datetime['start_date'], 2, \Altum\Date::$default_timezone) ?>
                <?php else: ?>
                    <?= \Altum\Date::get($data->datetime['start_date'], 2, \Altum\Date::$default_timezone) . ' - ' . \Altum\Date::get($data->datetime['end_date'], 2, \Altum\Date::$default_timezone) ?>
                <?php endif ?>
            </span>
            <i class="fa fa-fw fa-caret-down d-none d-lg-inline-block ml-lg-1"></i>
        </button>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="row">
    <div class="mb-5 mb-xl-0 col-12 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="nav flex-column nav-pills">
                    <a class="nav-link <?= $data->type == 'growth' ? 'active' : null ?>" href="<?= url('admin/statistics/growth?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-seedling mr-1"></i> <?= l('admin_statistics.growth.menu') ?></a>
                    <?php if(in_array(settings()->license->type, ['SPECIAL','Extended License'])): ?>
                        <a class="nav-link <?= $data->type == 'payments' ? 'active' : null ?>" href="<?= url('admin/statistics/payments?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= l('admin_statistics.payments.menu') ?></a>
                        <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                            <a class="nav-link <?= $data->type == 'affiliates_commissions' ? 'active' : null ?>" href="<?= url('admin/statistics/affiliates_commissions?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-wallet mr-1"></i> <?= l('admin_statistics.affiliates_commissions.menu') ?></a>
                            <a class="nav-link <?= $data->type == 'affiliates_withdrawals' ? 'active' : null ?>" href="<?= url('admin/statistics/affiliates_withdrawals?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-wallet mr-1"></i> <?= l('admin_statistics.affiliates_withdrawals.menu') ?></a>
                        <?php endif ?>
                    <?php endif ?>
                    <a class="nav-link <?= $data->type == 'links' ? 'active' : null ?>" href="<?= url('admin/statistics/links?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-link mr-1"></i> <?= l('admin_statistics.links.menu') ?></a>
                    <a class="nav-link <?= $data->type == 'track_links' ? 'active' : null ?>" href="<?= url('admin/statistics/track_links?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i> <?= l('admin_statistics.track_links.menu') ?></a>
                    <a class="nav-link <?= $data->type == 'biolinks_blocks' ? 'active' : null ?>" href="<?= url('admin/statistics/biolinks_blocks?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-th-large mr-1"></i> <?= l('admin_statistics.biolinks_blocks.menu') ?></a>
                    <a class="nav-link <?= $data->type == 'projects' ? 'active' : null ?>" href="<?= url('admin/statistics/projects?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-project-diagram mr-1"></i> <?= l('admin_statistics.projects.menu') ?></a>
                    <a class="nav-link <?= $data->type == 'pixels' ? 'active' : null ?>" href="<?= url('admin/statistics/pixels?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-adjust mr-1"></i> <?= l('admin_statistics.pixels.menu') ?></a>
                    <a class="nav-link <?= $data->type == 'qr_codes' ? 'active' : null ?>" href="<?= url('admin/statistics/qr_codes?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-qrcode mr-1"></i> <?= l('admin_statistics.qr_codes.menu') ?></a>
                    <a class="nav-link <?= $data->type == 'domains' ? 'active' : null ?>" href="<?= url('admin/statistics/domains?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-globe mr-1"></i> <?= l('admin_statistics.domains.menu') ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">

        <?php

        /* Load the proper type view */
        $partial = require THEME_PATH . 'views/admin/statistics/partials/' . $data->type . '.php';

        echo $partial->html;

        ?>

    </div>
</div>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/Chart.bundle.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/admin_chartjs_defaults.js' ?>"></script>

<script>
    'use strict';

    moment.tz.setDefault(<?= json_encode($this->user->timezone) ?>);

    /* Daterangepicker */
    $('#daterangepicker').daterangepicker({
        startDate: <?= json_encode($data->datetime['start_date']) ?>,
        endDate: <?= json_encode($data->datetime['end_date']) ?>,
        minDate: $('#daterangepicker').data('min-date'),
        maxDate: $('#daterangepicker').data('max-date'),
        ranges: {
            <?= json_encode(l('global.date.today')) ?>: [moment(), moment()],
            <?= json_encode(l('global.date.yesterday')) ?>: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            <?= json_encode(l('global.date.last_7_days')) ?>: [moment().subtract(6, 'days'), moment()],
            <?= json_encode(l('global.date.last_30_days')) ?>: [moment().subtract(29, 'days'), moment()],
            <?= json_encode(l('global.date.this_month')) ?>: [moment().startOf('month'), moment().endOf('month')],
            <?= json_encode(l('global.date.last_month')) ?>: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            <?= json_encode(l('global.date.all_time')) ?>: [moment('2015-01-01'), moment()]
        },
        alwaysShowCalendars: true,
        linkedCalendars: false,
        singleCalendar: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {

        /* Redirect */
        redirect(`<?= url('admin/statistics/' . $data->type) ?>?start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

    });

    let css = window.getComputedStyle(document.body)
</script>

<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php ob_start() ?>
<?= $partial->javascript ?>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
