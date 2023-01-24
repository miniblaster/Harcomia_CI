<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <div class="link-iframe-round">
        <iframe class="embed-responsive-item" scrolling="no" frameborder="no" style="height: 96px;width:100%;overflow:hidden;background:transparent;" src="<?= $data->link->location_url ?>"></iframe>
    </div>
</div>
