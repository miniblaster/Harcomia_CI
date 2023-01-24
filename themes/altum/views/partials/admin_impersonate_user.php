<?php if(\Altum\Authentication::check() && isset($_SESSION['admin_user_id'])): ?>
    <div class="w-100 py-2 bg-gray-100 border-bottom small text-muted">
        <div class="container d-flex justify-content-between">
            <div><?= l('global.admin_impersonate_user_help') ?> <span class="font-weight-bold"><?= $this->user->name . ' (' . $this->user->email . ')' ?></span></div>
            <div><a href="<?= url('logout?admin_impersonate_user') ?>"><i class="fa fa-fw fa-times mr-1"></i> <?= l('global.admin_impersonate_user_logout') ?></a></div>
        </div>
    </div>
<?php endif ?>
