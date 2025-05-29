<?= $this->extend('layouts/client_layaout'); ?>

<?= $this->section('title') ?>
Detalles de la transacción
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url("assets/css/completado.css") ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="transaction-details my-5">
        <div class="container">
            <h4>Detalles de la Transacción</h4>
            <div><img src="<?= base_url('assets/images/iconoPayphone.webp') ?>" alt=""></div>
        </div>

        <hr>
        <div class="detail-item">
            <span>ID de Autorización:</span>
            <span class="value"><?= $authorization_code ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Email:</span>
            <span class="value"><?= $email ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Estado de la Transacción:</span>
            <span class="value"><?= $transaction_status ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Monto:</span>
            <span class="value">$<?= ($amount / 100) ?></span>
        </div>
        <div class="detail-item">
            <span>Tipo de Tarjeta:</span>
            <span class="value"><?= $card_type ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Últimos Dígitos:</span>
            <span class="value"><?= $last_digits ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Teléfono:</span>
            <span class="value"><?= $phone_number ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Fecha:</span>
            <span class="value"><?= $transaction_date ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>ID de la Transacción:</span>
            <span class="value"><?= $transaction_id ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Documento:</span>
            <span class="value"><?= $document ?? '' ?></span>
        </div>

        <div class="detail-item">
            <span>Nombre del Comercio:</span>
            <span class="value"><?= $store_name ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Región:</span>
            <span class="value"><?= $region_iso ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Tipo de Transacción:</span>
            <span class="value"><?= $transaction_type ?? '' ?></span>
        </div>
        <div class="detail-item">
            <span>Mensaje:</span>
            <span class="value"><?= $message ?? 'N/A' ?></span>
        </div>
        <div class="detail-item">
            <span>Referencia:</span>
            <span class="value"><?= $reference ?? '' ?></span>
        </div>
        <hr>
        <div class="text-center">
            <?php
            if (isset($numAutorizacion["num_autorizacion"])):
                ?>
                <a target="_blank" href="<?= base_url('pdf/' . $numAutorizacion['num_autorizacion'] ?? 'error') ?>"
                    class="btn btn-outline-danger"><i class="fa-solid fa-file-pdf"></i> PDF</a>
                <?php
            endif ?>
        </div>
    </div>
<?= $this->endSection() ?>