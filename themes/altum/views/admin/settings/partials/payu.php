<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="is_enabled"><?= l('admin_settings.payu.is_enabled') ?></label>
            <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->payu->is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                <option value="0" <?= !settings()->payu->is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="mode"><?= l('admin_settings.payu.mode') ?></label>
            <select id="mode" name="mode" class="form-control form-control-lg">
                <option value="secure" <?= settings()->payu->mode == 'secure' ? 'selected="selected"' : null ?>>secure</option>
                <option value="sandbox" <?= settings()->payu->mode == 'sandbox' ? 'selected="selected"' : null ?>>sandbox</option>
            </select>
        </div>

        <div class="form-group">
            <label for="merchant_pos_id"><?= l('admin_settings.payu.merchant_pos_id') ?></label>
            <input id="merchant_pos_id" type="text" name="merchant_pos_id" class="form-control form-control-lg" value="<?= settings()->payu->merchant_pos_id ?>" />
        </div>

        <div class="form-group">
            <label for="signature_key"><?= l('admin_settings.payu.signature_key') ?></label>
            <input id="signature_key" type="text" name="signature_key" class="form-control form-control-lg" value="<?= settings()->payu->signature_key ?>" />
        </div>

        <div class="form-group">
            <label for="oauth_client_id"><?= l('admin_settings.payu.oauth_client_id') ?></label>
            <input id="oauth_client_id" type="text" name="oauth_client_id" class="form-control form-control-lg" value="<?= settings()->payu->oauth_client_id ?>" />
        </div>

        <div class="form-group">
            <label for="oauth_client_secret"><?= l('admin_settings.payu.oauth_client_secret') ?></label>
            <input id="oauth_client_secret" type="text" name="oauth_client_secret" class="form-control form-control-lg" value="<?= settings()->payu->oauth_client_secret ?>" />
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
