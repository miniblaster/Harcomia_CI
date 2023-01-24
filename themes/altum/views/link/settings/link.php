<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>

<div class="card">
    <div class="card-body">

        <form name="update_link" action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
            <input type="hidden" name="request_type" value="update" />
            <input type="hidden" name="type" value="link" />
            <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />

            <div class="notification-container"></div>

            <div class="form-group">
                <label for="location_url"><i class="fa fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('link.settings.location_url') ?></label>
                <input id="location_url" type="text" class="form-control" name="location_url" value="<?= $data->link->location_url ?>" maxlength="2048" required="required" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
            </div>

            <div class="form-group">
                <label><i class="fa fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('link.settings.url') ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <?php if(count($data->domains)): ?>
                            <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                    <option value="" <?= $data->link->domain ? 'selected="selected"' : null ?>><?= SITE_URL ?></option>
                                <?php endif ?>

                                <?php foreach($data->domains as $row): ?>
                                    <option value="<?= $row->domain_id ?>" <?= $data->link->domain && $row->domain_id == $data->link->domain->domain_id ? 'selected="selected"' : null ?>><?= $row->url ?></option>
                                <?php endforeach ?>
                            </select>
                        <?php else: ?>
                            <span class="input-group-text"><?= SITE_URL ?></span>
                        <?php endif ?>
                    </div>

                    <input
                        type="text"
                        class="form-control"
                        name="url"
                        placeholder="<?= l('link.settings.url_placeholder') ?>"
                        value="<?= $data->link->url ?>"
                        maxlength="256"
                        onchange="update_this_value(this, get_slug)"
                        onkeyup="update_this_value(this, get_slug)"
                        <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                        <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                    />
                </div>
                <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
            </div>

            <?php if(count($data->pixels)): ?>
                <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#pixels_container" aria-expanded="false" aria-controls="pixels_container">
                    <i class="fa fa-fw fa-adjust fa-sm mr-1"></i> <?= l('link.settings.pixels_header') ?>
                </button>

                <div class="collapse" id="pixels_container">
                    <div class="form-group">
                        <div class="d-flex flex-column flex-xl-row justify-content-between">
                            <label><i class="fa fa-fw fa-sm fa-adjust text-muted mr-1"></i> <?= l('link.settings.pixels_ids') ?></label>
                            <a href="<?= url('pixels') ?>" target="_blank" class="small mb-2"><i class="fa fa-fw fa-sm fa-plus mr-1"></i> <?= l('pixels.create') ?></a>
                        </div>

                        <div class="row">
                            <?php $available_pixels = require APP_PATH . 'includes/pixels.php'; ?>
                            <?php foreach($data->pixels as $pixel): ?>
                                <div class="col-12 col-lg-6">
                                    <div class="custom-control custom-checkbox my-2">
                                        <input id="pixel_id_<?= $pixel->pixel_id ?>" name="pixels_ids[]" value="<?= $pixel->pixel_id ?>" type="checkbox" class="custom-control-input" <?= in_array($pixel->pixel_id, $data->link->pixels_ids) ? 'checked="checked"' : null ?>>
                                        <label class="custom-control-label d-flex align-items-center" for="pixel_id_<?= $pixel->pixel_id ?>">
                                            <span class="mr-1"><?= $pixel->name ?></span>
                                            <small class="badge badge-light badge-pill"><?= $available_pixels[$pixel->type]['name'] ?></small>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#temporary_url_container" aria-expanded="false" aria-controls="temporary_url_container">
                <i class="fa fa-fw fa-clock fa-sm mr-1"></i> <?= l('link.settings.temporary_url_header') ?>
            </button>

            <div class="collapse" id="temporary_url_container">
                <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
                        <div class="custom-control custom-switch mb-3">
                            <input
                                    id="schedule"
                                    name="schedule"
                                    type="checkbox"
                                    class="custom-control-input"
                                <?= !empty($data->link->start_date) && !empty($data->link->end_date) ? 'checked="checked"' : null ?>
                                <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'disabled="disabled"' ?>
                            >
                            <label class="custom-control-label" for="schedule"><?= l('link.settings.schedule') ?></label>
                            <small class="form-text text-muted"><?= l('link.settings.schedule_help') ?></small>
                        </div>
                    </div>
                </div>

                <div id="schedule_container" style="display: none;">
                    <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                        <div class="<?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label><i class="fa fa-fw fa-clock fa-sm text-muted mr-1"></i> <?= l('link.settings.start_date') ?></label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                name="start_date"
                                                value="<?= \Altum\Date::get($data->link->start_date, 1) ?>"
                                                placeholder="<?= l('link.settings.start_date') ?>"
                                                autocomplete="off"
                                                data-daterangepicker
                                        >
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <label><i class="fa fa-fw fa-clock fa-sm text-muted mr-1"></i> <?= l('link.settings.end_date') ?></label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                name="end_date"
                                                value="<?= \Altum\Date::get($data->link->end_date, 1) ?>"
                                                placeholder="<?= l('link.settings.end_date') ?>"
                                                autocomplete="off"
                                                data-daterangepicker
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="form-group <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
                        <label for="clicks_limit"><i class="fa fa-fw fa-mouse fa-sm text-muted mr-1"></i> <?= l('link.settings.clicks_limit') ?></label>
                        <input id="clicks_limit" type="number" class="form-control" name="clicks_limit" value="<?= $data->link->settings->clicks_limit ?>" />
                        <small class="form-text text-muted"><?= l('link.settings.clicks_limit_help') ?></small>
                    </div>
                </div>

                <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="form-group <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
                        <label for="expiration_url"><i class="fa fa-fw fa-hourglass-end fa-sm text-muted mr-1"></i> <?= l('link.settings.expiration_url') ?></label>
                        <input id="expiration_url" type="url" class="form-control" name="expiration_url" value="<?= $data->link->settings->expiration_url ?>" maxlength="2048" />
                        <small class="form-text text-muted"><?= l('link.settings.expiration_url_help') ?></small>
                    </div>
                </div>

            </div>

            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#protection_container" aria-expanded="false" aria-controls="protection_container">
                <i class="fa fa-fw fa-user-shield fa-sm mr-1"></i> <?= l('link.settings.protection_header') ?>
            </button>

            <div class="collapse" id="protection_container">
                <div <?= $this->user->plan_settings->password ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->password ? null : 'container-disabled' ?>">
                        <div class="form-group">
                            <label for="qweasdzxc"><i class="fa fa-fw fa-key fa-sm text-muted mr-1"></i> <?= l('link.settings.password') ?></label>
                            <input id="qweasdzxc" type="password" class="form-control" name="qweasdzxc" value="<?= $data->link->settings->password ?>" autocomplete="new-password" <?= !$this->user->plan_settings->password ? 'disabled="disabled"': null ?> />
                            <small class="form-text text-muted"><?= l('link.settings.password_help') ?></small>
                        </div>
                    </div>
                </div>

                <div <?= $this->user->plan_settings->sensitive_content ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->sensitive_content ? null : 'container-disabled' ?>">
                        <div class="custom-control custom-switch mr-3 mb-3">
                            <input
                                    type="checkbox"
                                    class="custom-control-input"
                                    id="sensitive_content"
                                    name="sensitive_content"
                                <?= !$this->user->plan_settings->sensitive_content ? 'disabled="disabled"': null ?>
                                <?= $data->link->settings->sensitive_content ? 'checked="checked"' : null ?>
                            >
                            <label class="custom-control-label clickable" for="sensitive_content"><?= l('link.settings.sensitive_content') ?></label>
                            <small class="form-text text-muted"><?= l('link.settings.sensitive_content_help') ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#targeting_container" aria-expanded="false" aria-controls="targeting_container">
                <i class="fa fa-fw fa-bullseye fa-sm mr-1"></i> <?= l('link.settings.targeting_header') ?>
            </button>

            <div class="collapse" id="targeting_container">
                <div class="form-group">
                    <label for="targeting_type"><i class="fa fa-fw fa-bullseye fa-sm text-muted mr-1"></i> <?= l('link.settings.targeting_type') ?></label>
                    <select id="targeting_type" name="targeting_type" class="form-control">
                        <option value="false" <?= $data->link->settings->targeting_type == 'false' ? 'selected="selected"' : null?>><?= l('link.settings.targeting_type_null') ?></option>
                        <option value="country_code" <?= $data->link->settings->targeting_type == 'country_code' ? 'selected="selected"' : null?>><?= l('link.settings.targeting_type_country_code') ?></option>
                        <option value="device_type" <?= $data->link->settings->targeting_type == 'device_type' ? 'selected="selected"' : null?>><?= l('link.settings.targeting_type_device_type') ?></option>
                        <option value="browser_language" <?= $data->link->settings->targeting_type == 'browser_language' ? 'selected="selected"' : null?>><?= l('link.settings.targeting_type_browser_language') ?></option>
                        <option value="rotation" <?= $data->link->settings->targeting_type == 'rotation' ? 'selected="selected"' : null?>><?= l('link.settings.targeting_type_rotation') ?></option>
                        <option value="os_name" <?= $data->link->settings->targeting_type == 'os_name' ? 'selected="selected"' : null?>><?= l('link.settings.targeting_type_os_name') ?></option>
                    </select>
                </div>

                <div data-targeting-type="false" class="d-none"></div>

                <div data-targeting-type="country_code" class="d-none">
                    <p class="small text-muted"><?= l('link.settings.targeting_type_country_code_help') ?></p>

                    <div data-targeting-list="country_code">
                        <?php if(isset($data->link->settings->targeting_country_code) && !empty($data->link->settings->targeting_country_code)): ?>
                            <?php foreach($data->link->settings->targeting_country_code as $key => $targeting): ?>
                                <div class="form-row">
                                    <div class="form-group col-lg-6">
                                        <select name="targeting_country_code_key[<?= $key ?>]" class="form-control">
                                        <?php foreach(get_countries_array() as $country => $country_name): ?>
                                            <option value="<?= $country ?>" <?= $targeting->key == $country ? 'selected="selected"' : null ?>><?= $country_name ?></option>
                                        <?php endforeach ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-5">
                                        <input type="url" name="targeting_country_code_value[<?= $key ?>]" class="form-control" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
                                    </div>

                                    <div class="form-group col-lg-1 text-center">
                                        <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>

                    <div class="mb-3">
                        <button data-targeting-add="country_code" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
                    </div>
                </div>

                <div data-targeting-type="device_type" class="d-none">
                    <p class="small text-muted"><?= l('link.settings.targeting_type_device_type_help') ?></p>

                    <div data-targeting-list="device_type">
                        <?php if(isset($data->link->settings->targeting_device_type) && !empty($data->link->settings->targeting_device_type)): ?>
                            <?php foreach($data->link->settings->targeting_device_type as $key => $targeting): ?>
                                <div class="form-row">
                                    <div class="form-group col-lg-6">
                                        <select name="targeting_device_type_key[<?= $key ?>]" class="form-control">
                                            <?php foreach(['desktop', 'tablet', 'mobile'] as $device_type): ?>
                                                <option value="<?= $device_type ?>" <?= $targeting->key == $device_type ? 'selected="selected"' : null ?>><?= l('global.device.' . $device_type) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-5">
                                        <input type="url" name="targeting_device_type_value[<?= $key ?>]" class="form-control" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
                                    </div>

                                    <div class="form-group col-lg-1 text-center">
                                        <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>

                    <div class="mb-3">
                        <button data-targeting-add="device_type" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
                    </div>
                </div>

                <div data-targeting-type="browser_language" class="d-none">
                    <p class="small text-muted"><?= l('link.settings.targeting_type_browser_language_help') ?></p>

                    <div data-targeting-list="browser_language">
                        <?php if(isset($data->link->settings->targeting_browser_language) && !empty($data->link->settings->targeting_browser_language)): ?>
                            <?php foreach($data->link->settings->targeting_browser_language as $key => $targeting): ?>
                                <div class="form-row">
                                    <div class="form-group col-lg-6">
                                        <select name="targeting_browser_language_key[<?= $key ?>]" class="form-control">
                                            <?php foreach(get_locale_languages_array() as $locale => $language): ?>
                                                <option value="<?= $locale ?>" <?= $targeting->key == $locale ? 'selected="selected"' : null ?>><?= $language ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-5">
                                        <input type="url" name="targeting_browser_language_value[<?= $key ?>]" class="form-control" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
                                    </div>

                                    <div class="form-group col-lg-1 text-center">
                                        <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>

                    <div class="mb-3">
                        <button data-targeting-add="browser_language" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
                    </div>
                </div>

                <div data-targeting-type="rotation" class="d-none">
                    <p class="small text-muted"><?= l('link.settings.targeting_type_rotation_help') ?></p>

                    <div data-targeting-list="rotation">
                        <?php if(isset($data->link->settings->targeting_rotation) && !empty($data->link->settings->targeting_rotation)): ?>
                            <?php foreach($data->link->settings->targeting_rotation as $key => $targeting): ?>
                                <div class="form-row">
                                    <div class="form-group col-lg-6">
                                        <input type="number" min="0" max="100" name="targeting_rotation_key[<?= $key ?>]" class="form-control" value="<?= $targeting->key ?>" placeholder="<?= l('link.settings.targeting_type_percentage') ?>" />
                                    </div>

                                    <div class="form-group col-lg-5">
                                        <input type="url" name="targeting_rotation_value[<?= $key ?>]" class="form-control" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
                                    </div>

                                    <div class="form-group col-lg-1 text-center">
                                        <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>

                    <div class="mb-3">
                        <button data-targeting-add="rotation" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
                    </div>
                </div>

                <div data-targeting-type="os_name" class="d-none">
                    <p class="small text-muted"><?= l('link.settings.targeting_type_os_name_help') ?></p>

                    <div data-targeting-list="os_name">
                        <?php if(isset($data->link->settings->targeting_os_name) && !empty($data->link->settings->targeting_os_name)): ?>
                            <?php foreach($data->link->settings->targeting_os_name as $key => $targeting): ?>
                                <div class="form-row">
                                    <div class="form-group col-lg-6">
                                        <select name="targeting_os_name_key[<?= $key ?>]" class="form-control">
                                            <?php foreach(['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS'] as $os_name): ?>
                                                <option value="<?= $os_name ?>" <?= $targeting->key == $os_name ? 'selected="selected"' : null ?>><?= $os_name ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-5">
                                        <input type="url" name="targeting_os_name_value[<?= $key ?>]" class="form-control" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
                                    </div>

                                    <div class="form-group col-lg-1 text-center">
                                        <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>

                    <div class="mb-3">
                        <button data-targeting-add="os_name" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
                    </div>
                </div>
            </div>

            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#advanced_container" aria-expanded="false" aria-controls="advanced_container">
                <i class="fa fa-fw fa-user-tie fa-sm mr-1"></i> <?= l('link.settings.advanced_header') ?>
            </button>

            <div class="collapse" id="advanced_container">
                <div class="form-group">
                    <div class="d-flex flex-column flex-xl-row justify-content-between">
                        <label for="project_id"><i class="fa fa-fw fa-sm fa-project-diagram text-muted mr-1"></i> <?= l('projects.project_id') ?></label>
                        <a href="<?= url('projects') ?>" target="_blank" class="small mb-2"><i class="fa fa-fw fa-sm fa-plus mr-1"></i> <?= l('projects.create') ?></a>
                    </div>
                    <select id="project_id" name="project_id" class="form-control">
                        <option value=""><?= l('projects.project_id_null') ?></option>
                        <?php foreach($data->projects as $row): ?>
                            <option value="<?= $row->project_id ?>" <?= $data->link->project_id == $row->project_id ? 'selected="selected"' : null?>><?= $row->name ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.update') ?></button>
            </div>
        </form>

    </div>
</div>

<template id="template_targeting_country_code">
    <div class="form-row">
        <div class="form-group col-lg-6">
            <select name="targeting_country_code_key[]" class="form-control">
                <?php foreach(get_countries_array() as $country => $country_name): ?>
                    <option value="<?= $country ?>"><?= $country_name ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_country_code_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-1 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_device_type">
    <div class="form-row">
        <div class="form-group col-lg-6">
            <select name="targeting_device_type_key[]" class="form-control">
                <?php foreach(['desktop', 'tablet', 'mobile'] as $device_type): ?>
                    <option value="<?= $device_type ?>"><?= l('global.device.' . $device_type) ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_device_type_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-1 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_browser_language">
    <div class="form-row">
        <div class="form-group col-lg-6">
            <select name="targeting_browser_language_key[]" class="form-control">
                <?php foreach(get_locale_languages_array() as $locale => $language): ?>
                    <option value="<?= $locale ?>"><?= $language ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_browser_language_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-1 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_rotation">
    <div class="form-row">
        <div class="form-group col-lg-6">
            <input type="number" min="0" max="100" name="targeting_rotation_key[]" class="form-control" value="" placeholder="<?= l('link.settings.targeting_type_percentage') ?>" />
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_rotation_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-1 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_os_name">
    <div class="form-row">
        <div class="form-group col-lg-6">
            <select name="targeting_os_name_key[]" class="form-control">
                <?php foreach(['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS'] as $os_name): ?>
                    <option value="<?= $os_name ?>"><?= $os_name ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_os_name_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('link.settings.location_url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-1 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fa fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<?php $html = ob_get_clean() ?>


<?php ob_start() ?>
<script>
    /* Targeting */
    let targeting_type_handler = () => {
        let targeting_type = document.querySelector('#targeting_type').value;

        document.querySelectorAll('[data-targeting-type]').forEach(element => {
            let element_targeting_type = element.getAttribute('data-targeting-type');

            if(element_targeting_type == targeting_type) {
                document.querySelector(`[data-targeting-type="${element_targeting_type}"]`).classList.remove('d-none');
            } else {
                document.querySelector(`[data-targeting-type="${element_targeting_type}"]`).classList.add('d-none');
            }
        })
    }

    targeting_type_handler();
    document.querySelector('#targeting_type').addEventListener('change', targeting_type_handler);

    /* add new request header */
    let targeting_add = event => {
        let type = event.currentTarget.getAttribute('data-targeting-add');

        let clone = document.querySelector(`#template_targeting_${type}`).content.cloneNode(true);

        let request_headers_count = document.querySelectorAll(`[data-targeting-list="${type}"] .form-row`).length;

        clone.querySelector(`[name="targeting_${type}_key[]"`).setAttribute('name', `targeting_${type}_key[${request_headers_count}]`);
        clone.querySelector(`[name="targeting_${type}_value[]"`).setAttribute('name', `targeting_${type}_value[${request_headers_count}]`);

        document.querySelector(`[data-targeting-list="${type}"]`).appendChild(clone);

        targeting_remove_initiator();
    };

    document.querySelectorAll('[data-targeting-add]').forEach(element => {
        element.addEventListener('click', targeting_add);
    })

    /* remove request header */
    let targeting_remove = event => {
        event.currentTarget.closest('.form-row').remove();
    };

    let targeting_remove_initiator = () => {
        document.querySelectorAll('[data-targeting-remove]').forEach(element => {
            element.removeEventListener('click', targeting_remove);
            element.addEventListener('click', targeting_remove)
        })
    };

    targeting_remove_initiator();


    /* Settings Tab */
    let schedule_handler = () => {
        if($('#schedule').is(':checked')) {
            $('#schedule_container').show();
        } else {
            $('#schedule_container').hide();
        }
    };

    $('#schedule').on('change', schedule_handler);

    schedule_handler();

    /* Daterangepicker */
    let locale = <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>;
    $('[data-daterangepicker]').daterangepicker({
        minDate: new Date(),
        alwaysShowCalendars: true,
        singleCalendar: true,
        singleDatePicker: true,
        locale: {...locale, format: 'YYYY-MM-DD HH:mm:ss'},
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: true,
    }, (start, end, label) => {
    });

    /* Form handling */
    $('form[name="update_link"]').on('submit', event => {
        let form = $(event.currentTarget)[0];
        let data = new FormData(form);
        let notification_container = event.currentTarget.querySelector('.notification-container');
        notification_container.innerHTML = '';
        pause_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            url: `${url}link-ajax`,
            data: data,
            dataType: 'json',
            success: (data) => {
                display_notifications(data.message, data.status, notification_container);
                notification_container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

                if(data.status == 'success') {
                    update_main_url(data.details.url);
                }
            },
            error: () => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));
                display_notifications(<?= json_encode(l('global.error_message.basic')) ?>, 'error', notification_container);
            },
        });

        event.preventDefault();
    })
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
