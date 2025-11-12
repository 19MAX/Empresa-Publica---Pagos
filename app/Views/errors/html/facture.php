<?= $this->extend('layouts/client_layaout'); ?>

<?= $this->section('title') ?>
Error de pago
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<main class="flex-grow-1" style="background-color: #f5f7fa;">

    <div class="mb-4">
        <section class="container flex-grow-1 d-flex">

            <div class="mx-auto mt-5 pt-5 text-center">
                <h1 class="" style="color: #0C244B;">No encontrado</h1>
                <div>
                    <h3><i class="fa fa-warning text-danger"></i> El comprobante de recaudación no existe.</h3>
                    <p>Parece que no puedo encontrar el comprobante de recaudación que estabas buscando.
                        Mientras tanto, puedes <br>
                        <a href="<?= base_url("/") ?>" class="btn btn-danger text-light mt-3">
                            <i class="fa-solid fa-arrow-left me-2"></i> Volver al inicio
                        </a>
                    </p>

                </div>
            </div>
        </section>
    </div>

</main>

<?= $this->endSection() ?>