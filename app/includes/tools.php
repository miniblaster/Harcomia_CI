<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

return [
    'dns_lookup' => [
        'icon' => 'fa fa-network-wired',
        'similar' => [
            'reverse_ip_lookup',
            'ip_lookup',
            'ssl_lookup',
            'whois_lookup',
            'ping',
        ]
    ],

    'ip_lookup' => [
        'icon' => 'fa fa-search-location',
        'similar' => [
            'reverse_ip_lookup',
            'dns_lookup',
            'ssl_lookup',
            'whois_lookup',
            'ping',
        ]
    ],

    'reverse_ip_lookup' => [
        'icon' => 'fa fa-book',
        'similar' => [
            'ip_lookup',
            'dns_lookup',
            'ssl_lookup',
            'whois_lookup',
            'ping',
        ]
    ],

    'ssl_lookup' => [
        'icon' => 'fa fa-lock',
        'similar' => [
            'reverse_ip_lookup',
            'dns_lookup',
            'ip_lookup',
            'whois_lookup',
            'ping',
        ]
    ],

    'whois_lookup' => [
        'icon' => 'fa fa-fingerprint',
        'similar' => [
            'reverse_ip_lookup',
            'dns_lookup',
            'ip_lookup',
            'ssl_lookup',
            'ping',
        ]
    ],

    'ping' => [
        'icon' => 'fa fa-server',
        'similar' => [
            'reverse_ip_lookup',
            'dns_lookup',
            'ip_lookup',
            'ssl_lookup',
            'whois_lookup',
        ]
    ],

    'md2_generator' => [
        'icon' => 'fa fa-hand-sparkles',
        'similar' => [
            'md4_generator',
            'md5_generator',
        ]
    ],

    'md4_generator' => [
        'icon' => 'fa fa-columns',
        'similar' => [
            'md2_generator',
            'md5_generator',
        ]
    ],

    'md5_generator' => [
        'icon' => 'fa fa-hashtag',
        'similar' => [
            'md2_generator',
            'md4_generator',
        ]
    ],

    'whirlpool_generator' => [
        'icon' => 'fa fa-spinner'
    ],

    'sha1_generator' => [
        'icon' => 'fa fa-asterisk'
    ],

    'sha224_generator' => [
        'icon' => 'fa fa-atom'
    ],

    'sha256_generator' => [
        'icon' => 'fa fa-compact-disc'
    ],

    'sha384_generator' => [
        'icon' => 'fa fa-certificate'
    ],

    'sha512_generator' => [
        'icon' => 'fa fa-bahai'
    ],

    'sha512_224_generator' => [
        'icon' => 'fa fa-crosshairs'
    ],

    'sha512_256_generator' => [
        'icon' => 'fa fa-sun'
    ],

    'sha3_224_generator' => [
        'icon' => 'fa fa-compass'
    ],

    'sha3_256_generator' => [
        'icon' => 'fa fa-ring'
    ],

    'sha3_384_generator' => [
        'icon' => 'fa fa-life-ring'
    ],

    'sha3_512_generator' => [
        'icon' => 'fa fa-circle-notch'
    ],

    'base64_encoder' => [
        'icon' => 'fab fa-codepen',
        'similar' => [
            'base64_decoder',
        ]
    ],

    'base64_decoder' => [
        'icon' => 'fab fa-codepen',
        'similar' => [
            'base64_encoder',
        ]
    ],

    'base64_to_image' => [
        'icon' => 'fa fa-image',
        'similar' => [
            'image_to_base64',
        ]
    ],

    'image_to_base64' => [
        'icon' => 'fa fa-image',
        'similar' => [
            'base64_to_image',
        ]
    ],

    'url_encoder' => [
        'icon' => 'fa fa-link',
        'similar' => [
            'url_decoder',
        ]
    ],

    'url_decoder' => [
        'icon' => 'fa fa-link',
        'similar' => [
            'url_encoder',
        ]
    ],

    'lorem_ipsum_generator' => [
        'icon' => 'fa fa-paragraph'
    ],

    'markdown_to_html' => [
        'icon' => 'fa fa-code'
    ],

    'case_converter' => [
        'icon' => 'fa fa-text-height'
    ],

    'random_number_generator' => [
        'icon' => 'fa fa-random'
    ],

    'uuid_v4_generator' => [
        'icon' => 'fa fa-compress'
    ],

    'bcrypt_generator' => [
        'icon' => 'fa fa-passport'
    ],

    'password_generator' => [
        'icon' => 'fa fa-lock',
        'similar' => [
            'password_strength_checker',
        ]
    ],

    'password_strength_checker' => [
        'icon' => 'fa fa-key',
        'similar' => [
            'password_generator',
        ]
    ],

    'slug_generator' => [
        'icon' => 'fa fa-grip-lines'
    ],

    'html_minifier' => [
        'icon' => 'fab fa-html5',
        'similar' => [
            'css_minifier',
            'js_minifier'
        ]
    ],

    'css_minifier' => [
        'icon' => 'fab fa-css3',
        'similar' => [
            'html_minifier',
            'js_minifier'
        ]
    ],

    'js_minifier' => [
        'icon' => 'fab fa-js',
        'similar' => [
            'html_minifier',
            'css_minifier'
        ]
    ],

    'user_agent_parser' => [
        'icon' => 'fa fa-columns'
    ],

    'website_hosting_checker' => [
        'icon' => 'fa fa-server'
    ],

    'file_mime_type_checker' => [
        'icon' => 'fa fa-file'
    ],

    'gravatar_checker' => [
        'icon' => 'fa fa-user-circle'
    ],

    'character_counter' => [
        'icon' => 'fa fa-font'
    ],

    'list_randomizer' => [
        'icon' => 'fa fa-random'
    ],

    'reverse_words' => [
        'icon' => 'fa fa-yin-yang'
    ],

    'reverse_letters' => [
        'icon' => 'fa fa-align-right'
    ],

    'emojis_remover' => [
        'icon' => 'fa fa-icons'
    ],

    'reverse_list' => [
        'icon' => 'fa fa-list-ol'
    ],

    'list_alphabetizer' => [
        'icon' => 'fa fa-sort-alpha-up'
    ],

    'upside_down_text_generator' => [
        'icon' => 'fa fa-quote-left'
    ],

    'old_english_text_generator' => [
        'icon' => 'fa fa-font'
    ],

    'cursive_text_generator' => [
        'icon' => 'fa fa-italic'
    ],

    'url_parser' => [
        'icon' => 'fa fa-paperclip'
    ],

    'color_converter' => [
        'icon' => 'fa fa-paint-brush'
    ],

    'http_headers_lookup' => [
        'icon' => 'fa fa-asterisk'
    ],

    'duplicate_lines_remover' => [
        'icon' => 'fa fa-remove-format'
    ],

    'text_to_speech' => [
        'icon' => 'fa fa-microphone'
    ],

    'idn_punnycode_converter' => [
        'icon' => 'fa fa-italic'
    ],

    'json_validator_beautifier' => [
        'icon' => 'fa fa-project-diagram'
    ],

    'qr_code_reader' => [
        'icon' => 'fa fa-qrcode',
        'similar' => [
            'exif_reader',
        ]
    ],

    'meta_tags_checker' => [
        'icon' => 'fa fa-external-link-alt'
    ],

    'exif_reader' => [
        'icon' => 'fa fa-camera',
        'similar' => [
            'qr_code_reader',
        ]
    ],

    'color_picker' => [
        'icon' => 'fa fa-palette'
    ],

    'sql_beautifier' => [
        'icon' => 'fa fa-database'
    ],

    'html_entity_converter' => [
        'icon' => 'fa fa-file-code'
    ],

    'binary_converter' => [
        'icon' => 'fa fa-list-ol',
        'similar' => [
            'hex_converter',
            'ascii_converter',
            'decimal_converter',
            'octal_converter',
        ]
    ],

    'hex_converter' => [
        'icon' => 'fa fa-dice-six',
        'similar' => [
            'binary_converter',
            'ascii_converter',
            'decimal_converter',
            'octal_converter',
        ]
    ],

    'ascii_converter' => [
        'icon' => 'fa fa-subscript',
        'similar' => [
            'binary_converter',
            'hex_converter',
            'decimal_converter',
            'octal_converter',
        ]
    ],

    'decimal_converter' => [
        'icon' => 'fa fa-superscript',
        'similar' => [
            'binary_converter',
            'hex_converter',
            'ascii_converter',
            'octal_converter',
        ]
    ],

    'octal_converter' => [
        'icon' => 'fa fa-sort-numeric-up',
        'similar' => [
            'binary_converter',
            'hex_converter',
            'ascii_converter',
            'decimal_converter',
        ]
    ],

    'morse_converter' => [
        'icon' => 'fa fa-ellipsis-h'
    ],

    'number_to_words_converter' => [
        'icon' => 'fa fa-sort-amount-down'
    ],

    'mailto_link_generator' => [
        'icon' => 'fa fa-envelope-open'
    ],

    'youtube_thumbnail_downloader' => [
        'icon' => 'fab fa-youtube'
    ],

    'safe_url_checker' => [
        'icon' => 'fab fa-google'
    ],

    'utm_link_generator' => [
        'icon' => 'fa fa-external-link-alt'
    ],

    'whatsapp_link_generator' => [
        'icon' => 'fab fa-whatsapp'
    ],

    'youtube_timestamp_link_generator' => [
        'icon' => 'fab fa-youtube'
    ],

    'google_cache_checker' => [
        'icon' => 'fa fa-history'
    ],

    'url_redirect_checker' => [
        'icon' => 'fa fa-directions'
    ],

    'image_optimizer' => [
        'icon' => 'fa fa-image'
    ],

    'png_to_jpg' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'png_to_webp',
            'png_to_bmp',
            'png_to_gif',
            'png_to_ico',
        ]
    ],

    'png_to_webp' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'png_to_jpg',
            'png_to_bmp',
            'png_to_gif',
            'png_to_ico',
        ]
    ],

    'png_to_bmp' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'png_to_jpg',
            'png_to_webp',
            'png_to_gif',
            'png_to_ico',
        ]
    ],

    'png_to_gif' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'png_to_jpg',
            'png_to_webp',
            'png_to_bmp',
            'png_to_ico',
        ]
    ],

    'png_to_ico' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'png_to_jpg',
            'png_to_webp',
            'png_to_gif',
            'png_to_bmp',
        ]
    ],

    'jpg_to_png' => [
        'icon' => 'fa fa-photo-video',
        'similar' => [
            'jpg_to_webp',
            'jpg_to_gif',
            'jpg_to_ico',
            'jpg_to_bmp',
        ]
    ],

    'jpg_to_webp' => [
        'icon' => 'fa fa-photo-video',
        'similar' => [
            'jpg_to_png',
            'jpg_to_gif',
            'jpg_to_ico',
            'jpg_to_bmp',
        ]
    ],

    'jpg_to_gif' => [
        'icon' => 'fa fa-photo-video',
        'similar' => [
            'jpg_to_png',
            'jpg_to_webp',
            'jpg_to_ico',
            'jpg_to_bmp',
        ]
    ],

    'jpg_to_ico' => [
        'icon' => 'fa fa-photo-video',
        'similar' => [
            'jpg_to_png',
            'jpg_to_webp',
            'jpg_to_gif',
            'jpg_to_bmp',
        ]
    ],

    'jpg_to_bmp' => [
        'icon' => 'fa fa-photo-video',
        'similar' => [
            'jpg_to_png',
            'jpg_to_webp',
            'jpg_to_gif',
            'jpg_to_ico',
        ]
    ],

    'webp_to_jpg' => [
        'icon' => 'fa fa-film',
        'similar' => [
            'webp_to_png',
            'webp_to_bmp',
            'webp_to_gif',
            'webp_to_ico',
        ]
    ],

    'webp_to_gif' => [
        'icon' => 'fa fa-film',
        'similar' => [
            'webp_to_png',
            'webp_to_bmp',
            'webp_to_jpg',
            'webp_to_ico',
        ]
    ],

    'webp_to_png' => [
        'icon' => 'fa fa-film',
        'similar' => [
            'webp_to_gif',
            'webp_to_bmp',
            'webp_to_jpg',
            'webp_to_ico',
        ]
    ],

    'webp_to_bmp' => [
        'icon' => 'fa fa-film',
        'similar' => [
            'webp_to_gif',
            'webp_to_png',
            'webp_to_jpg',
            'webp_to_ico',
        ]
    ],

    'webp_to_ico' => [
        'icon' => 'fa fa-film',
        'similar' => [
            'webp_to_gif',
            'webp_to_png',
            'webp_to_jpg',
            'webp_to_bmp',
        ]
    ],

    'bmp_to_jpg' => [
        'icon' => 'fa fa-portrait',
        'similar' => [
            'bmp_to_png',
            'bmp_to_webp',
            'bmp_to_gif',
            'bmp_to_ico',
        ]
    ],

    'bmp_to_gif' => [
        'icon' => 'fa fa-portrait',
        'similar' => [
            'bmp_to_png',
            'bmp_to_webp',
            'bmp_to_jpg',
            'bmp_to_ico',
        ]
    ],

    'bmp_to_png' => [
        'icon' => 'fa fa-portrait',
        'similar' => [
            'bmp_to_gif',
            'bmp_to_webp',
            'bmp_to_jpg',
            'bmp_to_ico',
        ]
    ],

    'bmp_to_webp' => [
        'icon' => 'fa fa-portrait',
        'similar' => [
            'bmp_to_gif',
            'bmp_to_png',
            'bmp_to_jpg',
            'bmp_to_ico',
        ]
    ],

    'bmp_to_ico' => [
        'icon' => 'fa fa-portrait',
        'similar' => [
            'bmp_to_gif',
            'bmp_to_png',
            'bmp_to_jpg',
            'bmp_to_webp',
        ]
    ],

    'ico_to_jpg' => [
        'icon' => 'fa fa-icons',
        'similar' => [
            'ico_to_png',
            'ico_to_webp',
            'ico_to_gif',
            'ico_to_bmp',
        ]
    ],

    'ico_to_gif' => [
        'icon' => 'fa fa-icons',
        'similar' => [
            'ico_to_png',
            'ico_to_webp',
            'ico_to_jpg',
            'ico_to_bmp',
        ]
    ],

    'ico_to_png' => [
        'icon' => 'fa fa-icons',
        'similar' => [
            'ico_to_gif',
            'ico_to_webp',
            'ico_to_jpg',
            'ico_to_bmp',
        ]
    ],

    'ico_to_webp' => [
        'icon' => 'fa fa-icons',
        'similar' => [
            'ico_to_gif',
            'ico_to_png',
            'ico_to_jpg',
            'ico_to_bmp',
        ]
    ],

    'ico_to_bmp' => [
        'icon' => 'fa fa-icons',
        'similar' => [
            'ico_to_gif',
            'ico_to_png',
            'ico_to_jpg',
            'ico_to_webp',
        ]
    ],

    'gif_to_jpg' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'gif_to_png',
            'gif_to_webp',
            'gif_to_ico',
            'gif_to_bmp',
        ]
    ],

    'gif_to_ico' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'gif_to_png',
            'gif_to_webp',
            'gif_to_jpg',
            'gif_to_bmp',
        ]
    ],

    'gif_to_png' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'gif_to_ico',
            'gif_to_webp',
            'gif_to_jpg',
            'gif_to_bmp',
        ]
    ],

    'gif_to_webp' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'gif_to_ico',
            'gif_to_png',
            'gif_to_jpg',
            'gif_to_bmp',
        ]
    ],

    'gif_to_bmp' => [
        'icon' => 'fa fa-camera-retro',
        'similar' => [
            'gif_to_ico',
            'gif_to_png',
            'gif_to_jpg',
            'gif_to_webp',
        ]
    ],

    'text_separator' => [
        'icon' => 'fa fa-heading'
    ],

    'email_extractor' => [
        'icon' => 'fa fa-envelope'
    ],

    'url_extractor' => [
        'icon' => 'fa fa-window-restore'
    ],

    'text_size_calculator' => [
        'icon' => 'fa fa-text-width'
    ],

    'paypal_link_generator' => [
        'icon' => 'fab fa-paypal'
    ],

    'bbcode_to_html' => [
        'icon' => 'fab fa-html5'
    ],

    'html_tags_remover' => [
        'icon' => 'fab fa-html5'
    ],

    'celsius_to_fahrenheit' => [
        'icon' => 'fa fa-temperature-low',
        'similar' => [
            'fahrenheit_to_celsius'
        ]
    ],

    'celsius_to_kelvin' => [
        'icon' => 'fa fa-temperature-low',
        'similar' => [
            'kelvin_to_celsius'
        ]
    ],

    'fahrenheit_to_celsius' => [
        'icon' => 'fa fa-temperature-high',
        'similar' => [
            'celsius_to_fahrenheit'
        ]
    ],

    'fahrenheit_to_kelvin' => [
        'icon' => 'fa fa-temperature-high',
        'similar' => [
            'kelvin_to_fahrenheit'
        ]
    ],

    'kelvin_to_celsius' => [
        'icon' => 'fa fa-thermometer-empty',
        'similar' => [
            'celsius_to_kelvin'
        ]
    ],

    'kelvin_to_fahrenheit' => [
        'icon' => 'fa fa-thermometer-empty',
        'similar' => [
            'fahrenheit_to_kelvin'
        ]
    ],

    'miles_to_kilometers' => [
        'icon' => 'fa fa-road',
        'similar' => [
            'kilometers_to_miles'
        ]
    ],

    'kilometers_to_miles' => [
        'icon' => 'fa fa-archway',
        'similar' => [
            'miles_to_kilometers'
        ]
    ],

    'miles_per_hour_to_kilometers_per_hour' => [
        'icon' => 'fa fa-road',
        'similar' => [
            'kilometers_per_hour_to_miles_per_hour'
        ]
    ],

    'kilometers_per_hour_to_miles_per_hour' => [
        'icon' => 'fa fa-archway',
        'similar' => [
            'miles_per_hour_to_kilometers_per_hour'
        ]
    ],

    'kilograms_to_pounds' => [
        'icon' => 'fa fa-balance-scale-left',
        'similar' => [
            'pounds_to_kilograms'
        ]
    ],

    'pounds_to_kilograms' => [
        'icon' => 'fa fa-balance-scale-right',
        'similar' => [
            'kilograms_to_pounds'
        ]
    ],

    'number_to_roman_numerals' => [
        'icon' => 'fa fa-sort-numeric-up-alt',
        'similar' => [
            'roman_numerals_to_number'
        ]
    ],

    'roman_numerals_to_number' => [
        'icon' => 'fa fa-sort-numeric-up',
        'similar' => [
            'number_to_roman_numerals'
        ]
    ],

    'liters_to_gallons_us' => [
        'icon' => 'fa fa-tint',
        'similar' => [
            'liters_to_gallons_imperial',
            'gallons_us_to_liters',
            'gallons_imperial_to_liters',
        ]
    ],

    'liters_to_gallons_imperial' => [
        'icon' => 'fa fa-tint',
        'similar' => [
            'liters_to_gallons_us',
            'gallons_us_to_liters',
            'gallons_imperial_to_liters',
        ]
    ],

    'gallons_us_to_liters' => [
        'icon' => 'fa fa-tint',
        'similar' => [
            'liters_to_gallons_us',
            'liters_to_gallons_imperial',
            'gallons_imperial_to_liters',
        ]
    ],

    'gallons_imperial_to_liters' => [
        'icon' => 'fa fa-tint',
        'similar' => [
            'liters_to_gallons_us',
            'liters_to_gallons_imperial',
            'gallons_us_to_liters',
        ]
    ],

    'unix_timestamp_to_date' => [
        'icon' => 'fa fa-clock',
        'similar' => [
            'date_to_unix_timestamp',
        ]
    ],

    'date_to_unix_timestamp' => [
        'icon' => 'fa fa-clock',
        'similar' => [
            'unix_timestamp_to_date',
        ]
    ],

    'signature_generator' => [
        'icon' => 'fa fa-signature',
    ],
];
