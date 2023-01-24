<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_image" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" data-toggle="modal" data-target="#biolink_link_create_modal" data-dismiss="modal" class="btn btn-sm btn-link"><i class="fa fa-fw fa-chevron-circle-left text-muted"></i></button>
                <h5 class="modal-title"><?= l('create_biolink_image_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink_image" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="image" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="image_image"><i class="fa fa-fw fa-image fa-sm text-muted mr-1"></i> <?= l('create_biolink_image_modal.image') ?></label>
                        <input id="image_image" type="file" name="image" accept="<?= \Altum\Uploads::array_to_list_format($data->biolink_blocks['image']['whitelisted_image_extensions']) ?>" class="form-control-file altum-file-input" required="required" />
                        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::array_to_list_format($data->biolink_blocks['image']['whitelisted_image_extensions'])) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->image_size_limit) ?></small>
                    </div>

                    <div class="form-group">
                        <label for="image_image_alt"><i class="fa fa-fw fa-comment-dots fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.image_alt') ?></label>
                        <input id="image_image_alt" type="text" class="form-control" name="image_alt" value="" maxlength="100" />
                        <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.image_alt_help') ?></small>
                    </div>

                    <div class="form-group">
                        <label for="image_location_url"><i class="fa fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.location_url') ?></label>
                        <input id="image_location_url" type="text" class="form-control" name="location_url" maxlength="2048" />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
