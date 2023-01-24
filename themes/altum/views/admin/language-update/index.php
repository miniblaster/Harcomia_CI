<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/languages') ?>"><?= l('admin_languages.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_language_update.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-truncate"><i class="fa fa-fw fa-xs fa-language text-primary-900 mr-2"></i> <?= l('admin_language_update.header') ?></h1>

    <?= include_view(THEME_PATH . 'views/admin/languages/admin_language_dropdown_button.php', ['id' => $data->language['name'], 'resource_name' => $data->language['name']]) ?>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="alert alert-info" role="alert">
    <?php
    $total_translated = 0;
    $total = 0;
    foreach(\Altum\Language::$languages[\Altum\Language::$main_name]['content'] as $key => $value) {
        if(!empty(\Altum\Language::$languages[$data->language['name']]['content'][$key])) $total_translated++;
        $total++;
    }
    ?>
    <?= sprintf(l('admin_languages.info_message.total'), $total_translated, $total) ?>
</div>

<div class="alert <?= $total > ini_get('max_input_vars') ? 'alert-danger' : 'alert-info' ?>" role="alert">
    <?= sprintf(l('admin_languages.info_message.max_input_vars'), ini_get('max_input_vars')) ?>
</div>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

            <div class="form-group">
                <label for="language_name"><?= l('admin_languages.main.language_name') ?></label>
                <input id="language_name" type="text" name="language_name" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('language_name') ? 'is-invalid' : null ?>" value="<?= $data->language['name'] ?>" <?= $data->language['name'] == \Altum\Language::$main_name ? 'readonly="readonly"' : null ?> required="required" />
                <?= \Altum\Alerts::output_field_error('language_name') ?>
                <small class="form-text text-muted"><?= l('admin_languages.main.language_name_help') ?></small>
            </div>

            <div class="form-group">
                <label for="language_code"><?= l('admin_languages.main.language_code') ?></label>
                <input id="language_code" type="text" name="language_code" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('language_code') ? 'is-invalid' : null ?>" value="<?= $data->language['code'] ?>" <?= $data->language['name'] == \Altum\Language::$main_name ? 'readonly="readonly"' : null ?> required="required" />
                <?= \Altum\Alerts::output_field_error('language_code') ?>
                <small class="form-text text-muted"><?= l('admin_languages.main.language_code_help') ?></small>
            </div>

            <div class="form-group">
                <label for="status"><?= l('admin_languages.main.status') ?></label>
                <select id="status" name="status" class="form-control form-control-lg">
                    <option value="active" <?= $data->language['status'] == 'active' ? 'selected="selected"' : null ?>><?= l('global.active') ?></option>
                    <option value="disabled" <?= $data->language['status'] == 'disabled' ? 'selected="selected"' : null ?>><?= l('global.disabled') ?></option>
                </select>
            </div>

            <div class="d-flex align-items-center my-5">
                <div class="flex-fill">
                    <hr class="border-gray-200">
                </div>

                <div class="ml-3">
                    <select id="display" name="display" class="form-control" aria-label="<?= l('admin_languages.main.display') ?>">
                        <option value="all"><?= l('admin_languages.main.display_all') ?></option>
                        <option value="translated"><?= l('admin_languages.main.display_translated') ?></option>
                        <option value="not_translated"><?= l('admin_languages.main.display_not_translated') ?></option>
                    </select>
                </div>
            </div>

            <div id="translations">
                <?php $index = 1; ?>
                <?php foreach(\Altum\Language::$languages[\Altum\Language::$main_name]['content'] as $key => $value): ?>
                    <?php $form_key = str_replace('.', '##', $key) ?>

                    <?php if($key == 'direction'): ?>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="<?= \Altum\Language::$main_name . '_' . $form_key ?>"><?= $key ?></label>
                                    <input id="<?= \Altum\Language::$main_name . '_' . $form_key ?>" value="<?= $value ?>" class="form-control form-control-lg" readonly="readonly" />
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="<?= $form_key ?>">&nbsp;</label>
                                    <select id="<?= $form_key ?>" name="<?= $form_key ?>" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors($form_key) ? 'is-invalid' : null ?> <?= !isset(\Altum\Language::get($data->language['name'])[$key]) || (isset(\Altum\Language::get($data->language['name'])[$key]) && empty(\Altum\Language::get($data->language['name'])[$key])) ? 'border-danger' : null ?>" <?= $index++ >= ini_get('max_input_vars') ? 'readonly="readonly"' : null ?>>
                                        <option value="ltr" <?= (\Altum\Language::get($data->language['name'])[$key] ?? null) == 'ltr' ? 'selected="selected"' : null ?>>ltr</option>
                                        <option value="rtl" <?= (\Altum\Language::get($data->language['name'])[$key] ?? null) == 'rtl' ? 'selected="selected"' : null ?>>rtl</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row" data-display-container>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="<?= \Altum\Language::$main_name . '_' . $form_key ?>"><?= $key ?></label>
                                    <textarea id="<?= \Altum\Language::$main_name . '_' . $form_key ?>" class="form-control form-control-lg" readonly="readonly"><?= $value ?></textarea>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="<?= $form_key ?>">&nbsp;</label>
                                    <textarea data-display-input id="<?= $form_key ?>" name="<?= $form_key ?>" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors($form_key) ? 'is-invalid' : null ?> <?= !isset(\Altum\Language::get($data->language['name'])[$key]) || (isset(\Altum\Language::get($data->language['name'])[$key]) && empty(\Altum\Language::get($data->language['name'])[$key])) ? 'border-danger' : null ?>" <?= $index++ >= ini_get('max_input_vars') ? 'readonly="readonly"' : null ?>><?= \Altum\Language::get($data->language['name'])[$key] ?? null ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                <?php endforeach ?>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
        </form>

    </div>
</div>

<?php ob_start() ?>
<script>
    let display_handler = () => {
        let display_element = document.querySelector('#display');
        let display = display_element.value;

        switch(display) {
            case 'all':

                document.querySelectorAll('#translations [data-display-container]').forEach(element => {
                    element.classList.remove('d-none');
                });

                break;

            case 'translated':

                document.querySelectorAll('#translations [data-display-input]').forEach(element => {
                    if(element.value.trim() != '') {
                        element.closest('[data-display-container]').classList.remove('d-none');
                    } else {
                        element.closest('[data-display-container]').classList.add('d-none');
                    }
                });

                break;

            case 'not_translated':

                document.querySelectorAll('#translations [data-display-input]').forEach(element => {
                    if(element.value.trim() != '') {
                        element.closest('[data-display-container]').classList.add('d-none');
                    } else {
                        element.closest('[data-display-container]').classList.remove('d-none');
                    }
                });

                break;
        }
    }

    document.querySelector('#display').addEventListener('change', display_handler);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'language',
    'resource_id' => 'language_name',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/languages/delete/'
]), 'modals'); ?>
