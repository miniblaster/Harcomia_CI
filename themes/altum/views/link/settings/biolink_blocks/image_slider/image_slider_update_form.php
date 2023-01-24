<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form" enctype="multipart/form-data">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="image_slider" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'slider_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'slider_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_image_slider_modal.slider_settings_header') ?>
    </button>

    <div class="collapse" id="<?= 'slider_settings_container_' . $row->biolink_block_id ?>">
        <div class="form-group">
            <label for="<?= 'image_slider_width_height_' . $row->biolink_block_id ?>"><?= l('create_biolink_image_slider_modal.width_height') ?></label>
            <input id="<?= 'image_slider_width_height_' . $row->biolink_block_id ?>" type="number" min="10" max="25" name="width_height" class="form-control" value="<?= $row->settings->width_height ?>" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'image_slider_gap_' . $row->biolink_block_id ?>"><?= l('create_biolink_image_slider_modal.gap') ?></label>
            <input id="<?= 'image_slider_gap_' . $row->biolink_block_id ?>" type="number" min="0" max="5" name="gap" class="form-control" value="<?= $row->settings->gap ?>" required="required" />
        </div>

        <div class="custom-control custom-switch mb-3">
            <input
                    id="<?= 'image_slider_display_multiple_' . $row->biolink_block_id ?>"
                    name="display_multiple" type="checkbox"
                    class="custom-control-input"
                <?= $row->settings->display_multiple ? 'checked="checked"' : null ?>
            >
            <label class="custom-control-label" for="<?= 'image_slider_display_multiple_' . $row->biolink_block_id ?>"><?= l('create_biolink_image_slider_modal.display_multiple') ?></label>
        </div>

        <div class="custom-control custom-switch mb-3">
            <input
                    id="<?= 'image_slider_display_arrows_' . $row->biolink_block_id ?>"
                    name="display_arrows" type="checkbox"
                    class="custom-control-input"
                <?= $row->settings->display_arrows ? 'checked="checked"' : null ?>
            >
            <label class="custom-control-label" for="<?= 'image_slider_display_arrows_' . $row->biolink_block_id ?>"><?= l('create_biolink_image_slider_modal.display_arrows') ?></label>
        </div>

        <div class="custom-control custom-switch mb-3">
            <input
                    id="<?= 'image_slider_autoplay_' . $row->biolink_block_id ?>"
                    name="autoplay" type="checkbox"
                    class="custom-control-input"
                <?= $row->settings->autoplay ? 'checked="checked"' : null ?>
            >
            <label class="custom-control-label" for="<?= 'image_slider_autoplay_' . $row->biolink_block_id ?>"><?= l('create_biolink_image_slider_modal.autoplay') ?></label>
        </div>

        <div class="custom-control custom-switch mb-3">
            <input
                    id="<?= 'image_slider_display_pagination_' . $row->biolink_block_id ?>"
                    name="display_pagination" type="checkbox"
                    class="custom-control-input"
                <?= $row->settings->display_pagination ? 'checked="checked"' : null ?>
            >
            <label class="custom-control-label" for="<?= 'image_slider_display_pagination_' . $row->biolink_block_id ?>"><?= l('create_biolink_image_slider_modal.display_pagination') ?></label>
        </div>

        <div class="custom-control custom-switch mb-3">
            <input
                    id="<?= 'alert_open_in_new_tab_' . $row->biolink_block_id ?>"
                    name="open_in_new_tab" type="checkbox"
                    class="custom-control-input"
                <?= $row->settings->open_in_new_tab ? 'checked="checked"' : null ?>
            >
            <label class="custom-control-label" for="<?= 'alert_open_in_new_tab_' . $row->biolink_block_id ?>"><?= l('create_biolink_link_modal.input.open_in_new_tab') ?></label>
        </div>
    </div>


    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'slider_items_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'slider_items_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_image_slider_modal.slider_items_header') ?>
    </button>

    <div class="collapse" id="<?= 'slider_items_container_' . $row->biolink_block_id ?>">
        <div id="<?= 'image_slider_items_' . $row->biolink_block_id ?>" data-biolink-block-id="<?= $row->biolink_block_id ?>">
            <?php foreach($row->settings->items as $key => $item): ?>
                <div class="mb-4">
                    <div class="form-group">
                        <label for="<?= 'item_image_' . $key . '_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-image fa-sm text-muted mr-1"></i> <?= l('create_biolink_image_modal.image') ?></label>
                        <div data-image-container class="<?= !empty($item->image) ? null : 'd-none' ?>">
                            <div class="row">
                                <div class="m-1 col-6 col-xl-3">
                                    <img src="<?= $item->image ? UPLOADS_FULL_URL . 'block_images/' . $item->image : null ?>" class="img-fluid rounded <?= !empty($item->image) ? null : 'd-none' ?>" loading="lazy" />
                                </div>
                            </div>
                        </div>
                        <input id="<?= 'item_image_' . $key . '_' . $row->biolink_block_id ?>" type="file" name="item_image_<?= $key ?>" accept="<?= \Altum\Uploads::array_to_list_format($data->biolink_blocks['image_slider']['whitelisted_image_extensions']) ?>" class="form-control-file altum-file-input" />
                        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::array_to_list_format($data->biolink_blocks['image_slider']['whitelisted_image_extensions'])) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->image_size_limit) ?></small>
                    </div>

                    <div class="form-group">
                        <label for="<?= 'item_image_alt_' . $key . '_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-comment-dots fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.image_alt') ?></label>
                        <input id="<?= 'item_image_alt_' . $key . '_' . $row->biolink_block_id ?>" type="text" class="form-control" name="item_image_alt[<?= $key ?>]" value="<?= $item->image_alt ?>" maxlength="100" />
                        <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.image_alt_help') ?></small>
                    </div>

                    <div class="form-group">
                        <label for="<?= 'item_location_url_' . $key . '_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.location_url') ?></label>
                        <input id="<?= 'item_location_url_' . $key . '_' . $row->biolink_block_id ?>" type="text" class="form-control" name="item_location_url[<?= $key ?>]" value="<?= $item->location_url ?>" maxlength="2048" />
                    </div>

                    <button type="button" data-remove="item" class="btn btn-block btn-outline-danger"><i class="fa fa-fw fa-times"></i> <?= l('global.delete') ?></button>
                </div>
            <?php endforeach ?>
        </div>

        <div class="mb-3">
            <button data-add="image_slider_item" data-biolink-block-id="<?= $row->biolink_block_id ?>" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
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
