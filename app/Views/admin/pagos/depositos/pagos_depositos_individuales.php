<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Depósitos individuales
<?= $this->endSection() ?>


<?= $this->section('css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url("dist/plugins/datatables/css/dataTables.bootstrap.min.css") ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header sty-one">
        <h1 class="text-black">Depósitos del usuario <?= $datosPago[0]['name'] ?></h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li><i class="fa fa-angle-right"></i>Depósitos individuales</li>
        </ol>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="info-box">
            <!-- <h4 class="modal-title" id="myModalLabel">PROCEDER CON EL PAGO</h4> -->
            <h4 class="modal-title" id="myModalLabel">Estado del pago: <span
                    class=" <?= style_estado($datosPago[0]['payment_status']) ?> rounded-3 p-1">
                    <?= getPaymentStatusText($datosPago[0]['payment_status']) ?></span></h4>

            <div class="row">
                <div class="col-md-6">
                    <form action="<?= base_url("admin/pagos/aprobar") ?>" id="formPago" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Nombre:</label>
                                <input type="text" class="form-control" id="nombre" value="<?= $datosPago[0]['name'] ?>"
                                    readonly>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id_pago" value="<?= $datosPago[0]['id'] ?>">
                        <input type="hidden" name="cedula" id="cedula" value="<?= $datosPago[0]['ic'] ?>">
                        <input type="hidden" name="estado_pago" id="estado_pago"
                            value="<?= $datosPago[0]['payment_status'] ?>">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Evento:</label>
                                <input type="text" class="form-control" id="evento"
                                    value="<?= $datosPago[0]['evento'] ?>" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label>Categoría:</label>
                                <input type="text" class="form-control" id="categoria"
                                    value="<?= $datosPago[0]['categoria'] ?>" readonly>
                            </div>
                            <div class="col">
                                <label>Monto total a pagar:</label>
                                <div class="input-group">

                                    <div class="input-group-addon text-success">$</div>
                                    <input type="text" class="form-control" id="precio" name="precio"
                                        value="<?= $datosPago[0]['monto_total'] ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label>Monto pagado :</label>
                                <div class="input-group">

                                    <div class="input-group-addon text-success">$</div>
                                    <input type="text" class="form-control" id="montoPagado" readonly>
                                </div>
                            </div>

                            <div class="col">
                                <label>Pendiente:</label>
                                <div class="input-group">
                                    <div class="input-group-addon text-success">$</div>

                                    <input type="text" class="form-control text-danger" id="diferencia" readonly>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="m-0 p-0 mb-2">

                        <h5>Historial de depósitos</h5>
                        <div class="table-responsive mt-0" style="max-height: 250px; overflow-y: auto;">
                            <table id="depositosTable" class="table table-bordered table-hover rounded-3"
                                style="border: 3px solid #001a35;">
                                <thead class="bg-blue">
                                    <tr>
                                        <th># Comprobante</th>
                                        <th>Monto deposito</th>
                                        <th class="text-center">Fecha<p class="mb-0">(Y-m-d)</p>
                                        </th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div id="imagen-container" class="imagen-container bg-dark">
                        <div class="imagen-wrapper">
                            <img id="imagen-deposito" src="" alt="Comprobante de depósito" class="img-zoomable">
                        </div>
                    </div>
                    <div class="zoom-controls mt-2">
                        <button id="zoom-in" class="btn btn-sm btn-primary">+</button>
                        <button id="zoom-out" class="btn btn-sm btn-primary">-</button>
                        <button id="zoom-reset" class="btn btn-sm btn-secondary">Reiniciar</button>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-outline-dark m-2" id="btnPagoIncompleto">Pago incompleto</button>
                <button type="button" class="btn btn-danger m-2" id="btnSoloRechazo">Rechazar</button>
                <button form="formPago" type="submit" class="btn btn-primary m-2" id="btnAprobar">Aprobar</button>
            </div>

        </div>

    </div>

    <style>
        #depositosTable {
            width: 100% !important;
            font-size: 0.9em;
        }

        #depositosTable th,
        #depositosTable td {
            padding: 0.5rem;
        }
    </style>
    <style>
        .editable-input {
            width: 100%;
            box-sizing: border-box;
        }

        .editable-select {
            width: 100%;
            box-sizing: border-box;
        }

        .custom-select {
            /* padding: 0.375rem 0.75rem; */
            font-size: 1em;
            /* line-height: 1.5; */
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
    </style>

    <!-- <div class="modal fade" id="editDepositModal" tabindex="-1" role="dialog" aria-labelledby="editDepositModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepositModalLabel">Editar Depósito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editDepositForm">
                        <input type="hidden" name="id_deposito" id="edit_id_deposito">
                        <div class="form-group">
                            <label for="edit_estado">Estado</label>
                            <select class="form-control" id="edit_estado" name="estado">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Incompleto">Incompleto</option>
                                <option value="Aprobado">Aprobado</option>
                                <option value="Rechazado">Rechazado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_monto_deposito">Monto del Depósito</label>
                            <input type="text" class="form-control" id="edit_monto_deposito" name="monto_deposito">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Modal para pagos incompletos -->
    <div class="modal fade" id="modalRechazo" tabindex="-1" role="dialog" aria-labelledby="modalRechazoLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalRechazoLabel">Pago incompleto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formRechazo" action="<?= base_url("admin/pagos/incompleto") ?>" method="post">
                        <input type="hidden" name="id_pago_rechazo" id="id_pago_rechazo"
                            value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'id_pago_rechazo') : '' ?>">
                        <input type="hidden" name="email" class="email_rechazo_incompleto"
                            value="<?= $datosPago[0]['email'] ?>">
                        <div class="row mb-3">
                            <div class="form-group col">
                                <label for="names">Nombres</label>
                                <div class="input-group">
                                    <div class="input-group-addon text-success"><i class="fa fa-user-o"></i></div>
                                    <input class="form-control names" id="names_rechazo_incompleto" name="names"
                                        type="text"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'names') : '' ?>"
                                        readonly required>

                                </div>
                                <span class="text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'names') : '' ?>
                                </span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="motivo_rechazo">Motivo de Rechazo:</label>
                                <textarea class="form-control" id="motivo_rechazo" name="motivo_rechazo"
                                    rows="4"><?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'motivo_rechazo') : '' ?></textarea>
                                <span class="text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'motivo_rechazo') : '' ?>
                                </span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="form-group col">
                                <label for="precio_pagar">Precio a pagar</label>
                                <div class="input-group">
                                    <div class="input-group-addon text-success">$</div>
                                    <input class="form-control" id="precio_pagar" name="precio_pagar" type="text"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'precio_pagar') : '' ?>"
                                        readonly required>
                                </div>
                                <span class="text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'precio_pagar') : '' ?>
                                </span>
                            </div>
                            <div class="form-group col">
                                <label for="precio_pagado">Precio pagado:</label>
                                <div class="input-group">
                                    <div class="input-group-addon text-success">$</div>
                                    <input class="form-control" id="precio_pagado" name="precio_pagado" type="text"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'precio_pagado') : '' ?>"
                                        required>
                                </div>
                                <span class="text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'precio_pagado') : '' ?>
                                </span>
                            </div>
                        </div>
                        <div class="row mb-3">

                            <div class="form-group col">
                                <label for="valor_pendiente">Valor pendiente:</label>
                                <div class="input-group">
                                    <div class="input-group-addon text-success">$</div>
                                    <input class="form-control" id="valor_pendiente" name="valor_pendiente" type="text"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'valor_pendiente') : '' ?>"
                                        readonly required>
                                </div>
                                <span class="text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'valor_pendiente') : '' ?>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary  btn-close" data-dismiss="modal">Cerrar</button>
                    <button form="formRechazo" type="submit" class="btn btn-danger">Enviar email</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Nuevo Modal para motivo de rechazo y enviar a otra ruta -->
    <div class="modal fade" id="modalSoloRechazo" tabindex="-1" role="dialog" aria-labelledby="modalSoloRechazoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalSoloRechazoLabel">Motivo de Rechazo</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formSoloRechazo" action="<?= base_url("admin/pagos/rechazar") ?>" method="post">
                        <input type="hidden" name="id_pago_solo_rechazo" id="id_pago_solo_rechazo">
                        <input type="hidden" name="email" id="email_rechazo" class="email_rechazo_incompleto"
                            value="<?= $datosPago[0]['email'] ?>">

                        <div class="row mb-3">
                            <div class="form-group col">
                                <label for="names">Nombres</label>
                                <div class="input-group">
                                    <div class="input-group-addon text-success"><i class="fa fa-user-o"></i></div>
                                    <input class="form-control names" id="names_rechazo" name="names" type="text"
                                        readonly required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="motivo_solo_rechazo">Motivo de Rechazo:</label>
                                <textarea class="form-control" id="motivo_solo_rechazo" name="motivo_solo_rechazo"
                                    rows="4" required></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Cerrar</button>
                    <button form="formSoloRechazo" type="submit" class="btn btn-danger">Enviar Rechazo</button>
                </div>
            </div>
        </div>
    </div>



</div>
<style>
    .modal-lg {
        max-width: 90%;
    }

    .imagen-container {
        width: 100%;
        height: 400px;
        overflow: auto;
        position: relative;
        border: 1px solid #ddd;
    }

    .imagen-wrapper {
        min-height: 100%;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .img-zoomable {
        max-width: 100%;
        max-height: none;
        display: block;
        transition: transform 0.3s ease-out;
    }

    .zoom-controls {
        text-align: center;
    }
</style>
<?= $this->endSection() ?>



<?= $this->section('scripts') ?>

<!-- DataTable -->
<script src="<?= base_url("dist/plugins/datatables/jquery.dataTables.min.js") ?>"></script>
<script src="<?= base_url("dist/plugins/datatables/dataTables.bootstrap.min.js") ?>"></script>
<script>
    var idRuta = <?= $datosPago[0]['id'] ?>;
</script>
<script>

    // Mostrar el modal de depósito si es necesario
    <?php if ('update' == ($last_action ?? '')): ?>
        var myModal = new bootstrap.Modal(document.getElementById('modalRechazo'))
        myModal.show()
    <?php endif; ?>
</script>
<script src="<?= base_url("assets/js/admin/pagos_depositos_individuales.js") ?>"></script>


<?= $this->endSection() ?>