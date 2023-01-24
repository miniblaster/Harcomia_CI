<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_instagram_media" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" data-toggle="modal" data-target="#biolink_link_create_modal" data-dismiss="modal" class="btn btn-sm btn-link"><i class="fa fa-fw fa-chevron-circle-left text-muted"></i></button>
                <h5 class="modal-title"><?= l('create_biolink_instagram_media_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <p class="text-muted modal-subheader"><?= l('create_biolink_instagram_media_modal.subheader') ?></p>

            <div class="modal-body">
                <form name="create_biolink_instagram_media" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="instagram_media" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="instagram_media_location_url"><i class="fa fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('create_biolink_instagram_media_modal.location_url') ?></label>
                        <input id="instagram_media_location_url" type="text" class="form-control" name="location_url" required="required" maxlength="2048" placeholder="<?= l('create_biolink_instagram_media_modal.location_url_placeholder') ?>" />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
