<!DOCTYPE html>
<title>Voucher</title>
<html>

<head>
    <style>
        @page {
            margin: 10px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        header,
        footer {
            text-align: center;
        }

        .container {
            width: 100%;
        }

        .section {
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid black;
        }

        .table th,
        .table td {
            padding: 3px;
            text-align: left;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .text-right {
            text-align: right;
        }

        .break-word {
            word-wrap: break-word;
            word-break: break-all;
            white-space: normal;
        }
    </style>
</head>

<body>
    <header>
        <div class="title">PROSERVI UEB</div>
        <div class="title">COMPROBANTE DE RECAUDACIÓN</div>
        <div>Calle Sucre entre García Moreno y 10 de Agosto</div>
        <div>RUC: 0260024190001</div>
        <!-- <div>Factura Nro: 002-201-000000977</div> -->
        <div>Fecha autorización: <?= $fecha_emision ?></div>
        <div>Ambiente: PRODUCCIÓN</div>

        <hr>
        <div>NÚMERO DE AUTORIZACIÓN:</div>
        <div class="break-word">0201975844001<?= $num_autorizacion ?>140601</div>
    </header>
    <main>
        <hr>
        <div class="container">
            <div class="section">
                <div><strong>Cliente: </strong> <?= $user ?></div>
                <div><strong>C.I/RUC: </strong> <?= $user_ic ?></div>
                <?php
                $timestamp = strtotime($fecha_emision);
                $fecha_formateada = date('d/m/Y', $timestamp);
                ?>
                <div><strong>Fecha Emisión: </strong> <?= $fecha_formateada ?></div>
            </div>
            <div class="section">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cant.</th>
                            <th>Desc.</th>
                            <th>P.Unit</th>
                            <th>V.Tot</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Uso de plataforma </td>
                            <td><?= $precio_unitario ?></td>
                            <td><?= $valor_total ?></td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Inscripción</strong></td>
                            <td><?= $sub_total_0 ?></td>
                            <td><?= $sub_total_0 ?></td>
                        </tr>
                        <tr>
                            <td colspan="4">Inscripción en el <strong><?= $event_name ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="section text-right">
                <div><strong>Sub Total: $ </strong><?= $sub_total ?></div>
                <div><strong>Sub Total 0%: $ </strong><?= $sub_total_0 ?></div>
                <div><strong>Sub Total 15%: $ </strong><?= $sub_total_15 ?></div>
                <div><strong>IVA 15%: $ </strong><?= $iva ?></div>
                <div><strong>TOTAL: $ </strong><?= $valor_final ?></div>
            </div>
            <div class="section">
                <div>
                    <strong>FORMA DE PAGO:</strong>
                    <?php if ($metodo_pago == 1): ?>
                        PAGO CON DEPOSITO
                    <?php elseif ($metodo_pago == 2): ?>
                        SIN UTILIZACIÓN DEL SISTEMA FINANCIERO
                    <?php elseif ($metodo_pago == 3): ?>
                        PAGO EN LINEA CON TARJETA
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="section">
            <div><strong>Mail:</strong> <?= $email_user ?></div>
            <div><strong>Contacto:</strong> <?= $user_tel ?></div>
            <div><strong>Operador:</strong> <?= isset($operador) ? $operador : 'Payphone' ?></div>
        </div>
        <div class="title">
            <strong>VALOR TOTAL PAGADO:</strong>
            <h1> $ <?= $valor_final ?></h1>
        </div>
        <div class="section">
            <div>Documento sin valor tributario, en breve será emitido su comprobante electrónico, en caso de no estar en la bandeja principal, revisar
                en la bandeja de spam.</div>
        </div>
        </div>
    </main>
</body>

</html>