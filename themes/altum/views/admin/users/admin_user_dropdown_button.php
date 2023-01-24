<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
        <i class="fa fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="admin/user-view/<?= $data->id ?>"><i class="fa fa-fw fa-sm fa-eye mr-2"></i> <?= l('global.view') ?></a>
        <a class="dropdown-item" href="admin/user-update/<?= $data->id ?>"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
        <a href="#" data-toggle="modal" data-target="#user_delete_modal" data-user-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
        <a href="#" data-toggle="modal" data-target="#user_login_modal" data-user-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-sign-in-alt mr-2"></i> <?= l('global.login') ?></a>
    </div>
</div>
