<?php if(\Altum\Authentication::check() && \Altum\Teams::is_delegated()): ?>
    <div class="w-100 py-2 bg-gray-100 border-bottom small text-muted">
        <div class="container d-flex justify-content-between">
            <div><?= sprintf(l('global.team_delegate_access_help'), '<strong>' . $this->user->name . '</strong>', '<strong>' . \Altum\Teams::$team->name . '</strong>') ?></div>
            <div><a href="<?= url('logout?team') ?>"><i class="fa fa-fw fa-times mr-1"></i> <?= l('global.team_delegate_access_logout') ?></a></div>
        </div>
    </div>
<?php endif ?>
