<?php


if(
    !empty(settings()->ads->footer)
    && (
        !\Altum\Authentication::check() ||
        (\Altum\Authentication::check() && !$this->user->plan_settings->no_ads)
    )
    && \Altum\Router::$controller_settings['ads']
): ?>
    <div class="container my-3"><?= settings()->ads->footer ?></div>
<?php endif ?>
