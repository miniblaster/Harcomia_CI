<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-globe text-primary-900 mr-2"></i> <?= l('admin_domains.header') ?></h1>

    <div class="d-flex position-relative">
        <div>
            <a href="<?= url('admin/domain-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('admin_domain_create.menu') ?></a>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                    <i class="fa fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right d-print-none">
                    <a href="<?= url('admin/domains?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                    </a>
                    <a href="<?= url('admin/domains?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
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
                            <a href="<?= url('admin/domains') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
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
                                <option value="host" <?= $data->filters->search_by == 'host' ? 'selected="selected"' : null ?>><?= l('admin_domains.main.host') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_is_enabled" class="small"><?= l('global.filters.status') ?></label>
                            <select name="is_enabled" id="filters_is_enabled" class="form-control form-control-sm">
                                <option value=""><?= l('global.filters.all') ?></option>
                                <option value="1" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '1' ? 'selected="selected"' : null ?>><?= l('global.active') ?></option>
                                <option value="0" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '0' ? 'selected="selected"' : null ?>><?= l('global.disabled') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                            <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="host" <?= $data->filters->order_by == 'host' ? 'selected="selected"' : null ?>><?= l('admin_domains.main.host') ?></option>
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

        <div class="ml-3">
            <button id="bulk_enable" type="button" class="btn btn-outline-secondary" data-toggle="tooltip" title="<?= l('global.bulk_actions') ?>"><i class="fa fa-fw fa-sm fa-list"></i></button>

            <div id="bulk_group" class="btn-group d-none" role="group">
                <div class="btn-group" role="group">
                    <button id="bulk_actions" type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                        <?= l('global.bulk_actions') ?> <span id="bulk_counter" class="d-none"></span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="bulk_actions">
                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#bulk_delete_modal"><?= l('global.delete') ?></a>
                    </div>
                </div>

                <button id="bulk_disable" type="button" class="btn btn-outline-secondary" data-toggle="tooltip" title="<?= l('global.close') ?>"><i class="fa fa-fw fa-times"></i></button>
            </div>
        </div>

    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<form id="table" action="<?= SITE_URL . 'admin/domains/bulk' ?>" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
    <input type="hidden" name="type" value="" data-bulk-type />

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th data-bulk-table class="d-none">
                    <div class="custom-control custom-checkbox">
                        <input id="bulk_select_all" type="checkbox" class="custom-control-input" />
                        <label class="custom-control-label" for="bulk_select_all"></label>
                    </div>
                </th>
                <th><?= l('admin_domains.table.user') ?></th>
                <th><?= l('admin_domains.table.host') ?></th>
                <th><?= l('admin_domains.table.is_enabled') ?></th>
                <th></th>
                <th><?= l('global.datetime') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data->domains as $row): ?>
                <?php //ALTUMCODE:DEMO if(DEMO) {$row->user_email = 'hidden@demo.com'; $row->user_name = 'hidden on demo';} ?>
                <tr>
                    <td data-bulk-table class="d-none">
                        <div class="custom-control custom-checkbox">
                            <input id="selected_domain_id_<?= $row->domain_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->domain_id ?>" />
                            <label class="custom-control-label" for="selected_domain_id_<?= $row->domain_id ?>"></label>
                        </div>
                    </td>
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
                            <div>
                                <a href="<?= url('admin/domain-update/' . $row->domain_id) ?>"><?= $row->host ?></a>
                                <a href="<?= $row->scheme . $row->host ?>" rel="noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
                            </div>
                            <span class="text-muted">
                        <?php if($row->type == 1): ?>
                            <i class="fa fa-fw fa-sm fa-globe"></i> <?= l('admin_domains.main.type_global') ?>
                        <?php elseif($row->type == 0): ?>
                            <i class="fa fa-fw fa-sm fa-user"></i> <?= l('admin_domains.main.type_user') ?>
                        <?php endif ?>
                    </span>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <?php if($row->is_enabled == 0): ?>
                        <span class="badge badge-warning"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('global.disabled') ?>
                            <?php elseif($row->is_enabled == 1): ?>
                    <span class="badge badge-success"><i class="fa fa-fw fa-sm fa-check"></i> <?= l('global.active') ?>
                        <?php endif ?>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex align-items-center">
                            <a href="<?= url('admin/links?domain_id=' . $row->domain_id) ?>" class="mr-2" data-toggle="tooltip" title="<?= l('admin_links.menu') ?>">
                                <i class="fa fa-fw fa-link text-muted"></i>
                            </a>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                            <?= \Altum\Date::get($row->datetime, 2) ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-end">
                            <?= include_view(THEME_PATH . 'views/admin/domains/admin_domain_dropdown_button.php', ['id' => $row->domain_id, 'resource_name' => $row->host]) ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</form>

<div class="mt-3"><?= $data->pagination ?></div>

<?php require THEME_PATH . 'views/admin/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/partials/bulk_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'domain',
    'resource_id' => 'domain_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/domains/delete/'
]), 'modals'); ?>

