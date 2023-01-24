<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="pixel_update_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('pixel_update_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="pixel_update" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="update" />
                    <input type="hidden" name="pixel_id" value="" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="update_name"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('pixels.input.name') ?></label>
                        <input type="text" id="update_name" class="form-control" name="name" />
                    </div>

                    <div class="form-group">
                        <label for="update_type"><i class="fa fa-fw fa-adjust fa-sm text-muted mr-1"></i> <?= l('pixels.input.type') ?></label>
                        <select id="update_type" name="type" class="form-control">
                            <?php foreach(require APP_PATH . 'includes/pixels.php' as $pixel_key => $pixel): ?>
                                <option value="<?= $pixel_key ?>"><?= $pixel['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="update_pixel"><i class="fa fa-fw fa-code fa-sm text-muted mr-1"></i> <?= l('pixels.input.pixel') ?></label>
                        <input type="text" id="update_pixel" name="pixel" class="form-control" value="" required="required" />
                        <small class="text-muted form-text"><?= l('pixels.input.pixel_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    /* On modal show load new data */
    $('#pixel_update_modal').on('show.bs.modal', event => {
        let pixel_id = $(event.relatedTarget).data('pixel-id');
        let name = $(event.relatedTarget).data('name');
        let type = $(event.relatedTarget).data('type');
        let pixel = $(event.relatedTarget).data('pixel');

        $(event.currentTarget).find('input[name="pixel_id"]').val(pixel_id);
        $(event.currentTarget).find('input[name="name"]').val(name);
        $(event.currentTarget).find(`select[name="type"] option[value="${type}"]`).prop('selected', 'selected');
        $(event.currentTarget).find('input[name="pixel"]').val(pixel);
    });

    $('form[name="pixel_update"]').on('submit', event => {
        let notification_container = event.currentTarget.querySelector('.notification-container');
        notification_container.innerHTML = '';
        pause_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

        $.ajax({
            type: 'POST',
            url: `${url}pixel-ajax`,
            data: $(event.currentTarget).serialize(),
            dataType: 'json',
            success: (data) => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

                if (data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    /* Hide modal */
                    $('#pixel_update_modal').modal('hide');

                    /* Clear input values */
                    $('form[name="pixel_update"] input').val('');

                    redirect(`pixels`);
                }
            },
            error: () => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));
                display_notifications(<?= json_encode(l('global.error_message.basic')) ?>, 'error', notification_container);
            },
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
