<?php defined('ALTUMCODE') || die() ?>

<div>
    <p class="text-muted"><?= l('admin_settings.business.subheader') ?></p>

    <div class="form-group">
        <label for="brand_name"><?= l('admin_settings.business.brand_name') ?></label>
        <input id="brand_name" type="text" name="brand_name" class="form-control form-control-lg" value="<?= settings()->business->brand_name ?>" />
    </div>

    <div class="form-group">
        <label for="invoice_nr_prefix"><?= l('admin_settings.business.invoice_nr_prefix') ?></label>
        <input id="invoice_nr_prefix" type="text" name="invoice_nr_prefix" class="form-control form-control-lg" value="<?= settings()->business->invoice_nr_prefix ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.business.invoice_nr_prefix_help') ?></small>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="name"><?= l('admin_settings.business.name') ?></label>
                <input id="name" type="text" name="name" class="form-control form-control-lg" value="<?= settings()->business->name ?>" />
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                <label for="address"><?= l('admin_settings.business.address') ?></label>
                <input id="address" type="text" name="address" class="form-control form-control-lg" value="<?= settings()->business->address ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="city"><?= l('admin_settings.business.city') ?></label>
                <input id="city" type="text" name="city" class="form-control form-control-lg" value="<?= settings()->business->city ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="form-group">
                <label for="county"><?= l('admin_settings.business.county') ?></label>
                <input id="county" type="text" name="county" class="form-control form-control-lg" value="<?= settings()->business->county ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-2">
            <div class="form-group">
                <label for="zip"><?= l('admin_settings.business.zip') ?></label>
                <input id="zip" type="text" name="zip" class="form-control form-control-lg" value="<?= settings()->business->zip ?>" />
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                <label for="country"><?= l('admin_settings.business.country') ?></label>
                <select id="country" name="country" class="form-control form-control-lg">
                    <?php foreach(get_countries_array() as $key => $value): ?>
                        <option value="<?= $key ?>" <?= settings()->business->country == $key ? 'selected="selected"' : null ?>><?= $value ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="email"><?= l('admin_settings.business.email') ?></label>
                <input id="email" type="text" name="email" class="form-control form-control-lg" value="<?= settings()->business->email ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="phone"><?= l('admin_settings.business.phone') ?></label>
                <input id="phone" type="text" name="phone" class="form-control form-control-lg" value="<?= settings()->business->phone ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="tax_type"><?= l('admin_settings.business.tax_type') ?></label>
                <input id="tax_type" type="text" name="tax_type" class="form-control form-control-lg" value="<?= settings()->business->tax_type ?>" placeholder="<?= l('admin_settings.business.tax_type_placeholder') ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="tax_id"><?= l('admin_settings.business.tax_id') ?></label>
                <input id="tax_id" type="text" name="tax_id" class="form-control form-control-lg" value="<?= settings()->business->tax_id ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="custom_key_one"><?= l('admin_settings.business.custom_key_one') ?></label>
                <input id="custom_key_one" type="text" name="custom_key_one" class="form-control form-control-lg" value="<?= settings()->business->custom_key_one ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="custom_value_one"><?= l('admin_settings.business.custom_value_one') ?></label>
                <input id="custom_value_one" type="text" name="custom_value_one" class="form-control form-control-lg" value="<?= settings()->business->custom_value_one ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="custom_key_two"><?= l('admin_settings.business.custom_key_two') ?></label>
                <input id="custom_key_two" type="text" name="custom_key_two" class="form-control form-control-lg" value="<?= settings()->business->custom_key_two ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="custom_value_two"><?= l('admin_settings.business.custom_value_two') ?></label>
                <input id="custom_value_two" type="text" name="custom_value_two" class="form-control form-control-lg" value="<?= settings()->business->custom_value_two ?>" />
            </div>
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
