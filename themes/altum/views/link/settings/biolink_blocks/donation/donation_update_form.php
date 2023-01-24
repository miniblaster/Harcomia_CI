<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="donation" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'donation_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'donation_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_donation_modal.donation_header') ?>
    </button>

    <div class="collapse" id="<?= 'donation_settings_container_' . $row->biolink_block_id ?>">
        <div class="form-group">
            <label for="<?= 'donation_title_' . $row->biolink_block_id ?>"><?= l('create_biolink_donation_modal.title') ?></label>
            <input type="text" id="<?= 'donation_title_' . $row->biolink_block_id ?>" name="title" class="form-control" value="<?= $row->settings->title ?? null ?>" maxlength="<?= $data->biolink_blocks['donation']['fields']['title']['max_length'] ?>" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'donation_description_' . $row->biolink_block_id ?>"><?= l('create_biolink_donation_modal.description') ?></label>
            <input type="text" id="<?= 'donation_description_' . $row->biolink_block_id ?>" name="description" class="form-control" value="<?= $row->settings->description ?? null ?>" maxlength="<?= $data->biolink_blocks['donation']['fields']['description']['max_length'] ?>" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'donation_prefilled_amount_' . $row->biolink_block_id ?>"><?= l('create_biolink_donation_modal.prefilled_amount') ?></label>
            <input type="number" min="0" step="0.01" id="<?= 'donation_prefilled_amount_' . $row->biolink_block_id ?>" name="prefilled_amount" class="form-control" value="<?= $row->settings->prefilled_amount ?? null ?>" />
        </div>

        <div class="form-group">
            <label for="<?= 'donation_minimum_amount_' . $row->biolink_block_id ?>"><?= l('create_biolink_donation_modal.minimum_amount') ?></label>
            <input type="number" min="0" step="0.01" id="<?= 'donation_minimum_amount_' . $row->biolink_block_id ?>" name="minimum_amount" class="form-control" value="<?= $row->settings->minimum_amount ?? null ?>" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'donation_currency_' . $row->biolink_block_id ?>"><?= l('create_biolink_donation_modal.currency') ?></label>
            <input type="text" id="<?= 'donation_currency_' . $row->biolink_block_id ?>" name="currency" class="form-control" value="<?= $row->settings->currency ?? null ?>" maxlength="<?= $data->biolink_blocks['donation']['fields']['currency']['max_length'] ?>" required="required" />
            <small class="form-text text-muted"><?= l('create_biolink_donation_modal.currency_help') ?></small>
        </div>

        <div class="custom-control custom-switch my-3">
            <input id="<?= 'donation_allow_custom_amount_' . $row->biolink_block_id ?>" name="allow_custom_amount" type="checkbox" class="custom-control-input" <?= ($row->settings->allow_custom_amount ?? null) ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="<?= 'donation_allow_custom_amount_' . $row->biolink_block_id ?>"><?= l('create_biolink_donation_modal.allow_custom_amount') ?></label>
        </div>

        <div class="custom-control custom-switch my-3">
            <input id="<?= 'donation_allow_message_' . $row->biolink_block_id ?>" name="allow_message" type="checkbox" class="custom-control-input" <?= ($row->settings->allow_message ?? null) ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="<?= 'donation_allow_message_' . $row->biolink_block_id ?>"><?= l('create_biolink_donation_modal.allow_message') ?></label>
            <small class="form-text text-muted"><?= l('create_biolink_donation_modal.allow_message_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'donation_thank_you_title_' . $row->biolink_block_id ?>"><?= l('create_biolink_donation_modal.thank_you_title') ?></label>
            <input type="text" id="<?= 'donation_thank_you_title_' . $row->biolink_block_id ?>" name="thank_you_title" class="form-control" value="<?= $row->settings->thank_you_title ?? null ?>" maxlength="<?= $data->biolink_blocks['donation']['fields']['thank_you_title']['max_length'] ?>" />
        </div>

        <div class="form-group">
            <label for="<?= 'donation_thank_you_description_' . $row->biolink_block_id ?>"><?= l('create_biolink_donation_modal.thank_you_description') ?></label>
            <input type="text" id="<?= 'donation_thank_you_description_' . $row->biolink_block_id ?>" name="thank_you_description" class="form-control" value="<?= $row->settings->thank_you_description ?? null ?>" maxlength="<?= $data->biolink_blocks['donation']['fields']['thank_you_description']['max_length'] ?>" />
        </div>

        <div class="mb-3">
            <div class="d-flex flex-column flex-xl-row justify-content-between">
                <label for="<?= 'donation_payment_processors_ids_' . $row->biolink_block_id ?>"><?= l('payment_processors.payment_processors_ids') ?></label>
                <a href="<?= url('payment-processor-create') ?>" target="_blank" class="small mb-2"><i class="fa fa-fw fa-sm fa-plus mr-1"></i> <?= l('payment_processors.create') ?></a>
            </div>

            <?php foreach($data->payment_processors as $payment_processor): ?>
                <div class="custom-control custom-checkbox my-2">
                    <input id="<?= 'donation_payment_processors_ids' . $payment_processor->payment_processor_id . '_' . $row->biolink_block_id ?>" name="payment_processors_ids[]" value="<?= $payment_processor->payment_processor_id ?>" type="checkbox" class="custom-control-input" <?= in_array($payment_processor->payment_processor_id, $row->settings->payment_processors_ids ?? []) ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="<?= 'donation_payment_processors_ids' . $payment_processor->payment_processor_id . '_' . $row->biolink_block_id ?>">
                        <span class="mr-1"><?= $payment_processor->name ?></span>
                        <small class="badge badge-light badge-pill"><?= l('pay.custom_plan.' . $payment_processor->processor) ?></small>
                    </label>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'donation_data_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'donation_data_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_block_modal.data_header') ?>
    </button>

    <div class="collapse" id="<?= 'donation_data_container_' . $row->biolink_block_id ?>">
        <div class="alert alert-info">
            <i class="fa fa-fw fa-sm fa-info-circle mr-1"></i> <?= l('create_biolink_block_modal.data_help') ?>
        </div>

        <div class="form-group">
            <label for="<?= 'donation_email_notification_' . $row->biolink_block_id ?>"><?= l('create_biolink_block_modal.email_notification') ?></label>
            <input type="text" id="<?= 'donation_email_notification_' . $row->biolink_block_id ?>" name="email_notification" class="form-control" value="<?= $row->settings->email_notification ?? null ?>" maxlength="<?= $data->biolink_blocks['donation']['fields']['email_notification']['max_length'] ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_block_modal.email_notification_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'donation_webhook_url_' . $row->biolink_block_id ?>"><?= l('create_biolink_block_modal.webhook_url') ?></label>
            <input type="url" id="<?= 'donation_webhook_url_' . $row->biolink_block_id ?>" name="webhook_url" class="form-control" value="<?= $row->settings->webhook_url ?? null ?>" maxlength="<?= $data->biolink_blocks['donation']['fields']['webhook_url']['max_length'] ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_block_modal.webhook_url_help') ?></small>
        </div>
    </div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'button_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'button_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_link_modal.button_header') ?>
    </button>

    <div class="collapse" id="<?= 'button_settings_container_' . $row->biolink_block_id ?>">
        <div class="form-group">
            <label for="<?= 'donation_name_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.name') ?></label>
            <input id="<?= 'donation_name_' . $row->biolink_block_id ?>" type="text" name="name" class="form-control" value="<?= $row->settings->name ?>" maxlength="128" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'donation_image_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-image fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.image') ?></label>
            <div data-image-container class="<?= !empty($row->settings->image) ? null : 'd-none' ?>">
                <div class="row">
                    <div class="m-1 col-6 col-xl-3">
                        <img src="<?= $row->settings->image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $row->settings->image : null ?>" class="img-fluid rounded <?= !empty($row->settings->image) ? null : 'd-none' ?>" loading="lazy" />
                    </div>
                </div>
                <div class="custom-control custom-checkbox my-2">
                    <input id="<?= $row->biolink_block_id . '_image_remove' ?>" name="image_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#<?= 'donation_image_' . $row->biolink_block_id ?>').classList.add('d-none') : document.querySelector('#<?= 'donation_image_' . $row->biolink_block_id ?>').classList.remove('d-none')">
                    <label class="custom-control-label" for="<?= $row->biolink_block_id . '_image_remove' ?>">
                        <span class="text-muted"><?= l('global.delete_file') ?></span>
                    </label>
                </div>
            </div>
            <input id="<?= 'donation_image_' . $row->biolink_block_id ?>" type="file" name="image" accept="<?= \Altum\Uploads::array_to_list_format($data->biolink_blocks['donation']['whitelisted_thumbnail_image_extensions']) ?>" class="form-control-file altum-file-input" />
            <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::array_to_list_format($data->biolink_blocks['donation']['whitelisted_thumbnail_image_extensions'])) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->thumbnail_image_size_limit) ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'donation_icon_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-globe fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.icon') ?></label>
            <input id="<?= 'donation_icon_' . $row->biolink_block_id ?>" type="text" name="icon" class="form-control" value="<?= $row->settings->icon ?>" placeholder="<?= l('create_biolink_link_modal.input.icon_placeholder') ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.icon_help') ?></small>
        </div>

        <div <?= $this->user->plan_settings->custom_colored_links ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
            <div class="<?= $this->user->plan_settings->custom_colored_links ? null : 'container-disabled' ?>">
                <div class="form-group">
                    <label for="<?= 'donation_text_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.text_color') ?></label>
                    <input id="<?= 'donation_text_color_' . $row->biolink_block_id ?>" type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                    <div class="text_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="<?= 'donation_text_alignment_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-align-center fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.text_alignment') ?></label>
                    <select id="<?= 'donation_text_alignment_' . $row->biolink_block_id ?>" name="text_alignment" class="form-control">
                        <option value="center" <?= $row->settings->text_alignment == 'center' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.text_alignment.center') ?></option>
                        <option value="left" <?= $row->settings->text_alignment == 'left' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.text_alignment.left') ?></option>
                        <option value="right" <?= $row->settings->text_alignment == 'right' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.text_alignment.right') ?></option>
                        <option value="justify" <?= $row->settings->text_alignment == 'justify' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.text_alignment.justify') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'donation_background_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.background_color') ?></label>
                    <input id="<?= 'donation_background_color_' . $row->biolink_block_id ?>" type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
                    <div class="background_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="<?= 'donation_border_width_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-style fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_width') ?></label>
                    <input id="<?= 'donation_border_width_' . $row->biolink_block_id ?>" type="range" min="0" max="5" class="form-control" name="border_width" value="<?= $row->settings->border_width ?>" required="required" />
                </div>

                <div class="form-group">
                    <label for="<?= 'donation_border_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_color') ?></label>
                    <input id="<?= 'donation_border_color_' . $row->biolink_block_id ?>" type="hidden" name="border_color" class="form-control" value="<?= $row->settings->border_color ?>" required="required" />
                    <div class="border_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="<?= 'donation_border_radius_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-all fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_radius') ?></label>
                    <select id="<?= 'donation_border_radius_' . $row->biolink_block_id ?>" name="border_radius" class="form-control">
                        <option value="straight" <?= $row->settings->border_radius == 'straight' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_straight') ?></option>
                        <option value="round" <?= $row->settings->border_radius == 'round' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_round') ?></option>
                        <option value="rounded" <?= $row->settings->border_radius == 'rounded' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_rounded') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'donation_border_style_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-none fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_style') ?></label>
                    <select id="<?= 'donation_border_style_' . $row->biolink_block_id ?>" name="border_style" class="form-control">
                        <option value="solid" <?= $row->settings->border_style == 'solid' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_solid') ?></option>
                        <option value="dashed" <?= $row->settings->border_style == 'dashed' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_dashed') ?></option>
                        <option value="double" <?= $row->settings->border_style == 'double' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_double') ?></option>
                        <option value="outset" <?= $row->settings->border_style == 'outset' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_outset') ?></option>
                        <option value="inset" <?= $row->settings->border_style == 'inset' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_inset') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'donation_animation_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-film fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.animation') ?></label>
                    <select id="<?= 'donation_animation_' . $row->biolink_block_id ?>" name="animation" class="form-control">
                        <option value="false" <?= !$row->settings->animation ? 'selected="selected"' : null ?>>-</option>
                        <?php foreach(require APP_PATH . 'includes/biolink_animations.php' as $animation): ?>
                            <option value="<?= $animation ?>" <?= $row->settings->animation == $animation ? 'selected="selected"' : null ?>><?= $animation ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'donation_animation_runs_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-play-circle fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.animation_runs') ?></label>
                    <select id="<?= 'donation_animation_runs_' . $row->biolink_block_id ?>" name="animation_runs" class="form-control">
                        <option value="repeat-1" <?= $row->settings->animation_runs == 'repeat-1' ? 'selected="selected"' : null ?>>1</option>
                        <option value="repeat-2" <?= $row->settings->animation_runs == 'repeat-2' ? 'selected="selected"' : null ?>>2</option>
                        <option value="repeat-3" <?= $row->settings->animation_runs == 'repeat-3' ? 'selected="selected"' : null ?>>3</option>
                        <option value="infinite" <?= $row->settings->animation_runs == 'repeat-infinite' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.animation_runs_infinite') ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'display_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'display_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_link_modal.display_settings_header') ?>
    </button>

    <div class="collapse" id="<?= 'display_settings_container_' . $row->biolink_block_id ?>">
        <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
            <div class="<?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
                <div class="custom-control custom-switch mb-3">
                    <input
                            id="<?= 'link_schedule_' . $row->biolink_block_id ?>"
                            name="schedule" type="checkbox"
                            class="custom-control-input"
                        <?= !empty($row->start_date) && !empty($row->end_date) ? 'checked="checked"' : null ?>
                        <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'disabled="disabled"' ?>
                    >
                    <label class="custom-control-label" for="<?= 'link_schedule_' . $row->biolink_block_id ?>"><?= l('link.settings.schedule') ?></label>
                    <small class="form-text text-muted"><?= l('link.settings.schedule_help') ?></small>
                </div>
            </div>
        </div>

        <div class="mt-3 schedule_container" style="display: none;">
            <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                <div class="<?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="<?= 'link_start_date_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-clock fa-sm text-muted mr-1"></i> <?= l('link.settings.start_date') ?></label>
                                <input
                                        id="<?= 'link_start_date_' . $row->biolink_block_id ?>"
                                        type="text"
                                        class="form-control"
                                        name="start_date"
                                        value="<?= \Altum\Date::get($row->start_date, 1) ?>"
                                        placeholder="<?= l('link.settings.start_date') ?>"
                                        autocomplete="off"
                                        data-daterangepicker
                                >
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <label for="<?= 'link_end_date_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-clock fa-sm text-muted mr-1"></i> <?= l('link.settings.end_date') ?></label>
                                <input
                                        id="<?= 'link_end_date_' . $row->biolink_block_id ?>"
                                        type="text"
                                        class="form-control"
                                        name="end_date"
                                        value="<?= \Altum\Date::get($row->end_date, 1) ?>"
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

        <div class="form-group">
            <label for="<?= 'link_display_countries_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-globe fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.display_countries') ?></label>
            <select id="<?= 'link_display_countries_' . $row->biolink_block_id ?>" name="display_countries[]" class="form-control" multiple="multiple">
                <?php foreach(get_countries_array() as $country => $country_name): ?>
                    <option value="<?= $country ?>" <?= in_array($country, $row->settings->display_countries ?? []) ? 'selected="selected"' : null ?>><?= $country_name ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.display_countries_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'link_display_devices_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-laptop fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.display_devices') ?></label>
            <select id="<?= 'link_display_devices_' . $row->biolink_block_id ?>" name="display_devices[]" class="form-control" multiple="multiple">
                <?php foreach(['desktop', 'tablet', 'mobile'] as $device_type): ?>
                    <option value="<?= $device_type ?>" <?= in_array($device_type, $row->settings->display_devices ?? []) ? 'selected="selected"' : null ?>><?= l('global.device.' . $device_type) ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.display_devices_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'link_display_languages_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-language fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.display_languages') ?></label>
            <select id="<?= 'link_display_languages_' . $row->biolink_block_id ?>" name="display_languages[]" class="form-control" multiple="multiple">
                <?php foreach(get_locale_languages_array() as $locale => $language): ?>
                    <option value="<?= $locale ?>" <?= in_array($locale, $row->settings->display_languages ?? []) ? 'selected="selected"' : null ?>><?= $language ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.display_languages_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'link_display_operating_systems_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-window-restore fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.display_operating_systems') ?></label>
            <select id="<?= 'link_display_operating_systems_' . $row->biolink_block_id ?>" name="display_operating_systems[]" class="form-control" multiple="multiple">
                <?php foreach(['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS'] as $os_name): ?>
                    <option value="<?= $os_name ?>" <?= in_array($os_name, $row->settings->display_operating_systems ?? []) ? 'selected="selected"' : null ?>><?= $os_name ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.display_operating_systems_help') ?></small>
        </div>
    </div>


    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.update') ?></button>
    </div>
</form>
