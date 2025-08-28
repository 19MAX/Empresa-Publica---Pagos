<?= $this->extend('layouts/proservi_layout'); ?>

<?= $this->section('title') ?>
Usuarios
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url("assets/css/rounded.css") ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black"> Usuarios</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Usuarios</li>
            <li><i class="fa fa-angle-right"></i> Lista</li>
        </ol>
    </div>
    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="users" class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Cédula</th>
                            <th>Nombres</th>
                            <th class="exclude-view">Apellidos</th>
                            <th class="exclude-view">Teléfono</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Evento Asignado</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eventUsers as $key => $user): ?>
                            <tr>
                                <td><?= $user["ic"] ?></td>
                                <td><?= $user["first_name"] . ' ' . $user["last_name"] ?></td>
                                <td class="exclude-view"><?= $user["last_name"] ?></td>
                                <td class="exclude-view"><?= $user["phone_number"] ?></td>
                                <td><?= $user["email"] ?></td>
                                <td><?= $user["address"] ?></td>
                                <td><?= $user["event_name"] ?></td>
                                <td>
                                    <div class="d-flex">
                                        <button class="js-mytooltip btn btn-outline-warning m-1 btn-update"
                                            data-toggle="modal" data-target="#update" data-user-id="<?= $user['id'] ?>"
                                            data-user-ic="<?= $user['ic'] ?>"
                                            data-user-first_name="<?= $user['first_name'] ?>"
                                            data-user-last_name="<?= $user['last_name'] ?>"
                                            data-user-phone_number="<?= $user['phone_number'] ?>"
                                            data-user-email="<?= $user['email'] ?>"
                                            data-user-address="<?= $user['address'] ?>"
                                            data-user-event_id="<?= $user['event_id'] ?>"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="warning" data-mytooltip-content="Editar">
                                            <i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
                                        </button>

                                        <button class="js-mytooltip btn btn-outline-danger btn-delete m-1"
                                            data-toggle="modal" data-target="#delete"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="danger" data-mytooltip-content="Eliminar"
                                            data-user-name="<?= $user['first_name'] . ' ' . $user['last_name'] ?>"
                                            data-user-id="<?= $user['id'] ?>">
                                            <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                        </button>

                                        <button class="js-mytooltip btn btn-outline-dark btn-recover m-1"
                                            data-toggle="modal" data-target="#recoverPassword"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="dark" data-mytooltip-content="Recuperar contraseña"
                                            data-user-id="<?= $user['id'] ?>">
                                            <i class="fa fa-key" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal-->
    <div class="modal fade" id="delete" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteModalLabel">Eliminar Usuario de Evento</h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar al usuario <strong id="text-user"></strong>?</p>
                    <form action="<?= base_url("proservi/users/delete") ?>" id="formDelete" method="post">
                        <input type="hidden" name="id" id="id_user" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Cancelar</button>
                    <button form="formDelete" type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal password-->
    <div class="modal fade" id="recoverPassword" role="dialog" aria-labelledby="recoverPasswordLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="recoverPasswordLabel">Recuperar Contraseña</h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea generar una nueva contraseña para este usuario?</p>
                    <form action="<?= base_url("proservi/users/recover") ?>" id="formRecover" method="post">
                        <input type="hidden" name="id" id="id_user_recover" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Cancelar</button>
                    <button form="formRecover" type="submit" class="btn btn-warning">Generar Nueva Contraseña</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="update" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="updateModalLabel">Actualizar Usuario de Evento</h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("proservi/users/update") ?>" id="formUpdate" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Número de cédula</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-id-card-o" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" class="form-control" id="ic" name="cedula"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'ic') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'ic') : '' ?>
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label>Nombres</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user-o" aria-hidden="true"></i></div>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'first_name') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'first_name') : '' ?>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Apellidos</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'last_name') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'last_name') : '' ?>
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label>Teléfono</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-mobile" aria-hidden="true"></i></div>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'phone_number') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'phone_number') : '' ?>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Correo Electrónico</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" class="form-control" id="email" name="email"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'email') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'email') : '' ?>
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label>Dirección</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'address') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'address') : '' ?>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Evento</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i>
                                    </div>
                                    <select class="form-control" id="event_id_update" name="event_id" required>
                                        <option value="" disabled>Seleccione un evento</option>
                                        <?php
                                        $selectedEventId = (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'event_id') : '';
                                        foreach ($authorEvents as $event): ?>
                                            <option value="<?= $event["id"] ?>" <?= $event["id"] == $selectedEventId ? 'selected' : '' ?>>
                                                <?= $event["event_name"] ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'event_id') : '' ?>
                                </span>
                            </div>
                        </div>

                        <input type="hidden" name="id" id="id_usuario" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Cerrar</button>
                    <button form="formUpdate" type="submit" class="btn btn-success">Actualizar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para agregar usuario -->
    <div class="modal fade" id="addUserModal" role="dialog" aria-labelledby="addEventUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="addEventUserModalLabel">Agregar Usuario de Evento</h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("proservi/users/add") ?>" id="formAddEventUser" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Número de cédula</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-id-card-o" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" name="ic" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'ic') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'ic') : '' ?>
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label>Nombres</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user-o" aria-hidden="true"></i></div>
                                    <input type="text" name="first_name" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'first_name') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'first_name') : '' ?>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Apellidos</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                                    <input type="text" name="last_name" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'last_name') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'last_name') : '' ?>
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label>Teléfono</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-mobile" aria-hidden="true"></i></div>
                                    <input type="text" name="phone_number" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'phone_number') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'phone_number') : '' ?>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Correo Electrónico</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i>
                                    </div>
                                    <input type="email" name="email" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'email') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'email') : '' ?>
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label>Dirección</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" name="address" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'address') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'address') : '' ?>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Contraseña</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-key" aria-hidden="true"></i></div>
                                    <input type="password" name="password" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'password') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'password') : '' ?>
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label>Confirmar contraseña</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-repeat" aria-hidden="true"></i></div>
                                    <input type="password" name="password_repeat" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'password_repeat') : '' ?>"
                                        required>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'password_repeat') : '' ?>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Evento</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i>
                                    </div>
                                    <select class="form-control" name="event_id" required>
                                        <option value="" disabled selected>Seleccione un evento</option>
                                        <?php
                                        $selectedEventId = (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'event_id') : '';
                                        foreach ($authorEvents as $event): ?>
                                            <option value="<?= $event["id"] ?>" <?= $event["id"] == $selectedEventId ? 'selected' : '' ?>>
                                                <?= $event["event_name"] ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'event_id') : '' ?>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Cerrar</button>
                    <button form="formAddEventUser" type="submit" class="btn btn-success">Agregar</button>
                </div>
            </div>
        </div>
    </div>


</div>


<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<!-- Script para controlar visibilidad -->
<script>
    // JavaScript/jQuery para manejar el clic en el botón de eliminar
    $(document).ready(function () {

        // Manejo del botón eliminar
        $('.btn-delete').on('click', function () {
            let userName = $(this).data('user-name');
            let userId = $(this).data('user-id');

            $('#text-user').text(userName);
            $('#formDelete #id_user').val(userId);
        });

        // Manejo del botón recuperar contraseña
        $('.btn-recover').on('click', function () {
            let userId = $(this).data('user-id');
            $('#id_user_recover').val(userId);
        });

        // Manejo del botón actualizar
        $('.btn-update').on('click', function () {
            let userId = $(this).data('user-id');
            let userIc = $(this).data('user-ic');
            let userName = $(this).data('user-first_name');
            let userLastName = $(this).data('user-last_name');
            let userNumber = $(this).data('user-phone_number');
            let userEmail = $(this).data('user-email');
            let userAddress = $(this).data('user-address');
            let userEventId = $(this).data('user-event_id');

            $('#id_usuario').val(userId);
            $('#ic').val(userIc);
            $('#first_name').val(userName);
            $('#last_name').val(userLastName);
            $('#phone_number').val(userNumber);
            $('#email').val(userEmail);
            $('#address').val(userAddress);
            $('#event_id_update').val(userEventId);
        });

        // Mostrar modales según la acción
        <?php if ('insert' == ($last_action ?? '')): ?>
            var myModal = new bootstrap.Modal(document.getElementById('addEventUserModal'))
            myModal.show()
        <?php elseif ('update' == ($last_action ?? '')): ?>
            var myModal = new bootstrap.Modal(document.getElementById('update'))
            myModal.show()
        <?php elseif ('recover' == ($last_action ?? '')): ?>
            var myModal = new bootstrap.Modal(document.getElementById('recoverPassword'))
            myModal.show()
        <?php endif; ?>

        // Cerrar modales y limpiar formularios
        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('close-modal') || event.target.closest('.close-modal')) {
                let modal = event.target.closest('.modal');
                if (modal) {
                    let modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();

                        // Limpiar formulario
                        let form = modal.querySelector('form');
                        if (form) {
                            form.reset();
                        }

                        // Limpiar spans de error
                        let spans = modal.querySelectorAll('span.text-danger');
                        spans.forEach(function (span) {
                            span.textContent = '';
                        });
                    }
                }
            }
        });

        function clearFormAndSpans(modalId) {
            var modal = document.getElementById(modalId);
            if (modal) {
                var form = modal.querySelector('form');
                if (form) {
                    form.reset();
                    var inputs = form.querySelectorAll('input, select');
                    inputs.forEach(function (input) {
                        if (input.type !== 'hidden') {
                            input.value = '';
                        }
                    });
                }

                var spans = modal.querySelectorAll('span.text-danger');
                spans.forEach(function (span) {
                    span.textContent = '';
                });
            }
        }

        // Limpiar formularios al cerrar modales
        $('.modal').on('hidden.bs.modal', function () {
            clearFormAndSpans(this.id);
        });
    });

</script>

<?= $this->endSection() ?>