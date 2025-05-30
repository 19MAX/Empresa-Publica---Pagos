$(document).ready(function () {
    // Lista de tipos de botones de exportación a extender
    var exportButtonTypes = ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'];

    // Iterar sobre cada tipo de botón de exportación y extenderlo
    exportButtonTypes.forEach(function (btnType) {
        if ($.fn.dataTable.ext.buttons[btnType]) {
            // Extender el botón con las opciones de exportación deseadas
            $.fn.dataTable.ext.buttons[btnType] = $.extend(true, {}, $.fn.dataTable.ext.buttons[btnType], {
                exportOptions: {
                    columns: function (idx, data, node) {
                        // Solo exportar las columnas visibles
                        return $(node).is(':visible') && !$(node).hasClass('exclude-column');
                    }
                }
            });
        }
    });

    // Función para inicializar DataTables
    function initializeDataTable(tableId, options) {
        var defaultOptions = {
            columnDefs: [
                { targets: 'exclude-view', visible: false } // Excluye las columnas con clase 'exclude-view'
            ],
            language: {
                buttons: {
                    sLengthMenu: "Mostrar _MENU_ resultados",
                    pageLength: {
                        _: "Mostrar %d resultados",
                    },
                },
                zeroRecords: "No hay coincidencias",
                info: "Mostrando _END_ resultados de _MAX_",
                infoEmpty: "No hay datos disponibles",
                infoFiltered: "(Filtrado de _MAX_ registros totales)",
                search: '<i class="fa fa-search" aria-hidden="true"></i>',
                emptyTable: "No existen registros",
                paginate: {
                    first: "Primero",
                    previous: "<",
                    next: ">",
                    last: "Último",
                },
            },
            responsive: false,
            autoWidth: true,
            dom: "Bfrtip",
            lengthMenu: [10, 25, 50, 100],
            initComplete: function (settings, json) {
                $('.js-mytooltip').myTooltip();
            }
        };

        // Combinar las opciones por defecto con las opciones específicas de la tabla
        var mergedOptions = $.extend(true, {}, defaultOptions, options);

        // Inicializar DataTable
        var table = $(tableId).DataTable(mergedOptions);

        // Re-inicializar tooltips después de cada redibujo de la tabla
        table.on('draw', function () {
            $('.js-mytooltip').myTooltip('destroy');
            $('.js-mytooltip').myTooltip();
        });

        return table;
    }

    // Inicializar la tabla de usuarios
    let usersTable = initializeDataTable("#users", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                text: '<i class="fa fa-lg fas fa-plus-circle"></i>',
                titleAttr: 'Agregar',
                className: 'js-mytooltip bg-success text-white',
                attr: {
                    'data-mytooltip-custom-class': 'align-center',
                    'data-mytooltip-direction': 'top',
                    'data-mytooltip-theme': 'success',
                    'data-mytooltip-content': 'Agregar'
                },
                action: function () {
                    $('#addUserModal').modal('show');
                },
            },
            // {
            //     text: '<i class="fa fa-lg fa-minus-circle" aria-hidden="true"></i>',
            //     titleAttr: 'Eliminados',
            //     className: 'js-mytooltip btn bg-danger',
            //     attr: {
            //         'data-mytooltip-custom-class': 'align-center',
            //         'data-mytooltip-direction': 'top',
            //         'data-mytooltip-theme': 'danger',
            //         'data-mytooltip-content': 'Usuarios eliminados'
            //     },
            //     action: function () {
            //         window.location.href = base_url + 'admin/users/trash';
            //     },
            // }
        ]
    });

    // Inicializar la tabla de eventos
    let eventTable = initializeDataTable("#event", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                extend: 'collection',
                text: '<i class="fa fa-download"></i> Exportar',
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
                    {
                        extend: 'colvis',
                        text: 'Columnas visibles',
                        columnText: function (dt, idx, title) {
                            return (idx) + ': ' + title;
                        },
                        className: "btn btn-outline-success",
                    },
                ]
            },
            {
                text: '<i class="fa fa-lg fas fa-plus-circle"></i>',
                titleAttr: 'Agregar',
                className: 'js-mytooltip bg-success text-white',
                attr: {
                    'data-mytooltip-custom-class': 'align-center',
                    'data-mytooltip-direction': 'top',
                    'data-mytooltip-theme': 'success',
                    'data-mytooltip-content': 'Agregar'
                },
                action: function () {
                    window.location.href = base_url + "admin/event/new";
                },
            },
            {
                text: '<i class="fa fa-lg fa-minus-circle" aria-hidden="true"></i>',
                titleAttr: 'Eliminados',
                className: 'js-mytooltip btn bg-danger',
                attr: {
                    'data-mytooltip-custom-class': 'align-center',
                    'data-mytooltip-direction': 'right',
                    'data-mytooltip-theme': 'danger',
                    'data-mytooltip-content': 'Eventos eliminados'
                },
                action: function () {
                    window.location.href = base_url + 'admin/event/trash';
                },
            }
        ]
    });
    let eventTrashTable = initializeDataTable("#eventTrash", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                extend: 'collection',
                text: '<i class="fa fa-download"></i> Exportar',
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
                    {
                        extend: 'colvis',
                        text: 'Columnas visibles',
                        columnText: function (dt, idx, title) {
                            return (idx) + ': ' + title;
                        },
                        className: "btn btn-outline-success",
                    },
                ]
            },
            {
                text: '<i class="fa fa-lg fa-arrow-left" aria-hidden="true"></i>',
                titleAttr: 'Regresar',
                className: 'js-mytooltip bg-blue',
                attr: {
                    'data-mytooltip-custom-class': 'align-center',
                    'data-mytooltip-direction': 'top',
                    'data-mytooltip-theme': 'primary',
                    'data-mytooltip-content': 'Regresar'
                },
                action: function () {
                    window.location.href = base_url + 'admin/event';
                },
            },
        ]
    });

    //Tabla de autores
    let authorsTable = initializeDataTable("#authors", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
        ]
    });

    let inscripcionesTrashTable = initializeDataTable("#inscripcionesTrash", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                extend: 'collection',
                text: '<i class="fa fa-download"></i> Exportar',
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
                    {
                        extend: 'colvis',
                        text: 'Columnas visibles',
                        columnText: function (dt, idx, title) {
                            return (idx) + ': ' + title;
                        },
                        className: "btn btn-outline-success",
                    },
                ]
            }
        ]
    });

    let mis_cobros = initializeDataTable("#mis_cobros", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                extend: 'collection',
                text: '<i class="fa fa-download"></i> Exportar',
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
                    {
                        extend: 'colvis',
                        text: 'Columnas visibles',
                        columnText: function (dt, idx, title) {
                            return (idx) + ': ' + title;
                        },
                        className: "btn btn-outline-success",
                    },
                ]
            }
        ]
    });

    // Inicializar la tabla de categorías
    let categoryTable = initializeDataTable("#category", {
        responsive: true,
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                extend: 'collection',
                text: '<i class="fa fa-download"></i> Exportar',
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
                    }
                ]
            },
            {
                text: '<i class="fa fa-lg fas fa-plus-circle"></i>',
                titleAttr: 'Agregar',
                className: 'js-mytooltip bg-success text-white',
                attr: {
                    'data-mytooltip-custom-class': 'align-center',
                    'data-mytooltip-direction': 'top',
                    'data-mytooltip-theme': 'success',
                    'data-mytooltip-content': 'Agregar'
                },
                action: function () {
                    $('#addModal').modal('show');
                },
            }
        ],
    });

    // Inicializar la tabla de las inscripciones en un evento
    let inscritosTable = initializeDataTable("#eventInscriptions", {
        responsive: true,
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                extend: 'collection',
                text: '<i class="fa fa-download"></i> Exportar',
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
                    {
                        extend: 'colvis',
                        text: 'Columnas visibles',
                        columnText: function (dt, idx, title) {
                            return (idx) + ': ' + title;
                        },
                        className: "btn btn-outline-success",
                    },
                ]
            },
            // {
            //     text: '<i class="fa fa-lg fas fa-plus-circle"></i>',
            //     titleAttr: 'Agregar',
            //     className: 'js-mytooltip bg-success text-white',
            //     attr: {
            //         'data-mytooltip-custom-class': 'align-center',
            //         'data-mytooltip-direction': 'top',
            //         'data-mytooltip-theme': 'success',
            //         'data-mytooltip-content': 'Agregar'
            //     },
            //     action: function () {
            //         window.location.href = base_url + "admin/event/new";
            //     },
            // },
            // {
            //     text: '<i class="fa fa-lg fa-minus-circle" aria-hidden="true"></i>',
            //     titleAttr: 'Eliminados',
            //     className: 'js-mytooltip btn bg-danger',
            //     attr: {
            //         'data-mytooltip-custom-class': 'align-center',
            //         'data-mytooltip-direction': 'right',
            //         'data-mytooltip-theme': 'danger',
            //         'data-mytooltip-content': 'Eventos eliminados'
            //     },
            //     action: function () {
            //         window.location.href = base_url + 'admin/event/trash';
            //     },
            // }
        ]
    });

    // Inicializar la tabla de inscripciones
    let inscripciones = initializeDataTable("#inscripciones", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                text: '<i class="fa fa-lg fas fa-plus-circle"></i>',
                titleAttr: 'Agregar',
                className: 'js-mytooltip bg-success text-white',
                attr: {
                    'data-mytooltip-custom-class': 'align-center',
                    'data-mytooltip-direction': 'top',
                    'data-mytooltip-theme': 'success',
                    'data-mytooltip-content': 'Agregar'
                },
                action: function () {
                    $('#addUserModal').modal('show');
                },
            },
            // {
            //     text: '<i class="fa fa-lg fa-minus-circle" aria-hidden="true"></i>',
            //     titleAttr: 'Eliminados',
            //     className: 'js-mytooltip btn bg-danger',
            //     attr: {
            //         'data-mytooltip-custom-class': 'align-center',
            //         'data-mytooltip-direction': 'top',
            //         'data-mytooltip-theme': 'danger',
            //         'data-mytooltip-content': 'Inscripciones eliminados'
            //     },
            //     action: function () {
            //         window.location.href = base_url + 'admin/users/trash';
            //     },
            // }
        ]
    });

    // Inicializar todas las tablas de pagos
    let pagosDepPendientes = initializeDataTable("#pagos", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                extend: 'collection',
                text: '<i class="fa fa-download"></i>',
                titleAttr: 'Exportar',
                className: 'js-mytooltip bg-success',
                attr: {
                    'data-mytooltip-custom-class': 'align-center',
                    'data-mytooltip-direction': 'top',
                    'data-mytooltip-theme': 'success',
                    'data-mytooltip-content': 'Exportar'
                },
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
                text: '<i class="fa fa-columns" aria-hidden="true"></i>',
                titleAttr: 'Columnas visibles',
                className: 'js-mytooltip bg-info',
                attr: {
                    'data-mytooltip-custom-class': 'align-center',
                    'data-mytooltip-direction': 'top',
                    'data-mytooltip-theme': 'info',
                    'data-mytooltip-content': 'Columnas visibles'
                },
                columnText: function (dt, idx, title) {
                    return (idx) + ': ' + title;
                },
            },
        ]
    });

    // Inicializar todas las tablas de pagos
    let depositos = initializeDataTable("#depositos", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            // {
            //     extend: 'collection',
            //     text: '<i class="fa fa-download"></i>',
            //     titleAttr: 'Exportar',
            //     className: 'js-mytooltip bg-success',
            //     attr: {
            //         'data-mytooltip-custom-class': 'align-center',
            //         'data-mytooltip-direction': 'top',
            //         'data-mytooltip-theme': 'success',
            //         'data-mytooltip-content': 'Exportar'
            //     },
            //     buttons: [
            //         {
            //             extend: 'copyHtml5',
            //             text: '<i class="fa fa-files-o text-info"></i> Copiar',
            //             titleAttr: 'Copiar'
            //         },
            //         {
            //             extend: 'excelHtml5',
            //             text: '<i class="fa fa-file-excel-o text-success"></i> Excel',
            //             titleAttr: 'Excel'
            //         },
            //         {
            //             extend: 'csvHtml5',
            //             text: '<i class="fa fa-file-text-o text-primary"></i> CSV',
            //             titleAttr: 'CSV'
            //         },
            //         {
            //             extend: 'pdfHtml5',
            //             text: '<i class="fa fa-file-pdf-o text-red"></i> PDF',
            //             titleAttr: 'PDF'
            //         },
            //     ]
            // },

            {
                extend: 'colvis',
                text: '<i class="fa fa-columns" aria-hidden="true"></i>',
                titleAttr: 'Columnas visibles',
                className: 'js-mytooltip bg-info',
                attr: {
                    'data-mytooltip-custom-class': 'align-center',
                    'data-mytooltip-direction': 'top',
                    'data-mytooltip-theme': 'info',
                    'data-mytooltip-content': 'Columnas visibles'
                },
                columnText: function (dt, idx, title) {
                    return (idx) + ': ' + title;
                },
            },
        ]
    });


    let proservi = initializeDataTable("#proservi", {
        buttons: [
            {
                extend: "pageLength",
                className: "bg-secondary text-white",
            },
            {
                extend: 'collection',
                text: '<i class="fa fa-download"></i> Exportar',
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
        ]
    });
});