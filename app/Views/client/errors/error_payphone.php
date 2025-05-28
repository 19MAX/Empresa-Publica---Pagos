<?= $this->extend('layouts/client_layaout'); ?>

<?= $this->section('title') ?>
Error de pago
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="error-message">
    <h1 class="text-danger">Error</h1>
    <h3>No se encontró ningún pago aprobado a través de Payphone.</h3>
    <a href="<?= base_url('/') ?>" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Volver al
        Inicio</a>
</div>

<?= $this->endSection() ?>