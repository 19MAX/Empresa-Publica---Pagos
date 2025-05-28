<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Configuración
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1>Configuración</h1>
        <ol class="breadcrumb">
            <li><a href="#">Configuración</a></li>
            <li><i class="fa fa-angle-right"></i> Configuración</li>
        </ol>
    </div>

    <div class="content">

        <div class="row">
            <div class="col">

                <div class="card shadow-sm">

                    <div class="card-header bg-gray">

                        <h5 class="pull-left text-dark m-b-0">Monto adicional por cada pago</h5>

                    </div>

                    <div class="card-body">

                        <form method="post" action="<?= base_url('admin/config/update'); ?>">
                            <div class="form-group">
                                <label for="additional_charge">Monto adicional por cada pago</label>
                                <input type="text" class="form-control" id="additional_charge" name="additional_charge"
                                    value="<?= $additional_charge['value']; ?>">

                                    <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'additional_charge') : '' ?></span>
                            </div>
                            <!-- <div class="form-group">
                                <label for="iva">IVA (%)</label>
                                <input type="text" class="form-control" id="iva" name="iva" value="15">
                            </div> -->
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- Modal para agregar -->
    <div class="modal fade" id="addMethod" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="addUserModalLabel">Agregar</h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("admin/config/add") ?>" id="formAddUser" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Método de pago</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-credit-card-alt" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" name="method_name" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'ic') : '' ?>"
                                        required>
                                </div>

                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'ic') : '' ?>
                                </span>
                            </div>
                        </div>
                            <label>Descripción</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-commenting-o" aria-hidden="true"></i></div>
                                <textarea name="method_description" id=""
                                    class="form-control"><?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'first_name') : '' ?></textarea>
                            </div>

                            <span class="label m-0 p-0 text-danger">
                                <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'first_name') : '' ?>
                            </span>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Cerrar</button>
                    <button form="formAddUser" type="submit" class="btn btn-success">Agregar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para editar -->
    <div class="modal fade" id="editMethod" role="dialog" aria-labelledby="addMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="addMethodModalLabel">Editar</h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("admin/config/update") ?>" id="formAddUser" method="post">
                        <input type="hidden" name="id" id="method_id">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Método de pago</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-id-card-o" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" name="method_name" class="form-control" id="method_name"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'ic') : '' ?>"
                                        required>
                                </div>

                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'ic') : '' ?>
                                </span>
                            </div>

                            <div class="col">
                                <label>Descripción</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user-o" aria-hidden="true"></i></div>
                                    <textarea name="description" id="method_description"
                                        class="form-control"><?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'first_name') : '' ?></textarea>
                                </div>

                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'first_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Cerrar</button>
                    <button form="formAddUser" type="submit" class="btn btn-success">Editar</button>
                </div>
            </div>
        </div>
    </div>



</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    // JavaScript/jQuery para manejar el clic en el botón de eliminar
    $(document).ready(function () {
        $('.btn-delete').on('click', function () {
            // Obtener datos del evento desde atributos data-*
            var eventName = $(this).data('event-name');
            var eventId = $(this).data('event-id');

            // Llenar los campos del modal con los datos del evento
            $('#text-event').text(eventName);
            $('#formPago #id_pago').val(eventId);
        });



        $('.btn-update').on('click', function () {
            console.log('click');
            let methodId = $(this).data('method-id');
            let methodName = $(this).data('method-name');
            let methodDescription = $(this).data('method-description');

            $('#method_id').val(methodId);
            $('#method_name').val(methodName);
            $('#method_description').val(methodDescription);
        });


        // Mostrar el modal de agregar si es necesario
        <?php if ('insert' == ($last_action ?? '')): ?>
            var myModal = new bootstrap.Modal(document.getElementById('addMethod'))
            myModal.show()

        <?php elseif ('update' == ($last_action ?? '')): ?>

            var myModal = new bootstrap.Modal(document.getElementById('editMethod'))
            myModal.show()
        <?php elseif ('recover' == ($last_action ?? '')): ?>

            var myModal = new bootstrap.Modal(document.getElementById('recoverPassword'))
            myModal.show()

        <?php endif; ?>

    });

</script>

<?= $this->endSection() ?>