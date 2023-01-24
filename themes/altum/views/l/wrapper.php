<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html lang="<?= \Altum\Language::$code ?>" class="link-html" dir="<?= l('direction') ?>">
    <head>
        <title><?= \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL; ?>">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <?php if(\Altum\Meta::$description): ?>
            <meta name="description" content="<?= \Altum\Meta::$description ?>" />
        <?php endif ?>

        <?php if(\Altum\Meta::$open_graph['url']): ?>
            <!-- Open Graph / Facebook / Twitter -->
            <?php foreach(\Altum\Meta::$open_graph as $key => $value): ?>
                <?php if($value): ?>
                    <meta property="og:<?= $key ?>" content="<?= $value ?>" />
                    <meta property="twitter:<?= $key ?>" content="<?= $value ?>" />
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>

        <?php
        /* Block search engine indexing if the user wants, and if the system viewing links (for preview) are used */
        if($this->link->settings->seo->block ?? null || \Altum\Router::$original_request == 'l/link'):
        ?>
            <meta name="robots" content="noindex">
        <?php endif ?>

        <?php if(!empty($this->link->settings->favicon)): ?>
            <link href="<?= UPLOADS_FULL_URL . 'favicons/' . $this->link->settings->favicon ?>" rel="shortcut icon" />
        <?php elseif(!empty(settings()->main->favicon)): ?>
            <link href="<?= UPLOADS_FULL_URL . 'main/' . settings()->main->favicon ?>" rel="shortcut icon" />
        <?php endif ?>

        <?php foreach(['bootstrap.min.css', 'custom.css', 'link-custom.css', 'animate.min.css'] as $file): ?>
            <link href="<?= ASSETS_FULL_URL . 'css/' . $file . '?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
        <?php endforeach ?>

        <?php if($this->link->settings->font ?? null): ?>
            <?php $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php' ?>
            <?php if($biolink_fonts[$this->link->settings->font]['font_css_url']): ?>
                <link href="<?= $biolink_fonts[$this->link->settings->font]['font_css_url'] ?>" rel="stylesheet">
            <?php endif ?>

            <style>html, body {font-family: '<?= $biolink_fonts[$this->link->settings->font]['name'] ?>', "Helvetica Neue", Arial, sans-serif !important;}</style>
        <?php endif ?>
        <style>html {font-size: <?= (int) ($this->link->settings->font_size ?? 16) . 'px' ?> !important;}</style>

        <?= \Altum\Event::get_content('head') ?>

        <?php if(!empty(settings()->custom->head_js_biolink)): ?>
            <?= settings()->custom->head_js_biolink ?>
        <?php endif ?>

        <?php if(!empty(settings()->custom->head_css_biolink)): ?>
            <style><?= settings()->custom->head_css_biolink ?></style>
        <?php endif ?>

        <?php if(!empty($this->link->settings->custom_css) && $this->user->plan_settings->custom_css_is_enabled): ?>
            <style><?= $this->link->settings->custom_css ?></style>
        <?php endif ?>

        <?php if(!empty($this->link->settings->custom_js) && $this->user->plan_settings->custom_js_is_enabled): ?>
            <?= $this->link->settings->custom_js ?>
        <?php endif ?>

        <link rel="canonical" href="<?= $this->link->full_url ?>" />
    </head>
    <?php require THEME_PATH . 'views/partials/cookie_consent.php' ?>

    <?= $this->views['content'] ?>

    <?php require THEME_PATH . 'views/partials/js_global_variables.php' ?>

    <?php foreach(['libraries/jquery.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'custom.js', 'libraries/fontawesome-all.min.js'] as $file): ?>
        <script src="<?= ASSETS_FULL_URL ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
    <?php endforeach ?>

    <?= \Altum\Event::get_content('javascript') ?>
</html>
