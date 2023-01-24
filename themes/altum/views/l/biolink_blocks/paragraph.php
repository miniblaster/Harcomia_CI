<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <p class="text-break m-0" style="color: <?= $data->link->settings->text_color ?>; text-align: <?= ($data->link->settings->text_alignment ?? 'center') ?>;">
        <?= nl2br($data->link->settings->text) ?>
    </p>
</div>
