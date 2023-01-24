<?php defined('ALTUMCODE') || die() ?>

<section class="container">

    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0">
            <h1 class="h4 m-0"><?= l('domains.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('domains.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-auto d-flex">
            <div>
                <?php if($this->user->plan_settings->domains_limit != -1 && $data->total_domains >= $this->user->plan_settings->domains_limit): ?>
                    <button type="button" data-toggle="tooltip" title="<?= l('global.info_message.plan_feature_limit') ?>"  class="btn btn-primary disabled">
                        <i class="fa fa-fw fa-plus-circle"></i> <?= l('global.create') ?>
                    </button>
                <?php else: ?>
                    <button type="button" data-toggle="modal" data-target="#domain_create_modal" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('global.create') ?></button>
                <?php endif ?>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                        <i class="fa fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('domains?' . $data->filters->get_get() . '&export=csv')  ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('domains?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
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
                                <a href="<?= url('domains') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
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
                                    <option value="host" <?= $data->filters->search_by == 'host' ? 'selected="selected"' : null ?>><?= l('domains.table.host') ?></option>
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
                                    <option value="host" <?= $data->filters->order_by == 'host' ? 'selected="selected"' : null ?>><?= l('domains.table.host') ?></option>
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

    <?php if(count($data->domains)): ?>
        <?php foreach($data->domains as $row): ?>
            <div class="d-flex custom-row align-items-center my-4" data-domain-id="<?= $row->domain_id ?>">
                <div class="col-5">
                    <div class="font-weight-bold text-truncate">
                        <span class="align-middle"><?= $row->host ?></span>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <?php if($row->is_enabled): ?>
                        <span class="badge badge-success"><i class="fa fa-fw fa-sm fa-check"></i> <?= l('domains.table.is_enabled_active') ?></span>
                    <?php else: ?>
                        <span class="badge badge-warning"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= l('domains.table.is_enabled_pending') ?></span>
                    <?php endif ?>
                </div>

                <div class="d-none d-lg-flex col-lg-3">
                    <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(l('global.datetime_tooltip'), \Altum\Date::get($row->datetime, 1)) ?>">
                        <i class="fa fa-fw fa-calendar text-muted"></i>
                    </span>

                    <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(l('global.last_datetime_tooltip'), ($row->last_datetime ? \Altum\Date::get($row->last_datetime, 1) : '-')) ?>">
                        <i class="fa fa-fw fa-history text-muted"></i>
                    </span>
                </div>

                <div class="col-1 d-flex justify-content-end">
                    <div class="dropdown">
                        <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
                            <i class="fa fa-fw fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="#" data-toggle="modal" data-target="#domain_update_modal" data-domain-id="<?= $row->domain_id ?>" data-scheme="<?= $row->scheme ?>" data-host="<?= $row->host ?>" data-custom-index-url="<?= $row->custom_index_url ?>" data-custom-not-found-url="<?= $row->custom_not_found_url ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
                            <a href="#" data-toggle="modal" data-target="#domain_delete_modal" data-domain-id="<?= $row->domain_id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

        <div class="mt-3"><?= $data->pagination ?></div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= l('domains.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('domains.no_data') ?></h2>

                    <?php if($this->user->plan_settings->domains_limit != -1 && $data->total_domains < $this->user->plan_settings->domains_limit): ?>
                        <p><a href="#" data-toggle="modal" data-target="#domain_create_modal"><?= l('domains.no_data_help') ?></a></p>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</section>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/domains/domain_create_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/domains/domain_update_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/domains/domain_delete_modal.php'), 'modals'); ?>
