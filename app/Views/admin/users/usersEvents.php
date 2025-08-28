<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Usuarios de eventos
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url("assets/css/rounded.css") ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black"> Usuarios de eventos</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Usuarios</li>
            <li><i class="fa fa-angle-right"></i> Eventos</li>
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
                            <th>Rol</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $key => $user): ?>
                            <tr>
                                <td><?= $user["ic"] ?></td>
                                <td><?= $user["first_name"] . ' ' .$user["last_name"] ?></td>
                                <td><?= $user["last_name"] ?></td>
                                <td><?= $user["phone_number"] ?></td>
                                <td><?= $user["email"] ?></td>
                                <td><?= $user["address"] ?></td>
                                <td><span class="badge bg-black">Usuario con evento</span></td>
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
                                            data-user-rol_id="<?= $user['rol_id'] ?>"
                                            data-user-author_id="<?= $user['author_id'] ?? '' ?>"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="warning" data-mytooltip-content="Editar">
                                            <i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i></button>

                                        <button class="js-mytooltip btn btn-outline-danger btn-delete m-1"
                                            data-toggle="modal" data-target="#delete"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="danger" data-mytooltip-content="Eliminar"
                                            data-user-name="<?= $user['first_name'] ?>" data-user-id="<?= $user['id'] ?>">
                                            <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                        </button>

                                        <button class="js-mytooltip btn btn-outline-dark btn-recover m-1"
                                            data-toggle="modal" href="#recoverPassword"
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
    <div class="modal fade" id="delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Eliminar</h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("admin/users-events/delete") ?>" id="formDelete" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <p>Estas seguro de eliminar al usuario : <span class="text-danger"
                                        id="text-user"></span></p>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id_user">
                        <input type="hidden" name="cedula" id="cedula">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Cerrar</button>
                    <button form="formDelete" type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal password-->
    <div class="modal fade" id="recoverPassword" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Recuperar contraseña</h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("admin/users-events/recover_password") ?>" id="formRecover" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Contraseña</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-key" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" name="password" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'recover') ? display_data($last_data, 'password') : '' ?>"
                                        required>
                                </div>

                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'recover') ? display_data($validation, 'password') : '' ?>
                                </span>
                            </div>

                            <div class="col">
                                <label>Repita la contraseña</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-repeat" aria-hidden="true"></i>
                                    </div>
                                    <input type="password" name="password_repeat" class="form-control"
                                        value="<?= (isset($last_data) && ($last_action ?? null) == 'recover') ? display_data($last_data, 'password_repeat') : '' ?>"
                                        required>
                                </div>

                                <span class="label m-0 p-0 text-danger">
                                    <?= (isset($validation) && ($last_action ?? null) == 'recover') ? display_data($validation, 'password_repeat') : '' ?>
                                </span>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id_user_recover">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Cerrar</button>
                    <button form="formRecover" type="submit" class="btn btn-success">Recuperar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="update" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Actualizar</h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("admin/users-events/update") ?>" id="formUpdate" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <label>Numero de cédula</label>
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

                            <div class="col">
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
                            <div class="col">
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

                            <div class="col">
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
                            <div class="col">
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

                            <div class="col">
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

                        <input type="hidden" name="id" id="id_usuario"
                            value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'id') : '' ?>"
                            required>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Cerrar</button>
                    <button form="formUpdate" type="submit" class="btn btn-success">Actualizar</button>
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
        $('.btn-delete').on('click', function () {
            // Obtener datos del usuario desde atributos data-*
            let userName = $(this).data('user-name');
            let userId = $(this).data('user-id');

            // Llenar los campos del modal con los datos del usuario
            $('#text-user').text(userName);
            $('#formDelete #id_user').val(userId);
        });

        $('.btn-recover').on('click', function () {
            let userId = $(this).data('user-id');
            $('#id_user_recover').val(userId);
        });

        $('.btn-update').on('click', function () {
            let userId = $(this).data('user-id');
            let userIc = $(this).data('user-ic');
            let userName = $(this).data('user-first_name');
            let userLastName = $(this).data('user-last_name');
            let userNumber = $(this).data('user-phone_number');
            let userEmail = $(this).data('user-email');
            let userAddress = $(this).data('user-address');

            $('#id_usuario').val(userId);
            $('#ic').val(userIc);
            $('#first_name').val(userName);
            $('#last_name').val(userLastName);
            $('#phone_number').val(userNumber);
            $('#email').val(userEmail);
            $('#address').val(userAddress);

        });

        // Mostrar el modal de agregar si es necesario
        <?php if ('insert' == ($last_action ?? '')): ?>
            var myModal = new bootstrap.Modal(document.getElementById('addUserModal'))
            myModal.show()

        <?php elseif ('update' == ($last_action ?? '')): ?>
            var myModal = new bootstrap.Modal(document.getElementById('update'))
            myModal.show()

        <?php elseif ('recover' == ($last_action ?? '')): ?>
            var myModal = new bootstrap.Modal(document.getElementById('recoverPassword'))
            myModal.show()
        <?php endif; ?>

        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('close-modal') || event.target.closest('.close-modal')) {
                let modal = event.target.closest('.modal');
                if (modal) {
                    let modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();

                        // Limpiar el formulario dentro del modal
                        let form = modal.querySelector('form');
                        if (form) {
                            form.reset();
                        }

                        // Limpiar todos los spans dentro del modal
                        let spans = modal.querySelectorAll('span');
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
                    // Limpiar específicamente los inputs
                    var inputs = form.querySelectorAll('input');
                    inputs.forEach(function (input) {
                        input.value = '';
                    });
                    // Limpiar selects
                    var selects = form.querySelectorAll('select');
                    selects.forEach(function (select) {
                        select.selectedIndex = 0;
                    });
                }

                var spans = modal.querySelectorAll('span');
                spans.forEach(function (span) {
                    span.textContent = '';
                });

                // Ocultar contenedores de autor
                var authorContainers = modal.querySelectorAll('[id*="author-container"]');
                authorContainers.forEach(function (container) {
                    container.style.display = 'none';
                });
            } else {
                console.error('Modal con ID ' + modalId + ' no encontrado.');
            }
        }

        // Evento para limpiar el formulario y los spans cuando se cierra el modal
        $('.modal').on('hidden.bs.modal', function () {
            clearFormAndSpans(this.id);
        });
    });

</script>

<?= $this->endSection() ?>