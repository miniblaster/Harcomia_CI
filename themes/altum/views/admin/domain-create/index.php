<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/domains') ?>"><?= l('admin_domains.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_domain_create.breadcrumb') ?></li>
    </ol>
</nav>

<?php $url = parse_url(SITE_URL); $host = $url['host'] . (mb_strlen($url['path']) > 1 ? $url['path'] : null); ?>

<div class="d-flex align-items-center mb-4">
    <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-globe text-primary-900 mr-2"></i> <?= l('admin_domain_create.header') ?></h1>
    <div class="ml-2">
        <span data-toggle="tooltip" data-html="true" title="<?= sprintf(l('admin_domains.main.helper'), '<strong>' . $_SERVER['SERVER_ADDR'] . '</strong>', '<strong>' . $host . '</strong>') ?>">
            <i class="fa fa-fw fa-info-circle text-muted"></i>
        </span>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />


            <p class="text-muted"></p>

            <div class="form-group">
                <label for="host"><?= l('admin_domains.main.host') ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <select name="scheme" class="appearance-none select-custom-altum form-control form-control-lg input-group-text">
                            <option value="https://">https://</option>
                            <option value="http://">http://</option>
                        </select>
                    </div>
                    <input id="host" type="text" name="host" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('host') ? 'is-invalid' : null ?>" placeholder="<?= l('admin_domains.main.host_placeholder') ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('host') ?>
                </div>
                <small class="form-text text-muted"><?= l('admin_domains.main.host_help') ?></small>
            </div>

            <div class="form-group">
                <label for="custom_index_url"><?= l('admin_domains.main.custom_index_url') ?></label>
                <input id="custom_index_url" type="text" name="custom_index_url" class="form-control form-control-lg" placeholder="<?= l('admin_domains.main.custom_index_url_placeholder') ?>" />
                <small class="form-text text-muted"><?= l('admin_domains.main.custom_index_url_help') ?></small>
            </div>

            <div class="form-group">
                <label for="custom_not_found_url"><?= l('admin_domains.main.custom_not_found_url') ?></label>
                <input id="custom_not_found_url" type="text" name="custom_not_found_url" class="form-control form-control-lg" placeholder="<?= l('admin_domains.main.custom_not_found_url_placeholder') ?>" />
                <small class="form-text text-muted"><?= l('admin_domains.main.custom_not_found_url_help') ?></small>
            </div>

            <div class="form-group">
                <label for="is_enabled"><?= l('admin_domains.main.is_enabled') ?></label>
                <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                    <option value="1"><?= l('global.active') ?></option>
                    <option value="0"><?= l('global.disabled') ?></option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary"><?= l('global.create') ?></button>
            </div>
        </form>

    </div>
</div>

