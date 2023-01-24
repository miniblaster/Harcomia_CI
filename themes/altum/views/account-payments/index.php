<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <?= $this->views['account_header_menu'] ?>

    <div class="row mb-3">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0">
            <h1 class="h4 m-0"><?= l('account_payments.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('account_payments.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <?php if(count($data->payments) || count($data->filters->get)): ?>
            <div class="col-12 col-xl-auto d-flex">
                <div>
                    <div class="dropdown">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                            <i class="fa fa-fw fa-sm fa-download"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right d-print-none">
                            <a href="<?= url('account-payments?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                                <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                            </a>
                            <a href="<?= url('account-payments?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                                <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="ml-3">
                    <div class="dropdown">
                        <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport"><i class="fa fa-fw fa-sm fa-filter"></i></button>

                        <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                            <div class="dropdown-header d-flex justify-content-between">
                                <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                                <?php if(count($data->filters->get)): ?>
                                    <a href="<?= url('account-payments') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                                <?php endif ?>
                            </div>

                            <div class="dropdown-divider"></div>

                            <form action="" method="get" role="form">
                                <div class="form-group px-4">
                                    <label for="processor" class="small"><?= l('account_payments.filters.processor') ?></label>
                                    <select name="processor" id="processor" class="form-control form-control-sm">
                                        <option value=""><?= l('global.filters.all') ?></option>
                                        <?php foreach($data->payment_processors as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == $key ? 'selected="selected"' : null ?>><?= l('pay.custom_plan.' . $key) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="type" class="small"><?= l('account_payments.filters.type') ?></label>
                                    <select name="type" id="type" class="form-control form-control-sm">
                                        <option value=""><?= l('global.filters.all') ?></option>
                                        <option value="one_time" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'one_time' ? 'selected="selected"' : null ?>><?= l('account_payments.filters.type_one_time') ?></option>
                                        <option value="recurring" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'recurring' ? 'selected="selected"' : null ?>><?= l('account_payments.filters.type_recurring') ?></option>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="frequency" class="small"><?= l('account_payments.filters.frequency') ?></label>
                                    <select name="frequency" id="frequency" class="form-control form-control-sm">
                                        <option value=""><?= l('global.filters.all') ?></option>
                                        <option value="monthly" <?= isset($data->filters->filters['frequency']) && $data->filters->filters['frequency'] == 'monthly' ? 'selected="selected"' : null ?>><?= l('account_payments.filters.frequency_monthly') ?></option>
                                        <option value="annual" <?= isset($data->filters->filters['frequency']) && $data->filters->filters['frequency'] == 'annual' ? 'selected="selected"' : null ?>><?= l('account_payments.filters.frequency_annual') ?></option>
                                        <option value="lifetime" <?= isset($data->filters->filters['frequency']) && $data->filters->filters['frequency'] == 'lifetime' ? 'selected="selected"' : null ?>><?= l('account_payments.filters.frequency_lifetime') ?></option>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                    <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                        <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                        <option value="total_amount" <?= $data->filters->order_by == 'total_amount' ? 'selected="selected"' : null ?>><?= l('account_payments.filters.order_by_total_amount') ?></option>
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
        <?php endif ?>
    </div>

    <?php if(count($data->payments)): ?>
        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= l('account_payments.payments.customer') ?></th>
                    <th><?= l('account_payments.payments.plan_id') ?></th>
                    <th><?= l('account_payments.payments.type') ?></th>
                    <th><?= l('account_payments.payments.total_amount') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach($data->payments as $row): ?>

                    <tr>
                        <td class="text-nowrap">
                            <div class="d-flex flex-column">
                                <span><?= $row->email ?></span>
                                <span class="text-muted"><?= $row->name ?></span>
                            </div>
                        </td>

                        <td class="text-nowrap"><?= $row->plan_name ?></td>

                        <td class="text-nowrap">
                            <div class="d-flex flex-column">
                                <span><?= l('pay.custom_plan.' . $row->type . '_type') ?></span>
                                <span class="text-muted"><?= l('pay.custom_plan.' . $row->processor) ?></span>
                            </div>
                        </td>

                        <td class="text-nowrap">
                            <div class="d-flex flex-column">
                                <span><span class="text-success"><?= $row->total_amount ?></span> <?= $row->currency ?></span>
                                <span class="text-muted"><span data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>"><?= \Altum\Date::get($row->datetime, 2) ?></span></span>
                            </div>
                        </td>

                        <?php if($row->status): ?>
                            <?php if(settings()->payment->invoice_is_enabled): ?>

                                <td class="text-nowrap">
                                    <a href="<?= url('invoice/' . $row->id) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="fa fa-fw fa-sm fa-file-invoice"></i> <?= l('account_payments.payments.invoice') ?>
                                    </a>
                                </td>

                            <?php else: ?>

                                <td class="text-nowrap">
                                    <span class="badge badge-success"><?= l('account_payments.payments.status_approved') ?></span>
                                </td>

                            <?php endif ?>
                        <?php else: ?>

                            <td class="text-nowrap">
                                <span class="badge badge-warning"><?= l('account_payments.payments.status_pending') ?></span>
                            </td>

                        <?php endif ?>
                    </tr>
                <?php endforeach ?>

                </tbody>
            </table>
        </div>

        <div class="mt-3"><?= $data->pagination ?></div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('account_payments.payments.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('account_payments.payments.no_data') ?></h2>
                </div>
            </div>
        </div>
    <?php endif ?>

</div>
