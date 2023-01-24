<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2 d-flex justify-content-center">
    <blockquote class="instagram-media" data-instgrm-permalink="<?= $data->link->location_url ?>" data-instgrm-version="13"></blockquote>

    <?php if(!\Altum\Event::exists_content_type_key('javascript', 'instagram_media')): ?>
    <?php ob_start() ?>
        <script src="https://www.instagram.com/embed.js"></script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'instagram_media') ?>
    <?php endif ?>

    <script>
        setTimeout(() => {
            document.querySelector('div[data-biolink-block-id="<?= $data->link->biolink_block_id ?>"] iframe').style.width = '100%';
        }, 2000);
    </script>
</div>
