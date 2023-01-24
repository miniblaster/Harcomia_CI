<?php defined('ALTUMCODE') || die() ?>

<?php
if(isset(settings()->support->expiry_datetime)):
    $expiry_datetime = (new \DateTime(settings()->support->expiry_datetime ?? null));
    $is_active = (new \DateTime()) <= $expiry_datetime;
    ?>
    <?php if(!$is_active && !isset($_COOKIE['dismiss_inactive_support'])): ?>
    <div class="alert alert-warning">
        <i class="fa fa-fw fa-exclamation-triangle text-warning mr-1"></i>
        <?= sprintf(l('admin_index.support.inactive'), '<a href="https://altumco.de/' . PRODUCT_KEY . '-buy" target="_blank" class="font-weight-bold">', '</a>') ?>
        <button type="button" class="close" data-dismiss="alert" data-dismiss-inactive-support><i class="fa fa-fw fa-sm fa-times text-warning"></i></button>
        <?php ob_start() ?>
        <script>
            'use strict';
            document.querySelector('[data-dismiss-inactive-support]').addEventListener('click', event => {
                set_cookie('dismiss_inactive_support', 1, 7, <?= json_encode(COOKIE_PATH) ?>);
            });
        </script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
    </div>
<?php endif ?>
<?php endif ?>

<h1 class="h3 mb-4 text-truncate"><?= sprintf(l('admin_index.header'), $this->user->name) ?></h1>

<div class="mb-5 row justify-content-between">
    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-hashtag mr-1"></i> <?= l('admin_index.biolink_links') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->biolink_links) ?></span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/links?type=biolink') ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-link mr-1"></i> <?= l('admin_index.shortened_links') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->shortened_links) ?></span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/links?type=link') ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i> <?= l('admin_index.track_links') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->track_links) ?></span></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-qrcode mr-1"></i> <?= l('admin_qr_codes.menu') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->qr_codes) ?></span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/qr-codes') ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-globe mr-1"></i> <?= l('admin_domains.menu') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->domains) ?></span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/domains') ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-users mr-1"></i> <?= l('admin_users.menu') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->users) ?></span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/users') ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-funnel-dollar mr-1"></i> <?= l('admin_payments.menu') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->payments) ?></span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/payments') ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= l('admin_index.payments_total_amount') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->payments_total_amount, 2) ?></span> <small><?= settings()->payment->currency ?></small></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/payments') ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    <h1 class="h3 mb-4"><?= l('admin_index.users') ?></h1>

    <?php $result = database()->query("SELECT * FROM `users` ORDER BY `user_id` DESC LIMIT 5"); ?>
    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th><?= l('admin_users.table.user') ?></th>
                <th><?= l('admin_users.main.status') ?></th>
                <th><?= l('admin_users.main.plan_id') ?></th>
                <th><?= l('admin_users.table.details') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_object()): ?>
                <?php //ALTUMCODE:DEMO if(DEMO) {$row->email = 'hidden@demo.com'; $row->name = 'hidden on demo';} ?>
                <?php if(!isset($data->plans[$row->plan_id])) $data->plans[$row->plan_id] = (new \Altum\Models\Plan())->get_plan_by_id($row->plan_id) ?>
                <tr>
                    <td class="text-nowrap">
                        <div class="d-flex">
                            <img src="<?= get_gravatar($row->email) ?>" class="user-avatar rounded-circle mr-3" alt="" />

                            <div class="d-flex flex-column">
                                <div>
                                    <a href="<?= url('admin/user-view/' . $row->user_id) ?>" <?= $row->type == 1 ? 'class="font-weight-bold" data-toggle="tooltip" title="' . l('admin_users.main.type_admin') . '"' : null ?>><?= $row->name ?></a>
                                </div>

                                <span class="text-muted"><?= $row->email ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <?php if($row->status == 0): ?>
                        <span class="badge badge-warning"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('admin_users.main.status_unconfirmed') ?>
                            <?php elseif($row->status == 1): ?>
                        <span class="badge badge-success"><i class="fa fa-fw fa-sm fa-check"></i> <?= l('admin_users.main.status_active') ?>
                            <?php elseif($row->status == 2): ?>
                        <span class="badge badge-light"><i class="fa fa-fw fa-sm fa-times"></i> <?= l('admin_users.main.status_disabled') ?>
                            <?php endif ?>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex flex-column">
                            <span><?= $data->plans[$row->plan_id]->name ?></span>

                            <?php if($row->plan_id != 'free'): ?>
                                <div>
                                    <small class="text-muted" data-toggle="tooltip" title="<?= l('admin_users.main.plan_expiration_date') ?>"><?= \Altum\Date::get($row->plan_expiration_date, 1) ?></small>
                                </div>
                            <?php endif ?>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex align-items-center">
                            <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(l('admin_users.table.datetime'), \Altum\Date::get($row->datetime, 1)) ?>">
                                <i class="fa fa-fw fa-clock text-muted"></i>
                            </span>

                            <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(l('admin_users.table.last_activity'), ($row->last_activity ? \Altum\Date::get($row->last_activity, 1) : '-')) ?>">
                                <i class="fa fa-fw fa-history text-muted"></i>
                            </span>

                            <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(l('admin_users.table.total_logins'), nr($row->total_logins)) ?>">
                                <i class="fa fa-fw fa-user-clock text-muted"></i>
                            </span>

                            <?php if($row->country): ?>
                                <img src="<?= ASSETS_FULL_URL . 'images/countries/' . mb_strtolower($row->country) . '.svg' ?>" class="img-fluid icon-favicon mr-2" data-toggle="tooltip" title="<?= get_country_from_country_code($row->country) ?>" />
                            <?php else: ?>
                                <span class="mr-2" data-toggle="tooltip" title="<?= l('admin_users.main.country_unknown') ?>">
                                    <i class="fa fa-fw fa-globe text-muted"></i>
                                </span>
                            <?php endif ?>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-end">
                            <?= include_view(THEME_PATH . 'views/admin/users/admin_user_dropdown_button.php', ['id' => $row->user_id, 'resource_name' => $row->name]) ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile ?>
            </tbody>
        </table>
    </div>
</div>

<?php if(in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
    <?php $result = database()->query("SELECT `payments`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email` FROM `payments` LEFT JOIN `users` ON `payments`.`user_id` = `users`.`user_id` ORDER BY `id` DESC LIMIT 5"); ?>

    <?php if($result->num_rows): ?>
        <div class="mb-5">
            <h1 class="h3 mb-4"><?= l('admin_index.payments') ?></h1>

            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead>
                    <tr>
                        <th><?= l('admin_payments.table.user') ?></th>
                        <th><?= l('admin_payments.table.type') ?></th>
                        <th><?= l('admin_payments.table.plan') ?></th>
                        <th><?= l('admin_payments.table.total_amount') ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $result->fetch_object()): ?>
                        <?php //ALTUMCODE:DEMO if(DEMO) {$row->email = $row->user_email = 'hidden@demo.com'; $row->user_name = $row->name = 'hidden on demo';} ?>

                        <tr>
                            <td class="text-nowrap">
                                <div class="d-flex flex-column">
                                    <div>
                                        <a href="<?= url('admin/user-view/' . $row->user_id) ?>"><?= $row->user_name ?></a>
                                    </div>

                                    <span class="text-muted"><?= $row->user_email ?></span>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex flex-column">
                                    <span><?= l('pay.custom_plan.' . $row->type . '_type') ?></span>
                                    <div>
                                        <span class="text-muted"><?= l('pay.custom_plan.' . $row->frequency) ?></span> - <span class="text-muted"><?= l('pay.custom_plan.' . $row->processor) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex flex-column">
                                    <a href="<?= url('admin/plan-update/' . $row->plan_id) ?>"><?= $data->plans[$row->plan_id]->name ?></a>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex flex-column">
                                    <span class=""><?= nr($row->total_amount, 2) . ' ' . $row->currency ?></span>
                                    <div>
                                        <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                                            <?= \Altum\Date::get($row->datetime, 2) ?>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end">
                                    <?= include_view(THEME_PATH . 'views/admin/payments/admin_payment_dropdown_button.php', [
                                        'id' => $row->id,
                                        'payment_proof' => $row->payment_proof,
                                        'processor' => $row->processor,
                                        'status' => $row->status
                                    ]) ?>
                                </div>
                            </td>
                        </tr>

                    <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif ?>
<?php endif ?>

<div class="row justify-content-between">
    <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-code mr-1"></i> <?= PRODUCT_NAME ?></small>

                <div class="mt-2"><span class="h6"><?= 'v' . PRODUCT_VERSION ?></span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= PRODUCT_URL ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-book mr-1"></i> Read documentation</small>

                <div class="mt-2"><span class="h6">Docs</span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= PRODUCT_DOCUMENTATION_URL ?>" class="stretched-link" target="_blank">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-history mr-1"></i> Read changelog</small>

                <div class="mt-2"><span class="h6">Changelog</span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="<?= PRODUCT_CHANGELOG_URL ?>" class="stretched-link" target="_blank">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-globe mr-1"></i> Official website</small>

                <div class="mt-2"><span class="h6">altumcode.com</span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="https://altumco.de/site" class="stretched-link" target="_blank">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-envelope mr-1"></i> Get support</small>

                <div class="mt-2"><span class="h6">support@altumcode.com</span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="https://altumcode.com/contact" class="stretched-link" target="_blank">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fab fa-fw fa-sm fa-twitter mr-1"></i> Twitter</small>

                <div class="mt-2"><span class="h6">@altumcode</span></div>
            </div>

            <div class="px-2 d-flex flex-column justify-content-center">
                <a href="https://altumco.de/twitter" class="stretched-link" target="_blank">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'user',
    'resource_id' => 'user_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/users/delete/'
]), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_login_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'payment',
    'resource_id' => 'id',
    'has_dynamic_resource_name' => false,
    'path' => 'admin/payments/delete/'
]), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/payments/payment_approve_modal.php'), 'modals'); ?>
