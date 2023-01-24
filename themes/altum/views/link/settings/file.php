<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>

<div class="card">
    <div class="card-body">

        <form name="update_file" action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
            <input type="hidden" name="request_type" value="update" />
            <input type="hidden" name="type" value="file" />
            <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />

            <div class="notification-container"></div>

            <div class="form-group">
                <label for="file"><i class="fa fa-fw fa-sm fa-eye text-muted mr-1"></i> <?= l('create_file_modal.input.file') ?></label>
                <?php if(!empty($data->link->settings->file)): ?>
                    <div>
                        <small class="text-muted" id="file_name"><?= $data->link->settings->file ?></small>
                    </div>
                <?php endif ?>
                <input id="file" type="file" name="file" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('files') ?>" class="form-control-file altum-file-input" />
                <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('files')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->file_size_limit) ?></small>
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
<?php $html = ob_get_clean() ?>


<?php ob_start() ?>
<script>
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
    $('form[name="update_file"]').on('submit', event => {
        let form = $(event.currentTarget)[0];
        let data = new FormData(form);

        let notification_container = event.currentTarget.querySelector('.notification-container');
        notification_container.innerHTML = '';
        pause_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
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

                    event.currentTarget.querySelector('input[type="file"]').value = '';
                    event.currentTarget.querySelector('#file_name').innerText = data.details.file;
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
