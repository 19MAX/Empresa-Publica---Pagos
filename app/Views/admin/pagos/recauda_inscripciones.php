<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Pagos
<?= $this->endSection() ?>


<?= $this->section('css') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header sty-one">
        <h1 class="text-black">Cobrar en efectivo</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li><i class="fa fa-angle-right"></i>Cobrar en efectivo</li>
        </ol>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="inscripciones" class="table datatable table-bordered table-hover">
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
                            <th>Precio a pagar</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($registrations as $key => $registration):
                            $numAutorizacion = $registration['num_autorizacion'];
                            helper('payment_status');
                            ?>
                            <tr data-id-pago="<?= $registration["id_pago"] ?>" data-ic="<?= $registration["cedula"] ?>" data-estado-pago="<?= $registration["estado_pago"] ?>"
                                data-codigo-pago="<?= $registration["codigo_pago"] ?>"
                                data-nombres="<?= $registration["nombres"] ?>" data-evento="<?= $registration["evento"] ?>"
                                data-categoria="<?= $registration["categoria"] ?>"
                                data-precio="<?= $registration["precio"] ?>">
                                <td><?= $registration["codigo_pago"] ?></td>
                                <td><?= $registration["cedula"] ?></td>
                                <td><?= $registration["nombres"] ?></td>
                                <td><?= $registration["evento"] ?></td>
                                <td><?= $registration["categoria"] ?></td>
                                <td><?= $registration["direccion"] ?></td>
                                <td><?= $registration["telefono"] ?></td>
                                <td><?= $registration["email"] ?></td>
                                <td><span
                                        class="<?= get_payment_status_class($registration["estado_pago"]) ?>"><?= $registration["estado_pago"] ?></span>
                                </td>
                                <td><?= $registration["precio"] ?></td>
                                <td>
                                    <?php if ($registration['estado_pago'] == 'Pendiente'): ?>
                                        <button class="btn btn-outline-success btn-pagar" data-toggle="modal" href="#mi_modal"
                                            title="Proceder con el pago">
                                            <i class="fa fa-credit-card-alt" aria-hidden="true"></i> Cobrar
                                        </button>
                                    <?php elseif ($registration['estado_pago'] == 'Completado'): ?>
                                        <a class="btn btn-outline-danger"
                                            href="<?= base_url("pdf/$numAutorizacion") ?>" title="PDF">
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                                        </a>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <!-- Modal-->
    <div class="modal fade" id="mi_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">PROCEDER CON EL PAGO </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("admin/pago") ?>" id="formPago" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Nombre:</label>
                                <input type="text" class="form-control" id="nombre" readonly>
                                <!-- <label for="method_payment">Método de pago</label>
                                <select class="form-control" name="method_payment" id="method_payment">
                                    <option value="" disabled selected>Seleccione el método de pago</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="PayPhone">PayPhone</option>
                                </select> -->
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id_pago">
                        <input type="hidden" name="cedula" id="cedula">
                        <input type="hidden" name="estado_pago" id="estado_pago">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Evento:</label>
                                <input type="text" class="form-control" id="evento" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label>Categoría:</label>
                                <input type="text" class="form-control" id="categoria" readonly>
                            </div>
                            <div class="col">
                                <label>Precio:</label>
                                <input type="text" class="form-control" id="precio" name="precio" readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button form="formPago" type="submit" class="btn btn-primary">Continuar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>



<?= $this->section('scripts') ?>

<script>
        $(".btn-pagar").click(function () {
            var fila = $(this).closest("tr");
            var ic = fila.data("ic");
            var estadoPago = fila.data("estado-pago");
            var idPago = fila.data("id-pago");
            var codigoPago = fila.data("codigo-pago");
            var nombres = fila.data("nombres");
            var evento = fila.data("evento");
            var categoria = fila.data("categoria");
            var precio = fila.data("precio");

            // Actualizar los campos del modal
            $("#cedula").val(ic);
            $("#estado_pago").val(estadoPago);
            $("#id_pago").val(idPago);
            $("#nombre").val(nombres);
            $("#evento").val(evento);
            $("#categoria").val(categoria);
            $("#precio").val(precio);
        });
</script>

<?= $this->endSection() ?>