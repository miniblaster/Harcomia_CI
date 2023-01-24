<?php defined('ALTUMCODE') || die() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li>
                <a href="<?= url('admin/codes') ?>"><?= l('admin_codes.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li class="active" aria-current="page"><?= l('admin_code_create.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between mb-4">
        <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-tags text-primary-900 mr-2"></i> <?= l('admin_code_create.header') ?></h1>
    </div>

<?= \Altum\Alerts::output_alerts() ?>

    <div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="name"><?= l('admin_codes.main.name') ?></label>
                    <input type="text" id="name" name="name" class="form-control form-control-lg" required="required" />
                </div>

                <div class="form-group">
                    <label for="type"><?= l('admin_codes.main.type') ?></label>
                    <select id="type" name="type" class="form-control form-control-lg">
                        <option value="discount"><?= l('admin_codes.main.type_discount') ?></option>
                        <option value="redeemable"><?= l('admin_codes.main.type_redeemable') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="code"><?= l('admin_codes.main.code') ?></label>
                    <input type="text" id="code" name="code" class="form-control form-control-lg" required="required" />
                </div>

                <div id="discount_container" class="form-group">
                    <label for="discount"><?= l('admin_codes.main.discount') ?></label>
                    <input id="discount" type="number" min="1" max="99" name="discount" class="form-control form-control-lg" value="1" />
                    <small class="form-text text-muted"><?= l('admin_codes.main.discount_help') ?></small>
                </div>

                <div id="days_container" class="form-group">
                    <label for="days"><?= l('admin_codes.main.days') ?></label>
                    <input id="days" type="number" min="1" max="999999" name="days" class="form-control form-control-lg" value="1" />
                    <small class="form-text text-muted"><?= l('admin_codes.main.days_help') ?></small>
                </div>

                <div class="form-group">
                    <label for="quantity"><?= l('admin_codes.main.quantity') ?></label>
                    <input type="number" min="1" id="quantity" name="quantity" class="form-control form-control-lg" value="1" />
                    <small class="form-text text-muted"><?= l('admin_codes.main.quantity_help') ?></small>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="is_bulk" name="is_bulk" type="checkbox" class="custom-control-input">
                    <label class="custom-control-label" for="is_bulk"><?= l('admin_codes.main.is_bulk') ?></label>
                </div>

                <div id="bulk_container">
                    <div class="form-group">
                        <label for="amount"><?= l('admin_codes.main.amount') ?></label>
                        <input id="amount" type="number" min="1" max="50000" name="amount" class="form-control form-control-lg" value="1" />
                    </div>

                    <div class="form-group">
                        <label for="prefix"><?= l('admin_codes.main.prefix') ?></label>
                        <input type="text" id="prefix" name="prefix" class="form-control form-control-lg" />
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.create') ?></button>
            </form>

        </div>
    </div>

<?php ob_start() ?>
    <script>
        /* Bulk */
        let bulk_checker = () => {
            let is_bulk = document.querySelector('#is_bulk').checked;

            if(is_bulk) {
                document.querySelector('#code').setAttribute('disabled', 'disabled');
                document.querySelector('#name').setAttribute('disabled', 'disabled');
                document.querySelector('#bulk_container').classList.remove('d-none');
            } else {
                document.querySelector('#code').removeAttribute('disabled');
                document.querySelector('#name').removeAttribute('disabled');
                document.querySelector('#bulk_container').classList.add('d-none');
            }
        }

        bulk_checker();

        document.querySelector('#is_bulk').addEventListener('change', bulk_checker);

        /* Type checker */
        let type_checker = () => {
            let type = document.querySelector('select[name="type"]').value;

            switch(type) {
                case 'discount':
                    document.querySelector('#discount_container').style.display = 'block';
                    document.querySelector('#days_container').style.display = 'none';
                    break;

                case 'redeemable':
                    document.querySelector('#discount_container').style.display = 'none';
                    document.querySelector('#days_container').style.display = 'block';
                    break;
            }
        };

        type_checker();

        document.querySelector('select[name="type"]').addEventListener('change', type_checker);
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
