<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Depósitos
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black">Depósitos</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Depósitos</li>
        </ol>
    </div>
    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="pagos" class="table datatable table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th class="exclude-view">Código Pago</th>
                            <th>Cédula</th>
                            <th>Comprobante</th>
                            <th>Participante</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="exclude-view">Correo</th>
                            <th class="exclude-view">Evento</th>
                            <th>Valor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($depositos as $deposito): ?>
                            <tr>
                                <td><?= $deposito['codigo_pago'] ?></td>
                                <td><?= $deposito['ic'] ?></td>
                                <td><?= $deposito['num_comprobante'] ?></td>
                                <td><?= $deposito['full_name_user'] ?></td>
                                <td><?= $deposito['monto_deposito'] ?></td>
                                <td><?= $deposito['date_deposito'] ?></td>
                                <td><?= $deposito['status'] ?></td>
                                <td><?= $deposito['email'] ?></td>
                                <td><?= $deposito['event_name'] ?></td>
                                <td><?= $deposito['monto_category'] ?></td>
                                <td>
                                    <a href="<?= base_url('admin/pagos/' . $deposito["id_pago"]) ?>"
                                        class="js-mytooltip btn btn-outline-info m-1"
                                        data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                        data-mytooltip-theme="info" data-mytooltip-content="Todos los depósitos"
                                        title="Todos los depósitos">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
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