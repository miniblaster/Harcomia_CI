<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/biolinks-themes') ?>"><?= l('admin_biolinks_themes.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_biolink_theme_create.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-palette text-primary-900 mr-2"></i> <?= l('admin_biolink_theme_create.header') ?></h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

            <div class="form-group">
                <label for="name"><?= l('admin_biolinks_themes.table.name') ?></label>
                <input type="text" id="name" name="name" class="form-control form-control-lg" required="required" />
            </div>

            <div class="form-group">
                <label for="image"><?= l('admin_biolinks_themes.table.image') ?></label>
                <input id="image" type="file" name="image" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('biolinks_themes') ?>" class="form-control-file altum-file-input" />
                <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('biolinks_themes')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
            </div>

            <h2 class="h4"><?= l('admin_biolinks_themes.biolink') ?></h2>

            <div class="form-group">
                <label for="biolink_background_type"><i class="fa fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('link.settings.background_type') ?></label>
                <select id="biolink_background_type" name="biolink_background_type" class="form-control form-control-lg">
                    <?php foreach($data->biolink_backgrounds as $key => $value): ?>
                        <option value="<?= $key ?>" <?= $data->values['biolink_background_type'] == $key ? 'selected="selected"' : null?>><?= l('link.settings.background_type_' . $key) ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div id="biolink_background_type_preset" class="row">
                <?php foreach($data->biolink_backgrounds['preset'] as $key => $value): ?>
                    <label for="biolink_background_type_preset_<?= $key ?>" class="m-0 col-3 mb-3">
                        <input type="radio" name="biolink_background" value="<?= $key ?>" id="biolink_background_type_preset_<?= $key ?>" class="d-none" <?= $data->values['biolink_background_type'] == 'preset' && $data->values['biolink_background'] == $key ? 'checked="checked"' : null ?>/>
                        <div class="link-background-type-preset" style="<?= $value ?>"></div>
                    </label>
                <?php endforeach ?>
            </div>

            <div id="biolink_background_type_preset_abstract" class="row">
                <?php foreach($data->biolink_backgrounds['preset_abstract'] as $key => $value): ?>
                    <label for="biolink_background_type_preset_abstract_<?= $key ?>" class="m-0 col-3 mb-3">
                        <input type="radio" name="biolink_background" value="<?= $key ?>" id="biolink_background_type_preset_abstract_<?= $key ?>" class="d-none" <?= $data->values['biolink_background_type'] == 'preset_abstract' && $data->values['biolink_background'] == $key ? 'checked="checked"' : null ?>/>
                        <div class="link-background-type-preset" style="<?= $value ?>"></div>
                    </label>
                <?php endforeach ?>
            </div>

            <div id="biolink_background_type_gradient">
                <div class="form-group">
                    <label for="biolink_background_type_gradient_color_one"><?= l('link.settings.background_type_gradient_color_one') ?></label>
                    <input type="color" id="biolink_background_type_gradient_color_one" name="biolink_background_color_one" class="form-control form-control-lg" value="<?= $data->values['biolink_background_color_one'] ?? '#000000' ?>" />
                </div>

                <div class="form-group">
                    <label for="biolink_background_type_gradient_color_two"><?= l('link.settings.background_type_gradient_color_two') ?></label>
                    <input type="color" id="biolink_background_type_gradient_color_two" name="biolink_background_color_two" class="form-control form-control-lg" value="<?= $data->values['biolink_background_color_two'] ?? '#000000' ?>" />
                </div>
            </div>

            <div id="biolink_background_type_color">
                <div class="form-group">
                    <label for="biolink_background_type_color"><?= l('link.settings.background_type_color') ?></label>
                    <input type="color" id="biolink_background_type_color" name="biolink_background" class="form-control form-control-lg" value="<?= is_string($data->values['biolink_background']) ? $data->values['biolink_background'] : '#000000' ?>" />
                </div>
            </div>

            <div id="biolink_background_type_image">
                <div class="form-group">
                    <label for="biolink_background_type_image"><?= l('link.settings.background_type_image') ?></label>
                    <input id="biolink_background_type_image" type="file" name="biolink_background_image" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('biolink_background') ?>" class="form-control-file altum-file-input" />
                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('biolink_background')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->seo_image_size_limit) ?></small>
                </div>
            </div>

            <div class="form-group">
                <label for="biolink_font"><i class="fa fa-fw fa-pen-nib fa-sm text-muted mr-1"></i> <?= l('link.settings.font') ?></label>
                <select id="biolink_font" name="biolink_font" class="form-control form-control-lg">
                    <?php foreach($data->biolink_fonts as $key => $value): ?>
                        <option value="<?= $key ?>" <?= $data->values['biolink_font'] == $key ? 'selected="selected"' : null?>><?= $value['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="biolink_font_size"><i class="fa fa-fw fa-font fa-sm text-muted mr-1"></i> <?= l('link.settings.font_size') ?></label>
                <div class="input-group">
                    <input id="biolink_font_size" type="number" min="14" max="22" name="biolink_font_size" class="form-control form-control-lg" value="<?= $data->values['biolink_font_size'] ?>" />
                    <div class="input-group-append">
                        <span class="input-group-text">px</span>
                    </div>
                </div>
            </div>

            <h2 class="h4"><?= l('admin_biolinks_themes.biolink_block') ?></h2>

            <div class="form-group">
                <label for="biolink_block_text_color"><i class="fa fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.text_color') ?></label>
                <input id="biolink_block_text_color" type="color" name="biolink_block_text_color" class="form-control form-control-lg" value="<?= $data->values['biolink_block_text_color'] ?>" required="required" />
            </div>

            <div class="form-group">
                <label for="biolink_block_background_color"><i class="fa fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.background_color') ?></label>
                <input id="biolink_block_background_color" type="color" name="biolink_block_background_color" class="form-control form-control-lg" value="<?= $data->values['biolink_block_background_color'] ?>" required="required" />
            </div>

            <div class="form-group">
                <label for="biolink_block_border_width"><i class="fa fa-fw fa-border-style fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_width') ?></label>
                <input id="biolink_block_border_width" type="number" min="0" max="5" class="form-control form-control-lg" name="biolink_block_border_width" value="<?= $data->values['biolink_block_border_width'] ?>" required="required" />
            </div>

            <div class="form-group">
                <label for="biolink_block_border_color"><i class="fa fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_color') ?></label>
                <input id="biolink_block_border_color" type="color" name="biolink_block_border_color" class="form-control form-control-lg" value="<?= $data->values['biolink_block_border_color'] ?>" required="required" />
            </div>

            <div class="form-group">
                <label for="biolink_block_border_radius"><i class="fa fa-fw fa-border-none fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_radius') ?></label>
                <select id="biolink_block_border_radius" name="biolink_block_border_radius" class="form-control form-control-lg">
                    <option value="straight" <?= $data->values['biolink_block_border_radius'] == 'straight' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_straight') ?></option>
                    <option value="round" <?= $data->values['biolink_block_border_radius'] == 'round' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_round') ?></option>
                    <option value="rounded" <?= $data->values['biolink_block_border_radius'] == 'rounded' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_rounded') ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="biolink_block_border_style"><i class="fa fa-fw fa-border-all fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_style') ?></label>
                <select id="biolink_block_border_style" name="biolink_block_border_style" class="form-control form-control-lg">
                    <option value="solid" <?= $data->values['biolink_block_border_style'] == 'solid' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_solid') ?></option>
                    <option value="dashed" <?= $data->values['biolink_block_border_style'] == 'dashed' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_dashed') ?></option>
                    <option value="double" <?= $data->values['biolink_block_border_style'] == 'double' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_double') ?></option>
                    <option value="outset" <?= $data->values['biolink_block_border_style'] == 'outset' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_outset') ?></option>
                    <option value="inset" <?= $data->values['biolink_block_border_style'] == 'inset' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_inset') ?></option>
                </select>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.create') ?></button>
        </form>

    </div>
</div>

<?php ob_start() ?>
<style>
    .link-background-type-preset {
        width: 100%;
        height: 4rem;
        border-radius: .25rem;
        opacity: .75;
        transition: .3s opacity;
    }

    .link-background-type-preset:hover {
        cursor: pointer;
    }

    input[type="radio"]:checked ~ .link-background-type-preset {
        opacity: 1;
    }
</style>

<script>
    /* Background Type Handler */
    let biolink_background_type_handler = () => {
        let type = document.querySelector('#biolink_background_type').value;

        /* Show only the active background type */
        $(`div[id="biolink_background_type_${type}"]`).show();
        $(`div[id="biolink_background_type_${type}"]`).find('[name^="biolink_background"]').removeAttr('disabled');

        /* Disable the other possible types so they dont get submitted */
        let biolink_background_type_containers = $(`div[id^="biolink_background_type_"]:not(div[id$="_${type}"])`);

        biolink_background_type_containers.hide();
        biolink_background_type_containers.find('[name^="biolink_background"]').attr('disabled', 'disabled');
    };

    biolink_background_type_handler();
    document.querySelector('#biolink_background_type').addEventListener('change', biolink_background_type_handler);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
