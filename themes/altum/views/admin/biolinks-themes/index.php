<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-palette text-primary-900 mr-2"></i> <?= l('admin_biolinks_themes.header') ?></h1>

    <div class="d-flex position-relative">
        <div class="">
            <a href="<?= url('admin/biolink-theme-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('admin_biolink_theme_create.menu') ?></a>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                    <i class="fa fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right d-print-none">
                    <a href="<?= url('admin/biolinks-themes?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                    </a>
                    <a href="<?= url('admin/biolinks-themes?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                    </a>
                    <button type="button" onclick="window.print();" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-pdf mr-1"></i> <?= sprintf(l('global.export_to'), 'PDF') ?>
                    </button>
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

<form id="table" action="<?= SITE_URL . 'admin/biolinks-themes/bulk' ?>" method="post" role="form">
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
                <th><?= l('admin_biolinks_themes.table.name') ?></th>
                <th><?= l('admin_biolinks_themes.table.is_enabled') ?></th>
                <th><?= l('global.datetime') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data->biolinks_themes as $row): ?>
                <?php //ALTUMCODE:DEMO if(DEMO) {$row->user_email = 'hidden@demo.com'; $row->user_name = 'hidden on demo';} ?>
                <tr>
                    <td data-bulk-table class="d-none">
                        <div class="custom-control custom-checkbox">
                            <input id="selected_biolink_theme_id_<?= $row->biolink_theme_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->biolink_theme_id ?>" />
                            <label class="custom-control-label" for="selected_biolink_theme_id_<?= $row->biolink_theme_id ?>"></label>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <a href="<?= url('admin/biolink-theme-update/' . $row->biolink_theme_id) ?>"><?= $row->name ?></a>
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
                            <?= include_view(THEME_PATH . 'views/admin/biolinks-themes/admin_biolink_theme_dropdown_button.php', ['id' => $row->biolink_theme_id, 'resource_name' => $row->name]) ?>
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
    'name' => 'biolink_theme',
    'resource_id' => 'biolink_theme_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/biolinks-themes/delete/'
]), 'modals'); ?>
