<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('tools') ?>"><?= l('tools.breadcrumb') ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= l('tools.url_parser.name') ?></li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><?= l('tools.url_parser.name') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('tools.url_parser.description') ?>">
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
                    <label for="url"><i class="fa fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('tools.url') ?></label>
                    <input type="url" id="url" name="url" class="form-control <?= \Altum\Alerts::has_field_errors('url') ? 'is-invalid' : null ?>" value="<?= $data->values['url'] ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('url') ?>
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
                    <?php if(isset($data->result['scheme'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.url_parser.result.scheme') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result['scheme'] ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result['host'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.url_parser.result.host') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result['host'] ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result['path'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.url_parser.result.path') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result['path'] ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result['query'])): ?>
                        <tr>
                            <td class="font-weight-bold">
                                <?= l('tools.url_parser.result.query') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= $data->result['query'] ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php if(isset($data->result['query_array'])): ?>
                        <tr>
                            <td class="text-nowrap" colspan="2">
                                <pre><?= json_encode($data->result['query_array'], JSON_PRETTY_PRINT) ?></pre>
                            </td>
                        </tr>
                    <?php endif ?>
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

