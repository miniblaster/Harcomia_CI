<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="bulk_delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="modal-title">
                        <i class="fa fa-fw fa-sm fa-trash-alt text-primary-900 mr-2"></i>
                        <?= l('admin_bulk_delete_modal.header') ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <p class="text-muted"><?= l('admin_bulk_delete_modal.subheader') ?></p>

                <div class="mt-4">
                    <button type="submit" form="table" class="btn btn-lg btn-block btn-danger" onclick="document.querySelector('#table input[data-bulk-type]').value = 'delete'">
                        <?= l('global.delete') ?>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
