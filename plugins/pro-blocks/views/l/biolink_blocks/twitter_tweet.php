<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2 d-flex justify-content-center">
    <blockquote class="twitter-tweet">
        <a href="<?= $data->link->location_url ?>"></a>
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    </blockquote>
</div>
