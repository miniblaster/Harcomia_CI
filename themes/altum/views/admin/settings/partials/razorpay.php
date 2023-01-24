<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="is_enabled"><?= l('admin_settings.razorpay.is_enabled') ?></label>
            <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->razorpay->is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                <option value="0" <?= !settings()->razorpay->is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="key_id"><?= l('admin_settings.razorpay.key_id') ?></label>
            <input id="key_id" type="text" name="key_id" class="form-control form-control-lg" value="<?= settings()->razorpay->key_id ?>" />
        </div>

        <div class="form-group">
            <label for="key_secret"><?= l('admin_settings.razorpay.key_secret') ?></label>
            <input id="key_secret" type="text" name="key_secret" class="form-control form-control-lg" value="<?= settings()->razorpay->key_secret ?>" />
        </div>

        <div class="form-group">
            <label for="webhook_secret"><?= l('admin_settings.razorpay.webhook_secret') ?></label>
            <input id="webhook_secret" type="text" name="webhook_secret" class="form-control form-control-lg" value="<?= settings()->razorpay->webhook_secret ?>" />
        </div>

        <div class="form-group">
            <label for="webhook_url"><?= l('admin_settings.payment.webhook_url') ?></label>
            <input type="text" id="webhook_url" value="<?= SITE_URL . 'webhook-razorpay' ?>" class="form-control" onclick="this.select();" readonly="readonly" />
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
