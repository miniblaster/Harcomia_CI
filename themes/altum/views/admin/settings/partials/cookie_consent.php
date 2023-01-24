<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="is_enabled"><?= l('admin_settings.cookie_consent.is_enabled') ?></label>
        <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->cookie_consent->is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->cookie_consent->is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.cookie_consent.is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="logging_is_enabled"><?= l('admin_settings.cookie_consent.logging_is_enabled') ?></label>
        <select id="logging_is_enabled" name="logging_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->cookie_consent->logging_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->cookie_consent->logging_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.cookie_consent.logging_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="necessary_is_enabled"><?= l('admin_settings.cookie_consent.necessary_is_enabled') ?></label>
        <select id="necessary_is_enabled" name="necessary_is_enabled" class="form-control form-control-lg" disabled="disabled">
            <option value="1" <?= settings()->cookie_consent->necessary_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->cookie_consent->necessary_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="analytics_is_enabled"><?= l('admin_settings.cookie_consent.analytics_is_enabled') ?></label>
        <select id="analytics_is_enabled" name="analytics_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->cookie_consent->analytics_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->cookie_consent->analytics_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="targeting_is_enabled"><?= l('admin_settings.cookie_consent.targeting_is_enabled') ?></label>
        <select id="targeting_is_enabled" name="targeting_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->cookie_consent->targeting_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->cookie_consent->targeting_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="layout"><?= l('admin_settings.cookie_consent.layout') ?></label>
        <select id="layout" name="layout" class="form-control form-control-lg">
            <option value="cloud" <?= settings()->cookie_consent->layout == 'cloud' ? 'selected="selected"' : null ?>>Cloud</option>
            <option value="box" <?= settings()->cookie_consent->layout == 'box' ? 'selected="selected"' : null ?>>Box</option>
            <option value="bar" <?= settings()->cookie_consent->layout == 'bar' ? 'selected="selected"' : null ?>>Bar</option>
        </select>
    </div>

    <div class="form-group">
        <label for="position_y"><?= l('admin_settings.cookie_consent.position_y') ?></label>
        <select id="position_y" name="position_y" class="form-control form-control-lg">
            <option value="top" <?= settings()->cookie_consent->position_y == 'top' ? 'selected="selected"' : null ?>>Top</option>
            <option value="middle" <?= settings()->cookie_consent->position_y == 'middle' ? 'selected="selected"' : null ?>>Middle</option>
            <option value="bottom" <?= settings()->cookie_consent->position_y == 'bottom' ? 'selected="selected"' : null ?>>Bottom</option>
        </select>
    </div>

    <div class="form-group">
        <label for="position_x"><?= l('admin_settings.cookie_consent.position_x') ?></label>
        <select id="position_x" name="position_x" class="form-control form-control-lg">
            <option value="left" <?= settings()->cookie_consent->position_x == 'left' ? 'selected="selected"' : null ?>>Left</option>
            <option value="center" <?= settings()->cookie_consent->position_x == 'center' ? 'selected="selected"' : null ?>>Center</option>
            <option value="right" <?= settings()->cookie_consent->position_x == 'right' ? 'selected="selected"' : null ?>>Right</option>
        </select>
    </div>

    <?php if(settings()->cookie_consent->logging_is_enabled && file_exists(UPLOADS_PATH . 'cookie_consent/data.csv')): ?>
        <a href="<?= url('admin/settings/cookie_consent?export=csv') ?>" target="_blank" class="btn btn-lg btn-block btn-outline-info mt-4"><?= l('admin_settings.cookie_consent.logging_download') ?></a>
    <?php endif ?>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
