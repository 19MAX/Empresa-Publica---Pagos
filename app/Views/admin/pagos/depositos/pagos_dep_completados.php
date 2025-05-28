<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Pagos con depósitos
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black">Pagos con depósitos completados</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Pagos con depósitos</li>
            <li><i class="fa fa-angle-right"></i>Completados</li>
        </ol>
    </div>
    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="pagos" class="table datatable table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Código de pago</th>
                            <th>Cédula</th>
                            <th>Nombres</th>
                            <th>Evento</th>
                            <th>Categoría</th>
                            <th class="exclude-view">Dirección</th$>
                            <th class="exclude-view">Teléfono</th>
                            <th class="exclude-view">Correo</th>
                            <th>Estado de pago</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($depositos as $key => $deposito):
                            $numAutorizacion = $deposito['num_autorizacion'];
                            helper('payment_status');
                            ?>
                            <tr data-id-pago="<?= $deposito["id_pago"] ?>" data-ic="<?= $deposito["ic"] ?>"
                                data-estado-pago="<?= $deposito["payment_status"] ?>"
                                data-precio="<?= $deposito['cantidad_dinero'] ?>"
                                data-codigo-pago="<?= $deposito["codigo_pago"] ?>"
                                data-nombres="<?= $deposito["full_name_user"] ?>"
                                data-evento="<?= $deposito["event_name"] ?>"
                                data-categoria="<?= $deposito["category_name"] ?>" data-email="<?= $deposito["email"] ?>">
                                <td><?= $deposito["codigo_pago"] ?></td>
                                <td><?= $deposito["ic"] ?></td>
                                <td><?= $deposito["full_name_user"] ?></td>
                                <td><?= $deposito["event_name"] ?></td>
                                <td><?= $deposito["category_name"] ?></td>
                                <td><?= $deposito["address"] ?></td>
                                <td><?= $deposito["phone"] ?></td>
                                <td><?= $deposito["email"] ?></td>
                                <td><span
                                        class="<?= style_estado($deposito["payment_status"]) ?>"><?= getPaymentStatusText($deposito["payment_status"]) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <?php if ($deposito['payment_status'] == '2'): ?>
                                            <a href="<?= base_url('admin/pagos/' . $deposito["id_pago"]) ?>"
                                                class="js-mytooltip btn btn-outline-info m-1"
                                                data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                                data-mytooltip-theme="info" data-mytooltip-content="Todos los depósitos"
                                                title="Todos los depósitos">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                            <a class="js-mytooltip btn btn-outline-danger m-1" target="_blank"
                                                href="<?= base_url("pdf/$numAutorizacion") ?>"
                                                data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                                data-mytooltip-theme="danger" data-mytooltip-content="PDF" title="PDF">
                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                            </a>
                                        <?php endif ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>




</div>
<?= $this->endSection() ?>
