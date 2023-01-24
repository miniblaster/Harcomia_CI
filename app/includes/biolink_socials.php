<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

return [
    'email' => [
        'format' => 'mailto:%s',
        'input_group' => null,
        'max_length' => 320,
        'icon' => 'fa fa-envelope'
    ],
    'tel'=> [
        'format' => 'tel: %s',
        'input_group' => null,
        'max_length' => 32,
        'icon' => 'fa fa-phone-square-alt'
    ],
    'telegram'=> [
        'format' => 'https://t.me/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-telegram'
    ],
    'whatsapp'=> [
        'format' => 'https://api.whatsapp.com/send?phone=%s',
        'input_group' => null,
        'max_length' => 32,
        'icon' => 'fab fa-whatsapp'
    ],
    'facebook'=> [
        'format' => 'https://facebook.com/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-facebook'
    ],
    'facebook-messenger'=> [
        'format' => 'https://m.me/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-facebook-messenger'
    ],
    'instagram'=> [
        'format' => 'https://instagram.com/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-instagram'
    ],
    'twitter'=> [
        'format' => 'https://twitter.com/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-twitter'
    ],
    'tiktok'=> [
        'format' => 'https://tiktok.com/@%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-tiktok'
    ],
    'youtube'=> [
        'format' => 'https://youtube.com/channel/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-youtube'
    ],
    'soundcloud'=> [
        'format' => 'https://soundcloud.com/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-soundcloud'
    ],
    'linkedin'=> [
        'format' => 'https://linkedin.com/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-linkedin'
    ],
    'spotify' => [
        'format' => 'https://open.spotify.com/artist/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-spotify'
    ],
    'pinterest' => [
        'format' => 'https://pinterest.com/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-pinterest'
    ],
    'snapchat' => [
        'format' => 'https://snapchat.com/add/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-snapchat'
    ],
    'twitch' => [
        'format' => 'https://twitch.tv/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-twitch'
    ],
    'discord' => [
        'format' => 'https://discord.gg/%s',
        'input_group' => true,
        'max_length' => 128,
        'icon' => 'fab fa-discord'
    ],
];
