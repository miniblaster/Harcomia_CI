<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="is_enabled"><?= l('admin_settings.crypto_com.is_enabled') ?></label>
            <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->crypto_com->is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                <option value="0" <?= !settings()->crypto_com->is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="publishable_key"><?= l('admin_settings.crypto_com.publishable_key') ?></label>
            <input id="publishable_key" type="text" name="publishable_key" class="form-control form-control-lg" value="<?= settings()->crypto_com->publishable_key ?>" />
        </div>

        <div class="form-group">
            <label for="secret_key"><?= l('admin_settings.crypto_com.secret_key') ?></label>
            <input id="secret_key" type="text" name="secret_key" class="form-control form-control-lg" value="<?= settings()->crypto_com->secret_key ?>" />
        </div>

        <div class="form-group">
            <label for="webhook_secret"><?= l('admin_settings.crypto_com.webhook_secret') ?></label>
            <input id="webhook_secret" type="text" name="webhook_secret" class="form-control form-control-lg" value="<?= settings()->crypto_com->webhook_secret ?>" />
        </div>

        <div class="form-group">
            <label for="webhook_url"><?= l('admin_settings.payment.webhook_url') ?></label>
            <input type="text" id="webhook_url" value="<?= SITE_URL . 'webhook-crypto-com' ?>" class="form-control" onclick="this.select();" readonly="readonly" />
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
