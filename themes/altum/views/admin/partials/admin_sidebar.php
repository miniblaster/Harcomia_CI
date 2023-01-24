<?php defined('ALTUMCODE') || die() ?>

<section class="admin-sidebar">
    <div class="admin-sidebar-title">
        <a href="<?= url() ?>" class="text-decoration-none text-truncate" data-logo data-light-value="<?= settings()->main->logo_light != '' ? \Altum\Uploads::get_full_url('logo_light') . settings()->main->logo_light : settings()->main->title ?>" data-light-class="<?= settings()->main->logo_light != '' ? 'img-fluid admin-navbar-logo-top' : 'admin-navbar-brand' ?>" data-dark-value="<?= settings()->main->logo_dark != '' ? \Altum\Uploads::get_full_url('logo_dark') . settings()->main->logo_dark : settings()->main->title ?>" data-dark-class="<?= settings()->main->logo_dark != '' ? 'img-fluid admin-navbar-logo-top' : 'admin-navbar-brand' ?>">
            <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != ''): ?>
                <img src="<?= \Altum\Uploads::get_full_url('logo_' . \Altum\ThemeStyle::get()) . settings()->main->{'logo_' . \Altum\ThemeStyle::get()} ?>" class="img-fluid admin-navbar-logo" alt="<?= l('global.accessibility.logo_alt') ?>" />
            <?php else: ?>
                <span class="admin-navbar-brand"><?= settings()->main->title ?></span>
            <?php endif ?>
        </a>
    </div>

    <ul class="admin-sidebar-links">
        <li class="<?= \Altum\Router::$controller == 'AdminIndex' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-tv"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_index.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminUsers', 'AdminUserUpdate', 'AdminUserCreate', 'AdminUserView']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/users') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-users"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_users.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminUsersLogs']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/users-logs') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-scroll"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_users_logs.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminLinks']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/links') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-link"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_links.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminBiolinksThemes']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/biolinks-themes') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-palette"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_biolinks_themes.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminProjects']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/projects') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-project-diagram"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_projects.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminPixels']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/pixels') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-adjust"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_pixels.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminQrCodes']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/qr-codes') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-qrcode"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_qr_codes.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminDomains', 'AdminDomainCreate', 'AdminDomainUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/domains') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-globe"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_domains.menu') ?></span>
                </div>
            </a>
        </li>

        <?php if(\Altum\Plugin::is_active('teams')): ?>
            <li class="<?= \Altum\Router::$controller == 'AdminTeams' ? 'active' : null ?>">
                <a class="nav-link d-flex flex-row" href="<?= url('admin/teams') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-user-shield"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= l('admin_teams.menu') ?></span>
                    </div>
                </a>
            </li>
        <?php endif ?>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminPages', 'AdminPageCreate', 'AdminPageUpdate', 'AdminPagesCategories', 'AdminPagesCategoryCreate', 'AdminPagesCategoryUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="#admin_sidebar_resources_container" data-toggle="collapse" role="button" aria-expanded="false">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-info-circle"></i></div>
                <div class="col d-flex justify-content-between align-items-center">
                    <span class="d-inline"><?= l('admin_resources.menu') ?></span>
                    <i class="fa fa-fw fa-sm fa-caret-down"></i>
                </div>
            </a>
        </li>

        <div id="admin_sidebar_resources_container" class="collapse bg-gray-200 <?= in_array(\Altum\Router::$controller, ['AdminPages', 'AdminPageCreate', 'AdminPageUpdate', 'AdminPagesCategories', 'AdminPagesCategoryCreate', 'AdminPagesCategoryUpdate']) ? 'show' : null ?>">
            <li class="<?= in_array(\Altum\Router::$controller, ['AdminPagesCategories', 'AdminPagesCategoryCreate', 'AdminPagesCategoryUpdate']) ? 'active' : null ?>">
                <a class="nav-link d-flex flex-row" href="<?= url('admin/pages-categories') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-book"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= l('admin_pages_categories.menu') ?></span>
                    </div>
                </a>
            </li>

            <li class="<?= in_array(\Altum\Router::$controller, ['AdminPages', 'AdminPageCreate', 'AdminPageUpdate']) ? 'active' : null ?>">
                <a class="nav-link d-flex flex-row" href="<?= url('admin/pages') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-copy"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= l('admin_pages.menu') ?></span>
                    </div>
                </a>
            </li>
        </div>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminBlogPosts', 'AdminBlogPostCreate', 'AdminBlogPostUpdate', 'AdminBlogPostsCategories', 'AdminBlogPostsCategoryCreate', 'AdminBlogPostsCategoryUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="#admin_sidebar_blog_container" data-toggle="collapse" role="button" aria-expanded="false">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-blog"></i></div>
                <div class="col d-flex justify-content-between align-items-center">
                    <span class="d-inline"><?= l('admin_blog.menu') ?></span>
                    <i class="fa fa-fw fa-sm fa-caret-down"></i>
                </div>
            </a>
        </li>

        <div id="admin_sidebar_blog_container" class="collapse bg-gray-200 <?= in_array(\Altum\Router::$controller, ['AdminBlogPosts', 'AdminBlogPostCreate', 'AdminBlogPostUpdate', 'AdminBlogPostsCategories', 'AdminBlogPostsCategoryCreate', 'AdminBlogPostsCategoryUpdate']) ? 'show' : null ?>">
            <li class="<?= in_array(\Altum\Router::$controller, ['AdminBlogPostsCategories', 'AdminBlogPostsCategoryCreate', 'AdminBlogPostsCategoryUpdate']) ? 'active' : null ?>">
                <a class="nav-link d-flex flex-row" href="<?= url('admin/blog-posts-categories') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-map"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= l('admin_blog_posts_categories.menu') ?></span>
                    </div>
                </a>
            </li>

            <li class="<?= in_array(\Altum\Router::$controller, ['AdminBlogPosts', 'AdminBlogPostCreate', 'AdminBlogPostUpdate']) ? 'active' : null ?>">
                <a class="nav-link d-flex flex-row" href="<?= url('admin/blog-posts') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-paste"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= l('admin_blog_posts.menu') ?></span>
                    </div>
                </a>
            </li>
        </div>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminPlans', 'AdminPlanCreate', 'AdminPlanUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/plans') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-box-open"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_plans.menu') ?></span>
                </div>
            </a>
        </li>

        <?php if(in_array(settings()->license->type, ['SPECIAL','Extended License'])): ?>
        <li class="<?= in_array(\Altum\Router::$controller, ['AdminCodes', 'AdminCodeCreate', 'AdminCodeUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/codes') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-tags"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_codes.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= in_array(\Altum\Router::$controller, ['AdminTaxes', 'AdminTaxCreate', 'AdminTaxUpdate']) ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/taxes') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-paperclip"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_taxes.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= \Altum\Router::$controller == 'AdminPayments' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/payments') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-credit-card"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_payments.menu') ?></span>
                </div>
            </a>
        </li>

            <?php if(\Altum\Plugin::is_active('affiliate')): ?>
            <li class="<?= \Altum\Router::$controller == 'AdminAffiliatesWithdrawals' ? 'active' : null ?>">
                <a class="nav-link d-flex flex-row" href="<?= url('admin/affiliates-withdrawals') ?>">
                    <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-wallet"></i></div>
                    <div class="col">
                        <span class="d-inline"><?= l('admin_affiliates_withdrawals.menu') ?></span>
                    </div>
                </a>
            </li>
            <?php endif ?>
        <?php endif ?>

        <li class="<?= \Altum\Router::$controller == 'AdminStatistics' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/statistics') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-chart-bar"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_statistics.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= \Altum\Router::$controller == 'AdminApiDocumentation' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/api-documentation') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-code"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_api_documentation.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= \Altum\Router::$controller == 'AdminPlugins' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/plugins') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-puzzle-piece"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_plugins.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= \Altum\Router::$controller == 'AdminLanguages' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/languages') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-language"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_languages.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="<?= \Altum\Router::$controller == 'AdminSettings' ? 'active' : null ?>">
            <a class="nav-link d-flex flex-row" href="<?= url('admin/settings') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-wrench"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('admin_settings.menu') ?></span>
                </div>
            </a>
        </li>
    </ul>

    <hr />

    <ul class="admin-sidebar-links">
        <li>
            <a class="nav-link d-flex flex-row" target="_blank" href="<?= url('dashboard') ?>">
                <div class="col-1 d-flex align-items-center"><i class="fa fa-fw fa-sm fa-home"></i></div>
                <div class="col">
                    <span class="d-inline"><?= l('dashboard.menu') ?></span>
                </div>
            </a>
        </li>

        <li class="dropdown">
            <a class="nav-link d-flex flex-row dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                <div class="col-1 d-flex align-items-center"><img src="<?= get_gravatar($this->user->email) ?>" class="admin-avatar" loading="lazy" /></div>
                <div class="col text-truncate">
                    <span class="d-inline"><?= $this->user->name ?></span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= url('account') ?>"><i class="fa fa-fw fa-sm fa-sm fa-wrench mr-2"></i> <?= l('account.menu') ?></a>
                <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa fa-fw fa-sm fa-sm fa-sign-out-alt mr-2"></i> <?= l('global.menu.logout') ?></a>
            </div>
        </li>
    </ul>
</section>
