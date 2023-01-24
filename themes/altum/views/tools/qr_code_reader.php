<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('tools') ?>"><?= l('tools.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('tools.qr_code_reader.name') ?></li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><?= l('tools.qr_code_reader.name') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('tools.qr_code_reader.description') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form" enctype="multipart/form-data">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="image"><i class="fa fa-fw fa-qrcode fa-sm text-muted mr-1"></i> <?= l('tools.image') ?></label>
                    <input type="file" id="image" name="image" accept=".png, .jpg, .jpeg, .svg, .webp" class="form-control-file altum-file-input <?= \Altum\Alerts::has_field_errors('image') ? 'is-invalid' : null ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('image') ?>
                </div>
            </form>

        </div>
    </div>

    <div class="mt-4">
        <div class="card">
            <div class="card-body">

                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="result"><?= l('tools.qr_code_reader.result') ?></label>
                        <div>
                            <button
                                    type="button"
                                    class="btn btn-link text-muted"
                                    data-toggle="tooltip"
                                    title="<?= l('global.clipboard_copy') ?>"
                                    aria-label="<?= l('global.clipboard_copy') ?>"
                                    data-copy="<?= l('global.clipboard_copy') ?>"
                                    data-copied="<?= l('global.clipboard_copied') ?>"
                                    data-clipboard-target="#result"
                                    data-clipboard-text
                            >
                                <i class="fa fa-fw fa-sm fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <textarea id="result" class="form-control"></textarea>
                </div>

            </div>
        </div>
    </div>

    <div class="mt-5">
        <?= $this->views['extra_content'] ?>
    </div>

    <div class="mt-5">
        <?= $this->views['similar_tools'] ?>
    </div>
</div>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php ob_start() ?>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/html5-qrcode.min.js' ?>"></script>

    <script>
        'use strict';
        const html5QrCode = new Html5Qrcode('image');

        const image = document.getElementById('image');
        image.addEventListener('change', event => {
            const file = image.files[0];

            if(!file) {
                return;
            }

            html5QrCode.scanFile(file, true)
                .then(decodedText => {
                    document.querySelector('#result').value = decodedText;
                })
                .catch(err => {
                    document.querySelector('#result').value = <?= json_encode(l('tools.qr_code_reader.result.no_data')) ?>;
                });
        });
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>
