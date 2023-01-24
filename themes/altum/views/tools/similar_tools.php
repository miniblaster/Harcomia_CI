<?php defined('ALTUMCODE') || die() ?>

<?php if(isset($data->tools[$data->tool]['similar'])): ?>
    <h2 class="h4 mb-4"><?= l('tools.similar_tools') ?></h2>

    <div class="row" id="similar_tools">
        <?php foreach($data->tools[$data->tool]['similar'] as $key): ?>
            <?php if(settings()->tools->available_tools->{$key}): ?>
                <div class="col-12 mb-4 position-relative" data-tool-id="<?= $key ?>" data-tool-name="<?= l('tools.' . $key . '.name') ?>">
                    <div class="card d-flex flex-row h-100 overflow-hidden">
                        <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                            <a href="<?= url('tools/' . get_slug($key)) ?>" class="stretched-link">
                                <i class="<?= $data->tools[$key]['icon'] ?> fa-fw text-primary-600"></i>
                            </a>
                        </div>

                        <div class="card-body text-truncate">
                            <strong><?= l('tools.' . $key . '.name') ?></strong>
                            <p class="text-truncate small m-0"><?= l('tools.' . $key . '.description') ?></p>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
<?php endif ?>
