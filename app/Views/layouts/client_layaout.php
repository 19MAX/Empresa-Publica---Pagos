<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title'); ?></title>
    <link rel="icon" href="<?= base_url("assets/images/icono.jpeg"); ?>" type="image/jpeg">
    <link rel="stylesheet" href="<?= base_url("assets/css/styles.css") ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <!-- Preloader -->
    <link rel="stylesheet" href="<?= base_url("assets/css/preloader.css") ?>">

    <link rel="stylesheet" href="<?= base_url("assets/css/whatsapp.css") ?>">
    <?= $this->renderSection('css'); ?>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .error-message {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .error-message h3 {
            margin-bottom: 20px;
            color: #6c757d;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100" style="background-color:#d9d9d9;">

    <!-- Preloader HTML -->
    <div id="preloader" style="display: none;">
        <div class="spinner"></div>
        <p class="loading-text">Cargando<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
        </p>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0C244B;">
        <div class="container-fluid">
            <a class="navbar-brand ms-lg-4" href="<?= base_url('/') ?>">
                <h4>EVENTO PAGOS</h4>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button title="Información Bancaria" class="btn btn-outline-light me-2 mb-2 mb-lg-0"
                            data-bs-toggle="modal" data-bs-target="#modalCuentasBancarias" type="button"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Ver información bancaria">
                            <i class="fa-solid fa-circle-info"></i> INFORMACIÓN BANCARIA
                        </button>
                    </li>
                    <li class="nav-item">
                        <button title="Consultar Voucher" class="btn btn-outline-light mb-2 mb-lg-0"
                            data-bs-toggle="modal" data-bs-target="#modalVoucher" type="button" data-bs-toggle="tooltip"
                            data-bs-placement="bottom" title="Consultar detalles del voucher">
                            <i class="fa-solid fa-file-pdf"></i> CONSULTAR VOUCHER
                        </button>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <?= $this->renderSection('content'); ?>

    <!-- <a href="https://wa.me/+593993847289?text=Hola%2C%20necesito%20ayuda%20con%20el%20evento%20X" class="Btn text-decoration-none" target="_blank" title="¿Necesitas ayuda?">
        <div class="sign">
            <i class="fa-brands fa-whatsapp"></i>
        </div>
        <div class="text">Soporte</div>
    </a> -->

    <!-- Footer-->
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container-fluid text-center">
            <div class="small m-0 text-white p-1">Copyright 2025 &copy; PROSERVI UEB-EP|
                <a href="" class="text-decoration-none link-light">Softec Apps S.A.S</a>
            </div>
        </div>
    </footer>

    <!-- Modal de Cuentas Bancarias -->
    <div class="modal fade" id="modalCuentasBancarias" tabindex="-1" aria-labelledby="modalCuentasBancariasLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <!-- style="background: linear-gradient(to right, #BC157C, #FFD700); color: white;" -->
                    <h5 class="modal-title" id="modalCuentasBancariasLabel"><i class="fa-solid fa-circle-info"></i>
                        Información de Cuentas Bancarias</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <div class="col">
                            <h5 class="text-center mb-3">Cuenta Bancaria</h5>
                            <div class="table-responsive text-start">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                        <tr style="background:#BC157C;">
                                            <th class="text-white" scope="row">Banco</th>
                                            <td class="text-white">Banco de Guayaquil</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Tipo de Cuenta</th>
                                            <td>Corriente</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Nombre</th>
                                            <td>SOFTEC WEBSTORE S.A.S</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Cuenta Bancaria</th>
                                            <td>0029421609</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">RUC</th>
                                            <td>0291525784001</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- <div class="col">
                            <h5 class="text-center mb-3">Cuenta Bancaria 2</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Banco</th>
                                            <td>[Nombre del segundo banco]</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Tipo de Cuenta</th>
                                            <td>[Tipo de cuenta]</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Nombre</th>
                                            <td>[Nombre del titular]</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Cuenta Bancaria</th>
                                            <td>[Número de cuenta]</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">RUC</th>
                                            <td>[Número de RUC]</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Autorización-->
    <div class="modal fade" id="modalVoucher" tabindex="-1" aria-labelledby="modalVoucherLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalVoucherLabel"><i class="fa-solid fa-file-pdf"></i> Consultar
                        Voucher</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formVoucher" method="GET">
                        <div class="mb-3">
                            <label for="numero_auto" class="form-label">Número de autorización</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fa-solid fa-hashtag"></i></div>
                                <input type="text" class="form-control" id="numero_auto" name="numero_auto">
                            </div>
                        </div>
                        <div class="float-end">
                            <button type="button" class="btn btn-success me-1"
                                onclick="submitVoucherForm()">Buscar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- template -->
    <script src="<?= base_url("assets/js/sweetalert/sweetalert.min.js") ?>"></script>
    <script src="<?= base_url("assets/js/preloader.js") ?>"></script>
    <script>

        function submitVoucherForm() {
            var numeroAuto = document.getElementById('numero_auto').value.replace(/\s/g, '');

            if (numeroAuto.length > 19) {
                numeroAuto = numeroAuto.slice(13);
                numeroAuto = numeroAuto.slice(0, -6);
            }

            var url = '<?= base_url("pdf/") ?>' + numeroAuto;
            window.location.href = url;
        }
    </script>
</body>

</html>