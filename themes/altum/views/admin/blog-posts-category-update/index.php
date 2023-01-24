<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/blog-posts-categories') ?>"><?= l('admin_blog_posts_categories.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_blog_posts_category_update.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-truncate"><i class="fa fa-fw fa-xs fa-book text-primary-900 mr-2"></i> <?= l('admin_blog_posts_categories.header') ?></h1>

    <?= include_view(THEME_PATH . 'views/admin/blog-posts-categories/admin_blog_posts_category_dropdown_button.php', ['id' => $data->blog_posts_category->blog_posts_category_id, 'resource_name' => $data->blog_posts_category->title]) ?>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">
        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

            <div class="form-group">
                <label for="url"><?= l('admin_blog.main.url') ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?= SITE_URL . 'blog/category/' ?></span>
                    </div>

                    <input id="url" type="text" name="url" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('url') ? 'is-invalid' : null ?>" placeholder="<?= l('admin_blog.main.url_placeholder') ?>" value="<?= $data->blog_posts_category->url ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('url') ?>
                </div>
            </div>

            <div class="form-group">
                <label for="title"><?= l('admin_blog.main.title') ?></label>
                <input id="title" type="text" name="title" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('title') ? 'is-invalid' : null ?>" value="<?= $data->blog_posts_category->title ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('title') ?>
            </div>

            <div class="form-group">
                <label for="language"><?= l('admin_blog.main.language') ?></label>
                <select id="language" name="language" class="form-control form-control-lg">
                    <option value="" <?= !$data->blog_posts_category->language ? 'selected="selected"' : null ?>><?= l('admin_blog.main.language_all') ?></option>
                    <?php foreach(\Altum\Language::$languages as $language): ?>
                        <option value="<?= $language['name'] ?>" <?= $data->blog_posts_category->language == $language['name'] ? 'selected="selected"' : null ?>><?= $language['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="order"><?= l('admin_blog.main.order') ?></label>
                <input id="order" type="number" name="order" class="form-control form-control-lg" value="<?= $data->blog_posts_category->order ?>" />
                <small class="form-text text-muted"><?= l('admin_blog.main.order_help') ?></small>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
        </form>
    </div>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'blog_posts_category',
    'resource_id' => 'blog_posts_category_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/blog-posts-categories/delete/'
]), 'modals'); ?>
