<?php defined('ALTUMCODE') || die() ?>

<?php $payment_processors = require APP_PATH . 'includes/payment_processors.php'; ?>

<section class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0">
            <h1 class="h4 m-0"><?= l('guests_payments.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('guests_payments.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-auto d-flex">
            <div class="">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                        <i class="fa fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('guests-payments?' . $data->filters->get_get() . '&export=csv')  ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('guests-payments?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                        </a>
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
                                <a href="<?= url('guests-payments') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                            <?php endif ?>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form action="" method="get" role="form">
                            <div class="form-group px-4">
                                <label for="filters_search" class="small"><?= l('global.filters.search') ?></label>
                                <input type="search" name="search" id="filters_search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_search_by" class="small"><?= l('global.filters.search_by') ?></label>
                                <select name="search_by" id="filters_search_by" class="form-control form-control-sm">
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= l('guests_payments.filters.name') ?></option>
                                    <option value="email" <?= $data->filters->search_by == 'email' ? 'selected="selected"' : null ?>><?= l('guests_payments.filters.email') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="type" class="small">
                                    <?= l('guests_payments.filters.type') ?>
                                </label>
                                <select name="type" id="type" class="form-control form-control-sm">
                                    <option value=""><?= l('global.filters.all') ?></option>
                                    <?php foreach(['donation', 'product', 'service',] as $value): ?>
                                        <option value="<?= $value ?>" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == $value ? 'selected="selected"' : null ?>><?= l('link.biolink.blocks.' . $value) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="processor" class="small"><?= l('guests_payments.filters.processor') ?></label>
                                <select name="processor" id="processor" class="form-control form-control-sm">
                                    <option value=""><?= l('global.filters.all') ?></option>
                                    <option value="paypal" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'paypal' ? 'selected="selected"' : null ?>><?= l('pay.custom_plan.paypal') ?></option>
                                    <option value="stripe" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'stripe' ? 'selected="selected"' : null ?>><?= l('pay.custom_plan.stripe') ?></option>
                                    <option value="crypto_com" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'crypto_com' ? 'selected="selected"' : null ?>><?= l('pay.custom_plan.crypto_com') ?></option>
                                    <option value="razorpay" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'razorpay' ? 'selected="selected"' : null ?>><?= l('pay.custom_plan.razorpay') ?></option>
                                    <option value="paystack" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'paystack' ? 'selected="selected"' : null ?>><?= l('pay.custom_plan.paystack') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="total_amount" <?= $data->filters->order_by == 'total_amount' ? 'selected="selected"' : null ?>><?= l('guests_payments.filters.total_amount') ?></option>
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

    <?php if(count($data->guests_payments)): ?>

        <?php foreach($data->guests_payments as $row): ?>
            <div class="custom-row mb-4" data-datum-id="<?= $row->datum_id ?>">
                <div class="row">
                    <div class="col-3 col-lg-3 d-flex flex-column text-truncate">
                        <div class="text-truncate"><?= $row->name ?: '-' ?></div>
                        <div class="text-truncate text-muted"><?= $row->email ?: '-' ?></div>
                    </div>

                    <div class="col-3 col-lg-2 d-flex align-items-center justify-content-center">
                        <a href="<?= url('link/' . $row->link_id . '?tab=links') ?>" class="mr-2" data-toggle="tooltip" title="<?= l('guests_payments.biolink') ?>">
                            <?= l('link.biolink.blocks.' . $row->type) ?>
                        </a>
                    </div>

                    <div class="col-4 col-lg-3 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column">
                            <div><span class="text-success"><?= $row->total_amount ?></span> <?= $row->currency ?></div>
                            <div><i class="<?= $payment_processors[$row->processor]['icon'] ?> fa-fw mr-1"></i> <?= l('pay.custom_plan.' . $row->processor) ?></div>
                        </div>
                    </div>

                    <div class="col-2 col-lg-2 d-none d-lg-flex justify-content-center justify-content-lg-end align-items-center">
                        <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(l('global.datetime_tooltip'), \Altum\Date::get($row->datetime, 1)) ?>">
                            <i class="fa fa-fw fa-calendar text-muted"></i>
                        </span>
                    </div>

                    <div class="col-2 col-lg-2 d-flex justify-content-center justify-content-lg-end align-items-center">
                        <?= include_view(THEME_PATH . 'views/guests-payments/guest_payment_dropdown_button.php', ['id' => $row->guest_payment_id, 'resource_name' => l('link.biolink.blocks.' . $row->type)]) ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

        <div class="mt-3"><?= $data->pagination ?></div>

    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('guests_payments.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('guests_payments.no_data') ?></h2>
                </div>
            </div>
        </div>
    <?php endif ?>

</section>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'guest_payment',
    'resource_id' => 'guest_payment_id',
    'has_dynamic_resource_name' => true,
    'path' => 'guests-payments/delete'
]), 'modals'); ?>
