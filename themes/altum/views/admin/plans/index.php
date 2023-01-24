<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-box-open text-primary-900 mr-2"></i> <?= l('admin_plans.header') ?></h1>

    <div class="col-auto p-0">
        <a href="<?= url('admin/plan-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('admin_plans.create') ?></a>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= l('admin_plans.table.name') ?></th>
            <th><?= l('admin_plans.table.price') ?></th>
            <th><?= l('admin_plans.table.order') ?></th>
            <th><?= l('admin_plans.table.users') ?></th>
            <th><?= l('admin_plans.table.status') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="text-nowrap">
                <a href="<?= url('admin/plan-update/free') ?>"><?= settings()->plan_free->name ?></a>
                <a href="<?= url('pay/free') ?>" target="_blank" rel="noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
            </td>
            <td class="text-nowrap">-</td>
            <td class="text-nowrap">-</td>
            <td class="text-nowrap">
                <i class="fa fa-fw fa-users text-muted"></i>
                <a href="<?= url('admin/users?plan_id=free') ?>">
                    <?= nr(database()->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = 'free'")->fetch_object()->total ?? 0) ?>
                </a>
            </td>
            <td class="text-nowrap">
                <?php if(settings()->plan_free->status == 0): ?>
                    <span class="badge badge-warning"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('global.disabled') ?></span>
                <?php elseif(settings()->plan_free->status == 1): ?>
                    <span class="badge badge-success"><i class="fa fa-fw fa-sm fa-check"></i> <?= l('global.active') ?></span>
                <?php else: ?>
                    <span class="badge badge-info"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('global.hidden') ?></span>
                <?php endif ?>
            </td>
            <td class="text-nowrap">
                <div class="d-flex justify-content-end">
                    <?= include_view(THEME_PATH . 'views/admin/plans/admin_plan_dropdown_button.php', ['id' => 'free']) ?>
                </div>
            </td>
        </tr>

        <tr>
            <td class="text-nowrap">
                <a href="<?= url('admin/plan-update/custom') ?>"><?= settings()->plan_custom->name ?></a>
                <span data-toggle="tooltip" title="<?= l('admin_plans.table.custom_help') ?>"><i class="fa fa-fw fa-info-circle text-muted"></i></span>
            </td>
            <td class="text-nowrap">-</td>
            <td class="text-nowrap">-</td>
            <td class="text-nowrap">
                <i class="fa fa-fw fa-users text-muted"></i>
                <a href="<?= url('admin/users?plan_id=custom') ?>">
                    <?= nr(database()->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = 'custom'")->fetch_object()->total ?? 0) ?>
                </a>
            </td>
            <td class="text-nowrap">
                <?php if(settings()->plan_custom->status == 0): ?>
                    <span class="badge badge-warning"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('global.disabled') ?></span>
                <?php elseif(settings()->plan_custom->status == 1): ?>
                    <span class="badge badge-success"><i class="fa fa-fw fa-sm fa-check"></i> <?= l('global.active') ?></span>
                <?php else: ?>
                    <span class="badge badge-info"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('global.hidden') ?></span>
                <?php endif ?>
            </td>
            <td class="text-nowrap">
                <div class="d-flex justify-content-end">
                    <?= include_view(THEME_PATH . 'views/admin/plans/admin_plan_dropdown_button.php', ['id' => 'custom']) ?>
                </div>
            </td>
        </tr>

        <?php foreach($data->plans as $row): ?>

            <tr data-plan-id="<?= $row->plan_id ?>">
                <td class="text-nowrap">
                    <a href="<?= url('admin/plan-update/' . $row->plan_id) ?>"><?= $row->name ?></a>
                    <?php if($row->status != 0): ?>
                        <a href="<?= url('pay/' . $row->plan_id) ?>" target="_blank" rel="noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
                    <?php endif ?>
                </td>
                <td class="text-nowrap">
                    <div class="d-flex flex-column text-muted small">
                        <span><?= $row->monthly_price . ' ' . settings()->payment->currency . ' ' . l('admin_plans.table.monthly') ?></span>
                        <span><?= $row->annual_price . ' ' . settings()->payment->currency . ' ' . l('admin_plans.table.annual') ?></span>
                        <span><?= $row->lifetime_price . ' ' . settings()->payment->currency . ' ' . l('admin_plans.table.lifetime') ?></span>
                    </div>
                </td>
                <td class="text-muted"><?= $row->order ?></td>
                <td class="text-nowrap">
                    <i class="fa fa-fw fa-users text-muted"></i>
                    <a href="<?= url('admin/users?plan_id=' . $row->plan_id) ?>">
                        <?= nr(database()->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = '{$row->plan_id}'")->fetch_object()->total ?? 0) ?>
                    </a>
                </td>
                <td class="text-nowrap">
                    <?php if($row->status == 0): ?>
                        <span class="badge badge-warning"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('global.disabled') ?></span>
                    <?php elseif($row->status == 1): ?>
                        <span class="badge badge-success"><i class="fa fa-fw fa-sm fa-check"></i> <?= l('global.active') ?></span>
                    <?php else: ?>
                        <span class="badge badge-info"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('global.hidden') ?></span>
                    <?php endif ?>
                </td>
                <td class="text-nowrap">
                    <div class="d-flex justify-content-end">
                        <?= include_view(THEME_PATH . 'views/admin/plans/admin_plan_dropdown_button.php', ['id' => $row->plan_id]) ?>
                    </div>
                </td>
            </tr>

        <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/plans/plan_delete_modal.php'), 'modals'); ?>
