<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Todas las inscripciones
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url("assets/css/rounded.css") ?>">
<!-- Select 2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black">Todas las inscripciones</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Inscripciones</li>
        </ol>
    </div>
    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="inscripcionesTrash" class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th class="exclude-view">Código</th>
                            <th>Cédula</th>
                            <th>Nombres</th>
                            <th class="exclude-view">Dirección</th>
                            <th class="exclude-view">Teléfono</th>
                            <th>Email</th>
                            <th>Evento</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th class="exclude-view">Método de pago</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        helper('payment_status');
                        foreach ($registrations as $key => $registro): ?>
                            <tr>
                                <td><?= $registro["codigo_pago"] ?></td>
                                <td><?= $registro["ic"] ?></td>
                                <td><?= $registro['full_name_user'] ?></td>
                                <td><?= $registro["address"] ?></td>
                                <td><?= $registro["phone"] ?></td>
                                <td><?= $registro["email"] ?></td>
                                <td><?= $registro["event_name"] ?></td>
                                <td><?= $registro["monto_category"] ?></td>
                                <td><span
                                class="<?= style_estado($registro["estado_pago"]) ?>"><?= getPaymentStatusText($registro["estado_pago"]) ?></span></td>
                                <td><?= $registro["metodo_pago"] ?></td>
                                <td>
                                    <div class="d-flex">
                                    <?php if ($registro['estado_pago'] != 2): ?>
                                        <button class="js-mytooltip btn btn-outline-warning btn-edit m-1" title="Editar"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="warning" data-mytooltip-content="Editar"
                                            data-toggle="modal" data-target="#editModal" data-id="<?= $registro['id'] ?>"
                                            data-name="<?= $registro['full_name_user'] ?>"
                                            data-address="<?= $registro['address'] ?>"
                                            data-phone="<?= $registro['phone'] ?>" data-email="<?= $registro['email'] ?>"
                                            data-ic="<?= $registro['ic'] ?>"
                                            data-event-id="<?= $registro['event_cod'] ?>"
                                            data-category-id="<?= $registro['cat_id'] ?>"
                                            >
                                            <i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
                                        </button>
                                        <button class="js-mytooltip btn btn-outline-danger btn-delete m-1" title="Eliminar"
                                            data-toggle="modal" data-target="#delete"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="danger" data-mytooltip-content="Eliminar"
                                            data-inscription-name="<?= $registro['full_name_user'] ?>"
                                            data-inscription-category="<?= $registro["monto_category"] ?>" data-inscription-id="<?= $registro['id'] ?>">
                                            <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                        </button>
                                    <?php else: ?>
                                        <a class="js-mytooltip btn btn-outline-danger m-1" target="_blank"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="danger" data-mytooltip-content="PDF"
                                            href="<?= base_url("pdf/" . $registro['num_autorizacion']) ?>" title="PDF">
                                            <i class="fa fa-lg fa-file-pdf-o" aria-hidden="true"></i>
                                        </a>
                                    <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div class="modal fade" id="editModal" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar datos de la inscripción</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('admin/inscritos/update') ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="edit-name">Usuario inscrito</label>
                                <input type="text" class="form-control" id="edit-name" name="full_name_user">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="edit-ic">Cédula/Ruc</label>
                                <input type="text" class="form-control" id="edit-ic" name="ic">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="edit-address">Dirección</label>
                                <input type="text" class="form-control" id="edit-address" name="address">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="edit-phone">Teléfono</label>
                                <input type="text" class="form-control" id="edit-phone" name="phone">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col">
                                <label for="edit-email">Correo electrónico</label>
                                <input type="email" class="form-control" id="edit-email" name="email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="edit-event">Evento</label>
                                <select class="form-control select2" id="edit-event" name="event_id" style="width:100%">
                                    <option value="">Seleccione un evento</option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="edit-category">Categoría</label>
                                <select class="form-control" id="edit-category" name="category_id">
                                    <option value="" disabled>Seleccione una categoría</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Eliminar</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("admin/inscritos/delete") ?>" id="formPago" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <p>Estas seguro de eliminar la inscripción de : <span class="text-danger"
                                        id="text-inscription"></span> con el precio de : <strong>$</strong><strong id="text-category"></strong></p>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id_inscription">
                        <input type="hidden" name="id_event" id="id_event">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button form="formPago" type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select 2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    bindEditButtonEvents();
    bindDeleteButtonEvents();
    $(document).ready(function () {
        // Vincular los eventos de los botones de editar
        bindEditButtonEvents();

        // Vincular los eventos de los botones de eliminar
        bindDeleteButtonEvents();

        // Configuración del select2 para el evento
        $('#edit-event').select2({
            ajax: {
                url: '<?= base_url('admin/event/get_event') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // Término de búsqueda
                    };
                },
                processResults: function (data) {
                    return {
                        results: data // Retornar los datos como están
                    };
                },
                cache: true
            },
            minimumInputLength: 1, // Requiere al menos un carácter para buscar
            placeholder: 'Buscar evento...', // Mensaje de placeholder
            allowClear: true, // Permite limpiar la selección
            language: {
                inputTooShort: function () {
                    return "Escriba al menos un carácter para buscar"; // Mensaje personalizado
                },
                noResults: function () {
                    return "No se encontró ningún evento con ese nombre"; // Mensaje cuando no hay resultados
                },
                searching: function () {
                    return "Buscando eventos..."; // Mensaje de búsqueda
                }
            }
        }).on('select2:select', function (e) {
            var eventId = e.params.data.id;

            // Cargar las categorías asociadas al evento seleccionado
            loadCategoriesForEvent(eventId, null); // No hay categoría seleccionada aún
        });
    });

    // Función para vincular los eventos del botón de edición
    function bindEditButtonEvents() {
        $('.btn-edit').off('click').on('click', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var address = $(this).data('address');
            var phone = $(this).data('phone');
            var email = $(this).data('email');
            var ic = $(this).data('ic');
            var eventId = $(this).data('event-id');
            var categoryId = $(this).data('category-id');

            // Setear los campos de formulario con los datos de la inscripción
            $('#edit-id').val(id);
            $('#edit-name').val(name);
            $('#edit-address').val(address);
            $('#edit-phone').val(phone);
            $('#edit-email').val(email);
            $('#edit-ic').val(ic);

            // Cargar el evento seleccionado en el select2
            var $eventSelect = $('#edit-event');
            $.ajax({
                url: '<?= base_url('admin/event/get_event') ?>',
                dataType: 'json',
                success: function (data) {
                    // Vaciar el select y cargar los eventos
                    $eventSelect.empty();
                    data.forEach(function (event) {
                        var selected = (event.id == eventId) ? 'selected' : '';
                        $eventSelect.append('<option value="' + event.id + '" ' + selected + '>' + event.text + '</option>');
                    });

                    // Actualizar el select2 y cargar las categorías asociadas al evento
                    $eventSelect.trigger('change');
                    loadCategoriesForEvent(eventId, categoryId); // Cargar categorías
                }
            });
        });
    }

    // Función para cargar las categorías de un evento específico
    function loadCategoriesForEvent(eventId, selectedCategoryId) {
        var $categorySelect = $('#edit-category');
        $.ajax({
            url: '<?= base_url('admin/event/get_categories_by_event') ?>/' + eventId,
            dataType: 'json',
            success: function (data) {
                $categorySelect.empty(); // Limpiar las opciones previas
                $categorySelect.append('<option value="" disabled>Seleccione una categoría</option>');

                // Agregar las categorías al select
                data.forEach(function (category) {
                    var selected = (category.id == selectedCategoryId) ? 'selected' : '';
                    $categorySelect.append('<option value="' + category.id + '" ' + selected + '>' + category.text + " - " + category.precio + '</option>');
                });

                // Actualizar el select de categorías
                $categorySelect.trigger('change');
            }
        });
    }

    // Función para vincular los eventos del botón de eliminar
    function bindDeleteButtonEvents() {
        $('.btn-delete').on('click', function () {
            var inscriptionName = $(this).data('inscription-name');
            var categoryName = $(this).data('inscription-category');
            var id = $(this).data('inscription-id');
            var eventId = $(this).data('id-event');

            // Setear los datos en el modal de eliminación
            $('#text-inscription').text(inscriptionName);
            $('#text-category').text(categoryName);
            $('#id_inscription').val(id);
            $('#id_event').val(eventId);
        });
    }

</script>

<?= $this->endSection() ?>