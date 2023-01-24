<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('tools') ?>"><?= l('tools.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('tools.ping.name') ?></li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><?= l('tools.ping.name') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('tools.ping.description') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="type"><i class="fa fa-fw fa-sm fa-fingerprint text-muted mr-1"></i> <?= l('tools.ping.type') ?></label>
                    <select id="type" name="type" class="form-control" required="required">
                        <option value="website" <?= $data->values['type'] == 'website' ? 'selected="selected"' : null ?>><?= l('tools.ping.type_website') ?></option>
                        <option value="ping" <?= $data->values['type'] == 'ping' ? 'selected="selected"' : null ?>><?= l('tools.ping.type_ping') ?></option>
                        <option value="port" <?= $data->values['type'] == 'port' ? 'selected="selected"' : null ?>><?= l('tools.ping.type_port') ?></option>
                    </select>
                    <small id="type_website_help" data-type="website" class="form-text text-muted"><?= l('tools.ping.type_website_help') ?></small id=type_help>
                    <small id="type_ping_help" data-type="ping" class="form-text text-muted"><?= l('tools.ping.type_ping_help') ?></small id=type_help>
                    <small id="type_port_help" data-type="port" class="form-text text-muted"><?= l('tools.ping.type_port_help') ?></small>
                </div>

                <div class="form-group" data-type="website">
                    <label for="target_website_url"><i class="fa fa-fw fa-sm fa-globe text-muted mr-1"></i> <?= l('tools.ping.target_url') ?></label>
                    <input type="text" id="target_website_url" name="target" class="form-control <?= \Altum\Alerts::has_field_errors('target') ? 'is-invalid' : null ?>" value="<?= $data->values['target'] ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('target') ?>
                </div>

                <div class="form-group" data-type="ping">
                    <label for="target_ping_host"><i class="fa fa-fw fa-sm fa-globe text-muted mr-1"></i> <?= l('tools.ping.target_host') ?></label>
                    <input type="text" id="target_ping_host" name="target" class="form-control" value="<?= $data->values['target'] ?>" required="required" />
                </div>

                <div class="row" data-type="port">
                    <div class="col-lg-3">
                        <div class="form-group" data-type="port">
                            <label for="target_port_host"><i class="fa fa-fw fa-sm fa-globe text-muted mr-1"></i> <?= l('tools.ping.target_host') ?></label>
                            <input type="text" id="target_port_host" name="target" class="form-control" value="<?= $data->values['target'] ?>" required="required" />
                        </div>
                    </div>

                    <div class="col-lg-9">
                        <div class="form-group" data-type="port">
                            <label for="target_port_port"><i class="fa fa-fw fa-sm fa-dna text-muted mr-1"></i> <?= l('tools.ping.target_port') ?></label>
                            <input type="text" id="target_port_port" name="port" class="form-control" value="<?= $data->values['port'] ?>" required="required" />
                        </div>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.submit') ?></button>
            </form>

        </div>
    </div>

    <?php if(isset($data->result)): ?>
        <div class="mt-4">
            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <tbody>

                    <?php if(isset($data->result['ping_server_id'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.ping.result.ping_server_id') ?>
                            </td>
                            <td class="text-nowrap">
                                <img src="<?= ASSETS_FULL_URL . 'images/countries/' . mb_strtolower($data->ping_servers[$data->result['ping_server_id']]->country_code) . '.svg' ?>" class="img-fluid icon-favicon mr-1" /> <?= get_country_from_country_code($data->ping_servers[$data->result['ping_server_id']]->country_code). ', ' . $data->ping_servers[$data->result['ping_server_id']]->city_name ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result['is_ok'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.ping.result.status') ?>
                            </td>
                            <td class="text-nowrap">
                                <?php if($data->result['is_ok']): ?>
                                    <i class="fa fa-fw fa-sm fa-check-circle text-success"></i> <?= l('tools.ping.result.is_ok') ?>
                                <?php else: ?>
                                    <i class="fa fa-fw fa-sm fa-times-circle text-danger"></i> <?= l('tools.ping.result.is_not_ok') ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if($data->result['is_ok']): ?>
                        <?php if(isset($data->result['response_time'])): ?>
                            <tr>
                                <td class="font-weight-bold">
                                    <?= l('tools.ping.result.response_time') ?>
                                </td>
                                <td class="text-nowrap">
                                    <?= display_response_time($data->result['response_time']) ?>
                                </td>
                            </tr>
                        <?php endif ?>

                        <?php if(isset($data->result['response_status_code'])): ?>
                            <tr>
                                <td class="font-weight-bold">
                                    <?= l('tools.ping.result.response_status_code') ?>
                                </td>
                                <td class="text-nowrap">
                                    <?= $data->result['response_status_code'] ?>
                                </td>
                            </tr>
                        <?php endif ?>
                    <?php else: ?>
                        <?php if(isset($data->result['error'])): ?>
                            <tr>
                                <td class="font-weight-bold">
                                    <?= l('tools.ping.result.error') ?>
                                </td>
                                <td class="text-nowrap">
                                    <?= $data->result['error']['message'] ?>
                                </td>
                            </tr>
                        <?php endif ?>
                    <?php endif ?>

                    </tbody>
                </table>
            </div>
        </div>
    <?php endif ?>

    <div class="mt-5">
        <?= $this->views['extra_content'] ?>
    </div>

    <div class="mt-5">
        <?= $this->views['similar_tools'] ?>
    </div>
</div>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php ob_start() ?>
<script>
    'use strict';

    /* Type handler */
    let type_handler = () => {
        let type = document.querySelector('select[name="type"]').value;

        document.querySelectorAll(`[data-type]:not([data-type="${type}"])`).forEach(element => {
            element.classList.add('d-none');

            element.querySelector('input[name="target"],input[name="port"]') && element.querySelector('input[name="target"],input[name="port"]').setAttribute('disabled', 'disabled');
            element.querySelector('input[name="target"],input[name="port"]') && element.querySelector('input[name="target"],input[name="port"]').removeAttribute('required');
        });

        document.querySelectorAll(`[data-type="${type}"]`).forEach(element => {
            element.classList.remove('d-none');

            element.querySelector('input[name="target"],input[name="port"]') && element.querySelector('input[name="target"],input[name="port"]').removeAttribute('disabled');
            element.querySelector('input[name="target"],input[name="port"]') && element.querySelector('input[name="target"],input[name="port"]').setAttribute('required', 'required');
        });
    }

    type_handler();

    document.querySelector('select[name="type"]') && document.querySelector('select[name="type"]').addEventListener('change', type_handler);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

