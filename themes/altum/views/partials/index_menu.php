<?php defined('ALTUMCODE') || die() ?>

<nav id="navbar" class="navbar navbar-index navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand font-weight-bold" href="<?= url() ?>" data-logo data-light-value="<?= settings()->main->logo_light != '' ? \Altum\Uploads::get_full_url('logo_light') . settings()->main->logo_light : settings()->main->title ?>" data-light-class="<?= settings()->main->logo_light != '' ? 'img-fluid navbar-logo' : '' ?>" data-dark-value="<?= settings()->main->logo_dark != '' ? \Altum\Uploads::get_full_url('logo_dark') . settings()->main->logo_dark : settings()->main->title ?>" data-dark-class="<?= settings()->main->logo_dark != '' ? 'img-fluid navbar-logo' : '' ?>">
            <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != ''): ?>
                <img src="<?= \Altum\Uploads::get_full_url('logo_' . \Altum\ThemeStyle::get()) . settings()->main->{'logo_' . \Altum\ThemeStyle::get()} ?>" class="img-fluid navbar-logo" alt="<?= l('global.accessibility.logo_alt') ?>" />
            <?php else: ?>
                <?= settings()->main->title ?>
            <?php endif ?>
        </a>

        <button class="btn navbar-custom-toggler text-dark border-dark d-lg-none" type="button" data-toggle="collapse" data-target="#main_navbar" aria-controls="main_navbar" aria-expanded="false" aria-label="<?= l('global.accessibility.toggle_navigation') ?>">
            <i class="fa fa-fw fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="main_navbar">
            <ul class="navbar-nav">

                <?php foreach($data->pages as $data): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $data->url ?>" target="<?= $data->target ?>"><?= $data->title ?></a></li>
                <?php endforeach ?>

                <?php if(settings()->links->biolinks_is_enabled && settings()->links->directory_is_enabled): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('directory') ?>"><?= l('directory.menu') ?></a></li>
                <?php endif ?>

                <?php if(settings()->tools->is_enabled && settings()->tools->access == 'everyone'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('tools') ?>"><?= l('tools.menu') ?></a></li>
                <?php endif ?>

                <?php if(\Altum\Authentication::check()): ?>

                    <li class="nav-item"><a class="nav-link" href="<?= url('dashboard') ?>"> <?= l('dashboard.menu') ?></a></li>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                            <img src="<?= get_gravatar($this->user->email, 80, 'identicon') ?>" class="navbar-avatar mr-1" loading="lazy" />
                            <?= $this->user->name ?> <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if(!\Altum\Teams::is_delegated()): ?>
                                <?php if(\Altum\Authentication::is_admin()): ?>
                                    <a class="dropdown-item" href="<?= url('admin') ?>"><i class="fa fa-fw fa-sm fa-fingerprint mr-2"></i> <?= l('global.menu.admin') ?></a>
                                <?php endif ?>
                            <?php endif ?>

                            <?php if(settings()->links->biolinks_is_enabled): ?>
                                <a class="dropdown-item" href="<?= url('links?type=biolink') ?>"><i class="fa fa-fw fa-sm fa-hashtag mr-2"></i> <?= l('links.menu.biolinks') ?></a>
                            <?php endif ?>

                            <?php if(settings()->links->shortener_is_enabled): ?>
                                <a class="dropdown-item" href="<?= url('links?type=link') ?>"><i class="fa fa-fw fa-sm fa-link mr-2"></i> <?= l('links.menu.links') ?></a>
                            <?php endif ?>

                            <?php if(settings()->links->files_is_enabled): ?>
                                <a class="dropdown-item" href="<?= url('links?type=file') ?>"><i class="fa fa-fw fa-sm fa-file mr-2"></i> <?= l('links.menu.files') ?></a>
                            <?php endif ?>

                            <?php if(settings()->links->vcards_is_enabled): ?>
                                <a class="dropdown-item" href="<?= url('links?type=vcard') ?>"><i class="fa fa-fw fa-sm fa-id-card mr-2"></i> <?= l('links.menu.vcards') ?></a>
                            <?php endif ?>

                            <?php if(settings()->links->qr_codes_is_enabled): ?>
                                <a class="dropdown-item" href="<?= url('qr-codes') ?>"><i class="fa fa-fw fa-sm fa-qrcode mr-2"></i> <?= l('qr_codes.menu') ?></a>
                            <?php endif ?>

                            <?php if(settings()->tools->is_enabled && settings()->tools->access == 'users'): ?>
                                <a class="dropdown-item" href="<?= url('tools') ?>"><i class="fa fa-fw fa-sm fa-tools mr-2"></i> <?= l('tools.menu') ?></a>
                            <?php endif ?>

                            <div class="dropdown-divider"></div>

                            <?php if(settings()->links->domains_is_enabled): ?>
                                <a class="dropdown-item" href="<?= url('domains') ?>"><i class="fa fa-fw fa-sm fa-globe mr-2"></i> <?= l('domains.menu') ?></a>
                            <?php endif ?>

                            <a class="dropdown-item" href="<?= url('pixels') ?>"><i class="fa fa-fw fa-sm fa-adjust mr-2"></i> <?= l('pixels.menu') ?></a>

                            <?php if(settings()->links->biolinks_is_enabled): ?>
                                <a class="dropdown-item" href="<?= url('data') ?>"><i class="fa fa-fw fa-sm fa-database mr-2"></i> <?= l('data.menu') ?></a>

                                <?php if(\Altum\Plugin::is_active('payment-blocks')): ?>
                                    <a class="dropdown-item" href="<?= url('payment-processors') ?>"><i class="fa fa-fw fa-sm fa-credit-card mr-2"></i> <?= l('payment_processors.menu') ?></a>
                                    <a class="dropdown-item" href="<?= url('guests-payments') ?>"><i class="fa fa-fw fa-sm fa-coins mr-2"></i> <?= l('guests_payments.menu') ?></a>
                                <?php endif ?>
                            <?php endif ?>

                            <a class="dropdown-item" href="<?= url('projects') ?>"><i class="fa fa-fw fa-sm fa-project-diagram mr-2"></i> <?= l('projects.menu') ?></a>

                            <div class="dropdown-divider"></div>

                            <?php if(!\Altum\Teams::is_delegated()): ?>
                                <a class="dropdown-item" href="<?= url('account') ?>"><i class="fa fa-fw fa-sm fa-wrench mr-2"></i> <?= l('account.menu') ?></a>

                                <a class="dropdown-item" href="<?= url('account-plan') ?>"><i class="fa fa-fw fa-sm fa-box-open mr-2"></i> <?= l('account_plan.menu') ?></a>

                                <?php if(settings()->payment->is_enabled): ?>
                                    <a class="dropdown-item" href="<?= url('account-payments') ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-2"></i> <?= l('account_payments.menu') ?></a>

                                    <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                                        <a class="dropdown-item" href="<?= url('referrals') ?>"><i class="fa fa-fw fa-sm fa-wallet mr-2"></i> <?= l('referrals.menu') ?></a>
                                    <?php endif ?>
                                <?php endif ?>

                                <a class="dropdown-item" href="<?= url('account-api') ?>"><i class="fa fa-fw fa-sm fa-code mr-2"></i> <?= l('account_api.menu') ?></a>

                                <?php if(\Altum\Plugin::is_active('teams')): ?>
                                    <a class="dropdown-item" href="<?= url('teams-system') ?>"><i class="fa fa-fw fa-sm fa-user-shield mr-2"></i> <?= l('teams_system.menu') ?></a>
                                <?php endif ?>
                            <?php endif ?>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa fa-fw fa-sm fa-sign-out-alt mr-2"></i> <?= l('global.menu.logout') ?></a>
                        </div>
                    </li>

                <?php else: ?>

                    <li class="nav-item d-flex align-items-center">
                        <a class="btn btn-sm btn-light" href="<?= url('login') ?>"><i class="fa fa-fw fa-sm fa-sign-in-alt"></i> <?= l('login.menu') ?></a>
                    </li>

                    <?php if(settings()->users->register_is_enabled): ?>
                        <li class="nav-item d-flex align-items-center">
                            <a class="btn btn-sm btn-primary" href="<?= url('register') ?>"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('register.menu') ?></a>
                        </li>
                    <?php endif ?>

                <?php endif ?>

            </ul>
        </div>
    </div>
</nav>
