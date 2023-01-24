<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="is_enabled"><?= l('admin_settings.twitter.is_enabled') ?></label>
        <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->twitter->is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->twitter->is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="consumer_api_key"><?= l('admin_settings.twitter.consumer_api_key') ?></label>
        <input id="consumer_api_key" type="text" name="consumer_api_key" class="form-control form-control-lg" value="<?= settings()->twitter->consumer_api_key ?>" />
    </div>

    <div class="form-group">
        <label for="consumer_api_secret"><?= l('admin_settings.twitter.consumer_api_secret') ?></label>
        <input id="consumer_api_secret" type="text" name="consumer_api_secret" class="form-control form-control-lg" value="<?= settings()->twitter->consumer_api_secret ?>" />
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
