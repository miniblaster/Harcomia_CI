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
            <li class="active" aria-current="page"><?= l('team.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><?= sprintf(l('team.header'), $data->team->name) ?></h1>
        </div>

        <div class="col-12 col-xl-auto d-flex">
            <div>
                <?php if($this->user->plan_settings->team_members_limit != -1 && $data->total_team_members >= $this->user->plan_settings->team_members_limit): ?>
                    <button type="button" class="btn btn-outline-primary disabled" data-toggle="tooltip" title="<?= l('global.info_message.plan_feature_limit') ?>">
                        <i class="fa fa-fw fa-sm fa-plus"></i> <?= l('team_members.create') ?>
                    </button>
                <?php else: ?>
                    <a href="<?= url('team-member-create/' . $data->team->team_id) ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('team_members.create') ?></a>
                <?php endif ?>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                        <i class="fa fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('team/' . $data->team->team_id . '?' . $data->filters->get_get() . '&export=csv')  ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('team/' . $data->team->team_id . '?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.filters.header') ?>">
                        <i class="fa fa-fw fa-sm fa-filter"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                        <div class="dropdown-header d-flex justify-content-between">
                            <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                            <?php if(count($data->filters->get)): ?>
                                <a href="<?= url('team/' . $data->team->team_id) ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                            <?php endif ?>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form action="" method="get" role="form">
                            <div class="form-group px-4">
                                <label for="search" class="small"><?= l('global.filters.search') ?></label>
                                <input type="search" name="search" id="search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="search_by" class="small"><?= l('global.filters.search_by') ?></label>
                                <select name="search_by" id="search_by" class="form-control form-control-sm">
                                    <option value="user_email" <?= $data->filters->search_by == 'user_email' ? 'selected="selected"' : null ?>><?= l('team_members.input.user_email') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="order_by" class="form-control form-control-sm">
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="user_email" <?= $data->filters->order_by == 'user_email' ? 'selected="selected"' : null ?>><?= l('teams.input.user_email') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="order_type" class="small"><?= l('global.filters.order_type') ?></label>
                                <select name="order_type" id="order_type" class="form-control form-control-sm">
                                    <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
                                    <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="results_per_page" class="small"><?= l('global.filters.results_per_page') ?></label>
                                <select name="results_per_page" id="results_per_page" class="form-control form-control-sm">
                                    <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                        <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4 mt-4">
                                <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= l('global.submit') ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="ml-3">
                <?= include_view(THEME_PATH . 'views/team/team_dropdown_button.php', ['id' => $data->team->team_id, 'resource_name' => $data->team->name]) ?>
            </div>
        </div>
    </div>

    <?php if(count($data->team_members)): ?>
        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= l('team_members.table.member') ?></th>
                    <th><?= l('team_members.input.access') ?></th>
                    <th><?= l('team_members.table.status') ?></th>
                    <th><?= l('team_members.table.datetime') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($data->team_members as $row): ?>
                    <tr>
                        <td class="text-nowrap">
                            <div class="d-flex">
                                <?php if($row->status): ?>
                                    <img src="<?= get_gravatar($row->email, 45) ?>" class="rounded-circle mr-3" alt="" />

                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold"><?= $row->name ?></span>
                                        <span class="text-muted"><?= $row->email ?></span>
                                    </div>
                                <?php else: ?>
                                    <img src="<?= get_gravatar($row->user_email, 45) ?>" class="rounded-circle mr-3" alt="" />

                                    <div class="d-flex flex-column align-self-center">
                                        <span class="text-muted"><?= $row->user_email ?></span>
                                    </div>
                                <?php endif ?>
                            </div>
                        </td>

                        <td class="text-nowrap">
                            <span class="badge <?= $row->access->read ? 'badge-success' : 'badge-secondary' ?>">
                                <?= l('team_members.input.access.read') ?>
                            </span>

                            <span class="badge <?= $row->access->create ? 'badge-success' : 'badge-secondary' ?>">
                                <?= l('team_members.input.access.create') ?>
                            </span>

                            <span class="badge <?= $row->access->update ? 'badge-success' : 'badge-secondary' ?>">
                                <?= l('team_members.input.access.update') ?>
                            </span>

                            <span class="badge <?= $row->access->delete ? 'badge-success' : 'badge-secondary' ?>">
                                <?= l('team_members.input.access.delete') ?>
                            </span>
                        </td>

                        <td class="text-nowrap">
                            <?php if($row->status): ?>
                                <span class="badge badge-success"><?= l('team_members.table.status_accepted') ?></span>
                            <?php else: ?>
                                <span class="badge badge-warning"><?= l('team_members.table.status_invited') ?></span>
                            <?php endif ?>
                        </td>

                        <td class="text-nowrap"><span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>"><?= \Altum\Date::get_timeago($row->datetime) ?></span></td>

                        <td>
                            <div class="d-flex justify-content-end">
                                <?= include_view(THEME_PATH . 'views/team/team_member_dropdown_button.php', ['id' => $row->team_member_id, 'resource_name' => $row->user_email]) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3"><?= $data->pagination ?></div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('team_members.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('team_members.no_data') ?></h2>
                    <p class="text-muted"><?= l('team_members.no_data_help') ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'team',
    'resource_id' => 'team_id',
    'has_dynamic_resource_name' => true,
    'path' => 'teams/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'team_member',
    'resource_id' => 'team_member_id',
    'has_dynamic_resource_name' => true,
    'path' => 'teams-members/delete'
]), 'modals'); ?>
