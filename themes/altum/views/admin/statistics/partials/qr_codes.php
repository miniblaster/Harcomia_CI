<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-qrcode fa-xs text-muted"></i> <?= l('admin_statistics.qr_codes.header') ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['qr_codes']) ?></span> <?= l('admin_statistics.qr_codes.chart') ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="qr_codes"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    let color = css.getPropertyValue('--primary');
    let color_gradient = null;

    /* Prepare chart */
    let qr_codes_chart = document.getElementById('qr_codes').getContext('2d');
    color_gradient = qr_codes_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    /* Display chart */
    new Chart(qr_codes_chart, {
        type: 'line',
        data: {
            labels: <?= $data->qr_codes_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode(l('admin_statistics.qr_codes.chart')) ?>,
                data: <?= $data->qr_codes_chart['qr_codes'] ?? '[]' ?>,
                backgroundColor: color_gradient,
                borderColor: color,
                fill: true
            }]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
