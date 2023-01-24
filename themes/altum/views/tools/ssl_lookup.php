<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('tools') ?>"><?= l('tools.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('tools.ssl_lookup.name') ?></li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><?= l('tools.ssl_lookup.name') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('tools.ssl_lookup.description') ?>">
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
                    <label for="host"><i class="fa fa-fw fa-globe fa-sm text-muted mr-1"></i> <?= l('tools.ssl_lookup.host') ?></label>
                    <input type="text" id="host" name="host" class="form-control <?= \Altum\Alerts::has_field_errors('host') ? 'is-invalid' : null ?>" value="<?= $data->values['host'] ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('host') ?>
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

                    <?php if(isset($data->result['country'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.ssl_lookup.result.country') ?>
                            </td>
                            <td class="text-nowrap">
                                <img src="<?= ASSETS_FULL_URL . 'images/countries/' . mb_strtolower($data->result['country']) . '.svg' ?>" class="img-fluid icon-favicon mr-1" /> <?= get_country_from_country_code($data->result['country']) ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result['organization'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.ssl_lookup.result.organization') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result['organization'] ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result['common_name'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.ssl_lookup.result.common_name') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result['common_name'] ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result['start_datetime'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.ssl_lookup.result.start_datetime') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= \Altum\Date::get($data->result['start_datetime'], 2)  . '(' . \Altum\Date::get($data->result['start_datetime'], 1) . ')' ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result['end_datetime'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.ssl_lookup.result.end_datetime') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= \Altum\Date::get($data->result['end_datetime'], 2)  . '(' . \Altum\Date::get($data->result['end_datetime'], 1) . ')' ?>
                            </td>
                        </tr>
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

