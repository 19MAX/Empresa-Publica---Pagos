<?= $this->extend('layouts/proservi_layout'); ?>

<?= $this->section('title') ?>
Reporte de inscripciones
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url("assets/css/rounded.css") ?>">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black">Reporte de inscripciones</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Reporte de inscripciones</li>
        </ol>
    </div>

    <div class="content">
        <!-- Filtros -->
        <div class="info-box mb-3">
            <h4 class="mb-3">Filtros de búsqueda</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_event">Evento</label>
                        <select id="filter_event" class="form-control select2" style="width: 100%;">
                            <option value="">Todos los eventos</option>
                            <?php foreach ($allowedEvents as $event): ?>
                                <option value="<?= $event['id'] ?>"><?= $event['event_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_category">Categoría</label>
                        <select id="filter_category" class="form-control select2" style="width: 100%;" disabled>
                            <option value="">Todas las categorías</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_status">Estado de pago</label>
                        <select id="filter_status" class="form-control" style="width: 100%;">
                            <option value="">Todos los estados</option>
                            <option value="<?= PaymentStatus::Completado ?>">Completado</option>
                            <option value="<?= PaymentStatus::Pendiente ?>">Pendiente</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- <button type="button" id="btn_filter" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Filtrar
                    </button> -->
                    <button type="button" id="btn_clear_filters" class="btn btn-dark">
                        <i class="fa fa-times"></i> Limpiar filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="info-box">
            <div class="table-responsive">
                <table id="proserviUeb" class="table datatable table-striped table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th class="exclude-view">Código</th>
                            <th>Cédula</th>
                            <th>Participante</th>
                            <th class="exclude-view">Teléfono</th>
                            <th class="exclude-view">Correo</th>
                            <th class="exclude-view">Dirección</th>
                            <th>Evento</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th class="exclude-view">Monto</th>
                            <th>Método de pago</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Inicializar Select2
        $('.select2').select2();

        // Configuración de botones de exportación
        var exportButtonTypes = ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'];

        exportButtonTypes.forEach(function (btnType) {
            if ($.fn.dataTable.ext.buttons[btnType]) {
                $.fn.dataTable.ext.buttons[btnType] = $.extend(true, {}, $.fn.dataTable.ext.buttons[btnType], {
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return $(node).is(':visible') && !$(node).hasClass('exclude-column');
                        }
                    }
                });
            }
        });

        // Inicializar DataTable con AJAX
        var proserviTable = $('#proserviUeb').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: base_url + 'proservi/reportes/ajax',
                type: 'POST',
                data: function (d) {
                    // Agregar filtros personalizados
                    d.event_id = $('#filter_event').val();
                    d.category_name = $('#filter_category').val();
                    d.payment_status = $('#filter_status').val();
                },
                error: function (xhr, error, thrown) {
                    console.error('Error en la petición AJAX:', error);
                    alert('Error al cargar los datos. Por favor, intente nuevamente.');
                }
            },
            columns: [
                { data: 'codigo' },
                { data: 'participante_cedula' },
                { data: 'participante_name' },
                { data: 'participante_telefono' },
                { data: 'participante_email' },
                { data: 'participante_direccion' },
                { data: 'event_name' },
                { data: 'precio' },
                { data: 'estado' },
                {
                    data: 'amount_pay',
                    render: function (data, type, row) {
                        return '$' + data;
                    }
                },
                { data: 'method_pago' },
                // { data: 'date_time_payment' },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            columnDefs: [
                { targets: 'exclude-view', visible: false },
                { targets: 'exclude-column', className: 'exclude-column' }
            ],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "No hay datos disponibles en la tabla",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: '<i class="fa fa-search" aria-hidden="true"></i>',
                searchPlaceholder: "Buscar...",
                paginate: {
                    first: "Primero",
                    previous: "<",
                    next: ">",
                    last: "Último"
                },
                buttons: {
                    pageLength: {
                        _: "Mostrar %d registros"
                    }
                }
            },
            responsive: false,
            autoWidth: false,
            dom: "Bfrtip",
            lengthMenu: [10, 25, 50, 100, 1000, 5000],
            pageLength: 10,
            buttons: [
                {
                    extend: "pageLength",
                    className: "bg-secondary text-white"
                },

                {
                    extend: 'collection',
                    text: '<i class="fa fa-download"></i> Exportar',
                    className: "bg-danger text-white",
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            text: '<i class="fa fa-files-o text-info"></i> Copiar',
                            titleAttr: 'Copiar'
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fa fa-file-excel-o text-success"></i> Excel',
                            titleAttr: 'Excel'
                        },
                        {
                            extend: 'csvHtml5',
                            text: '<i class="fa fa-file-text-o text-primary"></i> CSV',
                            titleAttr: 'CSV'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fa fa-file-pdf-o text-red"></i> PDF',
                            titleAttr: 'PDF'
                        },
                    ]
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-plus" aria-hidden="true"></i> Ver más',
                    titleAttr: 'Ver más',
                    columnText: function (dt, idx, title) {
                        return (idx) + ': ' + title;
                    },
                    className: "btn btn-outline-success bg-success",
                },
            ],
            order: [[9, 'desc']], // Ordenar por fecha descendente
            initComplete: function (settings, json) {
                $('.js-mytooltip').myTooltip();
            },
            drawCallback: function (settings) {
                $('.js-mytooltip').myTooltip('destroy');
                $('.js-mytooltip').myTooltip();
            }
        });

        // Recargar tabla automáticamente al cambiar los filtros
        $('#filter_event, #filter_category, #filter_status').on('change', function () {
            proserviTable.ajax.reload();
        });


        // Evento al cambiar el evento seleccionado
        $('#filter_event').on('change', function () {
            var eventId = $(this).val();
            var $categorySelect = $('#filter_category');

            // Limpiar categorías
            $categorySelect.empty().append('<option value="">Todas las categorías</option>');

            if (eventId) {
                // Habilitar select de categorías
                $categorySelect.prop('disabled', true);

                // Cargar categorías del evento seleccionado
                $.ajax({
                    url: base_url + 'proservi/reportes/categories',
                    type: 'POST',
                    data: { event_id: eventId },
                    dataType: 'json',
                    success: function (categories) {
                        if (categories.length > 0) {
                            $.each(categories, function (index, category) {
                                $categorySelect.append(
                                    $('<option></option>')
                                        .val(category.id)
                                        .text(category.category_name + ' - $' + parseFloat(category.cantidad_dinero).toFixed(2)));
                            });
                            $categorySelect.prop('disabled', false);
                        }

                        proserviTable.ajax.reload();
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Error al cargar categorías:', error);
                    }
                });
            } else {
                $categorySelect.prop('disabled', true);
            }
        });

        // Botón filtrar
        // $('#btn_filter').on('click', function () {
        //     proserviTable.ajax.reload();
        // });

        // Botón limpiar filtros
        $('#btn_clear_filters').on('click', function () {
            $('#filter_event').val('').trigger('change');
            $('#filter_category').val('').trigger('change').prop('disabled', true);
            $('#filter_status').val('').trigger('change');
            proserviTable.ajax.reload();
        });
    });
</script>
<?= $this->endSection() ?>