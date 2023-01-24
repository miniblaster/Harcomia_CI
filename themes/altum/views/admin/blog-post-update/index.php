<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/blog-posts') ?>"><?= l('admin_pages.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_blog_post_update.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-truncate"><i class="fa fa-fw fa-xs fa-paste text-primary-900 mr-2"></i> <?= l('admin_blog_post_update.header') ?></h1>

    <?= include_view(THEME_PATH . 'views/admin/blog-posts/admin_blog_post_dropdown_button.php', ['id' => $data->blog_post->blog_post_id, 'resource_name' => $data->blog_post->title]) ?>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

            <div class="form-group">
                <label for="url"><?= l('admin_blog.main.url') ?></label>
                <div class="input-group">
                    <div id="url_prepend" class="input-group-prepend">
                        <span class="input-group-text"><?= SITE_URL . 'blog/' ?></span>
                    </div>

                    <input id="url" type="text" name="url" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('url') ? 'is-invalid' : null ?>" placeholder="<?= l('admin_blog.main.url_placeholder') ?>" value="<?= $data->blog_post->url ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('url') ?>
                </div>
            </div>

            <div class="form-group">
                <label for="title"><?= l('admin_blog.main.title') ?></label>
                <input id="title" type="text" name="title" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('title') ? 'is-invalid' : null ?>" value="<?= $data->blog_post->title ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('title') ?>
            </div>

            <div class="form-group">
                <label for="description"><?= l('admin_blog.main.description') ?></label>
                <input id="description" type="text" name="description" class="form-control form-control-lg" value="<?= $data->blog_post->description ?>" />
            </div>

            <div class="form-group">
                <label for="image"><?= l('admin_blog.main.image') ?></label>
                <?php if(!empty($data->blog_post->image)): ?>
                    <div class="m-1">
                        <img src="<?= UPLOADS_FULL_URL . 'blog/' . $data->blog_post->image ?>" class="img-fluid" style="max-height: 5rem;height: 5rem;" />
                    </div>
                    <div class="custom-control custom-checkbox my-2">
                        <input id="image_remove" name="image_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#image').classList.add('d-none') : document.querySelector('#image').classList.remove('d-none')">
                        <label class="custom-control-label" for="image_remove">
                            <span class="text-muted"><?= l('global.delete_file') ?></span>
                        </label>
                    </div>
                <?php endif ?>
                <input id="image" type="file" name="image" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('blog') ?>" class="form-control-file altum-file-input" />
                <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('blog')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
            </div>


            <div class="form-group">
                <label for="editor"><?= l('admin_blog.main.editor') ?></label>
                <select id="editor" name="editor" class="form-control form-control-lg">
                    <option value="wysiwyg" <?= $data->blog_post->editor == 'wysiwyg' ? 'selected="selected"' : null ?>><?= l('admin_blog.main.editor_wysiwyg') ?></option>
                    <option value="raw" <?= $data->blog_post->editor == 'raw' ? 'selected="selected"' : null ?>><?= l('admin_blog.main.editor_raw') ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="content"><?= l('admin_blog.main.content') ?></label>
                <div id="quill_container">
                    <div id="quill" style="height: 15rem;"></div>
                </div>
                <textarea name="content" id="content" class="form-control form-control-lg d-none" style="height: 15rem;"><?= $data->blog_post->content ?></textarea>
            </div>

            <div class="form-group">
                <label for="blog_posts_category_id"><?= l('admin_blog.main.blog_posts_category_id') ?></label>
                <select id="blog_posts_category_id" name="blog_posts_category_id" class="form-control form-control-lg">
                    <?php foreach($data->blog_posts_categories as $row): ?>
                        <option value="<?= $row->blog_posts_category_id ?>" <?= $data->blog_post->blog_posts_category_id == $row->blog_posts_category_id ? 'selected="selected"' : null ?>><?= $row->title ?></option>
                    <?php endforeach ?>
                    <option value="" <?= !$data->blog_post->blog_posts_category_id ? 'selected="selected"' : null ?>><?= l('admin_blog.main.blog_posts_category_id_null') ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="language"><?= l('admin_blog.main.language') ?></label>
                <select id="language" name="language" class="form-control form-control-lg">
                    <option value="" <?= !$data->blog_post->language ? 'selected="selected"' : null ?>><?= l('admin_blog.main.language_all') ?></option>
                    <?php foreach(\Altum\Language::$languages as $language): ?>
                        <option value="<?= $language['name'] ?>" <?= $data->blog_post->language == $language['name'] ? 'selected="selected"' : null ?>><?= $language['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
        </form>
    </div>
</div>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/quill.snow.css?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/quill.min.js' ?>"></script>

<script>
    'use strict';

    let quill = new Quill('#quill', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ "font": [] }, { "size": ["small", false, "large", "huge"] }],
                ["bold", "italic", "underline", "strike"],
                [{ "color": [] }, { "background": [] }],
                [{ "script": "sub" }, { "script": "super" }],
                [{ "header": 1 }, { "header": 2 }, "blockquote", "code-block"],
                [{ "list": "ordered" }, { "list": "bullet" }, { "indent": "-1" }, { "indent": "+1" }],
                [{ "direction": "rtl" }, { "align": [] }],
                ["link", "image", "video", "formula"],
                ["clean"]
            ]
        },
    });

    quill.root.innerHTML = document.querySelector('#content').value;

    /* Handle form submission with the editor */
    document.querySelector('form').addEventListener('submit', event => {
        let editor = document.querySelector('#editor').value;

        if(editor == 'wysiwyg') {
            document.querySelector('#content').value = quill.root.innerHTML;
        }
    });

    /* Editor change handlers */
    let current_editor = document.querySelector('#editor').value;

    let editor_handler = (event = null) => {
        if(event && !confirm(<?= json_encode(l('admin_blog.main.editor_confirm')) ?>)) {
            document.querySelector('#editor').value = current_editor;
            return;
        }

        let editor = document.querySelector('#editor').value;

        switch(editor) {
            case 'wysiwyg':
                document.querySelector('#quill_container').classList.remove('d-none');
                quill.enable(true);
                // quill.root.innerHTML = document.querySelector('#content').value;
                document.querySelector('#content').classList.add('d-none');
                break;

            case 'raw':
                // document.querySelector('#content').value = quill.root.innerHTML;
                document.querySelector('#quill_container').classList.add('d-none');
                quill.enable(false);
                document.querySelector('#content').classList.remove('d-none');
                break;
        }

        current_editor = document.querySelector('#editor').value;
    };

    document.querySelector('#editor').addEventListener('change', editor_handler);
    editor_handler();
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'blog_post',
    'resource_id' => 'blog_post_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/blog-posts/delete/'
]), 'modals'); ?>
