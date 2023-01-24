<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-xl d-flex align-items-center mb-3 mb-xl-0">
            <h1 class="h4 m-0"><?= l('tools.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('tools.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>
    </div>

    <form action="" method="get" role="form">
        <div class="form-group">
            <input type="search" name="search" class="form-control form-control-lg" value="" placeholder="<?= l('global.filters.search') ?>" aria-label="<?= l('global.filters.search') ?>" />
        </div>
    </form>

    <div class="row">
        <?php foreach($data->tools as $key => $value): ?>
            <?php if(settings()->tools->available_tools->{$key}): ?>
                <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative" data-tool-id="<?= $key ?>" data-tool-name="<?= l('tools.' . $key . '.name') ?>">
                    <div class="card d-flex flex-row h-100 overflow-hidden">
                        <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                            <a href="<?= url('tools/' . get_slug($key)) ?>" class="stretched-link">
                                <i class="<?= $value['icon'] ?> fa-fw text-primary-600"></i>
                            </a>
                        </div>

                        <div class="card-body">
                            <?= l('tools.' . $key . '.name') ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</div>


<?php ob_start() ?>
<script>
    'use strict';

    let tools = [];
    document.querySelectorAll('[data-tool-id]').forEach(element => tools.push({
        id: element.getAttribute('data-tool-id'),
        name: element.getAttribute('data-tool-name').toLowerCase(),
    }));

    document.querySelector('input[name="search"]').addEventListener('keyup', event => {
        let string = event.currentTarget.value.toLowerCase();

        for(let tool of tools) {
            if(tool.name.includes(string)) {
                document.querySelector(`[data-tool-id="${tool.id}"]`).classList.remove('d-none');
            } else {
                document.querySelector(`[data-tool-id="${tool.id}"]`).classList.add('d-none');
            }
        }
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
