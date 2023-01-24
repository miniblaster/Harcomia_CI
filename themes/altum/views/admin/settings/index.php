<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex mb-4">
    <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-wrench text-primary-900 mr-2"></i> <?= l('admin_settings.header') ?></h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="row">
    <div class="mb-3 mb-xl-5 mb-xl-0 col-12 col-xl-4">
        <div class="d-xl-none">
            <select name="settings_menu" class="form-control">
                <option value="<?= url('admin/settings/main') ?>" class="nav-link" <?= $data->method == 'main' ? 'selected="selected"' : null ?>><?= l('admin_settings.main.tab') ?></option>
                <option value="<?= url('admin/settings/users') ?>" class="nav-link" <?= $data->method == 'users' ? 'selected="selected"' : null ?>><?= l('admin_settings.users.tab') ?></option>
                <option value="<?= url('admin/settings/links') ?>" class="nav-link" <?= $data->method == 'links' ? 'selected="selected"' : null ?>><?= l('admin_settings.links.tab') ?></option>
                <option value="<?= url('admin/settings/tools') ?>" class="nav-link" <?= $data->method == 'tools' ? 'selected="selected"' : null ?>><?= l('admin_settings.tools.tab') ?></option>
                <option value="<?= url('admin/settings/payment') ?>" class="nav-link" <?= $data->method == 'payment' ? 'selected="selected"' : null ?>><?= l('admin_settings.payment.tab') ?></option>
                <option value="<?= url('admin/settings/business') ?>" class="nav-link" <?= $data->method == 'business' ? 'selected="selected"' : null ?>><?= l('admin_settings.business.tab') ?></option>
                <?php foreach($data->payment_processors as $key => $value): ?>
                    <option value="<?= url('admin/settings/' . $key) ?>" class="nav-link" <?= $data->method == $key ? 'selected="selected"' : null ?>><?= l('admin_settings.' . $key . '.tab') ?></option>
                <?php endforeach ?>
                <option value="<?= url('admin/settings/affiliate') ?>" class="nav-link" <?= $data->method == 'affiliate' ? 'selected="selected"' : null ?>><?= l('admin_settings.affiliate.tab') ?></option>
                <option value="<?= url('admin/settings/captcha') ?>" class="nav-link" <?= $data->method == 'captcha' ? 'selected="selected"' : null ?>><?= l('admin_settings.captcha.tab') ?></option>
                <option value="<?= url('admin/settings/facebook') ?>" class="nav-link" <?= $data->method == 'facebook' ? 'selected="selected"' : null ?>><?= l('admin_settings.facebook.tab') ?></option>
                <option value="<?= url('admin/settings/google') ?>" class="nav-link" <?= $data->method == 'google' ? 'selected="selected"' : null ?>><?= l('admin_settings.google.tab') ?></option>
                <option value="<?= url('admin/settings/twitter') ?>" class="nav-link" <?= $data->method == 'twitter' ? 'selected="selected"' : null ?>><?= l('admin_settings.twitter.tab') ?></option>
                <option value="<?= url('admin/settings/discord') ?>" class="nav-link" <?= $data->method == 'discord' ? 'selected="selected"' : null ?>><?= l('admin_settings.discord.tab') ?></option>
                <option value="<?= url('admin/settings/ads') ?>" class="nav-link" <?= $data->method == 'ads' ? 'selected="selected"' : null ?>><?= l('admin_settings.ads.tab') ?></option>
                <option value="<?= url('admin/settings/cookie_consent') ?>" class="nav-link" <?= $data->method == 'cookie_consent' ? 'selected="selected"' : null ?>><?= l('admin_settings.cookie_consent.tab') ?></option>
                <option value="<?= url('admin/settings/socials') ?>" class="nav-link" <?= $data->method == 'socials' ? 'selected="selected"' : null ?>><?= l('admin_settings.socials.tab') ?></option>
                <option value="<?= url('admin/settings/smtp') ?>" class="nav-link" <?= $data->method == 'smtp' ? 'selected="selected"' : null ?>><?= l('admin_settings.smtp.tab') ?></option>
                <option value="<?= url('admin/settings/custom') ?>" class="nav-link" <?= $data->method == 'custom' ? 'selected="selected"' : null ?>><?= l('admin_settings.custom.tab') ?></option>
                <option value="<?= url('admin/settings/announcements') ?>" class="nav-link" <?= $data->method == 'announcements' ? 'selected="selected"' : null ?>><?= l('admin_settings.announcements.tab') ?></option>
                <option value="<?= url('admin/settings/email_notifications') ?>" class="nav-link" <?= $data->method == 'email_notifications' ? 'selected="selected"' : null ?>><?= l('admin_settings.email_notifications.tab') ?></option>
                <option value="<?= url('admin/settings/webhooks') ?>" class="nav-link" <?= $data->method == 'webhooks' ? 'selected="selected"' : null ?>><?= l('admin_settings.webhooks.tab') ?></option>
                <option value="<?= url('admin/settings/offload') ?>" class="nav-link" <?= $data->method == 'offload' ? 'selected="selected"' : null ?>><?= l('admin_settings.offload.tab') ?></option>
                <option value="<?= url('admin/settings/cron') ?>" class="nav-link" <?= $data->method == 'cron' ? 'selected="selected"' : null ?>><?= l('admin_settings.cron.tab') ?></option>
                <option value="<?= url('admin/settings/cache') ?>" class="nav-link" <?= $data->method == 'cache' ? 'selected="selected"' : null ?>><?= l('admin_settings.cache.tab') ?></option>
                <option value="<?= url('admin/settings/license') ?>" class="nav-link" <?= $data->method == 'license' ? 'selected="selected"' : null ?>><?= l('admin_settings.license.tab') ?></option>
                <option value="<?= url('admin/settings/support') ?>" class="nav-link" <?= $data->method == 'support' ? 'selected="selected"' : null ?>><?= l('admin_settings.support.tab') ?></option>
            </select>
        </div>

        <?php ob_start() ?>
        <script>
            document.querySelector('select[name="settings_menu"]').addEventListener('change', event => {
                document.querySelector(`a[href="${event.currentTarget.value}"]`).click();
            })
        </script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

        <div class="card d-none d-xl-flex">
            <div class="card-body">
                <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                    <a class="nav-link <?= $data->method == 'main' ? 'active' : null ?>" href="<?= url('admin/settings/main') ?>"><i class="fa fa-fw fa-sm fa-home mr-1"></i> <?= l('admin_settings.main.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'users' ? 'active' : null ?>" href="<?= url('admin/settings/users') ?>"><i class="fa fa-fw fa-sm fa-users mr-1"></i> <?= l('admin_settings.users.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'links' ? 'active' : null ?>" href="<?= url('admin/settings/links') ?>"><i class="fa fa-fw fa-sm fa-link mr-1"></i> <?= l('admin_settings.links.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'tools' ? 'active' : null ?>" href="<?= url('admin/settings/tools') ?>"><i class="fa fa-fw fa-sm fa-tools mr-1"></i> <?= l('admin_settings.tools.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'payment' ? 'active' : null ?>" href="<?= url('admin/settings/payment') ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= l('admin_settings.payment.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'business' ? 'active' : null ?>" href="<?= url('admin/settings/business') ?>"><i class="fa fa-fw fa-sm fa-briefcase mr-1"></i> <?= l('admin_settings.business.tab') ?></a>

                    <a class="nav-link <?= array_key_exists($data->method, $data->payment_processors) ? 'active' : null ?>" data-toggle="collapse" href="#payment_processors_collapse">
                        <i class="fa fa-fw fa-sm fa-credit-card mr-1"></i> <?= l('admin_settings.payment_processors') ?>
                    </a>
                    <div class="bg-gray-300 rounded collapse <?= array_key_exists($data->method, $data->payment_processors) ? 'show' : null ?>" id="payment_processors_collapse">
                        <div>
                            <?php foreach($data->payment_processors as $key => $value): ?>
                                <a class="nav-link <?= $data->method == $key ? 'active' : null ?>" href="<?= url('admin/settings/' . $key) ?>"><i class="<?= $value['icon'] ?> fa-fw fa-sm mr-1"></i> <?= l('admin_settings.' . $key . '.tab') ?></a>
                            <?php endforeach ?>
                        </div>
                    </div>

                    <a class="nav-link <?= $data->method == 'affiliate' ? 'active' : null ?>" href="<?= url('admin/settings/affiliate') ?>"><i class="fa fa-fw fa-sm fa-wallet mr-1"></i> <?= l('admin_settings.affiliate.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'captcha' ? 'active' : null ?>" href="<?= url('admin/settings/captcha') ?>"><i class="fa fa-fw fa-sm fa-low-vision mr-1"></i> <?= l('admin_settings.captcha.tab') ?></a>

                    <a class="nav-link <?= in_array($data->method, ['facebook', 'google', 'twitter', 'discord']) ? 'active' : null ?>" data-toggle="collapse" href="#social_logins_collapse">
                        <i class="fa fa-fw fa-sm fa-share-alt mr-1"></i> <?= l('admin_settings.social_logins') ?>
                    </a>
                    <div class="bg-gray-300 rounded collapse <?= in_array($data->method, ['facebook', 'google', 'twitter', 'discord']) ? 'show' : null ?>" id="social_logins_collapse">
                        <div>
                            <a class="nav-link <?= $data->method == 'facebook' ? 'active' : null ?>" href="<?= url('admin/settings/facebook') ?>"><i class="fab fa-fw fa-sm fa-facebook mr-1"></i> <?= l('admin_settings.facebook.tab') ?></a>
                            <a class="nav-link <?= $data->method == 'google' ? 'active' : null ?>" href="<?= url('admin/settings/google') ?>"><i class="fab fa-fw fa-sm fa-google mr-1"></i> <?= l('admin_settings.google.tab') ?></a>
                            <a class="nav-link <?= $data->method == 'twitter' ? 'active' : null ?>" href="<?= url('admin/settings/twitter') ?>"><i class="fab fa-fw fa-sm fa-twitter mr-1"></i> <?= l('admin_settings.twitter.tab') ?></a>
                            <a class="nav-link <?= $data->method == 'discord' ? 'active' : null ?>" href="<?= url('admin/settings/discord') ?>"><i class="fab fa-fw fa-sm fa-discord mr-1"></i> <?= l('admin_settings.discord.tab') ?></a>
                        </div>
                    </div>

                    <a class="nav-link <?= $data->method == 'ads' ? 'active' : null ?>" href="<?= url('admin/settings/ads') ?>"><i class="fa fa-fw fa-sm fa-ad mr-1"></i> <?= l('admin_settings.ads.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'cookie_consent' ? 'active' : null ?>" href="<?= url('admin/settings/cookie_consent') ?>"><i class="fa fa-fw fa-sm fa-cookie mr-1"></i> <?= l('admin_settings.cookie_consent.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'socials' ? 'active' : null ?>" href="<?= url('admin/settings/socials') ?>"><i class="fab fa-fw fa-sm fa-instagram mr-1"></i> <?= l('admin_settings.socials.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'smtp' ? 'active' : null ?>" href="<?= url('admin/settings/smtp') ?>"><i class="fa fa-fw fa-sm fa-mail-bulk mr-1"></i> <?= l('admin_settings.smtp.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'custom' ? 'active' : null ?>" href="<?= url('admin/settings/custom') ?>"><i class="fa fa-fw fa-sm fa-paint-brush mr-1"></i> <?= l('admin_settings.custom.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'announcements' ? 'active' : null ?>" href="<?= url('admin/settings/announcements') ?>"><i class="fa fa-fw fa-sm fa-bullhorn mr-1"></i> <?= l('admin_settings.announcements.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'email_notifications' ? 'active' : null ?>" href="<?= url('admin/settings/email_notifications') ?>"><i class="fa fa-fw fa-sm fa-bell mr-1"></i> <?= l('admin_settings.email_notifications.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'webhooks' ? 'active' : null ?>" href="<?= url('admin/settings/webhooks') ?>"><i class="fa fa-fw fa-sm fa-code-branch mr-1"></i> <?= l('admin_settings.webhooks.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'offload' ? 'active' : null ?>" href="<?= url('admin/settings/offload') ?>"><i class="fa fa-fw fa-sm fa-copy mr-1"></i> <?= l('admin_settings.offload.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'cron' ? 'active' : null ?>" href="<?= url('admin/settings/cron') ?>"><i class="fa fa-fw fa-sm fa-sync mr-1"></i> <?= l('admin_settings.cron.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'cache' ? 'active' : null ?>" href="<?= url('admin/settings/cache') ?>"><i class="fa fa-fw fa-sm fa-database mr-1"></i> <?= l('admin_settings.cache.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'license' ? 'active' : null ?>" href="<?= url('admin/settings/license') ?>"><i class="fa fa-fw fa-sm fa-key mr-1"></i> <?= l('admin_settings.license.tab') ?></a>
                    <a class="nav-link <?= $data->method == 'support' ? 'active' : null ?>" href="<?= url('admin/settings/support') ?>"><i class="fa fa-fw fa-sm fa-life-ring mr-1"></i> <?= l('admin_settings.support.tab') ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card">
            <div class="card-body">

                <form action="<?= url('admin/settings/' . $data->method) ?>" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                    <?= $this->views['method'] ?>
                </form>

            </div>
        </div>

        <p class="text-muted my-3"><?= l('admin_settings.documentation') ?></p>
    </div>
</div>
