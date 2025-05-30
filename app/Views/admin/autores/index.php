<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Autores
<?= $this->endSection() ?>


<?= $this->section('css') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black">Autores</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Autores</li>
            <li><i class="fa fa-angle-right"></i> Lista</li>
        </ol>
    </div>
    <div class="content">

        <div class="row">
            <div class="col-md-6">

                <div class="info-box">
                    <div class="table-responsive">
                        <table id="authors" class="table datatable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Autor</th>
                                    <th class="exclude-view">Descripción del autor</th>
                                    <th class="exclude-column">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($authors as $key => $author): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <div class="avatar-wrapper mr-3">
                                                    <div class="avatar rounded-2">
                                                        <img src="<?= base_url("") . $author["img"] ?>" alt="Img"
                                                            class="rounded-2 img-fluid"
                                                            style="width:60px; height:40px; object-fit: cover;">
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="text-heading fw-medium event-name"><?= $author["name"] ?></span>
                                                    <small class="text-truncate d-none d-sm-block">
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= $author["description"] ?></td>
                                        <td>
                                            <button class="js-mytooltip btn btn-outline-warning m-1 btn-edit-author"
                                                data-id="<?= $author['id'] ?>"
                                                data-name="<?= htmlspecialchars($author['name']) ?>"
                                                data-description="<?= htmlspecialchars($author['description']) ?>"
                                                title="Editar" data-mytooltip-custom-class="align-center"
                                                data-mytooltip-direction="top" data-mytooltip-theme="warning"
                                                data-mytooltip-content="Editar">
                                                <i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
                                            </button>



                                            <button class="js-mytooltip btn btn-outline-danger btn-delete m-1"
                                                title="Eliminar" data-toggle="modal" data-target="#delete"
                                                data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                                data-mytooltip-theme="danger" data-mytooltip-content="Eliminar"
                                                data-author-name="<?= $author['name'] ?>"
                                                data-author-id="<?= $author['id'] ?>">
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
            <div class="col-md-6">

                <div class="info-box">
                    <form id="author-form" action="<?= base_url('admin/autores/add') ?>" method="post"
                        enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="author_id" id="author_id">

                        <div class="form-group">
                            <label for="name">Nombre del autor</label>
                            <input type="text" name="name" id="name" class="form-control" required
                                placeholder="Nombre de la empresa o autor" value="<?= isset($last_data) ? display_data($last_data, 'name') : '' ?>">
                            <span
                                class="text-danger"><?= isset($validation) ? display_data($validation, 'name') : '' ?></span>
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea name="description" id="description" class="form-control" rows="4"
                                placeholder="Breve descripción del autor"><?= isset($last_data) ? display_data($last_data, 'description') : '' ?></textarea>
                            <span
                                class="text-danger"><?= isset($validation) ? display_data($validation, 'description') : '' ?></span>
                        </div>

                        <div class="form-group">
                            <label for="image">Imagen del autor</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <span
                                class="text-danger"><?= isset($validation) ? display_data($validation, 'image') : '' ?></span>
                        </div>

                        <div class="form-group text-right mt-3">
                            <button type="submit" class="btn btn-primary" id="submit-button">Crear Autor</button>
                        </div>
                    </form>
                </div>


            </div>

        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel">¿Eliminar autor?</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar al autor <strong id="delete-author-name"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>



<?= $this->section('scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("author-form");
        const nameField = document.getElementById("name");
        const descriptionField = document.getElementById("description");
        const idField = document.getElementById("author_id");
        const submitButton = document.getElementById("submit-button");

        // EDITAR
        document.querySelectorAll(".btn-edit-author").forEach(button => {
            button.addEventListener("click", () => {
                const id = button.dataset.id;
                const name = button.dataset.name;
                const description = button.dataset.description;

                nameField.value = name;
                descriptionField.value = description;
                idField.value = id;
                submitButton.textContent = "Actualizar Autor";

                form.action = "<?= base_url('admin/autores/update') ?>/" + id;
            });
        });

        // ELIMINAR
        document.querySelectorAll(".btn-delete").forEach(button => {
            button.addEventListener("click", () => {
                const authorName = button.dataset.authorName;
                const authorId = button.dataset.authorId;

                document.getElementById("delete-author-name").textContent = authorName;
                document.getElementById("deleteForm").action = "<?= base_url('admin/autores/delete') ?>/" + authorId;
            });
        });
    });
</script>

<?= $this->endSection() ?>