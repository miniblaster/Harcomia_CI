<?php defined('ALTUMCODE') || die() ?>

<?= \Altum\Alerts::output_alerts() ?>

<h1 class="h5"><?= sprintf(l('login.header'), settings()->main->title) ?></h1>

<form action="" method="post" class="mt-4" role="form">
    <?php if(isset($_SESSION['twofa_required']) && $data->user && $data->user->twofa_secret && $data->user->status == 1): ?>
        <input id="email" type="hidden" name="email" value="<?= $data->user ? $data->values['email'] : null ?>" required="required" />
        <input id="password" type="hidden" name="password" value="<?= $data->user ? $data->values['password'] : null ?>" required="required" />
        <input id="rememberme" type="hidden" name="rememberme" value="<?= $data->values['rememberme'] ? '1' : null ?>">

        <div class="form-group">
            <label for="twofa_token"><?= l('login.form.twofa_token') ?></label>
            <input id="twofa_token" type="text" name="twofa_token" class="form-control <?= \Altum\Alerts::has_field_errors('twofa_token') ? 'is-invalid' : null ?>" required="required" autocomplete="off" autofocus="autofocus" />
            <?= \Altum\Alerts::output_field_error('twofa_token') ?>
        </div>

        <div class="form-group mt-3">
            <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= l('login.form.verify') ?></button>
        </div>

    <?php else: ?>

        <div class="form-group">
            <label for="email"><?= l('login.form.email') ?></label>
            <input id="email" type="text" name="email" class="form-control <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->values['email'] ?>" required="required" autofocus="autofocus" />
            <?= \Altum\Alerts::output_field_error('email') ?>
        </div>

        <div class="form-group">
            <label for="password"><?= l('login.form.password') ?></label>
            <input id="password" type="password" name="password" class="form-control <?= \Altum\Alerts::has_field_errors('password') ? 'is-invalid' : null ?>" value="<?= $data->user ? $data->values['password'] : null ?>" required="required" />
            <?= \Altum\Alerts::output_field_error('password') ?>
        </div>

        <?php if(settings()->captcha->login_is_enabled): ?>
            <div class="form-group">
                <?php $data->captcha->display() ?>
            </div>
        <?php endif ?>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="rememberme" class="custom-control-input" id="rememberme" <?= $data->values['rememberme'] ? 'checked="checked"' : null ?>>
                <label class="custom-control-label" for="rememberme"><small class="text-muted"><?= l('login.form.remember_me') ?></small></label>
            </div>

            <small class="text-muted">
                <a href="lost-password" class="text-muted"><?= l('login.display.lost_password') ?></a>
                <?php if(settings()->users->email_confirmation): ?>
                    / <a href="resend-activation" class="text-muted" role="button"><?= l('login.display.resend_activation') ?></a>
                <?php endif ?>
            </small>
        </div>

        <div class="form-group mt-4">
            <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= l('login.form.login') ?></button>
        </div>
    <?php endif ?>

    <?php if(settings()->facebook->is_enabled || settings()->google->is_enabled || settings()->twitter->is_enabled || settings()->discord->is_enabled): ?>
        <hr class="border-gray-100 my-3" />

        <div class="">
            <?php if(settings()->facebook->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/facebook-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/facebook.svg' ?>" class="mr-1" />
                        <?= l('login.display.facebook') ?>
                    </a>
                </div>
            <?php endif ?>
            <?php if(settings()->google->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/google-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/google.svg' ?>" class="mr-1" />
                        <?= l('login.display.google') ?>
                    </a>
                </div>
            <?php endif ?>
            <?php if(settings()->twitter->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/twitter-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/twitter.svg' ?>" class="mr-1" />
                        <?= l('login.display.twitter') ?>
                    </a>
                </div>
            <?php endif ?>
            <?php if(settings()->discord->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/discord-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/discord.svg' ?>" class="mr-1" />
                        <?= l('login.display.discord') ?>
                    </a>
                </div>
            <?php endif ?>
        </div>
    <?php endif ?>
</form>

<?php if(settings()->users->register_is_enabled): ?>
    <div class="mt-5 text-center text-muted">
        <?= sprintf(l('login.display.register'), '<a href="' . url('register') . '" class="font-weight-bold">' . l('login.display.register_help') . '</a>') ?></a>
    </div>
<?php endif ?>

