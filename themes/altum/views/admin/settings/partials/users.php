<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="register_is_enabled"><i class="fa fa-fw fa-sm fa-user-plus text-muted mr-1"></i> <?= l('admin_settings.users.register_is_enabled') ?></label>
        <select id="register_is_enabled" name="register_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->users->register_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->users->register_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="email_confirmation"><i class="fa fa-fw fa-sm fa-envelope text-muted mr-1"></i> <?= l('admin_settings.users.email_confirmation') ?></label>
        <select id="email_confirmation" name="email_confirmation" class="form-control form-control-lg">
            <option value="1" <?= settings()->users->email_confirmation ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->users->email_confirmation ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.users.email_confirmation_help') ?></small>
    </div>

    <div class="form-group">
        <label for="auto_delete_inactive_users"><i class="fa fa-fw fa-sm fa-users-slash text-muted mr-1"></i> <?= l('admin_settings.users.auto_delete_inactive_users') ?></label>
        <input id="auto_delete_inactive_users" type="number" min="0" name="auto_delete_inactive_users" class="form-control form-control-lg" value="<?= settings()->users->auto_delete_inactive_users ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.users.auto_delete_inactive_users_help') ?></small>
    </div>

    <div class="form-group">
        <label for="user_deletion_reminder"><i class="fa fa-fw fa-sm fa-calendar-minus text-muted mr-1"></i> <?= l('admin_settings.users.user_deletion_reminder') ?></label>
        <input id="user_deletion_reminder" type="text" max="<?= settings()->users->auto_delete_inactive_users - 1 ?>" name="user_deletion_reminder" class="form-control form-control-lg" value="<?= settings()->users->user_deletion_reminder ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.users.user_deletion_reminder_help') ?></small>
    </div>

    <div class="form-group">
        <label for="blacklisted_domains"><i class="fa fa-fw fa-sm fa-ban text-muted mr-1"></i> <?= l('admin_settings.users.blacklisted_domains') ?></label>
        <textarea id="blacklisted_domains" name="blacklisted_domains" class="form-control form-control-lg"><?= settings()->users->blacklisted_domains ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.users.blacklisted_domains_help') ?></small>
    </div>

    <div class="form-group">
        <label for="blacklisted_countries"><i class="fa fa-fw fa-sm fa-user-slash text-muted mr-1"></i> <?= l('admin_settings.users.blacklisted_countries') ?></label>
        <select id="blacklisted_countries" name="blacklisted_countries[]" class="form-control form-control-lg" multiple="multiple">
            <?php foreach(get_countries_array() as $key => $value): ?>
                <option value="<?= $key ?>" <?= in_array($key, settings()->users->blacklisted_countries ?? []) ? 'selected="selected"' : null ?>><?= $value ?></option>
            <?php endforeach ?>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.users.blacklisted_countries_help') ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
