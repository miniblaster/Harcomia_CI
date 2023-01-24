<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 text-truncate"><?= l('link.statistics.header') ?></h2>

    <div class="d-flex align-items-center col-auto p-0">
        <div data-toggle="tooltip" title="<?= l('global.reset') ?>">
            <button
                    type="button"
                    class="btn btn-link text-secondary"
                    data-toggle="modal"
                    data-target="#link_statistics_reset_modal"
                    aria-label="<?= l('global.reset') ?>"
                    data-link-id="<?= $data->link->link_id ?>"
                    data-start-date="<?= $data->datetime['start_date'] ?>"
                    data-end-date="<?= $data->datetime['end_date'] ?>"
            >
                <i class="fa fa-fw fa-sm fa-redo"></i>
            </button>
        </div>

        <button
                id="daterangepicker"
                type="button"
                class="btn btn-sm btn-outline-secondary"
                data-min-date="<?= \Altum\Date::get($data->link->datetime, 4) ?>"
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

<?php if(!$data->has_data): ?>
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column align-items-center justify-content-center py-3">
                <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('link.statistics.no_data') ?>" />
                <h2 class="h4 text-muted"><?= l('link.statistics.no_data') ?></h2>
                <p class="text-muted m-0"><?= l('link.statistics.no_data_help') ?></p>
            </div>
        </div>
    </div>
<?php else: ?>

    <ul class="account-header-navbar mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'overview' ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=overview&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-list mr-1"></i>
                <?= l('link.statistics.overview') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'entries' ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=entries&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i>
                <?= l('link.statistics.entries') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'country' ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=country&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-globe mr-1"></i>
                <?= l('link.statistics.country') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'city_name' ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=city_name&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-city mr-1"></i>
                <?= l('link.statistics.city_name') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= in_array($data->type, ['referrer_host', 'referrer_path']) ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=referrer_host&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-random mr-1"></i>
                <?= l('link.statistics.referrer_host') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'device' ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=device&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-laptop mr-1"></i>
                <?= l('link.statistics.device') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'os' ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=os&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-server mr-1"></i>
                <?= l('link.statistics.os') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'browser' ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=browser&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-window-restore mr-1"></i>
                <?= l('link.statistics.browser') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'language' ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=language&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-language mr-1"></i>
                <?= l('link.statistics.language') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= in_array($data->type, ['utm_source', 'utm_medium', 'utm_campaign']) ? 'active' : null ?>" href="<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=utm_source&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fa fa-fw fa-sm fa-link mr-1"></i>
                <?= l('link.statistics.utms') ?>
            </a>
        </li>
    </ul>

    <?= $this->views['statistics'] ?>

<?php endif ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>

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
        redirect(`<?= url((isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/' . $data->method . '?type=' . $data->type) ?>&start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/statistics_reset_modal.php', ['modal_id' => 'link_statistics_reset_modal', 'resource_id' => 'link_id', 'path' => (isset($data->link->biolink_block_id) ? 'biolink-block/' . $data->link->biolink_block_id : 'link/' . $data->link->link_id) . '/statistics/reset']), 'modals'); ?>
