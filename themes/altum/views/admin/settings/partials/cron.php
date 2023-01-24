<?php defined('ALTUMCODE') || die() ?>

<div>
    <label for="cron"><?= l('admin_settings.cron.cron') ?></label>
    <div class="input-group mb-3">
        <input id="cron" name="cron" type="text" class="form-control form-control-lg" value="<?= '* * * * * wget --quiet -O /dev/null ' . SITE_URL . 'cron?key=' . settings()->cron->key ?>" readonly="readonly" />
        <div class="input-group-append">
            <span class="input-group-text" data-toggle="tooltip" title="<?= sprintf(l('admin_settings.cron.last_execution'), isset(settings()->cron->cron_datetime) ? \Altum\Date::get_timeago(settings()->cron->cron_datetime) : '-') ?>">
                <i class="fa fa-fw fa-calendar text-muted"></i>
            </span>
        </div>
    </div>
</div>
