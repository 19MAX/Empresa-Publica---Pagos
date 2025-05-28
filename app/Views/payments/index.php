<?= $this->extend('layouts/payments_layout'); ?>

<?= $this->section('title') ?>
Panel
<?= $this->endSection() ?>


<?= $this->section('css') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1>Panel</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li><i class="fa fa-angle-right"></i> Panel</li>
        </ol>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-lg-6 col-xs-6">
                <a href="" class="info-box  btn-outline-success">
                    <span class="info-box-icon bg-dark"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-black h1">Mi Recaudación Total  por Cobros</span>
                        <span class="info-box-number">$<?= number_format($mis_ingresos_totales ?? 0, 2) ?></span>
                    </div>
                </a>
            </div>
            <div class="col-lg-6 col-xs-6">
                <a href="" class="info-box btn-outline-success">
                    <span class="info-box-icon bg-dark"><i class="fa fa-address-book-o" aria-hidden="true"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-black h1">Mi Recaudación del Día por Cobros</span>
                        <span class="info-box-number">$<?= number_format($mis_ingresos ?? 0, 2) ?></span>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="info-box">
                    <h5 class="text-black">Métodos de pago mas usados</h5>
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>



<?= $this->section('scripts') ?>
<!-- Morris JavaScript -->
<script src="<?= base_url("dist/plugins/raphael/raphael-min.js") ?>"></script>
<script src="<?= base_url("dist/plugins/morris/morris.js") ?>"></script>
<script src="<?= base_url("dist/plugins/functions/dashboard1.js") ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('paymentMethodChart').getContext('2d');
        var paymentMethodStats = <?= json_encode(isset($paymentMethodStats) ? $paymentMethodStats : '') ?>;

        var labels = paymentMethodStats.map(function (item) { return item.method_name; });
        var data = paymentMethodStats.map(function (item) { return item.count; });

        var chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                    ]
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Métodos de Pago Más Utilizados'
                }
            }
        });



    });
</script>
<?= $this->endSection() ?>