<?php defined('ALTUMCODE') || die() ?>

<div class="container my-5 d-flex justify-content-center">
    <div class="col-12 col-lg-10">

        <div class="d-print-none d-flex justify-content-between mb-5">
            <div></div>
            <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fa fa-fw fa-sm fa-print"></i> <?= l('invoice.print') ?></button>
        </div>

        <div class="card bg-gray-50 border-0">
            <div class="card-body p-5">

                <div class="d-flex flex-column flex-md-row justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()}): ?>
                            <img src="<?= \Altum\Uploads::get_full_url('logo_' . \Altum\ThemeStyle::get()) . settings()->main->{'logo_' . \Altum\ThemeStyle::get()} ?>" class="img-fluid navbar-logo invoice-logo" alt="<?= l('global.accessibility.logo_alt') ?>" />
                        <?php else: ?>
                            <h1><?= $data->payment->business->brand_name ?? settings()->business->brand_name ?></h1>
                        <?php endif ?>
                    </div>

                    <div class="d-flex flex-column">
                        <h3 class="text-muted"><?= l('invoice.invoice') ?></h3>

                        <table>
                            <tbody>
                            <tr>
                                <td class="font-weight-bold text-muted pr-3"><?= l('invoice.invoice_nr') ?>:</td>
                                <td><?= $data->payment->business->invoice_nr_prefix . $data->payment->id ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-muted pr-3"><?= l('invoice.invoice_date') ?>:</td>
                                <td><?= \Altum\Date::get($data->payment->datetime, 1) ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-6 mb-md-0">
                            <h5><?= l('invoice.vendor') ?></h5>

                            <table>
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold text-muted pr-3"><?= l('invoice.name') ?>:</td>
                                    <td><?= $data->payment->business->name ?></td>
                                </tr>

                                <?php if(!empty($data->payment->business->address)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= l('invoice.address') ?>:</td>
                                        <td><?= $data->payment->business->address ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->payment->business->city)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= l('invoice.city') ?>:</td>
                                        <td><?= $data->payment->business->city ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->payment->business->county)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= l('invoice.county') ?>:</td>
                                        <td><?= $data->payment->business->county ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->payment->business->zip)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= l('invoice.zip') ?>:</td>
                                        <td><?= $data->payment->business->zip ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->payment->business->country)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= l('invoice.country') ?>:</td>
                                        <td><?= get_countries_array()[$data->payment->business->country] ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->payment->business->email)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= l('invoice.email') ?>:</td>
                                        <td><?= $data->payment->business->email ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->payment->business->phone)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= l('invoice.phone') ?>:</td>
                                        <td><?= $data->payment->business->phone ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->payment->business->tax_type) && !empty($data->payment->business->tax_id)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $data->payment->business->tax_type ?>:</td>
                                        <td><?= $data->payment->business->tax_id ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->payment->business->custom_key_one) && !empty($data->payment->business->custom_value_one)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $data->payment->business->custom_key_one ?>:</td>
                                        <td><?= $data->payment->business->custom_value_one ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->payment->business->custom_key_two) && !empty($data->payment->business->custom_value_two)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $data->payment->business->custom_key_two ?>:</td>
                                        <td><?= $data->payment->business->custom_value_two ?></td>
                                    </tr>
                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-12 col-md-6">
                            <h5><?= l('invoice.customer') ?></h5>

                            <table>
                                <tbody>
                                <?php if($data->payment->billing): ?>

                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= l('invoice.name') ?>:</td>
                                        <td><?= $data->payment->billing->name ?></td>
                                    </tr>

                                    <?php if(!empty($data->payment->billing->address)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= l('invoice.address') ?>:</td>
                                            <td><?= $data->payment->billing->address ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->city)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= l('invoice.city') ?>:</td>
                                            <td><?= $data->payment->billing->city ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->county)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= l('invoice.county') ?>:</td>
                                            <td><?= $data->payment->billing->county ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->zip)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= l('invoice.zip') ?>:</td>
                                            <td><?= $data->payment->billing->zip ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->country)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= l('invoice.country') ?>:</td>
                                            <td><?= get_countries_array()[$data->payment->billing->country] ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->email)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= l('invoice.email') ?>:</td>
                                            <td><?= $data->payment->billing->email ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->billing->phone)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= l('invoice.phone') ?>:</td>
                                            <td><?= $data->payment->billing->phone ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if($data->payment->billing->type == 'business'): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= !empty($data->payment->business->tax_type) ? $data->payment->business->tax_type : l('invoice.tax_id') ?>:</td>
                                            <td><?= $data->payment->billing->tax_id ?></td>
                                        </tr>
                                    <?php endif ?>

                                <?php else: ?>

                                    <?php if(!empty($data->payment->name)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= l('invoice.name') ?>:</td>
                                            <td><?= $data->payment->name ?></td>
                                        </tr>
                                    <?php endif ?>

                                    <?php if(!empty($data->payment->email)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= l('invoice.email') ?>:</td>
                                            <td><?= $data->payment->email ?></td>
                                        </tr>
                                    <?php endif ?>

                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <table class="table invoice-table">
                        <thead>
                        <tr>
                            <th><?= l('invoice.table.item') ?></th>
                            <th class="text-right"><?= l('invoice.table.amount') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <span><?= sprintf(l('invoice.table.plan'), $data->payment->plan->name ?? $data->payment->plan_db->name) ?></span>
                                    <span class="text-muted"><?= sprintf(l('invoice.table.frequency'), l('invoice.table.frequency_' . $data->payment->frequency)) ?></span>
                                </div>
                            </td>
                            <td class="text-right"><?= ($data->payment->base_amount ? $data->payment->base_amount : $data->payment->total_amount) . ' ' . $data->payment->currency ?></td>
                        </tr>

                        <?php if($data->payment->discount_amount): ?>
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><?= l('invoice.table.code') ?></span>
                                        <span class="text-muted"><?= sprintf(l('invoice.table.code_help'), $data->payment->code) ?></span>
                                    </div>
                                </td>
                                <td class="text-right"><?= '-' . $data->payment->discount_amount . ' ' . $data->payment->currency ?></td>
                            </tr>
                        <?php endif ?>

                        <?php if(!empty($data->payment_taxes)): ?>
                            <?php foreach($data->payment_taxes as $row): ?>

                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span><?= $row->name ?></span>
                                            <div>
                                                <span class="text-muted"><?= l('pay.custom_plan.summary.' . ($row->type == 'inclusive' ? 'tax_inclusive' : 'tax_exclusive')) ?>.</span>
                                                <span class="text-muted"><?= $row->description ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <?php if($row->type == 'inclusive'): ?>
                                            <?= $row->amount ?>
                                        <?php else: ?>
                                            <?= '+' . $row->amount ?>
                                        <?php endif ?>
                                        <span><?= $data->payment->currency ?></span>
                                    </td>
                                </tr>

                            <?php endforeach ?>
                        <?php endif ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="d-flex flex-column">
                                <span class="font-weight-bold"><?= l('invoice.table.total') ?></span>
                                <small><?= sprintf(l('invoice.table.paid_via'), l('pay.custom_plan.' . $data->payment->processor)) ?></small>
                            </td>
                            <td class="text-right font-weight-bold"><?= $data->payment->total_amount . ' ' . $data->payment->currency ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-6">
                    <table class="table invoice-table">
                        <thead>
                        <tr>
                            <th><?= l('invoice.notes') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <textarea class="form-control mt-3" rows="4" aria-label="<?= l('invoice.notes') ?>"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
