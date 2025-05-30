<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Editar evento
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<!-- Select 2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- dropify -->
<link rel="stylesheet" href="<?= base_url("dist/plugins/dropify/dropify.min.css"); ?>">

<!-- select -->
<link rel="stylesheet" href="<?= base_url('assets/css/select.css') ?>">
<!-- select -->
<link rel="stylesheet" href="<?= base_url('assets/css/rounded.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper bg-white">
    <div class="content-header sty-one shadow">
        <h1 class="text-black">Editar evento</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Eventos</li>
            <li><i class="fa fa-angle-right"></i> Editar evento</li>
        </ol>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gray">


                        <h5 class="pull-left text-dark m-b-0">Actualizar el evento</h5>
                        <div class="pull-right">
                            <div class="row">
                                <div class="col mt-2">

                                    <span id="eventStatusLabel"
                                        class="<?= isset($event['event_status']) && $event['event_status'] === 'Activo' ? 'label-success' : 'label-danger' ?> p-1 rounded-5 mt-2"><?= $event['event_status'] ?></span>
                                </div>
                                <div class="col">

                                    <label class="switch">
                                        <input id="switch-onText" type="checkbox" <?= isset($event['event_status']) && $event['event_status'] === 'Activo' ? 'checked' : '' ?>>
                                        <span class="slider round"><span class="switch-text"></span></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <style>
                            .switch-container {
                                display: flex;
                                align-items: center;
                            }

                            .switch {
                                position: relative;
                                display: inline-block;
                                width: 60px;
                                height: 34px;
                            }

                            .switch input {
                                opacity: 0;
                                width: 0;
                                height: 0;
                            }

                            .slider {
                                position: absolute;
                                cursor: pointer;
                                top: 0;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                background-color: red;
                                transition: .4s;
                            }

                            .slider:before {
                                position: absolute;
                                content: "";
                                height: 26px;
                                width: 26px;
                                left: 4px;
                                bottom: 4px;
                                background-color: white;
                                transition: .4s;
                            }

                            input:checked+.slider {
                                background-color: green;
                            }

                            input:checked+.slider:before {
                                transform: translateX(26px);
                            }

                            .slider.round {
                                border-radius: 34px;
                            }

                            .slider.round:before {
                                border-radius: 50%;
                            }

                            .switch-status {
                                margin-left: 10px;
                            }
                        </style>

                    </div>
                    <div class="card-body">

                        <form action="<?= base_url("admin/event/update") ?>" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $event['id'] ?>">
                            <!-- Campo oculto para el estado del evento -->
                            <input type="hidden" name="event_status" id="hiddenEventStatus" value="">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Nombre del evento</label>
                                        <input class="form-control" name="event_name" placeholder=""
                                            value="<?= isset($last_data) ? display_data($last_data, 'event_name') : (isset($event) ? $event['event_name'] : '') ?>"
                                            type="text" required>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'event_name') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Fecha del evento</label>
                                        <input class="form-control" name="event_date" placeholder="" type="date"
                                            value="<?= isset($last_data) ? display_data($last_data, 'event_date') : (isset($event) ? $event['event_date'] : '') ?>"
                                            required>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'event_date') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Modalidad</label>
                                        <select class="form-select" name="modality" id="modality" required>
                                            <option value="" disabled <?= !isset($event) ? 'selected' : '' ?>>Seleccione
                                                la modalidad del evento</option>
                                            <option value="Presencial" <?= (isset($last_data) && $last_data['modality'] == 'Presencial') || (isset($event) && $event['modality'] == 'Presencial') ? 'selected' : '' ?>>Presencial
                                            </option>
                                            <option value="Virtual" <?= (isset($last_data) && $last_data['modality'] == 'Virtual') || (isset($event) && $event['modality'] == 'Virtual') ? 'selected' : '' ?>>Virtual</option>
                                            <option value="Hibrida" <?= (isset($last_data) && $last_data['modality'] == 'Hibrida') || (isset($event) && $event['modality'] == 'Hibrida') ? 'selected' : '' ?>>Híbrida</option>
                                        </select>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'modality') : '' ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-4"></div>
                                <div class="col-md-4"></div>
                                <!-- Nuevo campo para la dirección del evento -->
                                <div class="col-md-4" id="event-duration-container" style="display: none;">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Duración del evento</label>
                                        <input class="form-control" name="event_duration" id="event_duration"
                                            type="number"
                                            value="<?= isset($last_data) ? display_data($last_data, 'event_duration') : (isset($event) ? $event['event_duration'] : '') ?>">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'event_duration') : '' ?></span>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Autor del evento</label>
                                        <select id="author_id" class="form-control" name="author_id" required>
                                            <option value="" disabled <?= (!isset($last_data) && !isset($event)) ? 'selected' : '' ?>>Seleccione un autor</option>
                                            <?php foreach ($authors as $author): ?>
                                                <option value="<?= $author['id'] ?>" <?= ((isset($last_data) && $last_data['author_id'] == $author['id']) ||
                                                      (isset($event) && $event['author_id'] == $author['id'])
                                                  ) ? 'selected' : '' ?>>
                                                    <?= $author['name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <span class="text-danger">
                                            <?= isset($validation) ? display_data($validation, 'author_id') : '' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Dirección del evento</label>
                                        <input class="form-control" name="address" placeholder="" type="text"
                                            value="<?= isset($last_data) ? display_data($last_data, 'address') : (isset($event) ? $event['address'] : '') ?>">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'address') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Categorías del evento</label>
                                        <select id="id_cat" class="form-control select2" name="id_cat[]"
                                            multiple="multiple" style="width: 100%">
                                            <option value="" disabled>Seleccione una categoría</option>
                                            <?php foreach ($categories as $key => $category): ?>
                                                <option value="<?= $category["id"] ?>" <?= isset($event['category_ids']) && in_array($category["id"], explode(',', $event['category_ids'])) ? 'selected' : '' ?>>
                                                    <?= $category["category_name"] ?> -
                                                    $<?= number_format($category["cantidad_dinero"], 2) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'categories') : '' ?></span>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Fecha de inicio de la inscripción</label>
                                        <input class="form-control" name="registrations_start_date"
                                            placeholder="Company" type="date"
                                            value="<?= isset($last_data) ? display_data($last_data, 'registrations_start_date') : (isset($event) ? $event['registrations_start_date'] : '') ?>">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'registrations_start_date') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Fecha de finalización de la inscripción</label>
                                        <input class="form-control" name="registrations_end_date" placeholder=""
                                            type="date"
                                            value="<?= isset($last_data) ? display_data($last_data, 'registrations_end_date') : (isset($event) ? $event['registrations_end_date'] : '') ?>">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'registrations_end_date') : '' ?></span>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Descripción del evento</label>
                                        <textarea class="form-control" name="short_description" id="" rows="10"
                                            placeholder=""><?= isset($last_data) ? display_data($last_data, 'short_description') : (isset($event) ? $event['short_description'] : '') ?></textarea>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'short_description') : '' ?></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <label>Imagen del evento</label>
                                    <input type="file" id="input-file-now form-control" class="dropify" name="image"
                                        accept="image/jpeg, image/png"
                                        data-default-file="<?= isset($event['image']) ? base_url("{$event['image']}") : '' ?>" />
                                    <span
                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'image') : '' ?></span>
                                </div>

                            </div>

                            <div class="row px-3 mt-2">
                                <button type="submit" class="btn btn-warning col">Actualizar evento</button>
                            </div>
                    </div>
                    </form>


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

        // Nuevo script para manejar la visibilidad del campo de hora
        $('#modality').change(function () {
            var selectedModality = $(this).val();
            if (selectedModality === 'Virtual' || selectedModality === 'Hibrida') {
                $('#event-duration-container').show();
            } else {
                $('#event-duration-container').hide();
            }
        });

        // Trigger the change event on page load to set the initial state
        $('#modality').trigger('change');
        var switchOnText = document.getElementById('switch-onText');
        var eventStatusLabel = document.getElementById('eventStatusLabel');
        var hiddenEventStatus = document.getElementById('hiddenEventStatus');

        function updateStatusLabel() {
            if (switchOnText.checked) {
                eventStatusLabel.classList.remove('label-danger');
                eventStatusLabel.classList.add('label-success');
                eventStatusLabel.textContent = 'Activo';
            } else {
                eventStatusLabel.classList.remove('label-success');
                eventStatusLabel.classList.add('label-danger');
                eventStatusLabel.textContent = 'Desactivado';
            }

            // Actualizar el campo oculto para el estado del evento
            hiddenEventStatus.value = switchOnText.checked ? 'Activo' : 'Desactivado';
        }

        // Actualizar el estado al cargar la página
        updateStatusLabel();

        // Escuchar cambios en el checkbox
        switchOnText.addEventListener('change', updateStatusLabel);
        $('.select2').select2();

    });
</script>

<?= $this->endSection() ?>