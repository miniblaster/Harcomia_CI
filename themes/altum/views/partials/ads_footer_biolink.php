<?php


if(
    !empty(settings()->ads->footer_biolink)
    && !$data->user->plan_settings->no_ads
): ?>
    <div class="container my-3"><?= settings()->ads->footer_biolink ?></div>
<?php endif ?>
