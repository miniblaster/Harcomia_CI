<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="biolinks_is_enabled"><?= l('admin_settings.links.biolinks_is_enabled') ?></label>
        <select id="biolinks_is_enabled" name="biolinks_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->biolinks_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->biolinks_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.links.biolinks_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="branding"><?= l('admin_settings.links.branding') ?></label>
        <textarea id="branding" name="branding" class="form-control form-control-lg"><?= settings()->links->branding ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.links.branding_help') ?></small>
    </div>

    <div class="form-group">
        <label for="shortener_is_enabled"><?= l('admin_settings.links.shortener_is_enabled') ?></label>
        <select id="shortener_is_enabled" name="shortener_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->shortener_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->shortener_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.links.shortener_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="files_is_enabled"><?= l('admin_settings.links.files_is_enabled') ?></label>
        <select id="files_is_enabled" name="files_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->files_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->files_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.links.files_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="vcards_is_enabled"><?= l('admin_settings.links.vcards_is_enabled') ?></label>
        <select id="vcards_is_enabled" name="vcards_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->vcards_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->vcards_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.links.vcards_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="events_is_enabled"><?= l('admin_settings.links.events_is_enabled') ?></label>
        <select id="events_is_enabled" name="events_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->events_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->events_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.links.events_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="qr_codes_is_enabled"><?= l('admin_settings.links.qr_codes_is_enabled') ?></label>
        <select id="qr_codes_is_enabled" name="qr_codes_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->qr_codes_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->qr_codes_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.links.qr_codes_is_enabled_help') ?></small>
    </div>

    <hr class="my-4">

    <div class="form-group">
        <label for="directory_is_enabled"><?= l('admin_settings.links.directory_is_enabled') ?></label>
        <select id="directory_is_enabled" name="directory_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->directory_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->directory_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.links.directory_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="directory_display"><?= l('admin_settings.links.directory_display') ?></label>
        <select id="directory_display" name="directory_display" class="form-control form-control-lg">
            <option value="all" <?= settings()->links->directory_display == 'all' ? 'selected="selected"' : null ?>><?= l('admin_settings.links.directory_display_all') ?></option>
            <option value="verified" <?= settings()->links->directory_display == 'verified' ? 'selected="selected"' : null ?>><?= l('admin_settings.links.directory_display_verified') ?></option>
        </select>
    </div>

    <hr class="my-4">

    <div class="form-group">
        <label for="domains_is_enabled"><?= l('admin_settings.links.domains_is_enabled') ?></label>
        <select id="domains_is_enabled" name="domains_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->domains_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->domains_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.links.domains_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="main_domain_is_enabled"><?= l('admin_settings.links.main_domain_is_enabled') ?></label>
        <select id="main_domain_is_enabled" name="main_domain_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->main_domain_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->main_domain_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.links.main_domain_is_enabled_help') ?></small>
    </div>

    <hr class="my-4">

    <div class="form-group">
        <label for="blacklisted_domains"><?= l('admin_settings.links.blacklisted_domains') ?></label>
        <textarea id="blacklisted_domains" class="form-control form-control-lg" name="blacklisted_domains"><?= settings()->links->blacklisted_domains ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.links.blacklisted_domains_help') ?></small>
    </div>

    <div class="form-group">
        <label for="blacklisted_keywords"><?= l('admin_settings.links.blacklisted_keywords') ?></label>
        <textarea id="blacklisted_keywords" class="form-control form-control-lg" name="blacklisted_keywords"><?= settings()->links->blacklisted_keywords ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.links.blacklisted_keywords_help') ?></small>
    </div>

    <hr class="my-4">

    <div class="form-group">
        <label for="avatar_size_limit"><?= l('admin_settings.links.avatar_size_limit') ?></label>
        <input id="avatar_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="avatar_size_limit" class="form-control form-control-lg" value="<?= settings()->links->avatar_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="background_size_limit"><?= l('admin_settings.links.background_size_limit') ?></label>
        <input id="background_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="background_size_limit" class="form-control form-control-lg" value="<?= settings()->links->background_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="favicon_size_limit"><?= l('admin_settings.links.favicon_size_limit') ?></label>
        <input id="favicon_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="favicon_size_limit" class="form-control form-control-lg" value="<?= settings()->links->favicon_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="seo_image_size_limit"><?= l('admin_settings.links.seo_image_size_limit') ?></label>
        <input id="seo_image_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="seo_image_size_limit" class="form-control form-control-lg" value="<?= settings()->links->seo_image_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="thumbnail_image_size_limit"><?= l('admin_settings.links.thumbnail_image_size_limit') ?></label>
        <input id="thumbnail_image_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="thumbnail_image_size_limit" class="form-control form-control-lg" value="<?= settings()->links->thumbnail_image_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="image_size_limit"><?= l('admin_settings.links.image_size_limit') ?></label>
        <input id="image_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="image_size_limit" class="form-control form-control-lg" value="<?= settings()->links->image_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="audio_size_limit"><?= l('admin_settings.links.audio_size_limit') ?></label>
        <input id="audio_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="audio_size_limit" class="form-control form-control-lg" value="<?= settings()->links->audio_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="video_size_limit"><?= l('admin_settings.links.video_size_limit') ?></label>
        <input id="video_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="video_size_limit" class="form-control form-control-lg" value="<?= settings()->links->video_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="file_size_limit"><?= l('admin_settings.links.file_size_limit') ?></label>
        <input id="file_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="file_size_limit" class="form-control form-control-lg" value="<?= settings()->links->file_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="product_file_size_limit"><?= l('admin_settings.links.product_file_size_limit') ?></label>
        <input id="product_file_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="product_file_size_limit" class="form-control form-control-lg" value="<?= settings()->links->product_file_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.size_limit_help') ?></small>
    </div>

    <hr class="my-4">

    <p class="h5"><?= l('admin_settings.links.google_safe_browsing') ?></p>
    <p class="text-muted"><?= l('admin_settings.links.google_safe_browsing_help') ?></p>

    <div class="form-group">
        <label for="google_safe_browsing_is_enabled"><?= l('admin_settings.links.google_safe_browsing_is_enabled') ?></label>
        <select id="google_safe_browsing_is_enabled" name="google_safe_browsing_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->google_safe_browsing_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->google_safe_browsing_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="google_safe_browsing_api_key"><?= l('admin_settings.links.google_safe_browsing_api_key') ?></label>
        <input id="google_safe_browsing_api_key" type="text" name="google_safe_browsing_api_key" class="form-control form-control-lg" value="<?= settings()->links->google_safe_browsing_api_key ?>" />
    </div>

    <hr class="my-4">

    <p class="h5"><?= l('admin_settings.links.google_static_maps') ?></p>
    <p class="text-muted"><?= l('admin_settings.links.google_static_maps_help') ?></p>

    <div class="form-group">
        <label for="google_static_maps_is_enabled"><?= l('admin_settings.links.google_static_maps_is_enabled') ?></label>
        <select id="google_static_maps_is_enabled" name="google_static_maps_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->links->google_static_maps_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->links->google_static_maps_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="google_static_maps_api_key"><?= l('admin_settings.links.google_static_maps_api_key') ?></label>
        <input id="google_static_maps_api_key" type="text" name="google_static_maps_api_key" class="form-control form-control-lg" value="<?= settings()->links->google_static_maps_api_key ?>" />
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
