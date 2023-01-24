<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="user_new"><?= l('admin_settings.webhooks.user_new') ?></label>
        <input id="user_new" type="url" name="user_new" class="form-control form-control-lg" value="<?= settings()->webhooks->user_new ?>" />
        <small class="form-text text-muted"><?= sprintf(l('admin_settings.webhooks.help'), '<code>' . implode('</code>, <code>', ['user_id', 'email', 'name', 'source']) . '</code>') ?></small>
    </div>

    <div class="form-group">
        <label for="user_delete"><?= l('admin_settings.webhooks.user_delete') ?></label>
        <input id="user_delete" type="url" name="user_delete" class="form-control form-control-lg" value="<?= settings()->webhooks->user_delete ?>" />
        <small class="form-text text-muted"><?= sprintf(l('admin_settings.webhooks.help'), '<code>' . implode('</code>, <code>', ['user_id', 'email', 'name']) . '</code>') ?></small>
    </div>

    <div class="form-group">
        <label for="payment_new"><?= l('admin_settings.webhooks.payment_new') ?></label>
        <input id="payment_new" type="url" name="payment_new" class="form-control form-control-lg" value="<?= settings()->webhooks->payment_new ?>" />
        <small class="form-text text-muted"><?= sprintf(l('admin_settings.webhooks.help'), '<code>' . implode('</code>, <code>', ['user_id', 'email', 'name', 'plan_id', 'plan_expiration_date', 'payment_id', 'payment_processor', 'payment_type', 'payment_frequency', 'payment_total_amount', 'payment_currency', 'payment_code']) . '</code>') ?></small>
    </div>

    <div class="form-group">
        <label for="code_redeemed"><?= l('admin_settings.webhooks.code_redeemed') ?></label>
        <input id="code_redeemed" type="url" name="code_redeemed" class="form-control form-control-lg" value="<?= settings()->webhooks->code_redeemed ?>" />
        <small class="form-text text-muted"><?= sprintf(l('admin_settings.webhooks.help'), '<code>' . implode('</code>, <code>', ['user_id', 'email', 'name', 'plan_id', 'plan_expiration_date', 'code_id', 'code', 'code_name', 'redeemed_days']) . '</code>') ?></small>
    </div>

    <div class="form-group">
        <label for="contact"><?= l('admin_settings.webhooks.contact') ?></label>
        <input id="contact" type="url" name="contact" class="form-control form-control-lg" value="<?= settings()->webhooks->contact ?>" />
        <small class="form-text text-muted"><?= sprintf(l('admin_settings.webhooks.help'), '<code>' . implode('</code>, <code>', ['name', 'email', 'subject', 'message']) . '</code>') ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
