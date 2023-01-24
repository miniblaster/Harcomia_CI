<?php defined('ALTUMCODE') || die() ?>

<?php
$size = 'fa-2x';
switch ($data->link->settings->size) {
    case 's':
        $size = '';
        break;

    case 'm':
        $size = 'fa-lg';
        break;

    case 'l':
        $size = 'fa-2x';
        break;

    case 'xl':
        $size = 'fa-3x';
        break;
}
?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <div class="d-flex flex-wrap justify-content-center">
        <?php $biolink_socials = require APP_PATH . 'includes/biolink_socials.php'; ?>
        <?php foreach($data->link->settings->socials as $key => $value): ?>
            <?php if($value): ?>
                <div class="my-2 mx-3" data-toggle="tooltip" title="<?= l('create_biolink_socials_modal.socials.' . $key . '.name') ?>">
                    <a href="<?= sprintf($biolink_socials[$key]['format'], $value) ?>" target="_blank" class="link-hover-animation">
                        <i class="<?= $biolink_socials[$key]['icon'] ?> <?= $size ?> fa-fw" style="color: <?= $data->link->settings->color ?>"></i>
                    </a>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</div>

