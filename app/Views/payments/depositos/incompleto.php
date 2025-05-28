<?= $this->extend('layouts/payments_layout'); ?>

<?= $this->section('title') ?>
Depósitos incompletos
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header sty-one">
        <h1 class="text-black">Depósitos incompletos</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li><i class="fa fa-angle-right"></i>Depósitos incompletos</li>
        </ol>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="depositos" class="table datatable table-bordered table-hover" data-name="cool-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Código de pago</th>
                            <th class="exclude-view">Cédula</th>
                            <th>Nombres</th>
                            <th>Evento</th>
                            <th>Categoría</th>
                            <th class="exclude-view">Dirección</th$>
                            <th class="exclude-view">Teléfono</th>
                            <th class="exclude-view">Email</th>
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
                                    <?php if ($deposito['payment_status'] == '5'): ?>
                                        <a href="<?= base_url('punto/pago/deposito/' . $deposito["id_pago"]) ?>"
                                            class="js-mytooltip btn btn-outline-info m-1" data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="info" data-mytooltip-content="Todos los depósitos" title="Todos los depósitos">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
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
