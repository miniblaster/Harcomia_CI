<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <<?= $data->link->settings->heading_type ?> class="<?= $data->link->settings->heading_type ?> m-0 text-break" style="color: <?= $data->link->settings->text_color ?>; text-align: <?= ($data->link->settings->text_alignment ?? 'center') ?>;">
        <?= $data->link->settings->text ?>
    </<?= $data->link->settings->heading_type ?>>
</div>

