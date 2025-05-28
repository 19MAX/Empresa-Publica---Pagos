<?= $this->extend('layouts/client_layaout'); ?>

<?= $this->section('title') ?>
Pago No Encontrado
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="error-message">
    <h1 class="text-danger">Error</h1>
    <h3>No se encontró un pago asociado a los datos proporcionados.</h3>
    <a href="<?= base_url('/') ?>" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Volver al
        Inicio</a>
</div>

<?= $this->endSection() ?>