<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="title"><i class="fa fa-fw fa-sm fa-heading text-muted mr-1"></i> <?= l('admin_settings.main.title') ?></label>
        <input id="title" type="text" name="title" class="form-control form-control-lg" value="<?= settings()->main->title ?>" />
    </div>

    <div class="form-group">
        <label for="default_language"><i class="fa fa-fw fa-sm fa-language text-muted mr-1"></i> <?= l('admin_settings.main.default_language') ?></label>
        <select id="default_language" name="default_language" class="form-control form-control-lg">
            <?php foreach(\Altum\Language::$active_languages as $language_name => $language_code) echo '<option value="' . $language_name . '" ' . (settings()->main->default_language == $language_name ? 'selected="selected"' : null) . '>' . $language_name . ' - ' . $language_code . '</option>' ?>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.main.default_language_help') ?></small>
    </div>

    <div class="form-group">
        <label for="default_theme_style"><i class="fa fa-fw fa-sm fa-fill-drip text-muted mr-1"></i> <?= l('admin_settings.main.default_theme_style') ?></label>
        <select id="default_theme_style" name="default_theme_style" class="form-control form-control-lg">
            <?php foreach(\Altum\ThemeStyle::$themes as $key => $value) echo '<option value="' . $key . '" ' . (settings()->main->default_theme_style == $key ? 'selected="selected"' : null) . '>' . $key . '</option>' ?>
        </select>
    </div>

    <div class="form-group">
        <label for="logo_light"><i class="fa fa-fw fa-sm fa-sun text-muted mr-1"></i> <?= l('admin_settings.main.logo_light') ?></label>
        <?php if(!empty(settings()->main->logo_light)): ?>
            <div class="m-1">
                <img src="<?= UPLOADS_FULL_URL . 'main/' . settings()->main->logo_light ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
            </div>
            <div class="custom-control custom-checkbox my-2">
                <input id="logo_light_remove" name="logo_light_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#logo_light').classList.add('d-none') : document.querySelector('#logo_light').classList.remove('d-none')">
                <label class="custom-control-label" for="logo_light_remove">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
        <?php endif ?>
        <input id="logo_light" type="file" name="logo_light" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('logo_light') ?>" class="form-control-file altum-file-input" />
        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('logo_light')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
    </div>

    <div class="form-group">
        <label for="logo_dark"><i class="fa fa-fw fa-sm fa-moon text-muted mr-1"></i> <?= l('admin_settings.main.logo_dark') ?></label>
        <?php if(!empty(settings()->main->logo_dark)): ?>
            <div class="m-1">
                <img src="<?= UPLOADS_FULL_URL . 'main/' . settings()->main->logo_dark ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
            </div>
            <div class="custom-control custom-checkbox my-2">
                <input id="logo_dark_remove" name="logo_dark_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#logo_dark').classList.add('d-none') : document.querySelector('#logo_dark').classList.remove('d-none')">
                <label class="custom-control-label" for="logo_dark_remove">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
        <?php endif ?>
        <input id="logo_dark" type="file" name="logo_dark" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('logo_dark') ?>" class="form-control-file altum-file-input" />
        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('logo_dark')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
    </div>

    <div class="form-group">
        <label for="logo_email"><i class="fa fa-fw fa-sm fa-envelope text-muted mr-1"></i> <?= l('admin_settings.main.logo_email') ?></label>
        <?php if(!empty(settings()->main->logo_email)): ?>
            <div class="m-1">
                <img src="<?= UPLOADS_FULL_URL . 'main/' . settings()->main->logo_email ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
            </div>
            <div class="custom-control custom-checkbox my-2">
                <input id="logo_email_remove" name="logo_email_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#logo_email').classList.add('d-none') : document.querySelector('#logo_email').classList.remove('d-none')">
                <label class="custom-control-label" for="logo_email_remove">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
        <?php endif ?>
        <input id="logo_email" type="file" name="logo_email" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('logo_email') ?>" class="form-control-file altum-file-input" />
        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('logo_email')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
    </div>

    <div class="form-group">
        <label for="favicon"><i class="fa fa-fw fa-sm fa-icons text-muted mr-1"></i> <?= l('admin_settings.main.favicon') ?></label>
        <?php if(!empty(settings()->main->favicon)): ?>
            <div class="m-1">
                <img src="<?= UPLOADS_FULL_URL . 'main/' . settings()->main->favicon ?>" class="img-fluid" style="max-height: 32px;height: 32px;" />
            </div>
            <div class="custom-control custom-checkbox my-2">
                <input id="favicon_remove" name="favicon_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#favicon').classList.add('d-none') : document.querySelector('#favicon').classList.remove('d-none')">
                <label class="custom-control-label" for="favicon_remove">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
        <?php endif ?>
        <input id="favicon" type="file" name="favicon" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('favicon') ?>" class="form-control-file altum-file-input" />
        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('favicon')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
    </div>

    <div class="form-group">
        <label for="opengraph"><i class="fa fa-fw fa-sm fa-image text-muted mr-1"></i> <?= l('admin_settings.main.opengraph') ?></label>
        <?php if(!empty(settings()->main->opengraph)): ?>
            <div class="m-1">
                <img src="<?= UPLOADS_FULL_URL . 'main/' . settings()->main->opengraph ?>" class="img-fluid" style="max-height: 5rem;height: 5rem;" />
            </div>
            <div class="custom-control custom-checkbox my-2">
                <input id="opengraph_remove" name="opengraph_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#opengraph').classList.add('d-none') : document.querySelector('#opengraph').classList.remove('d-none')">
                <label class="custom-control-label" for="opengraph_remove">
                    <span class="text-muted"><?= l('global.delete_file') ?></span>
                </label>
            </div>
        <?php endif ?>
        <input id="opengraph" type="file" name="opengraph" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('opengraph') ?>" class="form-control-file altum-file-input" />
        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('opengraph')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
    </div>

    <div class="form-group">
        <label for="default_timezone"><i class="fa fa-fw fa-sm fa-atlas text-muted mr-1"></i> <?= l('admin_settings.main.default_timezone') ?></label>
        <select id="default_timezone" name="default_timezone" class="form-control form-control-lg">
            <?php foreach(DateTimeZone::listIdentifiers() as $timezone) echo '<option value="' . $timezone . '" ' . (settings()->main->default_timezone == $timezone ? 'selected="selected"' : null) . '>' . $timezone . '</option>' ?>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.main.default_timezone_help') ?></small>
    </div>

    <div class="form-group">
        <label for="se_indexing"><i class="fa fa-fw fa-sm fa-search text-muted mr-1"></i> <?= l('admin_settings.main.se_indexing') ?></label>
        <select id="se_indexing" name="se_indexing" class="form-control form-control-lg">
            <option value="1" <?= settings()->main->se_indexing ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->main->se_indexing ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="index_url"><i class="fa fa-fw fa-sm fa-sitemap text-muted mr-1"></i> <?= l('admin_settings.main.index_url') ?></label>
        <input id="index_url" type="text" name="index_url" class="form-control form-control-lg" value="<?= settings()->main->index_url ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.main.index_url_help') ?></small>
    </div>

    <div class="form-group">
        <label for="not_found_url"><i class="fa fa-fw fa-sm fa-compass text-muted mr-1"></i> <?= l('admin_settings.main.not_found_url') ?></label>
        <input id="not_found_url" type="url" name="not_found_url" class="form-control form-control-lg" value="<?= settings()->main->not_found_url ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.main.not_found_url_help') ?></small>
    </div>

    <div class="form-group">
        <label for="terms_and_conditions_url"><i class="fa fa-fw fa-sm fa-file-word text-muted mr-1"></i> <?= l('admin_settings.main.terms_and_conditions_url') ?></label>
        <input id="terms_and_conditions_url" type="text" name="terms_and_conditions_url" class="form-control form-control-lg" value="<?= settings()->main->terms_and_conditions_url ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.main.terms_and_conditions_url_help') ?></small>
    </div>

    <div class="form-group">
        <label for="privacy_policy_url"><i class="fa fa-fw fa-sm fa-file-word text-muted mr-1"></i> <?= l('admin_settings.main.privacy_policy_url') ?></label>
        <input id="privacy_policy_url" type="text" name="privacy_policy_url" class="form-control form-control-lg" value="<?= settings()->main->privacy_policy_url ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.main.privacy_policy_url_help') ?></small>
    </div>

    <div class="form-group">
        <label for="default_results_per_page"><i class="fa fa-fw fa-sm fa-list-ol text-muted mr-1"></i> <?= l('admin_settings.main.default_results_per_page') ?></label>
        <select id="default_results_per_page" name="default_results_per_page" class="form-control form-control-lg">
            <?php foreach([10, 25, 50, 100, 250, 500] as $key): ?>
                <option value="<?= $key ?>" <?= settings()->main->default_results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label for="default_order_type"><i class="fa fa-fw fa-sm fa-sort text-muted mr-1"></i> <?= l('admin_settings.main.default_order_type') ?></label>
        <select id="default_order_type" name="default_order_type" class="form-control form-control-lg">
            <option value="ASC" <?= settings()->main->default_order_type == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
            <option value="DESC" <?= settings()->main->default_order_type == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="auto_language_detection_is_enabled"><i class="fa fa-fw fa-sm fa-language text-muted mr-1"></i> <?= l('admin_settings.main.auto_language_detection_is_enabled') ?></label>
        <select id="auto_language_detection_is_enabled" name="auto_language_detection_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->main->auto_language_detection_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->main->auto_language_detection_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.main.auto_language_detection_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="blog_is_enabled"><i class="fa fa-fw fa-sm fa-blog text-muted mr-1"></i> <?= l('admin_settings.main.blog_is_enabled') ?></label>
        <select id="blog_is_enabled" name="blog_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->main->blog_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
            <option value="0" <?= !settings()->main->blog_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
        </select>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
