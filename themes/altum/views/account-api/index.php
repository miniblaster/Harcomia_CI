<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <?= $this->views['account_header_menu'] ?>

    <div class="row mb-3">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0">
            <h1 class="h4 m-0"><?= l('account_api.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('account_api.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-xl-auto d-flex">
            <a href="<?= url('api-documentation') ?>" class="btn btn-primary"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('api_documentation.menu') ?></a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div <?= $this->user->plan_settings->api_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="form-group <?= $this->user->plan_settings->api_is_enabled ? null : 'container-disabled' ?>">
                        <label for="api_key"><?= l('account_api.api_key') ?></label>
                        <input type="text" id="api_key" name="api_key" value="<?= $this->user->api_key ?>" class="form-control" onclick="this.select();" readonly="readonly" />
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-outline-secondary"><?= l('account_api.button') ?></button>
            </form>

        </div>
    </div>
</div>
