<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-6 my-2">
    <?php if($data->link->location_url): ?>
    <a href="<?= $data->link->location_url . $data->link->utm_query ?>" data-track-biolink-block-id="<?= $data->link->biolink_block_id ?>" target="<?= $data->link->settings->open_in_new_tab ? '_blank' : '_self' ?>">
    <?php endif ?>

        <div class="link-grid-image-wrapper <?= $data->link->location_url ? 'link-hover-animation' : null ?>" role="img" aria-label="<?= $data->link->settings->image_alt ?>" style="background-image: url('<?= UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image ?>')">

            <?php if($data->link->settings->name): ?>
                <div class="link-grid-image-overlay">
                    <span class="link-grid-image-overlay-text text-truncate"><?= $data->link->settings->name ?></span>
                </div>
            <?php endif ?>

        </div>

    <?php if($data->link->location_url): ?>
    </a>
    <?php endif ?>
</div>
