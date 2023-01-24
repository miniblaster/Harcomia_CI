<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/taxes') ?>"><?= l('admin_taxes.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_tax_create.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-receipt text-primary-900 mr-2"></i> <?= l('admin_tax_create.header') ?></h1>
</div>

<div class="alert alert-info" role="alert">
    <?= l('admin_tax_create.subheader') ?>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">
        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

            <div class="form-group">
                <label for="name"><?= l('admin_taxes.main.name') ?></label>
                <input type="text" id="name" name="name" class="form-control form-control-lg" required="required" />
            </div>

            <div class="form-group">
                <label for="description"><?= l('admin_taxes.main.description') ?></label>
                <input type="text" id="description" name="description" class="form-control form-control-lg" required="required" />
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="value"><?= l('admin_taxes.main.value') ?></label>
                        <input type="number" min="0" step=".01" id="value" name="value" class="form-control form-control-lg" value="1" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="value_type"><?= l('admin_taxes.main.value_type') ?></label>
                        <select id="value_type" name="value_type" class="form-control form-control-lg">
                            <option value="percentage"><?= l('admin_taxes.main.value_type_percentage') ?></option>
                            <option value="fixed"><?= l('admin_taxes.main.value_type_fixed') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="type"><?= l('admin_taxes.main.type') ?></label>
                <select id="type" name="type" class="form-control form-control-lg">
                    <option value="inclusive"><?= l('admin_taxes.main.type_inclusive') ?></option>
                    <option value="exclusive"><?= l('admin_taxes.main.type_exclusive') ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="billing_type"><?= l('admin_taxes.main.billing_type') ?></label>
                <select id="billing_type" name="billing_type" class="form-control form-control-lg">
                    <option value="personal"><?= l('admin_taxes.main.billing_type_personal') ?></option>
                    <option value="business"><?= l('admin_taxes.main.billing_type_business') ?></option>
                    <option value="both"><?= l('admin_taxes.main.billing_type_both') ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="countries"><?= l('admin_taxes.main.countries') ?></label>
                <select id="countries" name="countries[]" class="form-control form-control-lg" multiple="multiple">
                    <?php foreach(get_countries_array() as $key => $value): ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach ?>
                </select>
                <small class="form-text text-muted"><?= l('admin_taxes.main.countries_help') ?></small>
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?= l('global.create') ?></button>
        </form>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';

    let checker = () => {
        let value_type = document.querySelector('select[name="value_type"]').value;

        switch(value_type) {
            case 'percentage':

                document.querySelector('select[name="type"] option[value="inclusive"]').removeAttribute('disabled');
                document.querySelector('select[name="type"] option[value="exclusive"]').removeAttribute('selected');

                break;

            case 'fixed':

                document.querySelector('select[name="type"] option[value="inclusive"]').setAttribute('disabled', 'disabled');
                document.querySelector('select[name="type"] option[value="exclusive"]').setAttribute('selected', 'selected');

                break;
        }
    };

    checker();

    document.querySelector('select[name="value_type"]').addEventListener('change', checker);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
