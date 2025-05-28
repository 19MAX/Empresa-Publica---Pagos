<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Consolidado
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url("assets/css/rounded.css") ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black"> Consolidado</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Reportes</li>
            <li><i class="fa fa-angle-right"></i> Consolidado</li>
        </ol>
    </div>
    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="mis_cobros" class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th class="exclude-view">Código</th>
                            <th>Operador</th>
                            <th>Cédula</th>
                            <th>Nombres</th>
                            <th class="exclude-view">Teléfono</th>
                            <th>Correo</th>
                            <th class="exclude-view">Dirección</th>
                            <th>Evento</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Fecha</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $key => $user): ?>
                            <tr>
                                <td><?= $user["codigo"] ?></td>
                                <td><?= $user["operador"] . " " . $user["last_operador"]?></td>
                                <td><?= $user["participante_cedula"] ?></td>
                                <td><?= $user["participante_name"] ?></td>
                                <td><?= $user["participante_telefono"] ?></td>
                                <td><?= $user["participante_email"] ?></td>
                                <td><?= $user["participante_direccion"] ?></td>
                                <td><?= $user["event_name"] ?></td>
                                <td><?= $user["amount_pay"] ?></td>
                                <td><?= $user["method_pago"] ?></td>
                                <td><?= $user["date_time_payment"] ?></td></td>
                                <td>
                                    <div class="d-flex">

                                    <a class="btn btn-outline-danger"
                                        href="<?= base_url("pdf/".$user['num_autorizacion']) ?>" target="_blank" title="PDF">
                                            <i class="fa fa-lg fa-file-pdf-o" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


<?= $this->endSection() ?>