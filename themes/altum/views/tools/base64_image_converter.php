<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('tools') ?>"><?= l('tools.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('tools.base64_image_converter.name') ?></li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><?= l('tools.base64_image_converter.name') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('tools.base64_image_converter.description') ?>">
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
                    <label for="type"><i class="fa fa-fw fa-sm fa-fingerprint text-muted mr-1"></i> <?= l('tools.base64_image_converter.type') ?></label>
                    <select id="type" name="type" class="form-control" required="required">
                        <option value="image_to_base64" <?= $data->values['type'] == 'image_to_base64' ? 'selected="selected"' : null ?>><?= l('tools.base64_image_converter.image_to_base64') ?></option>
                        <option value="base64_to_image" <?= $data->values['type'] == 'base64_to_image' ? 'selected="selected"' : null ?>><?= l('tools.base64_image_converter.base64_to_image') ?></option>
                    </select>
                </div>

                <div class="form-group" data-type="base64_to_image">
                    <label for="text"><i class="fa fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('tools.text') ?></label>
                    <textarea id="text" name="text" class="form-control <?= \Altum\Alerts::has_field_errors('text') ? 'is-invalid' : null ?>" required="required"><?= $data->values['text'] ?></textarea>
                    <?= \Altum\Alerts::output_field_error('text') ?>
                </div>

                <div class="form-group" data-type="image_to_base64">
                    <label for="image"><i class="fa fa-fw fa-sm fa-image text-muted mr-1"></i> <?= l('tools.image') ?></label>
                    <input id="image" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file altum-file-input" />
                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), '.gif, .png, .jpg, .jpeg, .svg') . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.submit') ?></button>
            </form>

        </div>
    </div>

    <?php if(isset($data->result)): ?>
        <div class="mt-4">

            <div class="card">
                <div class="card-body">

                    <?php if($data->values['type'] == 'image_to_base64'): ?>
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="result"><?= l('tools.text') ?></label>
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
                            <textarea id="result" class="form-control"><?= $data->result['base64'] ?></textarea>
                        </div>
                    <?php else: ?>
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="result"><?= l('tools.image') ?></label>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-fw fa-sm fa-download"></i> <?= l('global.download') ?>
                                    </button>

                                    <div class="dropdown-menu">
                                        <a href="data:image/png;base64,<?= $data->result['base64'] ?>" class="dropdown-item" download="<?= l('tools.image') . '.png' ?>">PNG</a>
                                        <a href="data:image/jpg;base64,<?= $data->result['base64'] ?>" class="dropdown-item" download="<?= l('tools.image') . '.jpg' ?>">JPG</a>
                                        <a href="data:image/webp;base64,<?= $data->result['base64'] ?>" class="dropdown-item" download="<?= l('tools.image') . '.webp' ?>">WEBP</a>
                                        <a href="data:image/gif;base64,<?= $data->result['base64'] ?>" class="dropdown-item" download="<?= l('tools.image') . '.gif' ?>">GIF</a>
                                    </div>
                                </div>
                            </div>

                            <img src="data:image/png;base64,<?= $data->result['base64'] ?>" class="img-fluid" />
                        </div>
                    <?php endif ?>
                </div>
            </div>

        </div>
    <?php endif ?>

    <div class="mt-5">
        <?= $this->views['extra_content'] ?>
    </div>

    <div class="mt-5">
        <?= $this->views['similar_tools'] ?>
    </div>
</div>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php ob_start() ?>
<script>
    'use strict';

    /* Type handler */
    let type_handler = () => {
        let type = document.querySelector('select[name="type"]').value;

        document.querySelectorAll(`[data-type]:not([data-type="${type}"])`).forEach(element => {
            element.classList.add('d-none');
            let input = element.querySelector('input,select,textarea');

            if(input) {
                input.setAttribute('disabled', 'disabled');
                if(input.getAttribute('required')) {
                    input.setAttribute('data-is-required', 'true');
                }
                input.removeAttribute('required');
            }
        });

        document.querySelectorAll(`[data-type="${type}"]`).forEach(element => {
            element.classList.remove('d-none');
            let input = element.querySelector('input,select,textarea');

            if(input) {
                input.removeAttribute('disabled');
                if(input.getAttribute('data-is-required')) {
                    input.setAttribute('required', 'required')
                }
            }
        });
    }

    type_handler();

    document.querySelector('select[name="type"]') && document.querySelector('select[name="type"]').addEventListener('change', type_handler);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>


<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>
