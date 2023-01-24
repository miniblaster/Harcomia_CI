<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

$pro_blocks = \Altum\Plugin::is_active('pro-blocks') && file_exists(\Altum\Plugin::get('pro-blocks')->path . 'pro_blocks.php') ? include \Altum\Plugin::get('pro-blocks')->path . 'pro_blocks.php' : [];
$ultimate_blocks = \Altum\Plugin::is_active('ultimate-blocks') && file_exists(\Altum\Plugin::get('ultimate-blocks')->path . 'ultimate_blocks.php') ? include \Altum\Plugin::get('ultimate-blocks')->path . 'ultimate_blocks.php' : [];
$payment_blocks = \Altum\Plugin::is_active('payment-blocks') && file_exists(\Altum\Plugin::get('payment-blocks')->path . 'payment_blocks.php') ? include \Altum\Plugin::get('payment-blocks')->path . 'payment_blocks.php' : [];

$default_blocks = [
    'link' => [
        'type' => 'default',
        'icon' => 'fa fa-fw fa-link',
        'color' => '#00526b',
        'has_statistics' => true,
        'display_dynamic_name' => 'name',
        'whitelisted_thumbnail_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
    ],
    'heading' => [
        'type' => 'default',
        'icon' => 'fa fa-fw fa-heading',
        'color' => '#000',
        'has_statistics' => false,
        'display_dynamic_name' => 'text',
    ],
    'paragraph' => [
        'type' => 'default',
        'icon' => 'fa fa-fw fa-paragraph',
        'color' => '#494949',
        'has_statistics' => false,
        'display_dynamic_name' => false,
    ],
    'avatar' => [
        'type' => 'default',
        'icon' => 'fa fa-fw fa-user',
        'color' => '#8b2abf',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
    ],
    'image' => [
        'type' => 'default',
        'icon' => 'fa fa-fw fa-image',
        'color' => '#0682FF',
        'has_statistics' => true,
        'display_dynamic_name' => false,
        'whitelisted_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
    ],
    'socials' => [
        'type' => 'default',
        'icon' => 'fa fa-fw fa-users',
        'color' => '#63d2ff',
        'has_statistics' => false,
        'display_dynamic_name' => false,
    ],
    'mail' => [
        'type' => 'default',
        'icon' => 'fa fa-envelope',
        'color' => '#c91685',
        'has_statistics' => false,
        'display_dynamic_name' => 'name',
        'whitelisted_thumbnail_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
    ],
    'soundcloud' => [
        'type' => 'default',
        'icon' => 'fab fa-soundcloud',
        'color' => '#ff8800',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['soundcloud.com']
    ],
    'spotify' => [
        'type' => 'default',
        'icon' => 'fab fa-spotify',
        'color' => '#1db954',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['open.spotify.com']
    ],
    'youtube' => [
        'type' => 'default',
        'icon' => 'fab fa-youtube',
        'color' => '#ff0000',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['www.youtube.com', 'youtu.be']
    ],
    'twitch' => [
        'type' => 'default',
        'icon' => 'fab fa-twitch',
        'color' => '#6441a5',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['www.twitch.tv']
    ],
    'vimeo' => [
        'type' => 'default',
        'icon' => 'fab fa-vimeo',
        'color' => '#1ab7ea',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['vimeo.com']
    ],
    'tiktok' => [
        'type' => 'default',
        'icon' => 'fab fa-tiktok',
        'color' => '#FD3E3E',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['www.tiktok.com']
    ],
    'paypal' => [
        'type' => 'default',
        'icon' => 'fab fa-fw fa-paypal',
        'color' => '#00457C',
        'has_statistics' => true,
        'display_dynamic_name' => 'name',
        'whitelisted_thumbnail_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
    ],
    'phone_collector' => [
        'type' => 'default',
        'icon' => 'fa fa-phone-square-alt',
        'color' => '#39c640',
        'has_statistics' => false,
        'display_dynamic_name' => 'name',
        'whitelisted_thumbnail_image_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
    ],
    'opensea' => [
        'type' => 'default',
        'icon' => 'fa fa-water',
        'color' => '#2081E2',
        'has_statistics' => false,
        'display_dynamic_name' => false,
    ],
];

if(settings()->links->google_static_maps_is_enabled) {
    $default_blocks['map'] = [
        'type' => 'default',
        'icon' => 'fa fa-fw fa-map',
        'color' => '#31A952',
        'has_statistics' => true,
        'display_dynamic_name' => 'address',
    ];
}

return array_merge(
    $default_blocks,
    $pro_blocks,
    $ultimate_blocks,
    $payment_blocks,
);

