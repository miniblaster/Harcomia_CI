<?php defined('ALTUMCODE') || die() ?>

<div class="card">
    <div class="card-body">
        <?= l('tools.' . \Altum\Router::$method . '.extra_content') ?>
    </div>
</div>

<div class="mt-5">
    <h2 class="h4 mb-4"><?= l('tools.share') ?></h2>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <a href="mailto:?body=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                    <i class="fa fa-fw fa-envelope"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                    <i class="fab fa-fw fa-facebook"></i>
                </a>
                <a href="https://twitter.com/share?url=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                    <i class="fab fa-fw fa-twitter"></i>
                </a>
                <a href="https://pinterest.com/pin/create/link/?url=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                    <i class="fab fa-fw fa-pinterest"></i>
                </a>
                <a href="https://linkedin.com/shareArticle?url=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                    <i class="fab fa-fw fa-linkedin"></i>
                </a>
                <a href="https://www.reddit.com/submit?url=<?= url(\Altum\Router::$original_request) ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                    <i class="fab fa-fw fa-reddit"></i>
                </a>
                <a href="https://wa.me/?text=<?= url(\Altum\Router::$original_request) ?>" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                    <i class="fab fa-fw fa-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>
</div>
