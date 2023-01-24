<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="domain_create_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('domain_create_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $url = parse_url(SITE_URL); $host = $url['host'] . (mb_strlen($url['path']) > 1 ? $url['path'] : null); ?>

            <p class="text-muted modal-subheader"><?= sprintf(l('domain_create_modal.subheader'), '<strong>' . $_SERVER['SERVER_ADDR'] . '</strong>', '<strong>' . $host . '</strong>') ?></p>

            <div class="modal-body">
                <form name="domain_create_modal" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-sm fa-globe text-muted mr-1"></i> <?= l('domains.input.host') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <select id="create_scheme" name="scheme" class="appearance-none select-custom-altum form-control input-group-text">
                                    <option value="https://">https://</option>
                                    <option value="http://">http://</option>
                                </select>
                            </div>

                            <input type="text" class="form-control" name="host" placeholder="<?= l('domains.input.host_placeholder') ?>" required="required" />
                        </div>
                        <small class="form-text text-muted"><?= l('domains.input.host_help') ?></small>
                    </div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-sitemap fa-sm text-muted mr-1"></i> <?= l('domains.input.custom_index_url') ?></label>
                        <input type="url" class="form-control" name="custom_index_url" placeholder="<?= l('domains.input.custom_index_url_placeholder') ?>" />
                        <small class="form-text text-muted"><?= l('domains.input.custom_index_url_help') ?></small>
                    </div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-location-arrow fa-sm text-muted mr-1"></i> <?= l('domains.input.custom_not_found_url') ?></label>
                        <input type="url" class="form-control" name="custom_not_found_url" placeholder="<?= l('domains.input.custom_not_found_url_placeholder') ?>" />
                        <small class="form-text text-muted"><?= l('domains.input.custom_not_found_url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.create') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    $('form[name="domain_create_modal"]').on('submit', event => {
        let notification_container = event.currentTarget.querySelector('.notification-container');
        notification_container.innerHTML = '';
        pause_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

        $.ajax({
            type: 'POST',
            url: `${url}domains/create`,
            data: $(event.currentTarget).serialize(),
            dataType: 'json',
            success: (data) => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

                if (data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    display_notifications(data.message, 'success', notification_container);

                    setTimeout(() => {

                        /* Hide modal */
                        $('#domain_create_modal').modal('hide');

                        /* Clear input values */
                        $('form[name="domain_create_modal"] input').val('');

                        /* Redirect */
                        redirect(`domains`);

                    }, 500);

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
