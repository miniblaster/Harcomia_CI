<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the affiliate plugin system.
        </div>
    <?php endif ?>

    <div <?= !\Altum\Plugin::is_active('affiliate') ? 'data-toggle="tooltip" title="' . sprintf(l('admin_plugins.no_access'), \Altum\Plugin::get('affiliate')->name ?? 'affiliate') . '"' : null ?>>
        <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) || !\Altum\Plugin::is_active('affiliate') ? 'container-disabled' : null ?>">
            <div class="form-group">
                <label for="is_enabled"><?= l('admin_settings.affiliate.is_enabled') ?></label>
                <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                    <option value="1" <?= \Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                    <option value="0" <?= \Altum\Plugin::is_active('affiliate') && !settings()->affiliate->is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
                </select>
                <small class="form-text text-muted"><?= l('admin_settings.affiliate.is_enabled_help') ?></small>
            </div>

            <div class="form-group">
                <label for="commission_type"><?= l('admin_settings.affiliate.commission_type') ?></label>
                <select id="commission_type" name="commission_type" class="form-control form-control-lg">
                    <option value="once" <?= \Altum\Plugin::is_active('affiliate') && settings()->affiliate->commission_type == 'once' ? 'selected="selected"' : null ?>><?= l('admin_settings.affiliate.commission_type_once') ?></option>
                    <option value="forever" <?= \Altum\Plugin::is_active('affiliate') && settings()->affiliate->commission_type == 'forever' ? 'selected="selected"' : null ?>><?= l('admin_settings.affiliate.commission_type_forever') ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="minimum_withdrawal_amount"><?= l('admin_settings.affiliate.minimum_withdrawal_amount') ?></label>
                <input id="minimum_withdrawal_amount" type="number" min="1" name="minimum_withdrawal_amount" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('affiliate') ? settings()->affiliate->minimum_withdrawal_amount : 1 ?>" />
                <small class="form-text text-muted"><?= l('admin_settings.affiliate.minimum_withdrawal_amount_help') ?></small>
            </div>

            <div class="form-group">
                <label for="withdrawal_notes"><?= l('admin_settings.affiliate.withdrawal_notes') ?></label>
                <textarea id="withdrawal_notes" name="withdrawal_notes" class="form-control form-control-lg"><?= \Altum\Plugin::is_active('affiliate') ? settings()->affiliate->withdrawal_notes : null ?></textarea>
                <small class="form-text text-muted"><?= l('admin_settings.affiliate.withdrawal_notes_help') ?></small>
            </div>
        </div>
    </div>
</div>

<?php if(\Altum\Plugin::is_active('affiliate')): ?>
    <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
<?php endif ?>
