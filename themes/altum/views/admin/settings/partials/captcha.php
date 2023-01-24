<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="type"><?= l('admin_settings.captcha.type') ?></label>
        <select id="type" name="type" class="form-control form-control-lg">
            <option value="basic" <?= settings()->captcha->type == 'basic' ? 'selected="selected"' : null ?>><?= l('admin_settings.captcha.type.basic') ?></option>
            <option value="recaptcha" <?= settings()->captcha->type == 'recaptcha' ? 'selected="selected"' : null ?>><?= l('admin_settings.captcha.type.recaptcha') ?></option>
            <option value="hcaptcha" <?= settings()->captcha->type == 'hcaptcha' ? 'selected="selected"' : null ?>><?= l('admin_settings.captcha.type.hcaptcha') ?></option>
            <option value="turnstile" <?= settings()->captcha->type == 'turnstile' ? 'selected="selected"' : null ?>><?= l('admin_settings.captcha.type.turnstile') ?></option>
        </select>
    </div>

    <div id="recaptcha">
        <div class="form-group">
            <label for="recaptcha_public_key"><?= l('admin_settings.captcha.recaptcha_public_key') ?></label>
            <input id="recaptcha_public_key" type="text" name="recaptcha_public_key" class="form-control form-control-lg" value="<?= settings()->captcha->recaptcha_public_key ?>" />
        </div>

        <div class="form-group">
            <label for="recaptcha_private_key"><?= l('admin_settings.captcha.recaptcha_private_key') ?></label>
            <input id="recaptcha_private_key" type="text" name="recaptcha_private_key" class="form-control form-control-lg" value="<?= settings()->captcha->recaptcha_private_key ?>" />
        </div>
    </div>

    <div id="hcaptcha">
        <div class="form-group">
            <label for="hcaptcha_site_key"><?= l('admin_settings.captcha.hcaptcha_site_key') ?></label>
            <input id="hcaptcha_site_key" type="text" name="hcaptcha_site_key" class="form-control form-control-lg" value="<?= settings()->captcha->hcaptcha_site_key ?>" />
        </div>

        <div class="form-group">
            <label for="hcaptcha_secret_key"><?= l('admin_settings.captcha.hcaptcha_secret_key') ?></label>
            <input id="hcaptcha_secret_key" type="text" name="hcaptcha_secret_key" class="form-control form-control-lg" value="<?= settings()->captcha->hcaptcha_secret_key ?>" />
        </div>
    </div>

    <div id="turnstile">
        <div class="form-group">
            <label for="turnstile_site_key"><?= l('admin_settings.captcha.turnstile_site_key') ?></label>
            <input id="turnstile_site_key" type="text" name="turnstile_site_key" class="form-control form-control-lg" value="<?= settings()->captcha->turnstile_site_key ?>" />
        </div>

        <div class="form-group">
            <label for="turnstile_secret_key"><?= l('admin_settings.captcha.turnstile_secret_key') ?></label>
            <input id="turnstile_secret_key" type="text" name="turnstile_secret_key" class="form-control form-control-lg" value="<?= settings()->captcha->turnstile_secret_key ?>" />
        </div>
    </div>

    <?php foreach(['login', 'register', 'lost_password', 'resend_activation', 'contact', 'biolink'] as $key): ?>
        <div class="form-group">
            <label for="<?= $key ?>_is_enabled"><?= l('admin_settings.captcha.' . $key . '_is_enabled') ?></label>
            <select id="<?= $key ?>_is_enabled" name="<?= $key ?>_is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->captcha->{$key . '_is_enabled'} ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                <option value="0" <?= !settings()->captcha->{$key . '_is_enabled'} ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
            </select>
        </div>
    <?php endforeach ?>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>

<?php ob_start() ?>
<script>
    'use strict';

    /* Captcha */
    let initiate_captcha_type = () => {
        switch(document.querySelector('select[name="type"]').value) {
            case 'basic':
                document.querySelector('#hcaptcha').classList.add('d-none');
                document.querySelector('#recaptcha').classList.add('d-none');
                document.querySelector('#turnstile').classList.add('d-none');
                break;

            case 'recaptcha':
                document.querySelector('#hcaptcha').classList.add('d-none');
                document.querySelector('#recaptcha').classList.remove('d-none');
                document.querySelector('#turnstile').classList.add('d-none');
                break;

            case 'hcaptcha':
                document.querySelector('#hcaptcha').classList.remove('d-none');
                document.querySelector('#recaptcha').classList.add('d-none');
                document.querySelector('#turnstile').classList.add('d-none');
                break;

            case 'turnstile':
                document.querySelector('#hcaptcha').classList.add('d-none');
                document.querySelector('#recaptcha').classList.add('d-none');
                document.querySelector('#turnstile').classList.remove('d-none');
                break;
        }
    }

    initiate_captcha_type();
    document.querySelector('select[name="type"]').addEventListener('change', initiate_captcha_type);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

