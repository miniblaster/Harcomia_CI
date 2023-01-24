<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="is_enabled"><?= l('admin_settings.mollie.is_enabled') ?></label>
            <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->mollie->is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                <option value="0" <?= !settings()->mollie->is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="api_key"><?= l('admin_settings.mollie.api_key') ?></label>
            <input id="api_key" type="text" name="api_key" class="form-control form-control-lg" value="<?= settings()->mollie->api_key ?>" />
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
