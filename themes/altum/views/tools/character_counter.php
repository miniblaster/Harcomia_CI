<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('tools') ?>"><?= l('tools.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('tools.character_counter.name') ?></li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><?= l('tools.character_counter.name') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('tools.character_counter.description') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="text"><i class="fa fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('tools.text') ?></label>
                    <textarea id="text" name="text" class="form-control <?= \Altum\Alerts::has_field_errors('text') ? 'is-invalid' : null ?>" required="required"><?= $data->values['text'] ?></textarea>
                    <?= \Altum\Alerts::output_field_error('text') ?>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.submit') ?></button>
            </form>

        </div>
    </div>

    <?php if(isset($data->result)): ?>
        <div class="mt-4">
            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.character_counter.result.characters') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= nr($data->result['characters']) ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.character_counter.result.words') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= nr($data->result['words']) ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.character_counter.result.lines') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= nr($data->result['lines']) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
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

