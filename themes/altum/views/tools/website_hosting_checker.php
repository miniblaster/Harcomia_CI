<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('tools') ?>"><?= l('tools.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('tools.website_hosting_checker.name') ?></li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><?= l('tools.website_hosting_checker.name') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('tools.website_hosting_checker.description') ?>">
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
                    <label for="host"><i class="fa fa-fw fa-globe fa-sm text-muted mr-1"></i> <?= l('tools.website_hosting_checker.host') ?></label>
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

                    <?php if(isset($data->result->country)): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.website_hosting_checker.result.isp') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result->isp ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result->country)): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.website_hosting_checker.result.org') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result->org ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result->country)): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.website_hosting_checker.result.country') ?>
                            </td>
                            <td class="text-nowrap">
                                <img src="<?= ASSETS_FULL_URL . 'images/countries/' . mb_strtolower($data->result->countryCode) . '.svg' ?>" class="img-fluid icon-favicon mr-1" /> <?= get_country_from_country_code($data->result->countryCode) ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result->city)): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.website_hosting_checker.result.city') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result->city ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result->lat)): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.website_hosting_checker.result.latitude') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result->lat ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.website_hosting_checker.result.longitude') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result->lon ?>
                            </td>
                        </tr>
                    <?php endif ?>


                    <?php if(isset($data->result->timezone)): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.website_hosting_checker.result.timezone') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result->timezone ?>
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

