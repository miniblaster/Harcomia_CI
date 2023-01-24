<?php defined('ALTUMCODE') || die() ?>

<section class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0">
            <h1 class="h4 m-0"><?= l('pixels.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('pixels.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-auto d-flex">
            <div>
                <?php if($this->user->plan_settings->pixels_limit != -1 && $data->total_pixels >= $this->user->plan_settings->pixels_limit): ?>
                    <button type="button" data-toggle="tooltip" title="<?= l('global.info_message.plan_feature_limit') ?>" class="btn btn-primary disabled">
                        <i class="fa fa-fw fa-plus-circle"></i> <?= l('pixels.create') ?>
                    </button>
                <?php else: ?>
                    <button type="button" data-toggle="modal" data-target="#create_pixel_modal" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('pixels.create') ?></button>
                <?php endif ?>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                        <i class="fa fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('pixels?' . $data->filters->get_get() . '&export=csv')  ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('pixels?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
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
                                <a href="<?= url('pixels') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
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
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= l('pixels.input.name') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_type" class="small"><?= l('pixels.input.type') ?></label>
                                <select name="type" id="filters_type" class="form-control form-control-sm">
                                    <option value=""><?= l('global.filters.all') ?></option>
                                    <?php foreach(require APP_PATH . 'includes/pixels.php' as $pixel_key => $pixel): ?>
                                        <option value="<?= $pixel_key ?>" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == $pixel_key ? 'selected="selected"' : null ?>><?= $pixel['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= l('pixels.input.name') ?></option>
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

    <?php if(count($data->pixels)): ?>
        <?php $available_pixels = require APP_PATH . 'includes/pixels.php'; ?>
        <?php foreach($data->pixels as $row): ?>
            <div class="custom-row mb-4" data-pixel-id="<?= $row->pixel_id ?>">
                <div class="row">
                    <div class="col-4 col-lg-4 d-flex align-items-center">
                        <div class="font-weight-bold text-truncate">
                            <a href="#" data-toggle="modal" data-target="#pixel_update_modal" data-pixel-id="<?= $row->pixel_id ?>" data-name="<?= $row->name ?>" data-type="<?= $row->type ?>" data-pixel="<?= $row->pixel ?>"><?= $row->name ?></a>
                        </div>
                    </div>

                    <div class="col-4 col-lg-4 d-flex align-items-center">
                        <i class="<?= $available_pixels[$row->type]['icon'] ?> fa-fw fa-sm mr-1" style="color: <?= $available_pixels[$row->type]['color'] ?>"></i>
                        <?= $available_pixels[$row->type]['name'] ?>
                    </div>

                    <div class="col-2 col-lg-2 d-none d-lg-flex justify-content-center justify-content-lg-end align-items-center">
                        <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(l('global.datetime_tooltip'), \Altum\Date::get($row->datetime, 1)) ?>">
                            <i class="fa fa-fw fa-calendar text-muted"></i>
                        </span>

                        <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(l('global.last_datetime_tooltip'), ($row->last_datetime ? \Altum\Date::get($row->last_datetime, 1) : '-')) ?>">
                            <i class="fa fa-fw fa-history text-muted"></i>
                        </span>
                    </div>

                    <div class="col-2 col-lg-2 d-flex justify-content-center justify-content-lg-end align-items-center">
                        <div class="dropdown">
                            <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
                                <i class="fa fa-fw fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="#" data-toggle="modal" data-target="#pixel_update_modal" data-pixel-id="<?= $row->pixel_id ?>" data-name="<?= $row->name ?>" data-type="<?= $row->type ?>" data-pixel="<?= $row->pixel ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
                                <a href="#" data-toggle="modal" data-target="#pixel_delete_modal" data-pixel-id="<?= $row->pixel_id ?>" data-resource-name="<?= $row->name ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                            </div>
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
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('pixels.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('pixels.no_data') ?></h2>
                </div>
            </div>
        </div>
    <?php endif ?>

</section>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/pixels/pixel_create_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/pixels/pixel_update_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'pixel',
    'resource_id' => 'pixel_id',
    'has_dynamic_resource_name' => true,
    'path' => 'pixels/delete'
]), 'modals'); ?>
