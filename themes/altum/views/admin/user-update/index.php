<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/users') ?>"><?= l('admin_users.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_user_update.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-truncate"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= l('admin_user_update.header') ?></h1>

    <?= include_view(THEME_PATH . 'views/admin/users/admin_user_dropdown_button.php', ['id' => $data->user->user_id, 'resource_name' => $data->user->name]) ?>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<?php //ALTUMCODE:DEMO if(DEMO) {$data->user->email = 'hidden@demo.com'; $data->user->name = $data->user->ip = 'hidden on demo';} ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

            <div class="form-group">
                <label for="user_id"><?= l('admin_users.main.user_id') ?></label>
                <input type="text" id="user_id" name="user_id" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('user_id') ? 'is-invalid' : null ?>" value="<?= $data->user->user_id ?>" disabled="disabled" />
                <?= \Altum\Alerts::output_field_error('name') ?>
            </div>

            <div class="form-group">
                <label for="name"><?= l('admin_users.main.name') ?></label>
                <input id="name" type="text" name="name" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->user->name ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('name') ?>
            </div>

            <div class="form-group">
                <label for="email"><?= l('admin_users.main.email') ?></label>
                <input id="email" type="email" name="email" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->user->email ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('email') ?>
            </div>

            <div class="form-group">
                <label for="status"><?= l('admin_users.main.status') ?></label>
                <select id="status" name="status" class="form-control form-control-lg">
                    <option value="2" <?= $data->user->status == 2 ? 'selected="selected"' : null ?>><?= l('admin_users.main.status_disabled') ?></option>
                    <option value="1" <?= $data->user->status == 1 ? 'selected="selected"' : null ?>><?= l('admin_users.main.status_active') ?></option>
                    <option value="0" <?= $data->user->status == 0 ? 'selected="selected"' : null ?>><?= l('admin_users.main.status_unconfirmed') ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="type"><?= l('admin_users.main.type') ?></label>
                <select id="type" name="type" class="form-control form-control-lg">
                    <option value="1" <?= $data->user->type == 1 ? 'selected="selected"' : null ?>><?= l('admin_users.main.type_admin') ?></option>
                    <option value="0" <?= $data->user->type == 0 ? 'selected="selected"' : null ?>><?= l('admin_users.main.type_user') ?></option>
                </select>
                <small class="form-text text-muted"><?= l('admin_users.main.type_help') ?></small>
            </div>

            <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                <div class="form-group">
                    <label for="referred_by"><?= l('admin_users.main.referred_by') ?></label>
                    <input id="referred_by" type="number" name="referred_by" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('referred_by') ? 'is-invalid' : null ?>" value="<?= $data->user->referred_by ?>" />
                    <?= \Altum\Alerts::output_field_error('referred_by') ?>
                </div>
            <?php endif ?>

            <div class="mt-5"></div>

            <h2 class="h4"><?= l('admin_user_update.plan.header') ?></h2>

            <div class="form-group">
                <label for="plan_id"><?= l('admin_users.main.plan_id') ?></label>
                <select id="plan_id" name="plan_id" class="form-control form-control-lg">
                    <option value="free" <?= $data->user->plan->plan_id == 'free' ? 'selected="selected"' : null ?>><?= settings()->plan_free->name ?></option>
                    <option value="custom" <?= $data->user->plan->plan_id == 'custom' ? 'selected="selected"' : null ?>><?= settings()->plan_custom->name ?></option>

                    <?php foreach($data->plans as $plan): ?>
                        <option value="<?= $plan->plan_id ?>" <?= $data->user->plan->plan_id == $plan->plan_id ? 'selected="selected"' : null ?>><?= $plan->name ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="plan_trial_done"><?= l('admin_users.main.plan_trial_done') ?></label>
                <select id="plan_trial_done" name="plan_trial_done" class="form-control form-control-lg">
                    <option value="1" <?= $data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                    <option value="0" <?= !$data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
                </select>
            </div>

            <div id="plan_expiration_date_container" class="form-group">
                <label for="plan_expiration_date"><?= l('admin_users.main.plan_expiration_date') ?></label>
                <input id="plan_expiration_date" type="text" name="plan_expiration_date" class="form-control form-control-lg" autocomplete="off" value="<?= $data->user->plan_expiration_date ?>">
                <div class="invalid-feedback">
                    <?= l('admin_user_update.plan.plan_expiration_date_invalid') ?>
                </div>
            </div>

            <div id="plan_settings" style="display: none">
                <div class="form-group">
                    <label for="projects_limit"><?= l('admin_plans.plan.projects_limit') ?></label>
                    <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->projects_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                </div>

                <div class="form-group">
                    <label for="pixels_limit"><?= l('admin_plans.plan.pixels_limit') ?></label>
                    <input type="number" id="pixels_limit" name="pixels_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->pixels_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                </div>

                <div class="form-group">
                    <label for="qr_codes_limit"><?= l('admin_plans.plan.qr_codes_limit') ?></label>
                    <input type="number" id="qr_codes_limit" name="qr_codes_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->qr_codes_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                </div>

                <div class="form-group">
                    <label for="biolinks_limit"><?= l('admin_plans.plan.biolinks_limit') ?></label>
                    <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->biolinks_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.biolinks_limit_help') ?></small>
                </div>

                <div class="form-group">
                    <label for="biolink_blocks_limit"><?= l('admin_plans.plan.biolink_blocks_limit') ?></label>
                    <input type="number" id="biolink_blocks_limit" name="biolink_blocks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->biolink_blocks_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                </div>

                <div class="form-group" <?= !settings()->links->shortener_is_enabled ? 'style="display: none"' : null ?>>
                    <label for="links_limit"><?= l('admin_plans.plan.links_limit') ?></label>
                    <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->links_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.links_limit_help') ?></small>
                </div>

                <div class="form-group" <?= !settings()->links->files_is_enabled ? 'style="display: none"' : null ?>>
                    <label for="files_limit"><?= l('admin_plans.plan.files_limit') ?></label>
                    <input type="number" id="files_limit" name="files_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->files_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.files_limit_help') ?></small>
                </div>

                <div class="form-group" <?= !settings()->links->vcards_is_enabled ? 'style="display: none"' : null ?>>
                    <label for="vcards_limit"><?= l('admin_plans.plan.vcards_limit') ?></label>
                    <input type="number" id="vcards_limit" name="vcards_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->vcards_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.vcards_limit_help') ?></small>
                </div>

                <div class="form-group" <?= !settings()->links->events_is_enabled ? 'style="display: none"' : null ?>>
                    <label for="events_limit"><?= l('admin_plans.plan.events_limit') ?></label>
                    <input type="number" id="events_limit" name="events_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->events_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.events_limit_help') ?></small>
                </div>

                <div class="form-group" <?= !settings()->links->domains_is_enabled ? 'style="display: none"' : null ?>>
                    <label for="domains_limit"><?= l('admin_plans.plan.domains_limit') ?></label>
                    <input type="number" id="domains_limit" name="domains_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->domains_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.domains_limit_help') ?></small>
                </div>

                <?php if(\Altum\Plugin::is_active('payment-blocks')): ?>
                    <div class="form-group">
                        <label for="payment_processors_limit"><?= l('admin_plans.plan.payment_processors_limit') ?></label>
                        <input type="number" id="payment_processors_limit" name="payment_processors_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->payment_processors_limit ?>" />
                        <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                    </div>
                <?php endif ?>

                <?php if(\Altum\Plugin::is_active('teams')): ?>
                    <div class="form-group">
                        <label for="teams_limit"><?= l('admin_plans.plan.teams_limit') ?></label>
                        <input type="number" id="teams_limit" name="teams_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->teams_limit ?>" />
                        <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                    </div>

                    <div class="form-group">
                        <label for="team_members_limit"><?= l('admin_plans.plan.team_members_limit') ?></label>
                        <input type="number" id="team_members_limit" name="team_members_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->team_members_limit ?>" />
                        <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                    </div>
                <?php endif ?>

                <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                    <div class="form-group">
                        <label for="affiliate_commission_percentage"><?= l('admin_plans.plan.affiliate_commission_percentage') ?></label>
                        <input type="number" id="affiliate_commission_percentage" name="affiliate_commission_percentage" min="0" max="100" class="form-control form-control-lg" value="<?= $data->user->plan->settings->affiliate_commission_percentage ?>" />
                        <small class="form-text text-muted"><?= l('admin_plans.plan.affiliate_commission_percentage_help') ?></small>
                    </div>
                <?php endif ?>

                <div class="form-group">
                    <label for="track_links_retention"><?= l('admin_plans.plan.track_links_retention') ?></label>
                    <input type="number" id="track_links_retention" name="track_links_retention" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->track_links_retention ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.track_links_retention_help') ?></small>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="additional_global_domains" name="additional_global_domains" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->additional_global_domains ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="additional_global_domains"><?= l('admin_plans.plan.additional_global_domains') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.additional_global_domains_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_url" name="custom_url" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_url ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_url"><?= l('admin_plans.plan.custom_url') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.custom_url_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="deep_links" name="deep_links" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->deep_links ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="deep_links"><?= l('admin_plans.plan.deep_links') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.deep_links_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->no_ads ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="no_ads"><?= l('admin_plans.plan.no_ads') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.no_ads_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->removable_branding ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="removable_branding"><?= l('admin_plans.plan.removable_branding') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.removable_branding_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_branding ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_branding"><?= l('admin_plans.plan.custom_branding') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.custom_branding_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_colored_links ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_colored_links"><?= l('admin_plans.plan.custom_colored_links') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.custom_colored_links_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="statistics" name="statistics" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->statistics ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="statistics"><?= l('admin_plans.plan.statistics') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.statistics_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_backgrounds ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_backgrounds"><?= l('admin_plans.plan.custom_backgrounds') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.custom_backgrounds_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="temporary_url_is_enabled" name="temporary_url_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->temporary_url_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="temporary_url_is_enabled"><?= l('admin_plans.plan.temporary_url_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.temporary_url_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="seo" name="seo" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->seo ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="seo"><?= l('admin_plans.plan.seo') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.seo_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="utm" name="utm" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->utm ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="utm"><?= l('admin_plans.plan.utm') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.utm_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="fonts" name="fonts" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->fonts ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="fonts"><?= l('admin_plans.plan.fonts') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.fonts_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="password" name="password" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->password ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="password"><?= l('admin_plans.plan.password') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.password_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="sensitive_content" name="sensitive_content" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->sensitive_content ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="sensitive_content"><?= l('admin_plans.plan.sensitive_content') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.sensitive_content_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="leap_link" name="leap_link" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->leap_link ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="leap_link"><?= l('admin_plans.plan.leap_link') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.leap_link_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="dofollow_is_enabled" name="dofollow_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->dofollow_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="dofollow_is_enabled"><?= l('admin_plans.plan.dofollow_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.dofollow_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="api_is_enabled" name="api_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->api_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="api_is_enabled"><?= l('admin_plans.plan.api_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.api_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="custom_css_is_enabled" name="custom_css_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_css_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_css_is_enabled"><?= l('admin_plans.plan.custom_css_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.custom_css_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="custom_js_is_enabled" name="custom_js_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_js_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_js_is_enabled"><?= l('admin_plans.plan.custom_js_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.custom_js_is_enabled_help') ?></small></div>
                </div>

                <h3 class="h5 mt-4"><?= l('admin_plans.plan.enabled_biolink_blocks') ?></h3>
                <p class="text-muted"><?= l('admin_plans.plan.enabled_biolink_blocks_help') ?></p>

                <div class="row">
                    <?php foreach(require APP_PATH . 'includes/biolink_blocks.php' as $key => $value): ?>
                        <div class="col-6 mb-3">
                            <div class="custom-control custom-switch">
                                <input id="enabled_biolink_blocks_<?= $key ?>" name="enabled_biolink_blocks[]" value="<?= $key ?>" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->enabled_biolink_blocks->{$key} ? 'checked="checked"' : null ?>>
                                <label class="custom-control-label" for="enabled_biolink_blocks_<?= $key ?>"><?= l('link.biolink.blocks.' . mb_strtolower($key)) ?></label>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <div class="mt-5"></div>

            <h2 class="h4"><?= l('admin_user_update.change_password.header') ?></h2>
            <p class="text-muted"><?= l('admin_user_update.change_password.subheader') ?></p>

            <div class="form-group">
                <label for="new_password"><?= l('admin_user_update.change_password.new_password') ?></label>
                <input id="new_password" type="password" name="new_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" />
                <?= \Altum\Alerts::output_field_error('new_password') ?>
            </div>

            <div class="form-group">
                <label for="repeat_password"><?= l('admin_user_update.change_password.repeat_password') ?></label>
                <input id="repeat_password" type="password" name="repeat_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" />
                <?= \Altum\Alerts::output_field_error('new_password') ?>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
        </form>
    </div>
</div>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>

<script>
    'use strict';

    moment.tz.setDefault(<?= json_encode($this->user->timezone) ?>);

    let check_plan_id = () => {
        let selected_plan_id = document.querySelector('[name="plan_id"]').value;

        if(selected_plan_id == 'free') {
            document.querySelector('#plan_expiration_date_container').style.display = 'none';
        } else {
            document.querySelector('#plan_expiration_date_container').style.display = 'block';
        }

        if(selected_plan_id == 'custom') {
            document.querySelector('#plan_settings').style.display = 'block';
        } else {
            document.querySelector('#plan_settings').style.display = 'none';
        }
    };

    check_plan_id();

    /* Dont show expiration date when the chosen plan is the free one */
    document.querySelector('[name="plan_id"]').addEventListener('change', check_plan_id);

    /* Check for expiration date to show a warning if expired */
    let check_plan_expiration_date = () => {
        let plan_expiration_date = document.querySelector('[name="plan_expiration_date"]');

        let plan_expiration_date_object = new Date(plan_expiration_date.value);
        let today_date_object = new Date();

        if(plan_expiration_date_object < today_date_object) {
            plan_expiration_date.classList.add('is-invalid');
        } else {
            plan_expiration_date.classList.remove('is-invalid');
        }
    };

    check_plan_expiration_date();
    document.querySelector('[name="plan_expiration_date"]').addEventListener('change', check_plan_expiration_date);

    /* Daterangepicker */
    $('[name="plan_expiration_date"]').daterangepicker({
        startDate: <?= json_encode($data->user->plan_expiration_date) ?>,
        minDate: new Date(),
        alwaysShowCalendars: true,
        singleCalendar: true,
        singleDatePicker: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {
        check_plan_expiration_date()
    });

</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'user',
    'resource_id' => 'user_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/users/delete/'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_login_modal.php'), 'modals'); ?>
