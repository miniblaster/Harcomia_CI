<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
        <i class="fa fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <?php if($data->type == 'biolink'): ?>
            <?php if($data->is_verified): ?>
                <a href="<?= url('admin/links/is_verified/' . $data->id . '?' . \Altum\Csrf::get_url_query()) ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-user-alt-slash mr-2"></i> <?= l('admin_links.remove_verify') ?></a>
            <?php else: ?>
                <a href="<?= url('admin/links/is_verified/' . $data->id . '?' . \Altum\Csrf::get_url_query()) ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-check mr-2"></i> <?= l('admin_links.add_verify') ?></a>
            <?php endif ?>
        <?php endif ?>

        <a href="#" data-toggle="modal" data-target="#link_delete_modal" data-link-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
    </div>
</div>
