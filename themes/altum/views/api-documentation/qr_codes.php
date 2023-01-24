<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url() ?>"><?= l('index.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li><a href="<?= url('api-documentation') ?>"><?= l('api_documentation.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('api_documentation.qr_codes.breadcrumb') ?></li>
        </ol>
    </nav>

    <h1 class="h4 mb-4"><?= l('api_documentation.qr_codes.header') ?></h1>

    <div class="accordion">
        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#qr_codes_read_all" aria-expanded="true" aria-controls="qr_codes_read_all">
                        <?= l('api_documentation.read_all') ?>
                    </a>
                </h3>
            </div>

            <div id="qr_codes_read_all" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= l('api_documentation.endpoint') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-success mr-3">GET</span> <span class="text-muted"><?= SITE_URL ?>api/qr-codes/</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= l('api_documentation.example') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request GET \<br />
                                --url '<?= SITE_URL ?>api/qr-codes/' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-custom-container mb-4">
                        <table class="table table-custom">
                            <thead>
                            <tr>
                                <th><?= l('api_documentation.parameters') ?></th>
                                <th><?= l('api_documentation.details') ?></th>
                                <th><?= l('api_documentation.description') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>page</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span>
                                </td>
                                <td><?= l('api_documentation.filters.page') ?></td>
                            </tr>
                            <tr>
                                <td>results_per_page</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span>
                                </td>
                                <td><?= sprintf(l('api_documentation.filters.results_per_page'), '<code>' . implode('</code> , <code>', [10, 25, 50, 100, 250, 500]) . '</code>', 25) ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <label><?= l('api_documentation.response') ?></label>
                        <div class="card bg-gray-100 border-0">
                                        <pre class="card-body">
{
    "data": [
        {
            "id": 1,
            "type": "url",
            "name": "Example name",
            "qr_code": "<?= SITE_URL ?>uploads/qr_code/example.svg",
            "qr_code_logo": null,
            "settings": {
                "foreground_type": "color",
                "foreground_color": "#000000",
                "background_color": "#ffffff",
                "custom_eyes_color": false,
                "qr_code_logo_size": 25,
                "size": 500,
                "margin": 0,
                "ecc": "L",
                "url": "https://example.com"
            },
            "last_datetime": "2021-10-31 09:47:25",
            "datetime": "2021-10-29 16:32:25"
        },
    ],
    "meta": {
        "page": 1,
        "results_per_page": 25,
        "total": 1,
        "total_pages": 1
    },
    "links": {
        "first": "<?= SITE_URL ?>api/qr-codes?&page=1",
        "last": "<?= SITE_URL ?>api/qr-codes?&page=1",
        "next": null,
        "prev": null,
        "self": "<?= SITE_URL ?>api/qr-codes?&page=1"
    }
}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#qr_codes_read" aria-expanded="true" aria-controls="qr_codes_read">
                        <?= l('api_documentation.read') ?>
                    </a>
                </h3>
            </div>

            <div id="qr_codes_read" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= l('api_documentation.endpoint') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-success mr-3">GET</span> <span class="text-muted"><?= SITE_URL ?>api/qr-codes/</span><span class="text-primary">{qr_code_id}</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= l('api_documentation.example') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request GET \<br />
                                --url '<?= SITE_URL ?>api/qr-codes/<span class="text-primary">{qr_code_id}</span>' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= l('api_documentation.response') ?></label>
                        <div class="card bg-gray-100 border-0">
                                        <pre class="card-body">
{
    "data": {
        "id": 1,
        "type": "url",
        "name": "Example name",
        "qr_code": "<?= SITE_URL ?>uploads/qr_code/example.svg",
        "qr_code_logo": null,
        "settings": {
            "foreground_type": "color",
            "foreground_color": "#000000",
            "background_color": "#ffffff",
            "custom_eyes_color": false,
            "qr_code_logo_size": 25,
            "size": 500,
            "margin": 0,
            "ecc": "L",
            "url": "https://example.com"
        },
        "last_datetime": "2021-10-31 09:47:25",
        "datetime": "2021-10-29 16:32:25"
    }
}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#qr_codes_create" aria-expanded="true" aria-controls="qr_codes_create">
                        <?= l('api_documentation.create') ?>
                    </a>
                </h3>
            </div>

            <div id="qr_codes_create" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= l('api_documentation.endpoint') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-info mr-3">POST</span> <span class="text-muted"><?= SITE_URL ?>api/qr-codes</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-custom-container mb-4">
                        <table class="table table-custom">
                            <thead>
                            <tr>
                                <th><?= l('api_documentation.parameters') ?></th>
                                <th><?= l('api_documentation.details') ?></th>
                                <th><?= l('api_documentation.description') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>project_id</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>name</td>
                                <td>
                                    <span class="badge badge-danger"><?= l('api_documentation.required') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>type</td>
                                <td>
                                    <span class="badge badge-danger"><?= l('api_documentation.required') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><?= '<code>' . implode('</code> , <code>',  array_keys((require APP_PATH . 'includes/qr_code.php')['type'])) . '</code>' ?></td>
                            </tr>
                            <tr>
                                <td>style</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>square</code>, <code>dot</code>, <code>round</code></td>
                            </tr>
                            <tr>
                                <td>foreground_type</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>color</code>, <code>gradient</code></td>
                            </tr>
                            <tr>
                                <td>foreground_color</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(foreground_type=color)</td>
                            </tr>
                            <tr>
                                <td>foreground_gradient_style</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>vertical</code> <code>horizontal</code> <code>diagonal</code> <code>inverse_diagonal</code> <code>radial</code> (foreground_type=gradient)</td>
                            </tr>
                            <tr>
                                <td>foreground_gradient_one</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(foreground_type=gradient)</td>
                            </tr>
                            <tr>
                                <td>foreground_gradient_two</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(foreground_type=gradient)</td>
                            </tr>
                            <tr>
                                <td>background_color</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>background_color_transparency</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>custom_eyes_color</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.boolean') ?></span>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>eyes_inner_color</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(custom_eyes_color=1)</td>
                            </tr>
                            <tr>
                                <td>eyes_outer_color</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(custom_eyes_color=1)</td>
                            </tr>
                            <tr>
                                <td>qr_code_logo</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.file') ?></span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>qr_code_logo_size</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span>
                                </td>
                                <td>5-35</td>
                            </tr>
                            <tr>
                                <td>size</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span>
                                </td>
                                <td>50-2000</td>
                            </tr>
                            <tr>
                                <td>margin</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span>
                                </td>
                                <td>0-25</td>
                            </tr>
                            <tr>
                                <td>ecc</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>L</code>, <code>M</code>, <code>Q</code>, <code>H</code></td>
                            </tr>
                            <tr>
                                <td>text</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=text)</td>
                            </tr>
                            <tr>
                                <td>url</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=url)</td>
                            </tr>
                            <tr>
                                <td>phone</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=phone)</td>
                            </tr>
                            <tr>
                                <td>sms</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=sms)</td>
                            </tr>
                            <tr>
                                <td>email</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=email)</td>
                            </tr>
                            <tr>
                                <td>email_subject</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=email)</td>
                            </tr>
                            <tr>
                                <td>email_body</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=email)</td>
                            </tr>
                            <tr>
                                <td>whatsapp</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=whatsapp)</td>
                            </tr>
                            <tr>
                                <td>facetime</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=facetime)</td>
                            </tr>
                            <tr>
                                <td>location_latitude</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.float') ?></span></td>
                                <td>(type=location)</td>
                            </tr>
                            <tr>
                                <td>location_longitude</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.float') ?></span></td>
                                <td>(type=location)</td>
                            </tr>
                            <tr>
                                <td>wifi_ssid</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=wifi)</td>
                            </tr>
                            <tr>
                                <td>wifi_encryption</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>nopass</code>, <code>WEP</code>, <code>WPA/WPA2</code> (type=wifi)</td>
                            </tr>
                            <tr>
                                <td>wifi_password</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=wifi)</td>
                            </tr>
                            <tr>
                                <td>wifi_is_hidden</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.boolean') ?></span></td>
                                <td>(type=wifi)</td>
                            </tr>
                            <tr>
                                <td>event</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_location</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_url</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_note</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_timezone</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_start_datetime</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_end_datetime</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>crypto_coin</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=crypto)</td>
                            </tr>
                            <tr>
                                <td>crypto_address</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=crypto)</td>
                            </tr>
                            <tr>
                                <td>crypto_amount</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=crypto)</td>
                            </tr>
                            <tr>
                                <td>vcard_first_name</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_last_name</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_phone</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_email</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_url</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_job_title</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_birthday</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_street</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_city</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_zip</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_region</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_country</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_note</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_social_label[index]</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_social_value[index]</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= l('api_documentation.example') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request POST \<br />
                                --url '<?= SITE_URL ?>api/qr-codes' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                                --header 'Content-Type: multipart/form-data' \<br />
                                --form 'name=<span class="text-primary">New York</span>' \<br />
                                --form 'type=<span class="text-primary">text</span>' \<br />
                                --form 'text=<span class="text-primary">Hello!</span>' \<br />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= l('api_documentation.response') ?></label>
                        <div class="card bg-gray-100 border-0">
                                <pre class="card-body">
{
    "data": {
        "id": 1
    }
}</pre>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#qr_codes_update" aria-expanded="true" aria-controls="qr_codes_update">
                        <?= l('api_documentation.update') ?>
                    </a>
                </h3>
            </div>

            <div id="qr_codes_update" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= l('api_documentation.endpoint') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-info mr-3">POST</span> <span class="text-muted"><?= SITE_URL ?>api/qr-codes/</span><span class="text-primary">{qr_code_id}</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-custom-container mb-4">
                        <table class="table table-custom">
                            <thead>
                            <tr>
                                <th><?= l('api_documentation.parameters') ?></th>
                                <th><?= l('api_documentation.details') ?></th>
                                <th><?= l('api_documentation.description') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>project_id</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>name</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>type</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><?= '<code>' . implode('</code> , <code>',  array_keys((require APP_PATH . 'includes/qr_code.php')['type'])) . '</code>' ?></td>
                            </tr>
                            <tr>
                                <td>style</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>square</code>, <code>dot</code>, <code>round</code></td>
                            </tr>
                            <tr>
                                <td>foreground_type</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>color</code>, <code>gradient</code></td>
                            </tr>
                            <tr>
                                <td>foreground_color</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(foreground_type=color)</td>
                            </tr>
                            <tr>
                                <td>foreground_gradient_style</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>vertical</code> <code>horizontal</code> <code>diagonal</code> <code>inverse_diagonal</code> <code>radial</code> (foreground_type=gradient)</td>
                            </tr>
                            <tr>
                                <td>foreground_gradient_one</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(foreground_type=gradient)</td>
                            </tr>
                            <tr>
                                <td>foreground_gradient_two</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(foreground_type=gradient)</td>
                            </tr>
                            <tr>
                                <td>background_color</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>background_color_transparency</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>custom_eyes_color</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.boolean') ?></span></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>eyes_inner_color</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(custom_eyes_color=1)</td>
                            </tr>
                            <tr>
                                <td>eyes_outer_color</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(custom_eyes_color=1)</td>
                            </tr>
                            <tr>
                                <td>qr_code_logo</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.file') ?></span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>qr_code_logo_size</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span></td>
                                <td>5-35</td>
                            </tr>
                            <tr>
                                <td>size</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span></td>
                                <td>50-2000</td>
                            </tr>
                            <tr>
                                <td>margin</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.int') ?></span></td>
                                <td>0-25</td>
                            </tr>
                            <tr>
                                <td>ecc</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>L</code>, <code>M</code>, <code>Q</code>, <code>H</code></td>
                            </tr>
                            <tr>
                                <td>text</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=text)</td>
                            </tr>
                            <tr>
                                <td>url</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=url)</td>
                            </tr>
                            <tr>
                                <td>phone</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=phone)</td>
                            </tr>
                            <tr>
                                <td>sms</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=sms)</td>
                            </tr>
                            <tr>
                                <td>email</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=email)</td>
                            </tr>
                            <tr>
                                <td>email_subject</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=email)</td>
                            </tr>
                            <tr>
                                <td>email_body</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=email)</td>
                            </tr>
                            <tr>
                                <td>whatsapp</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=whatsapp)</td>
                            </tr>
                            <tr>
                                <td>facetime</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=facetime)</td>
                            </tr>
                            <tr>
                                <td>location_latitude</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.float') ?></span></td>
                                <td>(type=location)</td>
                            </tr>
                            <tr>
                                <td>location_longitude</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.float') ?></span></td>
                                <td>(type=location)</td>
                            </tr>
                            <tr>
                                <td>wifi_ssid</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=wifi)</td>
                            </tr>
                            <tr>
                                <td>wifi_encryption</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td><code>nopass</code>, <code>WEP</code>, <code>WPA/WPA2</code> (type=wifi)</td>
                            </tr>
                            <tr>
                                <td>wifi_password</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=wifi)</td>
                            </tr>
                            <tr>
                                <td>wifi_is_hidden</td>
                                <td><span class="badge badge-info"><?= l('api_documentation.optional') ?></span> <span class="badge badge-secondary"><?= l('api_documentation.boolean') ?></span></td>
                                <td>(type=wifi)</td>
                            </tr>
                            <tr>
                                <td>event</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_location</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_url</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_note</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_timezone</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_start_datetime</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>event_end_datetime</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=event)</td>
                            </tr>
                            <tr>
                                <td>crypto_coin</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=crypto)</td>
                            </tr>
                            <tr>
                                <td>crypto_address</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=crypto)</td>
                            </tr>
                            <tr>
                                <td>crypto_amount</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=crypto)</td>
                            </tr>
                            <tr>
                                <td>vcard_first_name</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_last_name</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_phone</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_email</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_url</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_job_title</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_birthday</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_street</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_city</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_zip</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_region</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_country</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_note</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_social_label[index]</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            <tr>
                                <td>vcard_social_value[index]</td>
                                <td>
                                    <span class="badge badge-info"><?= l('api_documentation.optional') ?></span>
                                    <span class="badge badge-secondary"><?= l('api_documentation.string') ?></span>
                                </td>
                                <td>(type=vcard)</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= l('api_documentation.example') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request POST \<br />
                                --url '<?= SITE_URL ?>api/qr-codes/<span class="text-primary">{qr_code_id}</span>' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                                --header 'Content-Type: multipart/form-data' \<br />
                                --form 'name=<span class="text-primary">Las Vegas</span>' \<br />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= l('api_documentation.response') ?></label>
                        <div class="card bg-gray-100 border-0">
                                <pre class="card-body">
{
  "data": {
    "id": 1
  }
}</pre>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#qr_codes_delete" aria-expanded="true" aria-controls="qr_codes_delete">
                        <?= l('api_documentation.delete') ?>
                    </a>
                </h3>
            </div>

            <div id="qr_codes_delete" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= l('api_documentation.endpoint') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-danger mr-3">DELETE</span> <span class="text-muted"><?= SITE_URL ?>api/qr-codes/</span><span class="text-primary">{qr_code_id}</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= l('api_documentation.example') ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request DELETE \<br />
                                --url '<?= SITE_URL ?>api/qr-codes/<span class="text-primary">{qr_code_id}</span>' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
