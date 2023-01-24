<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-wallet text-primary-900 mr-2"></i> <?= l('admin_affiliates_withdrawals.header') ?></h1>

    <div class="d-flex position-relative">
        <div class="">
            <div class="dropdown">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                    <i class="fa fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right d-print-none">
                    <a href="<?= url('admin/payments?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                    </a>
                    <a href="<?= url('admin/payments?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                    </a>
                    <button type="button" onclick="window.print();" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-pdf mr-1"></i> <?= sprintf(l('global.export_to'), 'PDF') ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.filters.header') ?>">
                    <i class="fa fa-fw fa-sm fa-filter"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                    <div class="dropdown-header d-flex justify-content-between">
                        <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                        <?php if(count($data->filters->get)): ?>
                            <a href="<?= url('admin/payments') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                        <?php endif ?>
                    </div>

                    <div class="dropdown-divider"></div>

                    <form action="" method="get" role="form">
                        <div class="form-group px-4">
                            <label for="is_paid" class="small"><?= l('admin_affiliates_withdrawals.table.is_paid') ?></label>
                            <select name="is_paid" id="is_paid" class="form-control form-control-sm">
                                <option value=""><?= l('global.filters.all') ?></option>
                                <option value="1" <?= isset($data->filters->filters['is_paid']) && $data->filters->filters['is_paid'] == '1' ? 'selected="selected"' : null ?>><?= l('admin_affiliates_withdrawals.table.is_paid_paid') ?></option>
                                <option value="0" <?= isset($data->filters->filters['is_paid']) && $data->filters->filters['is_paid'] == '0' ? 'selected="selected"' : null ?>><?= l('admin_affiliates_withdrawals.table.is_paid_pending') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                            <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="amount" <?= $data->filters->order_by == 'amount' ? 'selected="selected"' : null ?>><?= l('admin_affiliates_withdrawals.table.amount') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_order_type" class="small"><?= l('global.filters.order_type') ?></label>
                            <select name="order_type" id="filters_order_type" class="form-control form-control-sm">
                                <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
                                <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_results_per_page" class="small"><?= l('global.filters.results_per_page') ?></label>
                            <select name="results_per_page" id="filters_results_per_page" class="form-control form-control-sm">
                                <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                    <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group px-4 mt-4">
                            <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= l('global.submit') ?></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= l('admin_affiliates_withdrawals.table.user') ?></th>
            <th><?= l('admin_affiliates_withdrawals.table.amount') ?></th>
            <th><?= l('admin_affiliates_withdrawals.table.is_paid') ?></th>
            <th><?= l('global.datetime') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data->affiliates_withdrawals as $row): ?>
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
                        <span><?= nr($row->amount, 2) . ' ' . settings()->payment->currency ?></span>
                        <small class="text-muted"><?= $row->note ?></small>
                    </div>
                </td>
                <td class="text-nowrap">
                    <?php if($row->is_paid): ?>
                        <span class="badge badge-success"><i class="fa fa-fw fa-sm fa-check"></i> <?= l('admin_affiliates_withdrawals.table.is_paid_paid') ?></span>
                    <?php else: ?>
                        <span class="badge badge-warning"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('admin_affiliates_withdrawals.table.is_paid_pending') ?></span>
                    <?php endif ?>
                </td>
                <td class="text-nowrap text-muted">
                    <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                        <?= \Altum\Date::get($row->datetime, 2) ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex justify-content-end">
                        <?= include_view(THEME_PATH . 'views/admin/affiliates-withdrawals/admin_affiliate_withdrawal_dropdown_button.php', [
                            'id' => $row->affiliate_withdrawal_id,
                            'is_paid' => $row->is_paid
                        ]) ?>
                    </div>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<div class="mt-3"><?= $data->pagination ?></div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/affiliates-withdrawals/affiliate_withdrawal_approve_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'affiliate_withdrawal',
    'resource_id' => 'affiliate_withdrawal_id',
    'has_dynamic_resource_name' => false,
    'path' => 'admin/affiliates-withdrawals/delete/'
]), 'modals'); ?>

