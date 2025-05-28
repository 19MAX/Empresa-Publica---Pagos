<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción actualizada</title>
    <style>
        body {
            font-family: Tahoma, Verdana, Segoe, sans-serif;
            background-color: #F5F5F5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #F5F5F5;
        }

        .header {
            background-color: #fff;
            border-bottom: 2px solid #0c244b;
            border-radius: 10px 10px 0 0;
        }

        .header h1 {
            color: #c3171b;
        }

        .content {
            border: 2px solid #0c244b;
            border-radius: 10px;
        }

        .content-d {
            padding: 20px;
            background-color: #FFFFFF;
            border-radius: 10px;
        }

        .logo {
            max-width: 152px;
        }

        h1 {
            color: #c3171b;
            margin: 0;
            font-size: 1.5rem;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .highlight {
            color: #0c244b;
            font-weight: bold;
        }

        .text-success {
            color: green;
        }

        .alert-payment {
            color: #c3171b;
            background-color: #ffe5e5;
            border: 2px solid #c3171b;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .space {
            padding-bottom: 5%;
            background-color: #F5F5F5;
        }

        .footer {
            background-color: #0c244b;
            color: white;
            padding: 20px;
            text-align: center;
        }

        /* Estilos adaptados de Bootstrap */

        .row {
            width: 100%;
            display: table;
            table-layout: fixed;
            border-spacing: 0;
        }

        .col {
            display: table-cell;
            padding-right: 0.75rem;
            padding-left: 0.75rem;
            vertical-align: top;
        }

        .col-6 {
            width: 50%;
        }

        .col-12 {
            width: 100%;
        }

        @media (max-width: 600px) {
            .header {
                text-align: center;
            }

            .header img {
                margin-bottom: 0px;
            }

            .header .bottom {
                border-bottom: 2px solid #0c244b;
            }

            .col {
                display: block;
                width: 100%;
            }
        }

        /* Padding general */

        .p-0 {
            padding: 0rem !important;
        }

        .p-2 {
            padding: 0.5rem !important;
        }

        .pt-2,
        .py-2 {
            padding-top: 0.5rem !important;
        }

        .pb-2,
        .py-2 {
            padding-bottom: 0.5rem !important;
        }
    </style>
</head>

<body>
    <header>
        <img src="<?= base_url('assets/images/email/bg_top.jpg'); ?>" alt="bg-top" style="width: 100%;">
    </header>
    <main style="background-color: #F5F5F5;">

        <div class="container">
            <div class="space"></div>
            <div class="content">
                <div class="header row">
                    <div class="col bottom">
                        <img src="<?= base_url('assets/images/email/logo-ep.png') ?>" alt="Logo" class="logo">
                    </div>
                    <div class="col">
                        <h1 class="pt-2 pb-2">Inscripción actualizada</h1>
                    </div>
                </div>
                <div class="content-d">
                    <p>Estimado(a),
                        <strong>
                            <?= $user ?>
                        </strong>
                    </p>
                    <p> Tu inscripción se actualizo exitosamente</p>
                    <div class="alert-payment p-0">
                        <p>Tu inscripción se completará cuando verifiquemos tu pago</p>
                    </div>
                    <table class="details-table">
                        <tr>
                            <th>Evento:</th>
                            <td>
                                <?= $evento ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Categoría:</th>
                            <td>
                                <?= $categoria ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Precio:</th>
                            <td class="text-success">$
                                <?= number_format($precio, 2) ?>
                            </td>
                        </tr>
                        <!-- <tr>
                            <th>Código de Pago:</th>
                            <td class="highlight">
                                <?= $codigoPago ?>
                            </td>
                        </tr> -->
                        <!-- <tr>
                            <th>Fecha Límite de Pago:</th>
                            <td class="highlight">
                                <?= $fecha_limite_pago ?>
                            </td>
                        </tr> -->
                        <tr>
                            <th>Fecha de Emisión:</th>
                            <td>
                                <?= $fechaEmision ?>
                            </td>
                        </tr>
                    </table>

                    <!-- Nueva sección para subir el comprobante de pago -->
                    <p style="text-align: center;">
                        Con el código de pago <span class="highlight">
                            <?= $codigoPago ?>
                        </span>,
                        puedes elegir tu método de pago (depósito o tarjeta) en el sistema para completar tu pago.
                    </p>
                    <p style="text-align: center;">Haz clic en <a
                            style="border-bottom: 2px solid #0c244b; text-decoration: none; border-radius: 10px; color:#0c244b; padding: 5px 5px; background-color: #e5e8ff;"
                            href="<?= base_url('?modal=metodo&codigoPago=' . $codigoPago) ?>"> <strong>PAGAR EN EL
                                SISTEMA</strong></a></p>
                    <hr>
                    <h3 style="text-align: center;">Para depósitos o transferencias bancarias:</h3>
                    <table class="details-table">
                        <tr>
                            <th>Banco:</th>
                            <td style="background:#BC157C; color:white;">Banco de Guayaquil</td>
                        </tr>
                        <tr>
                            <th>Tipo de Cuenta:</th>
                            <td>Corriente</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>SOFTEC WEBSTORE S.A.S</td>
                        </tr>
                        <tr>
                            <th>Cuenta Bancaria:</th>
                            <td>0029421609</td>
                        </tr>
                        <tr>
                            <th>RUC:</th>
                            <td>0291525784001</td>
                        </tr>
                        <!-- <tr>
                            <td colspan="2" style="text-align: center; font-weight: bold; padding-top: 15px;">Otra cuenta</td>
                        </tr>
                        <tr>
                            <td>Banco:</td>
                            <td>Banco Pichincha</td>
                        </tr>
                        <tr>
                            <td>Tipo de Cuenta:</td>
                            <td>Ahorros</td>
                        </tr>
                        <tr>
                            <td>Nombre:</td>
                            <td>SOFTEC WEBSTORE S A S</td>
                        </tr>
                        <tr>
                            <td>Cuenta Bancaria:</td>
                            <td>65656</td>
                        </tr>
                        <tr>
                            <td>RUC:</td>
                            <td>342434343535</td>
                        </tr> -->
                    </table>
                    <h3 style="text-align: center;">Métodos de pago disponibles:</h3>
                    <ul class="row" style="list-style-type: none; padding: 0;">
                        <li class="col" style="padding-left: 0;"><strong>Depósito Bancario:</strong> Realiza un depósito
                            en nuestra cuenta bancaria y luego
                            sube tu comprobante en el sistema.</li>
                        <li class="col" style="padding-left: 0;"><strong>Pago con Tarjeta:</strong> Usa PayPhone para
                            pagar de manera segura con tu tarjeta
                            de crédito o débito.</li>
                    </ul>

                    <hr>
                    <p style="text-align: center;">Gracias por tu participación.</p>
                </div>
            </div>
            <div class="space"></div>
        </div>

        <div class="footer">
            <div>
            </div>
            <p>© 2024 PROSERVI-UEB-EP | Softec Apps S.A.S. Todos los derechos reservados.</p>
        </div>

    </main>
</body>

</html>