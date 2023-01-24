<?php defined('ALTUMCODE') || die(); ?>

<?php if(settings()->cookie_consent->is_enabled): ?>

    <?php ob_start() ?>
    <script src="<?= ASSETS_FULL_URL ?>js/libraries/cookieconsent.js?v=<?= PRODUCT_CODE ?>"></script>
    <link href="<?= ASSETS_FULL_URL . 'css/libraries/cookieconsent.css?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen">

    <script>
        let language_code = document.documentElement.getAttribute('lang');
        let languages = {};
        languages[language_code] = {
            consent_modal: {
                title: <?= json_encode(l('global.cookie_consent.header')) ?>,
                description: <?= json_encode(l('global.cookie_consent.subheader')) ?>,
                primary_btn: {
                    text: <?= json_encode(l('global.cookie_consent.accept_all')) ?>,
                    role: 'accept_all'
                },
                secondary_btn: {
                    text: <?= json_encode(l('global.cookie_consent.reject_all')) ?>,
                    role: 'accept_necessary'
                }
            },
            settings_modal: {
                title: <?= json_encode(l('global.cookie_consent.modal.preferences.header')) ?>,
                save_settings_btn: <?= json_encode(l('global.cookie_consent.save')) ?>,
                accept_all_btn: <?= json_encode(l('global.cookie_consent.accept_all')) ?>,
                reject_all_btn: <?= json_encode(l('global.cookie_consent.reject_all')) ?>,
                close_btn_label: <?= json_encode(l('global.cookie_consent.close')) ?>,
                blocks: [
                    {
                        title: <?= json_encode(l('global.cookie_consent.modal.header')) ?>,
                        description: <?= json_encode(sprintf(l('global.cookie_consent.modal.subheader'), settings()->main->privacy_policy_url)) ?>
                    },

                    <?php if(settings()->cookie_consent->necessary_is_enabled): ?>
                    {
                        title: <?= json_encode(l('global.cookie_consent.modal.necessary.header')) ?>,
                        description: <?= json_encode(l('global.cookie_consent.modal.necessary.subheader')) ?>,
                        toggle: {
                            value: 'necessary',
                            enabled: true,
                            readonly: true
                        }
                    },
                    <?php endif ?>

                    <?php if(settings()->cookie_consent->analytics_is_enabled): ?>
                    {
                        title: <?= json_encode(l('global.cookie_consent.modal.analytics.header')) ?>,
                        description: <?= json_encode(l('global.cookie_consent.modal.analytics.subheader')) ?>,
                        toggle: {
                            value: 'analytics',
                            enabled: false,
                            readonly: false
                        }
                    },
                    <?php endif ?>

                    <?php if(settings()->cookie_consent->targeting_is_enabled): ?>
                    {
                        title: <?= json_encode(l('global.cookie_consent.modal.targeting.header')) ?>,
                        description: <?= json_encode(l('global.cookie_consent.modal.targeting.subheader')) ?>,
                        toggle: {
                            value: 'targeting',
                            enabled: false,
                            readonly: false
                        }
                    }
                    <?php endif ?>
                ]
            }
        };

        window.addEventListener('load', () => {
            let cc = initCookieConsent();

            cc.run({
                current_lang: language_code,
                autoclear_cookies: true,
                page_scripts: true,
                gui_options: {
                    consent_modal: {
                        layout: <?= json_encode(settings()->cookie_consent->layout) ?>,
                        position: <?= json_encode(settings()->cookie_consent->position_y . ' ' . settings()->cookie_consent->position_x) ?>,
                        transition: 'slide',
                        swap_buttons: false
                    },
                    settings_modal: {
                        layout: 'box',
                        transition: 'slide'
                    }
                },
                languages: languages,

                <?php if(settings()->cookie_consent->logging_is_enabled): ?>
                onAccept: (cookie) => {
                    if(!get_cookie('cookie_consent_logged')) {
                        navigator.sendBeacon(`${url}cookie-consent`, JSON.stringify({global_token, level: cookie.level}));
                        set_cookie('cookie_consent_logged', '1', 182, <?= json_encode(COOKIE_PATH) ?>);
                    }
                },
                onChange: (cookie) => {
                    navigator.sendBeacon(`${url}cookie-consent`, JSON.stringify({global_token, level: cookie.level}));
                    set_cookie('cookie_consent_logged', '1', 182, <?= json_encode(COOKIE_PATH) ?>);
                }
                <?php endif ?>
            });
        });
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php endif ?>
