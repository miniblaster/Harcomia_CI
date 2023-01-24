<?php defined('ALTUMCODE') || die() ?>

<footer class="mt-5 d-flex flex-column flex-lg-row justify-content-between">
    <div class="mb-3 mb-lg-0">
        <div class="mb-2"><?= sprintf(l('global.footer.copyright'), date('Y'), settings()->main->title) ?></div>

        <div>Powered by <img src="<?= ASSETS_FULL_URL . 'images/altumcode.png' ?>" class="icon-favicon" alt="AltumCode logo" /> <a href="https://altumcode.com/" target="_blank">AltumCode</a>.</div>
    </div>

    <div class="d-flex flex-column flex-lg-row">
        <?php if(count(\Altum\Language::$active_languages) > 1): ?>
            <div class="dropdown mb-2 ml-lg-3" data-toggle="tooltip" title="<?= l('global.choose_language') ?>">
                <button type="button" class="btn btn-link text-decoration-none p-0" id="language_switch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-fw fa-sm fa-language mr-1"></i> <?= \Altum\Language::$name ?>
                </button>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="language_switch">
                    <?php foreach(\Altum\Language::$active_languages as $language_name => $language_code): ?>
                        <a class="dropdown-item" href="<?= SITE_URL . $language_code . '/' . \Altum\Router::$original_request . '?set_language=' . $language_name ?>">
                            <?php if($language_name == \Altum\Language::$name): ?>
                                <i class="fa fa-fw fa-sm fa-check mr-2 text-success"></i>
                            <?php else: ?>
                                <i class="fa fa-fw fa-sm fa-circle-notch mr-2 text-muted"></i>
                            <?php endif ?>

                            <?= $language_name ?>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>

        <?php if(count(\Altum\ThemeStyle::$themes) > 1): ?>
            <div class="mb-2 ml-lg-3">
                <button type="button" id="switch_theme_style" class="btn btn-link text-decoration-none p-0" data-toggle="tooltip" title="<?= sprintf(l('global.theme_style'), (\Altum\ThemeStyle::get() == 'light' ? l('global.theme_style_dark') : l('global.theme_style_light'))) ?>" data-title-theme-style-light="<?= sprintf(l('global.theme_style'), l('global.theme_style_light')) ?>" data-title-theme-style-dark="<?= sprintf(l('global.theme_style'), l('global.theme_style_dark')) ?>">
                    <span data-theme-style="light" class="<?= \Altum\ThemeStyle::get() == 'light' ? null : 'd-none' ?>"><i class="fa fa-fw fa-sm fa-sun mr-1"></i> <?=  l('global.theme_style_light') ?></span>
                    <span data-theme-style="dark" class="<?= \Altum\ThemeStyle::get() == 'dark' ? null : 'd-none' ?>"><i class="fa fa-fw fa-sm fa-moon mr-1"></i> <?=  l('global.theme_style_dark') ?></span>
                </button>
            </div>

            <?php include_view(THEME_PATH . 'views/partials/theme_style_js.php') ?>
        <?php endif ?>
    </div>
</footer>
