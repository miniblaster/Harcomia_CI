<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li>
                <a href="<?= url('teams-system') ?>"><?= l('teams_system.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li>
                <a href="<?= url('teams') ?>"><?= l('teams.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li>
                <a href="<?= url('team/' . $data->team->team_id) ?>"><?= l('team.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li class="active" aria-current="page"><?= l('team_member_create.breadcrumb') ?></li>
        </ol>
    </nav>

    <h1 class="h4 mb-4 text-truncate"><?= l('team_member_create.header') ?></h1>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="user_email"><i class="fa fa-fw fa-envelope fa-sm text-muted mr-1"></i> <?= l('team_members.input.user_email') ?></label>
                    <input type="email" id="user_email" name="user_email" class="form-control <?= \Altum\Alerts::has_field_errors('user_email') ? 'is-invalid' : null ?>" value="<?= $data->values['user_email'] ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('user_email') ?>
                </div>

                <div class="mb-3">
                    <div class="d-flex flex-column flex-xl-row justify-content-between">
                        <span class="mb-2 mb-xl-0"><i class="fa fa-fw fa-sm fa-check-double text-muted mr-1"></i> <?= l('team_members.input.access') ?></span>
                    </div>
                    <div><small class="form-text text-muted"><?= l('team_members.input.access_help') ?></small></div>

                    <div class="row">
                        <?php foreach(['read', 'create', 'update', 'delete'] as $access): ?>
                            <div class="col-12 col-lg-6">
                                <div class="custom-control custom-checkbox my-2">
                                    <input id="<?= 'access_' . $access ?>" name="access[]" value="<?= $access ?>" type="checkbox" class="custom-control-input" <?= in_array($access, $data->values['access']) ? 'checked="checked"' : null ?> <?= $access == 'read' ? 'disabled="disabled"' : null ?>>
                                    <label class="custom-control-label" for="<?= 'access_' . $access ?>">
                                        <span><?= l('team_members.input.access.' . $access) ?></span>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <div class="alert alert-info"><?= l('team_members.info_message.access') ?></div>

                <button type="submit" name="submit" class="btn btn-block btn-primary mt-3"><?= l('team_member_create.submit') ?></button>
            </form>

        </div>
    </div>
</div>
