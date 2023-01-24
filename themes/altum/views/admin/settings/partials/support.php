<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php
    if(isset(settings()->support->expiry_datetime)):
    $expiry_datetime = (new \DateTime(settings()->support->expiry_datetime ?? null));
    $is_active = (new \DateTime()) <= $expiry_datetime;
    ?>
        <div class="form-group">
            <label for="status"><?= l('admin_settings.support.status') ?></label>
            <input id="status" name="status" type="text" class="form-control form-control-lg disabled <?= ($is_active ? 'is-valid' : 'is-invalid') ?>" value="<?= sprintf(l('admin_settings.support.status.' . ($is_active ? 'active' : 'inactive')), $expiry_datetime->format('Y-m-d H:i:s')) ?>" readonly="readonly" />
            <small class="form-text <?= $is_active ? 'text-muted' : 'text-danger' ?>"><?= l('admin_settings.support.status.' . ($is_active ? 'active' : 'inactive') . '.help') ?></small>
        </div>

        <?php if(!$is_active): ?>
            <a href="https://altumco.de/<?= PRODUCT_KEY ?>-buy" target="_blank" class="btn btn-block btn-success mb-3"><?= l('admin_settings.support.extend') ?></a>
        <?php endif ?>
    <?php else: ?>
        <div class="form-group">
            <label for="status"><?= l('admin_settings.support.status') ?></label>
            <input id="status" name="status" type="text" class="form-control form-control-lg disabled" value="-" readonly="readonly" />
            <small class="form-text text-muted"><?= l('admin_settings.support.status.unknown.help') ?></small>
        </div>
    <?php endif ?>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('admin_settings.support.submit') ?></button>
