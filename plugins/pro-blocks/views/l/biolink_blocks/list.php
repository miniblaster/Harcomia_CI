<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <div class="card <?= 'link-btn-' . $data->link->settings->border_radius ?>" style="<?= 'border-width: ' . $data->link->settings->border_width . ';' . 'border-color: ' . $data->link->settings->border_color . ';' . 'border-style: ' . $data->link->settings->border_style . ';' . 'background: ' . $data->link->settings->background_color . ';' ?>">
        <div class="card-body d-flex flex-column" style="<?= 'color: ' . $data->link->settings->text_color . ';' . 'text-align: ' . $data->link->settings->text_alignment . ';' ?>">
            <?php
            $items = explode("\r\n", $data->link->settings->text);
            ?>
            <?php foreach($items as $item): ?>
                <div class="<?= 'my-' . $data->link->settings->margin_items_y ?>">
                    <?php if($data->link->settings->icon): ?>
                        <span class="<?= 'mr-' . $data->link->settings->margin_items_x ?>">
                        <?php if(string_starts_with('fa', $data->link->settings->icon)): ?>
                            <i class="<?= $data->link->settings->icon ?>"></i>
                        <?php else: ?>
                            <?= $data->link->settings->icon ?>
                        <?php endif ?>
                        </span>
                    <?php endif ?>

                    <?= $item ?>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
