<?php $has_announcements = false ?>
<?php foreach(['guests', 'users'] as $type): ?>
    <?php if(
        settings()->announcements->{$type . '_content'}
        && (!isset($_COOKIE['announcement_' . $type . '_id']) || (isset($_COOKIE['announcement_' . $type . '_id']) && $_COOKIE['announcement_' . $type . '_id'] != settings()->announcements->{$type . '_id'}))
        && (
            ($type == 'guests' && !\Altum\Authentication::check())
            || ($type == 'users' && \Altum\Authentication::check())
        )
    ): ?>
        <?php $has_announcements = true; ?>
        <div data-announcement="<?= $type ?>" class="w-100 py-3" style="background-color: <?= settings()->announcements->{$type . '_background_color'} ?>;">
            <div class="container d-flex justify-content-center position-relative">
                <div class="row w-100">
                    <div class="col">
                        <div class="text-center" style="color: <?= settings()->announcements->{$type . '_text_color'} ?>;"><?= settings()->announcements->{$type . '_content'} ?></div>
                    </div>
                    <div class="col-auto px-0">
                        <div>
                            <button data-announcement-close="<?= $type ?>" data-announcement-id="<?= settings()->announcements->{$type . '_id'} ?>" type="button" class="close" data-dismiss="alert">
                                <i class="fa fa-sm fa-times" style="color: <?= settings()->announcements->{$type . '_text_color'} ?>; opacity: .5;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
<?php endforeach ?>

<?php if($has_announcements): ?>
    <?php ob_start() ?>
    <script>
        document.querySelector('[data-announcement-close]').addEventListener('click', event => {
            let type = event.currentTarget.getAttribute('data-announcement-close');
            let id = event.currentTarget.getAttribute('data-announcement-id');
            document.querySelector(`[data-announcement="${type}"]`).style.display = 'none';
            set_cookie(`announcement_${type}_id`, id, 15, <?= json_encode(COOKIE_PATH) ?>);
        })
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
<?php endif ?>
