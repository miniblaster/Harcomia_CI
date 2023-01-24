<?php defined('ALTUMCODE') || die() ?>

<ul class="pricing-features">
    <?php if(settings()->links->biolinks_is_enabled): ?>
        <li>
            <div><?= sprintf(l('global.plan_settings.biolinks_limit'), ($data->plan_settings->biolinks_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->biolinks_limit))) ?></div>
        </li>

        <li>
            <div><?= sprintf(l('global.plan_settings.biolink_blocks_limit'), ($data->plan_settings->biolink_blocks_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->biolink_blocks_limit))) ?></div>
        </li>

        <?php $enabled_biolink_blocks = array_filter((array) $data->plan_settings->enabled_biolink_blocks) ?>
        <?php $enabled_biolink_blocks_count = count($enabled_biolink_blocks) ?>
        <?php
        $enabled_biolink_blocks_string = implode(', ', array_map(function($key) {
            return l('link.biolink.blocks.' . mb_strtolower($key));
        }, array_keys($enabled_biolink_blocks)));
        ?>
        <li>
            <div class="<?= $enabled_biolink_blocks_count ? null : 'text-muted' ?>">
            <span data-toggle="tooltip" title="<?= $enabled_biolink_blocks_string ?>">
                <?php if($enabled_biolink_blocks_count == count(require APP_PATH . 'includes/biolink_blocks.php')): ?>
                    <?= l('global.plan_settings.enabled_biolink_blocks_all') ?>
                <?php else: ?>
                    <?= sprintf(l('global.plan_settings.enabled_biolink_blocks_x'), '<strong>' . nr($enabled_biolink_blocks_count) . '</strong>') ?>
                <?php endif ?>
            </span>
            </div>
        </li>

        <?php if(\Altum\Plugin::is_active('payment-blocks')): ?>
            <li>
                <div><?= sprintf(l('global.plan_settings.payment_processors_limit'), ($data->plan_settings->payment_processors_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->payment_processors_limit))) ?></div>
            </li>
        <?php endif ?>
    <?php endif ?>

    <?php if(settings()->links->shortener_is_enabled): ?>
        <li>
            <div><?= sprintf(l('global.plan_settings.links_limit'), ($data->plan_settings->links_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->links_limit))) ?></div>
        </li>
    <?php endif ?>

    <?php if(settings()->links->files_is_enabled): ?>
        <li>
            <div><?= sprintf(l('global.plan_settings.files_limit'), ($data->plan_settings->files_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->files_limit))) ?></div>
        </li>
    <?php endif ?>

    <?php if(settings()->links->vcards_is_enabled): ?>
        <li>
            <div><?= sprintf(l('global.plan_settings.vcards_limit'), ($data->plan_settings->vcards_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->vcards_limit))) ?></div>
        </li>
    <?php endif ?>

    <?php if(settings()->links->events_is_enabled): ?>
        <li>
            <div><?= sprintf(l('global.plan_settings.events_limit'), ($data->plan_settings->events_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->events_limit))) ?></div>
        </li>
    <?php endif ?>

    <?php if(settings()->links->qr_codes_is_enabled): ?>
        <li>
            <div><?= sprintf(l('global.plan_settings.qr_codes_limit'), ($data->plan_settings->qr_codes_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->qr_codes_limit))) ?></div>
        </li>
    <?php endif ?>

    <li>
        <div><?= sprintf(l('global.plan_settings.projects_limit'), ($data->plan_settings->projects_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->projects_limit))) ?></div>
    </li>

    <li>
        <div><?= sprintf(l('global.plan_settings.pixels_limit'), ($data->plan_settings->pixels_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->pixels_limit))) ?></div>
    </li>

    <?php if(\Altum\Plugin::is_active('teams')): ?>
        <li>
            <div><?= sprintf(l('global.plan_settings.teams_limit'), ($data->plan_settings->teams_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->teams_limit))) ?></div>
        </li>

        <li>
            <div><?= sprintf(l('global.plan_settings.team_members_limit'), ($data->plan_settings->team_members_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->team_members_limit))) ?></div>
        </li>
    <?php endif ?>

    <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
        <li>
            <div><?= sprintf(l('global.plan_settings.affiliate_commission_percentage'), nr($data->plan_settings->affiliate_commission_percentage) . '%') ?></div>
        </li>
    <?php endif ?>

    <?php if(settings()->links->domains_is_enabled): ?>
        <li>
            <div><?= sprintf(l('global.plan_settings.domains_limit'), ($data->plan_settings->domains_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->domains_limit))) ?></div>
        </li>
    <?php endif ?>

    <li>
        <div><?= sprintf(l('global.plan_settings.track_links_retention'), ($data->plan_settings->track_links_retention == -1 ? l('global.unlimited') : nr($data->plan_settings->track_links_retention))) ?></div>
    </li>

    <?php foreach(require APP_PATH . 'includes/simple_user_plan_settings.php' as $plan_setting): ?>
        <li>
            <div class="<?= $data->plan_settings->{$plan_setting} ? null : 'text-muted' ?>">
                <span data-toggle="tooltip" title="<?= l('global.plan_settings.' . $plan_setting . '_help') ?>"><?= l('global.plan_settings.' . $plan_setting) ?></span>
            </div>

            <i class="fa fa-fw fa-sm <?= $data->plan_settings->{$plan_setting} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        </li>
    <?php endforeach ?>
</ul>
