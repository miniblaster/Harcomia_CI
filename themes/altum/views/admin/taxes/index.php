<?php defined('ALTUMCODE') || die() ?>

<?php if(count($data->taxes) || count($data->filters->get)): ?>

    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-receipt text-primary-900 mr-2"></i> <?= l('admin_taxes.header') ?></h1>

        <div class="d-flex position-relative">
            <div class="">
                <a href="<?= url('admin/tax-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('admin_taxes.create') ?></a>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                        <i class="fa fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('admin/taxes?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('admin/taxes?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
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
                                <a href="<?= url('admin/taxes') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
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
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.name') ?></option>
                                    <option value="description" <?= $data->filters->search_by == 'description' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.description') ?></option>
                                    <option value="value" <?= $data->filters->search_by == 'value' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.value') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_value_type" class="small"><?= l('admin_taxes.main.value_type') ?></label>
                                <select name="value_type" id="filters_value_type" class="form-control form-control-sm">
                                    <option value=""><?= l('global.filters.all') ?></option>
                                    <option value="percentage" <?= isset($data->filters->filters['value_type']) && $data->filters->filters['value_type'] == 'percentage' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.value_type_percentage') ?></option>
                                    <option value="fixed" <?= isset($data->filters->filters['value_type']) && $data->filters->filters['value_type'] == 'fixed' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.value_type_fixed') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_type" class="small"><?= l('admin_taxes.main.type') ?></label>
                                <select name="type" id="filters_type" class="form-control form-control-sm">
                                    <option value=""><?= l('global.filters.all') ?></option>
                                    <option value="inclusive" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'inclusive' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.type_inclusive') ?></option>
                                    <option value="exclusive" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'exclusive' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.type_exclusive') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_billing_type" class="small"><?= l('admin_taxes.main.billing_type') ?></label>
                                <select name="billing_type" id="filters_billing_type" class="form-control form-control-sm">
                                    <option value=""><?= l('global.filters.all') ?></option>
                                    <option value="personal" <?= isset($data->filters->filters['billing_type']) && $data->filters->filters['billing_type'] == 'personal' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.billing_type_personal') ?></option>
                                    <option value="business" <?= isset($data->filters->filters['billing_type']) && $data->filters->filters['billing_type'] == 'business' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.billing_type_business') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.name') ?></option>
                                    <option value="value" <?= $data->filters->search_by == 'value' ? 'selected="selected"' : null ?>><?= l('admin_taxes.main.value') ?></option>
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
                <th><?= l('admin_taxes.table.tax') ?></th>
                <th><?= l('admin_taxes.table.details') ?></th>
                <th><?= l('admin_taxes.table.billing_type') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data->taxes as $row): ?>
                <tr data-tax-id="<?= $row->tax_id ?>">
                    <td class="text-nowrap">
                        <div class="d-flex flex-column">
                            <a href="<?= url('admin/tax-update/' . $row->tax_id) ?>"><?= $row->name ?></a>
                            <span class="text-muted"><?= $row->description ?></span>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex flex-column">
                            <span><?= $row->value_type == 'percentage' ? $row->value . '%' : $row->value . ' ' . settings()->payment->currency ?></span>
                            <span class="text-muted"><?= $row->type == 'inclusive' ? l('admin_taxes.main.type_inclusive') : l('admin_taxes.main.type_exclusive') ?></span>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <?= l('admin_taxes.main.billing_type_' . $row->billing_type) ?>
                    </td>
                    <td>
                        <div class="d-flex justify-content-end">
                            <?= include_view(THEME_PATH . 'views/admin/taxes/admin_tax_dropdown_button.php', ['id' => $row->tax_id]) ?>
                        </div>
                    </td>
                </tr>

            <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3"><?= $data->pagination ?></div>

<?php else: ?>

    <div class="d-flex flex-column flex-md-row align-items-md-center">
        <div class="mb-3 mb-md-0 mr-md-5">
            <i class="fa fa-fw fa-7x fa-receipt text-primary-200"></i>
        </div>

        <div class="d-flex flex-column">
            <h1 class="h3 m-0"><?= l('admin_taxes.header_no_data') ?></h1>
            <p class="text-muted"><?= l('admin_taxes.subheader_no_data') ?></p>

            <div>
                <a href="<?= url('admin/tax-create') ?>" class="btn btn-primary"><i class="fa fa-fw fa-sm fa-plus-circle"></i> <?= l('admin_taxes.create') ?></a>
            </div>
        </div>
    </div>

<?php endif ?>
