<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Categorías
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black">Categorías</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i>Categorías de eventos</li>
        </ol>
    </div>

    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="category" class="table datatable table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre de la categoría</th>
                            <th>Valor por la categoría</th>
                            <th>Descripción de la categoría</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $key => $category): ?>
                            <tr>
                                <td><?= $category["category_name"] ?></td>
                                <td><?= $category["cantidad_dinero"] ?></td>
                                <td><?= $category["short_description"] ?></td>
                                <td>

                                    <button class="js-mytooltip btn btn-outline-warning m-1 btn-update" data-toggle="modal"
                                        href="#update" data-mytooltip-custom-class="align-center"
                                        data-mytooltip-direction="top" data-mytooltip-theme="warning"
                                        data-mytooltip-content="Editar"
                                        data-category-name="<?= $category['category_name'] ?>"
                                        data-category-value="<?= $category['cantidad_dinero'] ?>"
                                        data-category-description="<?= $category['short_description'] ?>"
                                        data-category-id="<?= $category['id'] ?>">
                                        <i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
                                    </button>

                                    <button class="js-mytooltip btn btn-outline-danger m-1 btn-delete" data-toggle="modal"
                                        href="#delete" data-mytooltip-custom-class="align-center"
                                        data-mytooltip-direction="top" data-mytooltip-theme="danger"
                                        data-mytooltip-content="Eliminar"
                                        data-category-name="<?= $category['category_name'] ?>"
                                        data-category-id="<?= $category['id'] ?>">
                                        <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal delete-->
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
                <form action="<?= base_url("admin/category/delete") ?>" id="form_delete" method="post">
                    <div class="row mb-3">
                        <div class="col">
                            <p>Estas seguro de eliminar la categoría : <span class="text-danger"
                                    id="text-category"></span></p>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id_cat_delete">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button form="form_delete" type="submit" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="update" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-2">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Editar</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="closeModal('addModal')">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("admin/category/update") ?>" id="form_edit" method="post">
                    <div class="row text-center">
                        <div class="col">
                            <label class="control-label">No aplica el monto de comisión <p class="text-success m-0 p-0">
                                    $<?= $additional_charge ?> USD</p></label>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Nombre de la categoría</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-tasks" aria-hidden="true"></i>
                                </div>

                                <input class="form-control" type="text" name="category_name" id="name_edit"
                                    value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'category_name') : '' ?>">
                            </div>

                            <span class="label m-0 p-0 text-danger">
                                <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'category_name') : '' ?>
                            </span>
                        </div>
                        <div class="col">
                            <label>Valor de la categoría</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i>
                                </div>

                                <input class="form-control" type="text" name="category_value" id="value_edit"
                                    value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'cantidad_dinero') : '' ?>">
                            </div>

                            <span class="label m-0 p-0 text-danger">
                                <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'cantidad_dinero') : '' ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Descripción de la categoría</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-commenting-o" aria-hidden="true"></i>
                                </div>
                                <textarea class="form-control" name="short_description" id="description_edit" rows="3"
                                    placeholder=""><?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'short_description') : '' ?></textarea>

                            </div>

                            <span class="label m-0 p-0 text-danger">
                                <?= (isset($validation) && ($last_action ?? null) == 'update') ? display_data($validation, 'short_description') : '' ?>
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id_category"
                        value="<?= (isset($last_data) && ($last_action ?? null) == 'update') ? display_data($last_data, 'id_category') : '' ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                    onclick="closeModal('update')">Cerrar</button>
                <button form="form_edit" type="submit" class="btn btn-warning">Actualizar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para agregar -->
<div class="modal fade" id="addModal" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-2">
            <div class="modal-header">
                <h4 class="modal-title" id="addModalLabel">Agregar</h4>
                <button type="button" class="close close-modal" data-dismiss="modal" onclick="closeModal('addModal')">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("admin/category/add") ?>" id="formAdd" method="post">
                    <div class="row text-center">
                        <div class="col">
                            <label class="control-label">Aplica el monto de comisión <p class="text-success m-0 p-0">
                                    $<?= $additional_charge ?> USD</p></label>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Nombre de la categoría</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-tasks" aria-hidden="true"></i>
                                </div>
                                <input type="text" name="category_name" class="form-control"
                                    value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'category_name') : '' ?>"
                                    required>
                            </div>

                            <span class="label m-0 p-0 text-danger">
                                <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'category_name') : '' ?>
                            </span>
                        </div>
                        <div class="col">
                            <label>Valor de la categoría</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i>
                                </div>
                                <input type="text" name="category_value" class="form-control"
                                    value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'cantidad_dinero') : '' ?>"
                                    required>
                            </div>

                            <span class="label m-0 p-0 text-danger">
                                <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'cantidad_dinero') : '' ?>
                            </span>
                        </div>
                    </div>
                    <label>Descripción de la categoría</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-commenting-o" aria-hidden="true"></i></div>
                        <textarea name="short_description" id=""
                            class="form-control"><?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'short_description') : '' ?></textarea>
                    </div>

                    <span class="label m-0 p-0 text-danger">
                        <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'short_description') : '' ?>
                    </span>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal"
                    onclick="closeModal('addModal')">Cerrar</button>
                <button form="formAdd" type="submit" class="btn btn-success">Agregar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        $('.btn-delete').on('click', function () {
            // Obtener datos del usuario desde atributos data-*
            let categoryName = $(this).data('category-name');
            let categoryId = $(this).data('category-id');

            // Llenar los campos del modal con los datos del usuario
            $('#text-category').text(categoryName);
            $('#id_cat_delete').val(categoryId);
        });
        $('.btn-update').on('click', function () {
            let categoryId = $(this).data('category-id');
            let catName = $(this).data('category-name');
            let catValue = $(this).data('category-value');
            let catDescription = $(this).data('category-description');

            $('#id_category').val(categoryId);
            $('#name_edit').val(catName);
            $('#value_edit').val(catValue);
            $('#description_edit').val(catDescription);
        });


        // Mostrar el modal de agregar si es necesario
        <?php if ('insert' == ($last_action ?? '')): ?>

            openModal('addModal');


        <?php elseif ('update' == ($last_action ?? '')): ?>

            openModal('update');

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

                        // Limpiar todos los spans dentro del modal, excepto los que están dentro de un botón con clase 'close'
                        let spans = modal.querySelectorAll('span');
                        spans.forEach(function (span) {
                            if (!span.closest('button.close')) {
                                span.textContent = '';
                            }
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
                }

                // Limpiar todos los spans dentro del modal, excepto los que están dentro de un botón con clase 'close'
                var spans = modal.querySelectorAll('span');
                spans.forEach(function (span) {
                    if (!span.closest('button.close')) {
                        span.textContent = '';
                    }
                });
            } else {
                console.error('Modal con ID ' + modalId + ' no encontrado.');
            }
        }

        // Evento para limpiar el formulario y los spans cuando se cierra el modal
        $('.modal').on('hidden.bs.modal', function () {
            clearFormAndSpans(this.id);
        });


        function openModal(modalId) {
            let modal = document.getElementById(modalId);
            if (modal) {
                setTimeout(function () {
                    let modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                }, 200); // Espera de 200ms antes de abrir el modal
            }
        }




    });
    function closeModal(modalId) {
        let modal = document.getElementById(modalId);
        if (modal) {
            console.log('modal obtenido' + modal);
            let modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    }
</script>

<?= $this->endSection() ?>