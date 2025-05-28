<?= $this->extend('layouts/client_layaout'); ?>

<?= $this->section('title') ?>
Error al Aprobar el Pago
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="error-message">
<h1 class="text-danger">Error</h1>
            <h3>No se pudo aprobar el pago. Por favor, inténtelo de nuevo más tarde.</h3>
            <a href="<?= base_url('/') ?>" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Volver al
            Inicio</a>
</div>

<?= $this->endSection() ?>
