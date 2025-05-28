<?= $this->extend('layouts/client_layaout'); ?>

<?= $this->section('title') ?>
Pago no aprobado
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="error-message">
<h1 class="text-danger">Pago rechazado</h1>
            <h3>El pago no fue aprobado por la plataforma de PayPhone, inténtenlo más tarde.</h3>
            <a href="<?= base_url('/') ?>" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Volver al
            Inicio</a>
</div>

<?= $this->endSection() ?>
