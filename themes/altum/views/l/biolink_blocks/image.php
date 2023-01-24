<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <?php if($data->link->location_url): ?>
    <a href="<?= $data->link->location_url . $data->link->utm_query ?>" data-track-biolink-block-id="<?= $data->link->biolink_block_id ?>" target="<?= $data->link->settings->open_in_new_tab ? '_blank' : '_self' ?>" class="link-hover-animation">
        <img src="<?= UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image ?>" class="img-fluid rounded link-hover-animation" alt="<?= $data->link->settings->image_alt ?>" loading="lazy" />
    </a>
    <?php else: ?>
    <img src="<?= UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image ?>" class="img-fluid rounded" alt="<?= $data->link->settings->image_alt ?>" loading="lazy" />
    <?php endif ?>
</div>

