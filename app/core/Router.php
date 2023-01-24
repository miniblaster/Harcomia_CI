<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

class Router {
    public static $params = [];
    public static $original_request = '';
    public static $language_code = '';
    public static $path = '';
    public static $controller_key = 'index';
    public static $controller = 'Index';
    public static $controller_settings = [
        'body_white' => true,
        'wrapper' => 'wrapper',
        'no_authentication_check' => false,

        /* Enable / disable browser language detection & redirection */
        'no_browser_language_detection' => false,

        /* Should we see a view for the controller? */
        'has_view' => true,

        /* If set on yes, ads won't show on these pages at all */
        'ads' => false,

        /* Authentication guard check (potential values: null, 'guest', 'user', 'admin') */
        'authentication' => null,

        /* Teams */
        'allow_team_access' => null,
    ];
    public static $method = 'index';
    public static $data = [];

    public static $routes = [
        'l' => [
            'link' => [
                'controller' => 'Link',
                'settings' => [
                    'no_authentication_check' => true,
                    'no_browser_language_detection' => true,
                    'ads' => true,
                ]
            ],

            'guest-payment-webhook' => [
                'controller' => 'GuestPaymentWebhook',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'guest-payment-download' => [
                'controller' => 'GuestPaymentDownload',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],
        ],

        '' => [
            'directory' => [
                'controller' => 'Directory',
                'settings' => [
                    'ads' => true,
                ]
            ],

            'dashboard' => [
                'controller' => 'Dashboard',
                'settings' => [
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'links' => [
                'controller' => 'Links',
                'settings' => [
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'projects' => [
                'controller' => 'Projects',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'data' => [
                'controller' => 'Data',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'guests-payments-statistics' => [
                'controller' => 'GuestsPaymentsStatistics',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'guests-payments' => [
                'controller' => 'GuestsPayments',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'payment-processors' => [
                'controller' => 'PaymentProcessors',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'payment-processor-create' => [
                'controller' => 'PaymentProcessorCreate',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'payment-processor-update' => [
                'controller' => 'PaymentProcessorUpdate',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'pixels' => [
                'controller' => 'Pixels',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'qr-codes' => [
                'controller' => 'QrCodes',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'qr-code-create' => [
                'controller' => 'QrCodeCreate',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'qr-code-update' => [
                'controller' => 'QrCodeUpdate',
                'settings' => [
                    'menu_no_margin' => false,
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'qr-code-generator' => [
                'controller' => 'QrCodeGenerator',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'link' => [
                'controller' => 'Link',
                'settings' => [
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'biolink-block' => [
                'controller' => 'BiolinkBlock',
                'settings' => [
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'domains' => [
                'controller' => 'Domains',
                'settings' => [
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'tools' => [
                'controller' => 'Tools',
                'settings' => [
                    'body_white' => false,
                    'ads' => true,
                ]
            ],

            'project-ajax' => [
                'controller' => 'ProjectAjax'
            ],

            'pixel-ajax' => [
                'controller' => 'PixelAjax'
            ],

            'biolink-block-ajax' => [
                'controller' => 'BiolinkBlockAjax'
            ],

            'link-ajax' => [
                'controller' => 'LinkAjax'
            ],

            /* Common routes */
            'index' => [
                'controller' => 'Index',
            ],

            'login' => [
                'controller' => 'Login',
                'settings' => [
                    'wrapper' => 'basic_wrapper',
                    'no_browser_language_detection' => true,
                ]
            ],

            'register' => [
                'controller' => 'Register',
                'settings' => [
                    'wrapper' => 'basic_wrapper',
                    'no_browser_language_detection' => true,
                ]
            ],

            'affiliate' => [
                'controller' => 'Affiliate'
            ],

            'pages' => [
                'controller' => 'Pages'
            ],

            'page' => [
                'controller' => 'Page'
            ],

            'blog' => [
                'controller' => 'Blog'
            ],

            'api-documentation' => [
                'controller' => 'ApiDocumentation',
            ],

            'contact' => [
                'controller' => 'Contact',
                'settings' => [
                    'allow_team_access' => false,
                ]
            ],

            'activate-user' => [
                'controller' => 'ActivateUser'
            ],

            'lost-password' => [
                'controller' => 'LostPassword',
                'settings' => [
                    'wrapper' => 'basic_wrapper',
                ]
            ],

            'reset-password' => [
                'controller' => 'ResetPassword',
                'settings' => [
                    'wrapper' => 'basic_wrapper',
                ]
            ],

            'resend-activation' => [
                'controller' => 'ResendActivation',
                'settings' => [
                    'wrapper' => 'basic_wrapper',
                ]
            ],

            'logout' => [
                'controller' => 'Logout'
            ],

            'notfound' => [
                'controller' => 'NotFound',
            ],

            'account' => [
                'controller' => 'Account',
                'settings' => [
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'account-plan' => [
                'controller' => 'AccountPlan',
                'settings' => [
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'account-redeem-code' => [
                'controller' => 'AccountRedeemCode',
                'settings' => [
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'account-payments' => [
                'controller' => 'AccountPayments',
                'settings' => [
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'account-logs' => [
                'controller' => 'AccountLogs',
                'settings' => [
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'account-api' => [
                'controller' => 'AccountApi',
                'settings' => [
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'account-delete' => [
                'controller' => 'AccountDelete',
                'settings' => [
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'referrals' => [
                'controller' => 'Referrals',
                'settings' => [
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'refer' => [
                'controller' => 'Refer',
                'settings' => [
                    'has_view' => false
                ]
            ],

            'invoice' => [
                'controller' => 'Invoice',
                'settings' => [
                    'wrapper' => 'invoice/invoice_wrapper',
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'plan' => [
                'controller' => 'Plan',
                'settings' => [
                ]
            ],

            'pay' => [
                'controller' => 'Pay',
                'settings' => [
                    'body_white' => false,
                    'allow_team_access' => false,
                ]
            ],

            'pay-billing' => [
                'controller' => 'PayBilling',
                'settings' => [
                    'allow_team_access' => false,
                ]
            ],

            'pay-thank-you' => [
                'controller' => 'PayThankYou',
                'settings' => [
                    'allow_team_access' => false,
                ]
            ],

            'teams-system' => [
                'controller' => 'TeamsSystem',
                'settings' => [
                    'ads' => true,
                    'allow_team_access' => false,
                    'body_white' => false,
                ]
            ],

            'teams' => [
                'controller' => 'Teams',
                'settings' => [
                    'ads' => true,
                    'allow_team_access' => false,
                    'body_white' => false,
                ]
            ],

            'team-create' => [
                'controller' => 'TeamCreate',
                'settings' => [
                    'ads' => true,
                    'allow_team_access' => false,
                    'body_white' => false,
                ]
            ],

            'team-update' => [
                'controller' => 'TeamUpdate',
                'settings' => [
                    'ads' => true,
                    'allow_team_access' => false,
                    'body_white' => false,
                ]
            ],

            'team' => [
                'controller' => 'Team',
                'settings' => [
                    'ads' => true,
                    'allow_team_access' => false,
                    'body_white' => false,
                ]
            ],

            'teams-members' => [
                'controller' => 'TeamsMembers',
                'settings' => [
                    'ads' => true,
                    'allow_team_access' => false,
                    'body_white' => false,
                ]
            ],

            'team-member-create' => [
                'controller' => 'TeamMemberCreate',
                'settings' => [
                    'ads' => true,
                    'allow_team_access' => false,
                    'body_white' => false,
                ]
            ],

            'team-member-update' => [
                'controller' => 'TeamMemberUpdate',
                'settings' => [
                    'ads' => true,
                    'allow_team_access' => false,
                    'body_white' => false,
                ]
            ],

            'teams-member' => [
                'controller' => 'TeamsMember',
                'settings' => [
                    'ads' => true,
                    'allow_team_access' => false,
                    'body_white' => false,
                ]
            ],

            /* Webhooks */
            'webhook-paypal' => [
                'controller' => 'WebhookPaypal',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'webhook-stripe' => [
                'controller' => 'WebhookStripe',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'webhook-coinbase' => [
                'controller' => 'WebhookCoinbase',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'webhook-payu' => [
                'controller' => 'WebhookPayu',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'webhook-paystack' => [
                'controller' => 'WebhookPaystack',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'webhook-razorpay' => [
                'controller' => 'WebhookRazorpay',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'webhook-mollie' => [
                'controller' => 'WebhookMollie',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'webhook-yookassa' => [
                'controller' => 'WebhookYookassa',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'webhook-crypto-com' => [
                'controller' => 'WebhookCryptoCom',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            'webhook-paddle' => [
                'controller' => 'WebhookPaddle',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],

            /* Others */
            'cookie-consent' => [
                'controller' => 'CookieConsent',
                'settings' => [
                    'no_authentication_check' => true,
                    'no_browser_language_detection' => true,
                ]
            ],

            'sitemap' => [
                'controller' => 'Sitemap',
                'settings' => [
                    'no_authentication_check' => true,
                    'no_browser_language_detection' => true,
                ]
            ],

            'cron' => [
                'controller' => 'Cron',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                    'no_browser_language_detection' => true,
                ]
            ],
        ],

        'api' => [
            'links' => [
                'controller' => 'ApiLinks',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],
            'statistics' => [
                'controller' => 'ApiStatistics',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],
            'projects' => [
                'controller' => 'ApiProjects',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],
            'pixels' => [
                'controller' => 'ApiPixels',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false
                ]
            ],
            'qr-codes' => [
                'controller' => 'ApiQrCodes',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false
                ]
            ],
            'data' => [
                'controller' => 'ApiData',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false
                ]
            ],
            'domains' => [
                'controller' => 'ApiDomains',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],

            /* Common routes */
            'teams' => [
                'controller' => 'ApiTeams',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],
            'teams-member' => [
                'controller' => 'ApiTeamsMember',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],
            'team-members' => [
                'controller' => 'ApiTeamMembers',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],
            'user' => [
                'controller' => 'ApiUser',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],
            'payments' => [
                'controller' => 'ApiPayments',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],
            'logs' => [
                'controller' => 'ApiLogs',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false,
                ]
            ],
        ],

        /* Admin Panel */
        'admin' => [
            'links' => [
                'controller' => 'AdminLinks'
            ],

            'biolinks-themes' => [
                'controller' => 'AdminBiolinksThemes'
            ],

            'biolink-theme-create' => [
                'controller' => 'AdminBiolinkThemeCreate'
            ],

            'biolink-theme-update' => [
                'controller' => 'AdminBiolinkThemeUpdate'
            ],

            'projects' => [
                'controller' => 'AdminProjects'
            ],

            'pixels' => [
                'controller' => 'AdminPixels'
            ],

            'qr-codes' => [
                'controller' => 'AdminQrCodes'
            ],

            'domains' => [
                'controller' => 'AdminDomains'
            ],

            'domain-create' => [
                'controller' => 'AdminDomainCreate'
            ],

            'domain-update' => [
                'controller' => 'AdminDomainUpdate'
            ],

            /* Common routes */
            'index' => [
                'controller' => 'AdminIndex'
            ],

            'users' => [
                'controller' => 'AdminUsers'
            ],

            'user-create' => [
                'controller' => 'AdminUserCreate'
            ],

            'user-view' => [
                'controller' => 'AdminUserView'
            ],

            'user-update' => [
                'controller' => 'AdminUserUpdate'
            ],

            'users-logs' => [
                'controller' => 'AdminUsersLogs',
            ],

            'redeemed-codes' => [
                'controller' => 'AdminRedeemedCodes',
            ],

            'blog-posts' => [
                'controller' => 'AdminBlogPosts'
            ],

            'blog-post-create' => [
                'controller' => 'AdminBlogPostCreate'
            ],

            'blog-post-update' => [
                'controller' => 'AdminBlogPostUpdate'
            ],

            'blog-posts-categories' => [
                'controller' => 'AdminBlogPostsCategories'
            ],

            'blog-posts-category-create' => [
                'controller' => 'AdminBlogPostsCategoryCreate'
            ],

            'blog-posts-category-update' => [
                'controller' => 'AdminBlogPostsCategoryUpdate'
            ],

            'resources' => [
                'controller' => 'AdminResources'
            ],

            'pages' => [
                'controller' => 'AdminPages'
            ],

            'page-create' => [
                'controller' => 'AdminPageCreate'
            ],

            'page-update' => [
                'controller' => 'AdminPageUpdate'
            ],

            'pages-categories' => [
                'controller' => 'AdminPagesCategories'
            ],

            'pages-category-create' => [
                'controller' => 'AdminPagesCategoryCreate'
            ],

            'pages-category-update' => [
                'controller' => 'AdminPagesCategoryUpdate'
            ],

            'plans' => [
                'controller' => 'AdminPlans'
            ],

            'plan-create' => [
                'controller' => 'AdminPlanCreate'
            ],

            'plan-update' => [
                'controller' => 'AdminPlanUpdate'
            ],

            'codes' => [
                'controller' => 'AdminCodes'
            ],

            'code-create' => [
                'controller' => 'AdminCodeCreate'
            ],

            'code-update' => [
                'controller' => 'AdminCodeUpdate'
            ],

            'taxes' => [
                'controller' => 'AdminTaxes'
            ],

            'tax-create' => [
                'controller' => 'AdminTaxCreate'
            ],

            'tax-update' => [
                'controller' => 'AdminTaxUpdate'
            ],

            'affiliates-withdrawals' => [
                'controller' => 'AdminAffiliatesWithdrawals',
            ],

            'payments' => [
                'controller' => 'AdminPayments'
            ],

            'statistics' => [
                'controller' => 'AdminStatistics'
            ],

            'plugins' => [
                'controller' => 'AdminPlugins',
            ],

            'languages' => [
                'controller' => 'AdminLanguages'
            ],

            'language-create' => [
                'controller' => 'AdminLanguageCreate'
            ],

            'language-update' => [
                'controller' => 'AdminLanguageUpdate'
            ],

            'settings' => [
                'controller' => 'AdminSettings'
            ],

            'api-documentation' => [
                'controller' => 'AdminApiDocumentation',
            ],

            'teams' => [
                'controller' => 'AdminTeams',
            ],
        ],

        'admin-api' => [
            'users' => [
                'controller' => 'AdminApiUsers',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false
                ]
            ],

            'plans' => [
                'controller' => 'AdminApiPlans',
                'settings' => [
                    'no_authentication_check' => true,
                    'has_view' => false
                ]
            ],
        ],
    ];


    public static function parse_url() {

        $params = self::$params;

        if(isset($_GET['altum'])) {
            $params = explode('/', input_clean(rtrim($_GET['altum'], '/')));
        }

        if(php_sapi_name() == 'cli' && isset($_SERVER['argv'])) {
            $params = explode('/', input_clean(rtrim($_SERVER['argv'][1] ?? '', '/')));
            parse_str(implode('&', array_slice($_SERVER['argv'], 2)), $_GET);
        }

        self::$params = $params;

        return $params;

    }

    public static function get_params() {

        return self::$params = array_values(self::$params);
    }

    public static function parse_language() {

        /* Check for potential language set in the first parameter */
        if(!empty(self::$params[0]) && in_array(self::$params[0], Language::$active_languages)) {

            /* Set the language */
            $language_code = input_clean(self::$params[0]);
            Language::set_by_code($language_code);
            self::$language_code = $language_code;

            /* Unset the parameter so that it wont be used further */
            unset(self::$params[0]);
            self::$params = array_values(self::$params);

        }

    }

    public static function parse_controller() {

        self::$original_request = input_clean(implode('/', self::$params));

        /* Check if the current link accessed is actually the original url or not (multi domain use) */
        $original_url_host = parse_url(url(), PHP_URL_HOST);
        $request_url_host = input_clean($_SERVER['HTTP_HOST']);

        if($original_url_host != $request_url_host) {

            /* Make sure the custom domain is attached */
            $domain = (new \Altum\Models\Domain())->get_domain_by_host($request_url_host);;

            if($domain && $domain->is_enabled) {
                self::$controller_key = 'link';
                self::$controller = 'Link';
                self::$path = 'l';

                /* Set some route data */
                self::$data['domain'] = $domain;
            }

        }

        /* Check for potential other paths than the default one (admin panel) */
        if(!empty(self::$params[0])) {

            if(in_array(self::$params[0], ['admin', 'admin-api', 'l', 'api']) && $original_url_host == $request_url_host) {
                self::$path = self::$params[0];

                unset(self::$params[0]);

                self::$params = array_values(self::$params);
            }

        }

        if(!empty(self::$params[0])) {

            if(array_key_exists(self::$params[0], self::$routes[self::$path]) && file_exists(APP_PATH . 'controllers/' . (self::$path != '' ? self::$path . '/' : null) . self::$routes[self::$path][self::$params[0]]['controller'] . '.php')) {

                self::$controller_key = self::$params[0];

                unset(self::$params[0]);

            } else {

                /* Try to check if the link exists via the cache */
                $cache_instance = \Altum\Cache::$adapter->getItem('link?url=' . md5(self::$params[0]) . (isset(self::$data['domain']) ? '&domain_id=' . self::$data['domain']->domain_id : null));

                /* Set cache if not existing */
                if(!$cache_instance->get()) {

                    /* Get data from the database */
                    if(isset(self::$data['domain'])) {
                        $link = db()->where('url', self::$params[0])->where('domain_id', self::$data['domain']->domain_id)->getOne('links');
                    } else {
                        $link = db()->where('url', self::$params[0])->where('domain_id', 0)->getOne('links');
                    }

                    if($link) {
                        \Altum\Cache::$adapter->save($cache_instance->set($link)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('link_id=' . $link->link_id));

                        /* Set some route data */
                        self::$data['link'] = $link;
                    }

                } else {

                    /* Get cache */
                    $link = $cache_instance->get();

                    /* Set some route data */
                    self::$data['link'] = $link;

                }


                /* Check if there is any link available in the database */
                if($link) {

                    self::$controller_key = 'link';
                    self::$controller = 'Link';
                    self::$path = 'l';

                } else {

                    /* Check for a custom domain 404 redirect */
                    if(isset(self::$data['domain']) && self::$data['domain']->custom_not_found_url) {
                        header('Location: ' . self::$data['domain']->custom_not_found_url);
                        die();
                    }

                    else {
                        /* Not found controller */
                        self::$path = '';
                        self::$controller_key = 'notfound';
                    }

                }

            }

        }

        /* Check for a custom index url redirect in case there is no link requested  */
        if(!isset(self::$params[0]) && !isset(self::$params[1]) && self::$path == 'l' && $original_url_host != $request_url_host && isset(self::$data['domain']) && self::$data['domain']->custom_index_url) {
            header('Location: ' . self::$data['domain']->custom_index_url);
            die();
        }

        /* Save the current controller */
        if(!isset(self::$routes[self::$path][self::$controller_key])) {
            /* Not found controller */
            self::$path = '';
            self::$controller_key = 'notfound';
        }
        self::$controller = self::$routes[self::$path][self::$controller_key]['controller'];

        /* Admin path */
        if(self::$path == 'admin' && !isset(self::$routes[self::$path][self::$controller_key]['settings'])) {
            self::$routes[self::$path][self::$controller_key]['settings'] = [
                'authentication' => 'admin',
                'allow_team_access' => false,
            ];
        }

        /* Make sure we also save the controller specific settings */
        if(isset(self::$routes[self::$path][self::$controller_key]['settings'])) {
            self::$controller_settings = array_merge(self::$controller_settings, self::$routes[self::$path][self::$controller_key]['settings']);
        }

        return self::$controller;

    }

    public static function get_controller($controller_name, $path = '') {

        require_once APP_PATH . 'controllers/' . ($path != '' ? $path . '/' : null) . $controller_name . '.php';

        /* Create a new instance of the controller */
        $class = 'Altum\\Controllers\\' . $controller_name;

        /* Instantiate the controller class */
        $controller = new $class;

        return $controller;
    }

    public static function parse_method($controller) {

        $method = self::$method;

        /* Start the checks for existing potential methods */
        if(isset(self::get_params()[0])) {

            /* Try to check the methods with prettier URLs */
            self::$params[0] = str_replace('-', '_', self::$params[0]);

            /* Make sure to check the class method if set in the url */
            if(method_exists($controller, self::get_params()[0])) {

                /* Make sure the method is not private */
                $reflection = new \ReflectionMethod($controller, self::get_params()[0]);
                if($reflection->isPublic()) {
                    $method = self::get_params()[0];
                    unset(self::$params[0]);
                }

            }

            /* Restore pretty URL if not used */
            else {
                self::$params[0] = str_replace('_', '-', self::$params[0]);
            }
        }

        return self::$method = $method;

    }

}
