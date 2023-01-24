<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-link text-primary-900 mr-2"></i> <?= l('admin_links.header') ?></h1>

    <div class="d-flex position-relative">
        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                    <i class="fa fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right d-print-none">
                    <a href="<?= url('admin/links?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                    </a>
                    <a href="<?= url('admin/links?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
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
                            <a href="<?= url('admin/links') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
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
                                <option value="url" <?= $data->filters->search_by == 'url' ? 'selected="selected"' : null ?>><?= l('links.filters.url') ?></option>
                                <option value="location_url" <?= $data->filters->search_by == 'location_url' ? 'selected="selected"' : null ?>><?= l('links.filters.location_url') ?></option>
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
                            <label for="filters_type" class="small"><?= l('links.filters.type') ?></label>
                            <select name="type" id="filters_type" class="form-control form-control-sm">
                                <option value=""><?= l('global.filters.all') ?></option>
                                <option value="biolink" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'biolink' ? 'selected="selected"' : null ?>><?= l('links.filters.type.biolink') ?></option>
                                <option value="link" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'link' ? 'selected="selected"' : null ?>><?= l('links.filters.type.link') ?></option>
                                <option value="file" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'file' ? 'selected="selected"' : null ?>><?= l('links.filters.type.file') ?></option>
                                <option value="vcard" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'vcard' ? 'selected="selected"' : null ?>><?= l('links.filters.type.vcard') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                            <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="url" <?= $data->filters->order_by == 'url' ? 'selected="selected"' : null ?>><?= l('links.filters.url') ?></option>
                                <option value="location_url" <?= $data->filters->order_by == 'location_url' ? 'selected="selected"' : null ?>><?= l('links.filters.location_url') ?></option>
                                <option value="clicks" <?= $data->filters->order_by == 'clicks' ? 'selected="selected"' : null ?>><?= l('links.filters.order_by_clicks') ?></option>
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

<form id="table" action="<?= SITE_URL . 'admin/links/bulk' ?>" method="post" role="form">
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
            <th><?= l('admin_links.table.user') ?></th>
            <th></th>
            <th><?= l('admin_links.table.url') ?></th>
            <th><?= l('admin_links.table.stats') ?></th>
            <th><?= l('admin_links.table.is_enabled') ?></th>
            <th><?= l('global.datetime') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data->links as $row): ?>
            <?php //ALTUMCODE:DEMO if(DEMO) {$row->user_email = 'hidden@demo.com'; $row->user_name = 'hidden on demo';} ?>
            <tr>
                <td data-bulk-table class="d-none">
                    <div class="custom-control custom-checkbox">
                        <input id="selected_link_id_<?= $row->link_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->link_id ?>" />
                        <label class="custom-control-label" for="selected_link_id_<?= $row->link_id ?>"></label>
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
                    <span class="fa-stack fa-1x" data-toggle="tooltip" title="<?=  l('link.' . $row->type . '.name') ?>">
                        <i class="fas fa-circle fa-stack-2x" style="color: <?= $data->links_types[$row->type]['color'] ?>"></i>
                        <i class="fas <?= $data->links_types[$row->type]['icon'] ?> fa-stack-1x fa-inverse"></i>
                    </span>
                </td>
                <td class="text-nowrap">
                    <div class="d-flex flex-column">
                        <div>
                            <?= $row->domain_id ? $row->scheme . $row->host . '/' . $row->url : '/' . $row->url ?>
                            <a href="<?= $row->domain_id ? $row->scheme . $row->host . '/' . $row->url : url($row->url) ?>" target="_blank" rel="noreferrer">
                                <i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i>
                            </a>

                            <?php if($row->type == 'biolink' && $row->is_verified): ?>
                                <span data-toggle="tooltip" title="<?= l('link.biolink.verified') ?>"><i class="fa fa-fw fa-xs fa-check-circle link-verified" style="color: #3897F0"></i></span>
                            <?php endif ?>
                        </div>
                        <?php if($row->type == 'link'): ?>
                        <div class="text-muted">
                            <?= string_truncate($row->location_url, 48) ?>
                            <a href="<?= $row->location_url ?>" target="_blank" rel="noreferrer">
                                <i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i>
                            </a>
                        </div>
                        <?php endif ?>
                    </div>
                </td>
                <td class="text-muted">
                    <?= sprintf(l('admin_links.table.clicks'), nr($row->clicks)) ?>
                </td>
                <td class="text-nowrap">
                    <?php if($row->is_enabled == 0): ?>
                    <span class="badge badge-warning"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('global.disabled') ?>
                    <?php elseif($row->is_enabled == 1): ?>
                    <span class="badge badge-success"><i class="fa fa-fw fa-sm fa-check"></i> <?= l('global.active') ?>
                    <?php endif ?>
                </td>
                <td class="text-nowrap">
                    <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                        <?= \Altum\Date::get($row->datetime, 2) ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex justify-content-end">
                        <?= include_view(THEME_PATH . 'views/admin/links/admin_link_dropdown_button.php', ['id' => $row->link_id, 'is_verified' => $row->is_verified, 'type' => $row->type, 'resource_name' => $row->url]) ?>
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
    'name' => 'link',
    'resource_id' => 'link_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/links/delete/'
]), 'modals'); ?>
