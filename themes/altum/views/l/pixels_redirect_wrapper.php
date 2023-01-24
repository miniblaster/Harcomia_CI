<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html>
    <head></head>

    <body>
        <?= $this->views['pixels'] ?>

        <script>
            setTimeout(() => {
                window.location = <?= json_encode($data->location_url) ?>;
            }, 650);
        </script>
    </body>
</html>
