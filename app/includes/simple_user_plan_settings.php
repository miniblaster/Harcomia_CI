<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

$features = [
    'additional_global_domains',
    'custom_url',
    'deep_links',
    'removable_branding',
];

if(settings()->links->biolinks_is_enabled) {
    $features = array_merge($features, [
        'custom_backgrounds',
        'custom_branding',
        'custom_colored_links',
        'dofollow_is_enabled',
        'leap_link',
        'seo',
        'fonts',
        'custom_css_is_enabled',
        'custom_js_is_enabled',
    ]);
}

$features = array_merge($features, [
    'statistics',
    'temporary_url_is_enabled',
    'utm',
    'password',
    'sensitive_content',
    'no_ads',
    'api_is_enabled',
]);

return $features;

