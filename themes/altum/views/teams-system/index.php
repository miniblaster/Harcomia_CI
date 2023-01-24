<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="mb-4">
        <h1 class="h4 m-0"><?= l('teams_system.header') ?></h1>
    </div>

    <div class="row">
        <div class="col-12 mb-4 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-4 d-flex flex-column justify-content-center">
                    <a href="<?= url('teams') ?>" class="stretched-link">
                        <i class="fa fa-fw fa-user-cog text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body d-flex flex-column">
                    <span class="font-weight-bold"><?= l('teams.menu') ?></span>
                    <span class="small text-muted"><?= l('teams.subheader') ?></span>
                </div>
            </div>
        </div>

        <div class="col-12 mb-4 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-4 d-flex flex-column justify-content-center">
                    <a href="<?= url('teams-member') ?>" class="stretched-link">
                        <i class="fa fa-fw fa-user-tag text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body d-flex flex-column">
                    <span class="font-weight-bold"><?= l('teams_member.menu') ?></span>
                    <span class="small text-muted"><?= l('teams_member.subheader') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

