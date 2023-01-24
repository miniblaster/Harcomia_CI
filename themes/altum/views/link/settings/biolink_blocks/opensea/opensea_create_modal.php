<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_opensea" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" data-toggle="modal" data-target="#biolink_link_create_modal" data-dismiss="modal" class="btn btn-sm btn-link"><i class="fa fa-fw fa-chevron-circle-left text-muted"></i></button>
                <h5 class="modal-title"><?= l('create_biolink_opensea_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <p class="text-muted modal-subheader"><?= l('create_biolink_opensea_modal.subheader') ?></p>

            <div class="modal-body">
                <form name="create_biolink_opensea" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="opensea" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="opensea_token_address"><i class="fa fa-fw fa-passport fa-sm text-muted mr-1"></i> <?= l('create_biolink_opensea_modal.token_address') ?></label>
                        <input id="opensea_token_address" type="text" class="form-control" name="token_address" required="required" maxlength="128" />
                    </div>

                    <div class="form-group">
                        <label for="opensea_token_id"><i class="fa fa-fw fa-id-card fa-sm text-muted mr-1"></i> <?= l('create_biolink_opensea_modal.token_id') ?></label>
                        <input id="opensea_token_id" type="number" step="1" class="form-control" name="token_id" required="required" />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
