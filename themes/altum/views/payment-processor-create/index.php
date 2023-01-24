<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="d-print-none">
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumbs small">
                <li>
                    <a href="<?= url('payment-processors') ?>"><?= l('payment_processors.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
                </li>
                <li class="active" aria-current="page"><?= l('payment_processor_create.breadcrumb') ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-center mb-4">
            <h1 class="h4 text-truncate mb-0 mr-2"><?= l('payment_processor_create.header') ?></h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="name"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('payment_processors.input.name') ?></label>
                    <input type="text" id="name" name="name" class="form-control <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->values['name'] ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('name') ?>
                </div>

                <div class="form-group">
                    <label for="processor"><i class="fa fa-fw fa-credit-card fa-sm text-muted mr-1"></i> <?= l('payment_processors.input.processor') ?></label>
                    <select id="processor" name="processor" class="form-control <?= \Altum\Alerts::has_field_errors('processor') ? 'is-invalid' : null ?>">
                        <?php foreach(['paypal', 'stripe', 'crypto_com', 'razorpay', 'paystack'] as $processor): ?>
                            <option value="<?= $processor ?>" <?= $data->values['processor'] == $processor ? 'selected="selected"' : null ?>><?= l('pay.custom_plan.' . $processor) ?></option>
                        <?php endforeach ?>
                    </select>
                    <?= \Altum\Alerts::output_field_error('processor') ?>
                </div>

                <p><small class="form-text text-muted"><i class="fa fa-fw fa-sm fa-info-circle"></i> <?= l('payment_processors.info') ?></small></p>

                <button type="submit" name="submit" class="btn btn-block btn-primary mt-3"><?= l('global.create') ?></button>
            </form>
        </div>
    </div>
</div>
