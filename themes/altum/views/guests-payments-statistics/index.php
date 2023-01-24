<?php defined('ALTUMCODE') || die() ?>

<?php $payment_processors = require APP_PATH . 'includes/payment_processors.php'; ?>

<section class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('guests-payments') ?>"><?= l('guests_payments.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('guests_payments_statistics.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 text-truncate m-0"><?= sprintf(l('guests_payments_statistics.header'), $data->biolink_block->settings->name) ?></h1>

        <div class="d-flex align-items-center col-auto p-0">
            <button
                    id="daterangepicker"
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    data-min-date="<?= \Altum\Date::get($data->biolink_block->datetime, 4) ?>"
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

    <?php if(count($data->guests_payments)): ?>
        <div class="card">
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="guests_payments_chart"></canvas>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('guests_payments_statistics.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('guests_payments_statistics.no_data') ?></h2>
                </div>
            </div>
        </div>
    <?php endif ?>
</section>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/Chart.bundle.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/chartjs_defaults.js' ?>"></script>

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
            <?= json_encode(l('global.date.all_time')) ?>: [moment($('#daterangepicker').data('min-date')), moment()]
        },
        alwaysShowCalendars: true,
        linkedCalendars: false,
        singleCalendar: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {

        /* Redirect */
        redirect(`<?= url('guests-payments-statistics?biolink_block_id=' . $data->biolink_block->biolink_block_id) ?>&start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

    });

    <?php if(count($data->guests_payments)): ?>

    let css = window.getComputedStyle(document.body)

    /* Orders chart */
    let guests_payments_chart = document.getElementById('guests_payments_chart').getContext('2d');

    let total_amount_color = '#38B2AC';
    let total_amount_gradient = guests_payments_chart.createLinearGradient(0, 0, 0, 250);
    total_amount_gradient.addColorStop(0, 'rgba(56, 178, 172, 0.6)');
    total_amount_gradient.addColorStop(1, 'rgba(56, 178, 172, 0.05)');

    let payments_color = '#383eb2';
    let payments_gradient = guests_payments_chart.createLinearGradient(0, 0, 0, 250);
    payments_gradient.addColorStop(0, 'rgba(56,62,178,0.6)');
    payments_gradient.addColorStop(1, 'rgba(56, 62, 178, 0.05)');

    /* Display chart */
    new Chart(guests_payments_chart, {
        type: 'line',
        data: {
            labels: <?= $data->guests_payments_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(l('guests_payments_statistics.payments_label')) ?>,
                    data: <?= $data->guests_payments_chart['payments'] ?? '[]' ?>,
                    backgroundColor: payments_gradient,
                    borderColor: payments_color,
                    fill: true
                },
                {
                    label: <?= json_encode(l('guests_payments_statistics.total_amount_label')) ?>,
                    data: <?= $data->guests_payments_chart['total_amount'] ?? '[]' ?>,
                    backgroundColor: total_amount_gradient,
                    borderColor: total_amount_color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });
    <?php endif ?>
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
</div>
