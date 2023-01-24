<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_link" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_link_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_link" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="link" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="location_url"><i class="fa fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('create_link_modal.input.location_url') ?></label>
                        <input id="location_url" type="text" class="form-control" name="location_url" maxlength="2048" required="required" placeholder="<?= l('create_link_modal.input.location_url_placeholder') ?>" />
                    </div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('create_link_modal.input.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= SITE_URL ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                        <option value="<?= $row->domain_id ?>"><?= $row->url ?></option>
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
                                maxlength="256"
                                onchange="update_this_value(this, get_slug)"
                                onkeyup="update_this_value(this, get_slug)"
                                placeholder="<?= $this->user->plan_settings->custom_url ? l('create_link_modal.input.url_placeholder_custom') : l('create_link_modal.input.url_placeholder') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('create_link_modal.input.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_link_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="create_biolink" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_biolink_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="biolink" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('link.settings.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= SITE_URL ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                            <option value="<?= $row->domain_id ?>"><?= $row->url ?></option>
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
                                maxlength="256"
                                onchange="update_this_value(this, get_slug)"
                                onkeyup="update_this_value(this, get_slug)"
                                placeholder="<?= $this->user->plan_settings->custom_url ? l('link.settings.url_placeholder_custom') :  l('link.settings.url_placeholder') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_biolink_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="create_file" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_file_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_file" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="file" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="file"><i class="fa fa-fw fa-sm fa-file text-muted mr-1"></i> <?= l('create_file_modal.input.file') ?></label>
                        <input id="file" type="file" name="file" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('files') ?>" class="form-control-file altum-file-input" required="required" />
                        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('files')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->file_size_limit) ?></small>
                    </div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('create_link_modal.input.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= SITE_URL ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                            <option value="<?= $row->domain_id ?>"><?= $row->url ?></option>
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
                                    maxlength="256"
                                    onchange="update_this_value(this, get_slug)"
                                    onkeyup="update_this_value(this, get_slug)"
                                    placeholder="<?= $this->user->plan_settings->custom_url ? l('link.settings.url_placeholder_custom') : l('link.settings.url_placeholder') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_file_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="create_vcard" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_vcard_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_file" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="vcard" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('link.settings.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= SITE_URL ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                            <option value="<?= $row->domain_id ?>"><?= $row->url ?></option>
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
                                    maxlength="256"
                                    onchange="update_this_value(this, get_slug)"
                                    onkeyup="update_this_value(this, get_slug)"
                                    placeholder="<?= $this->user->plan_settings->custom_url ? l('link.settings.url_placeholder_custom') : l('link.settings.url_placeholder') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_vcard_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="create_event" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_event_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_file" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="event" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-bolt fa-sm text-muted mr-1"></i> <?= l('link.settings.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                            <option value=""><?= SITE_URL ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                            <option value="<?= $row->domain_id ?>"><?= $row->url ?></option>
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
                                    maxlength="256"
                                    onchange="update_this_value(this, get_slug)"
                                    onkeyup="update_this_value(this, get_slug)"
                                    placeholder="<?= $this->user->plan_settings->custom_url ? l('link.settings.url_placeholder_custom') : l('link.settings.url_placeholder') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('link.settings.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('create_event_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    $('form[name="create_link"],form[name="create_biolink"],form[name="create_file"],form[name="create_vcard"],form[name="create_event"]').on('submit', event => {
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
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

                if(data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {
                    display_notifications(data.message, 'success', notification_container);

                    setTimeout(() => {
                        $(event.currentTarget).modal('hide');
                        redirect(data.details.url, true);
                    }, 500);
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
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
