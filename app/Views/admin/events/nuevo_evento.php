<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Agregar eventos
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<!-- Select 2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- select -->
<link rel="stylesheet" href="<?= base_url('assets/css/select.css') ?>">
<!-- dropify -->
<link rel="stylesheet" href="<?= base_url("dist/plugins/dropify/dropify.min.css"); ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper bg-white">
    <div class="content-header sty-one shadow">
        <h1 class="text-black">Nuevo evento</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Eventos</li>
            <li><i class="fa fa-angle-right"></i> Nuevo evento</li>
        </ol>
    </div>


    <!-- Main content -->

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gray ">
                        <h5 class="text-dark m-b-0">Agregar un evento</h5>
                    </div>
                    <div class="card-body">

                        <form action="<?= base_url("admin/event/add") ?>" method="POST" enctype="multipart/form-data">
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Nombre del evento</label>
                                        <input class="form-control" name="event_name" placeholder=""
                                            value="<?= isset($last_data) ? display_data($last_data, 'event_name') : '' ?>"
                                            type="text" required>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'event_name') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Fecha del evento</label>
                                        <input class="form-control" name="event_date" placeholder="" type="date"
                                            value="<?= isset($last_data) ? display_data($last_data, 'event_date') : '' ?>"
                                            required>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'event_date') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Modalidad</label>
                                        <select class="form-select" name="modality" id="modality" required>
                                            <option value="" disabled selected>Seleccione la modalidad del evento
                                            </option>
                                            <option value="Presencial" <?= isset($last_data) && $last_data['modality'] == 'Presencial' ? 'selected' : '' ?>>Presencial
                                            </option>
                                            <option value="Virtual" <?= isset($last_data) && $last_data['modality'] == 'Virtual' ? 'selected' : '' ?>>Virtual</option>
                                            <option value="Hibrida" <?= isset($last_data) && $last_data['modality'] == 'Hibrida' ? 'selected' : '' ?>>Híbrida</option>
                                        </select>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'modality') : '' ?></span>
                                    </div>
                                </div>

                                <div class="col-md-4"></div>
                                <div class="col-md-4"></div>
                                <!-- Nuevo campo para la dirección del evento -->
                                <div class="col-md-4" id="event-duration-container" style="display: none;">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Duración del evento</label>
                                        <input class="form-control" name="event_duration" id="event_duration"
                                            type="number"
                                            value="<?= isset($last_data) ? display_data($last_data, 'event_duration') : '' ?>">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'event_duration') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Dirección del evento</label>
                                        <input class="form-control" name="address" placeholder="" type="text"
                                            value="<?= isset($last_data) ? display_data($last_data, 'address') : '' ?>">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'address') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Categorías del evento</label>

                                        <select id="id_cat" class="form-control select2" name="id_cat[]"
                                            multiple="multiple" style="width: 100%">
                                            <option value="" disabled>Seleccione una categoría</option>
                                            <?php foreach ($categories as $key => $category): ?>
                                                <option value="<?= $category["id"] ?>" <?= isset($last_data['categories']) && in_array($category["id"], $last_data['categories']) ? 'selected' : '' ?>>
                                                    <?= $category["category_name"] ?> -
                                                    $<?= number_format($category["cantidad_dinero"], 2) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'categories') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Fecha de inicio de la inscripción</label>
                                        <input class="form-control" name="registrations_start_date"
                                            placeholder="Company" type="date"
                                            value="<?= isset($last_data) ? display_data($last_data, 'registrations_start_date') : '' ?>">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'registrations_start_date') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Fecha de finalización de la inscripción</label>
                                        <input class="form-control" name="registrations_end_date" placeholder=""
                                            type="date"
                                            value="<?= isset($last_data) ? display_data($last_data, 'registrations_end_date') : '' ?>">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'registrations_end_date') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Descripción del evento</label>
                                        <textarea class="form-control" name="short_description" id="" rows="10"
                                            placeholder=""><?= isset($last_data) ? display_data($last_data, 'short_description') : '' ?></textarea>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'short_description') : '' ?></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <label>Imagen del evento</label>
                                    <input type="file" id="input-file-now form-control" class="dropify" name="image"
                                        accept="image/jpeg, image/png"
                                        value="<?= isset($last_data) ? display_data($last_data, 'image') : '' ?>" />
                                    <span
                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'image') : '' ?></span>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success">Agregar evento</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<!-- Select 2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- dropify -->
<script src="<?= base_url("dist/plugins/dropify/dropify.min.js") ?>"></script>
<script>
    $(document).ready(function () {
        // Basic
        $('.dropify').dropify();

        // Translated
        $('.dropify-fr').dropify({
            messages: {
                default: 'Glissez-déposez un fichier ici ou cliquez',
                replace: 'Glissez-déposez un fichier ou cliquez pour remplacer',
                remove: 'Supprimer',
                error: 'Désolé, le fichier trop volumineux'
            }
        });

        // Used events
        var drEvent = $('#input-file-events').dropify();

        drEvent.on('dropify.beforeClear', function (event, element) {
            return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
        });

        drEvent.on('dropify.afterClear', function (event, element) {
            alert('File deleted');
        });

        drEvent.on('dropify.errors', function (event, element) {
            console.log('Has Errors');
        });

        var drDestroy = $('#input-file-to-destroy').dropify();
        drDestroy = drDestroy.data('dropify')
        $('#toggleDropify').on('click', function (e) {
            e.preventDefault();
            if (drDestroy.isDropified()) {
                drDestroy.destroy();
            } else {
                drDestroy.init();
            }
        })

        // Manejo de la visibilidad y limpieza del campo de duración del evento
        $('#modality').change(function () {
            var selectedModality = $(this).val();
            if (selectedModality === 'Virtual' || selectedModality === 'Hibrida') {
                $('#event-duration-container').show();
            } else {
                $('#event-duration-container').hide();
                $('#event_duration').val(''); // Limpiar el campo de duración del evento
            }
        });

        // Trigger the change event on page load to set the initial state
        $('#modality').trigger('change');

        $('.select2').select2();
    });
</script>

<?= $this->endSection() ?>