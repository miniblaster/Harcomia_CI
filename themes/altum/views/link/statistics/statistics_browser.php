<?php defined('ALTUMCODE') || die() ?>

<div class="card my-3">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <h3 class="h5 text-truncate m-0"><?= l('link.statistics.browser') ?></h3>

                <div class="ml-2">
                    <span data-toggle="tooltip" title="<?= l('link.statistics.browser_help') ?>">
                        <i class="fa fa-fw fa-info-circle text-muted"></i>
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center col-auto p-0">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                        <i class="fa fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url($data->url . '/statistics?type=' . $data->type . '&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date'] . '&export=csv') ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url($data->url . '/statistics?type=' . $data->type . '&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date'] . '&export=json') ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!count($data->rows)): ?>
            <div class="d-flex flex-column align-items-center justify-content-center py-3">
                <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('global.no_data') ?>" />
                <h2 class="h4 text-muted"><?= l('global.no_data') ?></h2>
            </div>
        <?php else: ?>

            <?php foreach($data->rows as $row): ?>
                <?php $percentage = round($row->total / $data->total_sum * 100, 1) ?>

                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-1">
                        <div class="text-truncate">
                            <?php if(!$row->browser_name): ?>
                                <span><?= l('global.unknown') ?></span>
                            <?php else: ?>
                                <span><?= $row->browser_name ?></span>
                            <?php endif ?>
                        </div>

                        <div>
                            <small class="text-muted"><?= nr($percentage) . '%' ?></small>
                            <span class="ml-3"><?= nr($row->total) ?></span>
                        </div>
                    </div>

                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            <?php endforeach ?>

        <?php endif ?>
    </div>
</div>
