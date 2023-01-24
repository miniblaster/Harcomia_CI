<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

return [
    /* Max qr code logo upload in mb */
    'qr_code_logo_size_limit' => 0.5,

    'type' => [
        'text' => [
            'icon' => 'fa fa-paragraph',
            'max_length' => 2048
        ],
        'url' => [
            'icon' => 'fa fa-link',
            'max_length' => 2048
        ],
        'phone' => [
            'icon' => 'fa fa-phone-square-alt',
            'max_length' => 32
        ],
        'sms' => [
            'icon' => 'fa fa-sms',
            'max_length' => 32,
            'body' => [
                'max_length' => 160,
            ]
        ],
        'email' => [
            'icon' => 'fa fa-envelope',
            'max_length' => 360,
            'subject' => [
                'max_length' => 256,
            ],
            'body' => [
                'max_length' => 2048,
            ]
        ],
        'whatsapp' => [
            'icon' => 'fab fa-whatsapp',
            'max_length' => 32,
            'body' => [
                'max_length' => 2048,
            ]
        ],
        'facetime' => [
            'icon' => 'fa fa-headset',
            'max_length' => 32
        ],
        'location' => [
            'icon' => 'fa fa-map-pin',
            'latitude' => [
                'max_length' => 32,
            ],
            'longitude' => [
                'max_length' => 32,
            ]
        ],
        'wifi' => [
            'icon' => 'fa fa-wifi',
            'ssid' => [
                'max_length' => 128,
            ],
            'password' => [
                'max_length' => 128,
            ]
        ],
        'event' => [
            'icon' => 'fa fa-calendar-alt',
            'max_length' => 128,
            'location' => [
                'max_length' => 128,
            ],
            'url' => [
                'max_length' => 1024,
            ],
            'note' => [
                'max_length' => 256,
            ]
        ],
        'crypto' => [
            'icon' => 'fab fa-bitcoin',
            'coins' => [
                'bitcoin' => 'Bitcoin BTC',
                'ethereum' => 'Ethereum ETH',
                'elrond' => 'Elrond EGLD',
            ],
            'address' => [
                'max_length' => 128,
            ],
            'amount' => []
        ],
        'vcard' => [
            'icon' => 'fa fa-id-card',
            'first_name' => [
                'max_length' => 64,
            ],
            'last_name' => [
                'max_length' => 64,
            ],
            'email' => [
                'max_length' => 320,
            ],
            'url' => [
                'max_length' => 1024,
            ],
            'company' => [
                'max_length' => 64,
            ],
            'job_title' => [
                'max_length' => 64,
            ],
            'birthday' => [
                'max_length' => 16,
            ],
            'street' => [
                'max_length' => 128,
            ],
            'city' => [
                'max_length' => 64,
            ],
            'zip' => [
                'max_length' => 32,
            ],
            'region' => [
                'max_length' => 32,
            ],
            'country' => [
                'max_length' => 32,
            ],
            'note' => [
                'max_length' => 256,
            ],
            'phone_number' => [
                'max_length' => 32,
            ],
            'social_label' => [
                'max_length' => 32
            ],
            'social_value' => [
                'max_length' => 1024
            ]
        ],
        'paypal' => [
            'icon' => 'fab fa-paypal',
            'type' => [
                'buy_now' => '_xclick',
                'add_to_cart' => '_cart',
                'donation' => '_donations'
            ],
            'email' => [
                'max_length' => 320,
            ],
            'title' => [
                'max_length' => 256,
            ],
            'currency' => [
                'max_length' => 8,
            ],
            'price' => [],
            'thank_you_url' => [
                'max_length' => 1024,
            ],
            'cancel_url' => [
                'max_length' => 1024,
            ],
        ],
    ],
];
