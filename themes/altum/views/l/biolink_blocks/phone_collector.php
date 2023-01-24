<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <a href="#" data-toggle="modal" data-target="<?= '#phone_collector_' . $data->link->biolink_block_id ?>" class="btn btn-block btn-primary link-btn link-hover-animation <?= 'link-btn-' . $data->link->settings->border_radius ?> <?= $data->link->design->link_class ?>" style="<?= $data->link->design->link_style ?>">
        <div class="link-btn-image-wrapper <?= 'link-btn-' . $data->link->settings->border_radius ?>" <?= $data->link->settings->image ? null : 'style="display: none;"' ?>>
            <img src="<?= $data->link->settings->image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $data->link->settings->image : null ?>" class="link-btn-image" loading="lazy" />
        </div>

        <?php if($data->link->settings->icon): ?>
            <i class="<?= $data->link->settings->icon ?> mr-1"></i>
        <?php endif ?>

        <?= $data->link->settings->name ?>
    </a>
</div>

<?php ob_start() ?>
<div class="modal fade" id="<?= 'phone_collector_' . $data->link->biolink_block_id ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= $data->link->settings->name ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="<?= 'phone_collector_form_' . $data->link->biolink_block_id ?>" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="biolink_block_id" value="<?= $data->link->biolink_block_id ?>" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <input type="text" class="form-control form-control-lg" name="phone" maxlength="32" required="required" placeholder="<?= $data->link->settings->phone_placeholder ?>" aria-label="<?= $data->link->settings->phone_placeholder ?>" />
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control form-control-lg" name="name" maxlength="64" required="required" placeholder="<?= $data->link->settings->name_placeholder ?>" aria-label="<?= $data->link->settings->name_placeholder ?>" />
                    </div>

                    <?php if($data->link->settings->show_agreement): ?>
                        <div class="d-flex align-items-center">
                            <input type="checkbox" id="agreement" name="agreement" class="mr-3" required="required" />
                            <label for="agreement" class="text-muted mb-0">
                                <a href="<?= $data->link->settings->agreement_url ?>">
                                    <?= $data->link->settings->agreement_text ?>
                                </a>
                            </label>
                        </div>
                    <?php endif ?>

                    <?php if(settings()->captcha->biolink_is_enabled && settings()->captcha->type != 'basic'): ?>
                        <div class="form-group">
                            <?php (new \Altum\Captcha())->display() ?>
                        </div>
                    <?php endif ?>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-lg btn-primary" data-is-ajax><?= $data->link->settings->button_text ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php \Altum\Event::add_content(ob_get_clean(), 'modals') ?>


<?php if(!\Altum\Event::exists_content_type_key('javascript', 'phone_collector')): ?>
    <?php ob_start() ?>
    <script>
        'use strict';

        /* Go over all phone buttons to make sure the user can still submit phone */
        $('form[id^="phone_collector_"]').each((index, element) => {
            let biolink_block_id = $(element).find('input[name="biolink_block_id"]').val();
            let is_converted = localStorage.getItem(`phone_collector_${biolink_block_id}`);

            if(is_converted) {
                /* Set the submit button to disabled */
                $(element).find('button[type="submit"]').attr('disabled', 'disabled');
            }
        });
        /* Form handling for phone submissions if any */
        $('form[id^="phone_collector_"]').on('submit', event => {
            let biolink_block_id = $(event.currentTarget).find('input[name="biolink_block_id"]').val();
            let is_converted = localStorage.getItem(`phone_collector_${biolink_block_id}`);

            let notification_container = event.currentTarget.querySelector('.notification-container');
            notification_container.innerHTML = '';
            pause_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

            if(!is_converted) {
                $.ajax({
                    type: 'POST',
                    url: `${site_url}l/link/phone_collector`,
                    data: $(event.currentTarget).serialize(),
                    dataType: 'json',
                    success: (data) => {
                        enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

                        if (data.status == 'error') {
                            display_notifications(data.message, 'error', notification_container);
                        } else if (data.status == 'success') {
                            display_notifications(data.message, 'success', notification_container);

                            setTimeout(() => {

                                /* Hide modal */
                                $(event.currentTarget).closest('.modal').modal('hide');

                                /* Remove the notification */
                                notification_container.innerHTML = '';

                                /* Set the localstorage to mention that the user was converted */
                                localStorage.setItem(`phone_collector_${biolink_block_id}`, true);

                                /* Set the submit button to disabled */
                                $(event.currentTarget).find('button[type="submit"]').attr('disabled', 'disabled');

                                if(data.details.thank_you_url) {
                                    window.location.replace(data.details.thank_you_url);
                                }

                            }, 1500);

                        }

                        /* Reset captcha */
                        try {
                            grecaptcha.reset();
                            hcaptcha.reset();
                            turnstile.reset();
                        } catch (error) {}
                    },
                    error: () => {
                        enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));
                        display_notifications(<?= json_encode(l('global.error_message.basic')) ?>, 'error', notification_container);
                    },
                });

            }

            event.preventDefault();
        })
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'phone_collector') ?>
<?php endif ?>

