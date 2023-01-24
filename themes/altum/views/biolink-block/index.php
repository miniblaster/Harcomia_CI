<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('links') ?>"><?= l('links.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li><a href="<?= url('link/' . $data->biolink_block->link_id . '?tab=links') ?>"><?= l('link.breadcrumb.biolink') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page">
                <?= l('link.breadcrumb.biolink_block') . ' ' . l('link.statistics.breadcrumb') ?>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col text-truncate">
            <h1 class="h3 text-truncate"><?= sprintf(l('link.header.header'), $data->biolink_block->location_url ?? $data->biolink_block->settings->name ?? l('biolink_block.title')) ?></h1>
        </div>
    </div>

    <div class="d-flex align-items-baseline mb-5">
        <span class="mr-1">
            <i class="fa fa-fw fa-circle fa-sm" style="color: <?= $data->biolink_blocks[$data->biolink_block->type]['color'] ?>"></i>
        </span>

        <div class="text-muted text-truncate">
            <?= l('link.biolink.blocks.' . $data->biolink_block->type) ?>
        </div>
    </div>

    <?= $this->views['method'] ?>
</div>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

