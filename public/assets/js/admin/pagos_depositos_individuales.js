document.addEventListener("DOMContentLoaded", async function () {
    idRuta = idRuta;
    var zoomLevel = 1;
    var zoomStep = 0.1;
    var minZoom = 0.5;
    var maxZoom = 2;

    var defaultImageUrl = `${base_url}assets/images/dep_defecto.png`; // Imagen por defecto

    var table = $('#depositosTable').DataTable({
        paging: false,
        info: false,
        searching: true,
        lengthChange: false,
        ordering: false,
        language: {
            search: "Buscar:"
        },
        columns: [
            { data: 'num_comprobante' },
            { data: 'monto_deposito' },
            { data: 'date_deposito' },
            { data: 'status' },
            {
                data: 'comprobante_pago',
                visible: false
            }
        ]
    });

    function reloadSection() {
        let id_pago = idRuta;
        try {
            fetch(`${base_url}admin/pagos/getDatosPgDeposito/${id_pago}`).then(
                (response) => {
                    if (!response.ok) {
                        throw new Error(
                            "Hubo un problema al obtener los detalles del campus."
                        );
                    }
                    response.json().then((data) => {
                        table.clear().draw();
                        table.rows.add(data).draw();
                        actualizarMontos();

                        // Cargar la imagen por defecto al recargar la tabla
                        $('#imagen-deposito').attr('src', defaultImageUrl);
                    });
                }
            );
        } catch (error) {
            console.error("Error al obtener los detalles del campus:", error);
        }
    }

    reloadSection();

    $('#depositosTable').on('click', 'td:nth-child(2), td:nth-child(4)', function (e) {
        if (e.detail === 1) {
            var cell = table.cell(this);
            var data = cell.data();

            if (cell.index().column === 3) {
                var $select = $('<select class="form-control  editable-select custom-select"></select>')
                    .append('<option value="Pendiente">Pendiente</option>')
                    .append('<option value="Incompleto">Incompleto</option>')
                    .append('<option value="Aprobado">Aprobado</option>')
                    .append('<option value="Rechazado">Rechazado</option>')
                    .val(data);

                $(this).html($select);
                $select.focus();

                $select.on('change', function () {
                    var newValue = $select.val();
                    confirmUpdate(cell, data, newValue);
                });
            } else {
                var $input = $('<input type="text" class="form-control editable-input">').val(data);

                $(this).html($input);
                $input.focus();

                $input.on('blur', function () {
                    var newValue = $input.val();
                    confirmUpdate(cell, data, newValue);
                });
            }
        }
    });

    function confirmUpdate(cell, oldValue, newValue) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Quieres guardar los cambios?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                cell.data(newValue).draw();
                updateDeposito(cell.index().row, oldValue);
            } else {
                cell.data(oldValue).draw();
            }
        });
    }

    function updateDeposito(rowIndex, oldData) {
        var rowData = table.row(rowIndex).data();
        fetch(`${base_url}admin/pagos/actualizarEstado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_deposito: rowData.id,
                estado: rowData.status,
                monto_deposito: rowData.monto_deposito
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Éxito', data.message, 'success');
                    reloadSection();
                    actualizarMontos();
                } else {
                    let errorMessage = data.message;
                    if (data.errors) {
                        errorMessage += "<br>" + Object.values(data.errors).join("<br>");
                    }
                    Swal.fire('Error', errorMessage, 'error');
                    reloadSection();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema al actualizar el depósito.', 'error');
            });
    }

    $('#depositosTable tbody').on('click', 'tr', function () {
        $('#depositosTable tbody tr').removeClass('selected');
        $(this).addClass('selected');
        var imagenUrl = table.row(this).data().comprobante_pago;
        if (imagenUrl) {
            // Extrae solo el nombre del archivo de la URL (después de "comprobantes/")
            var fileName = imagenUrl.split('/').pop();
            // Construye la URL completa para acceder al comprobante
            $('#imagen-deposito').attr('src', `${base_url}admin/comprobantes/${fileName}`);
        } else {
            $('#imagen-deposito').attr('src', defaultImageUrl);
        }
    });

    function actualizarMontos() {
        var montoTotal = parseFloat($('#precio').val().replace(/,/g, ''));
        var montoPagado = 0;

        $('#depositosTable tbody tr').each(function () {
            var monto = parseFloat($(this).find('td:eq(1)').text().replace(/,/g, ''));
            if (!isNaN(monto)) {
                montoPagado += monto;
            }
        });

        var diferencia = montoTotal - montoPagado;

        $('#montoTotal').val(montoTotal.toFixed(2));
        $('#montoPagado').val(montoPagado.toFixed(2));
        $('#diferencia').val(diferencia.toFixed(2));

        // Actualizar el estado del botón de aprobar
        if (diferencia <= 0) {
            $('#btnAprobar').prop('disabled', false);
        } else {
            $('#btnAprobar').prop('disabled', true);
        }
    }

    $('#btnAprobar').click(function (e) {
        e.preventDefault();
        var montoTotal = parseFloat($('#montoTotal').val());
        var montoPagado = parseFloat($('#montoPagado').val());
        var montosPendientes = parseFloat($('#montosPendientes').val());
        var diferencia = parseFloat($('#diferencia').val());

        if (diferencia <= 0) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `\nMonto pagado: $${montoPagado.toFixed(2)}\nPago restante: $${diferencia.toFixed(2)}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#formPago').submit();
                }
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: 'No se puede aprobar. Aún hay una diferencia pendiente de $' + diferencia.toFixed(2),
                icon: 'error'
            });
        }
    });

    $('#zoom-in').on('click', function () {
        if (zoomLevel < maxZoom) {
            zoomLevel += zoomStep;
            $('#imagen-deposito').css('transform', 'scale(' + zoomLevel + ')');
        }
    });

    $('#zoom-out').on('click', function () {
        if (zoomLevel > minZoom) {
            zoomLevel -= zoomStep;
            $('#imagen-deposito').css('transform', 'scale(' + zoomLevel + ')');
        }
    });

    $('#zoom-reset').on('click', function () {
        zoomLevel = 1;
        $('#imagen-deposito').css('transform', 'scale(1)');
    });

    function closeModal(modalId) {
        $(modalId).modal('hide');
        $(modalId).find('input, textarea, select').val('');
    }

    $('.close, .btn-close').click(function () {
        var modalId = $(this).closest('.modal').attr('id');
        closeModal('#' + modalId);
    });

    $("#btnSoloRechazo").click(function () {
        var email = $(".email_rechazo_incompleto").val();
        var nombre = $("#nombre").val();

        $("#id_pago_solo_rechazo").val(idRuta);
        $("#email_rechazo").val(email);
        $("#names_rechazo").val(nombre);

        $("#modalSoloRechazo").modal("show");
    });


    $("#btnPagoIncompleto").click(function () {
        var nombre = $("#nombre").val();
        var montoTotal = parseFloat($('#precio').val().replace(/,/g, ''));
        var montoPagado = parseFloat($('#montoPagado').val());
        var diferencia = parseFloat($('#diferencia').val());

        $("#id_pago_rechazo").val(idRuta);
        $("#names_rechazo_incompleto").val(nombre);
        $("#precio_pagar").val(montoTotal.toFixed(2));
        $("#precio_pagado").val(montoPagado.toFixed(2));
        $("#valor_pendiente").val(diferencia.toFixed(2));

        $("#modalRechazo").modal("show");
    });

    $("#precio_pagado").on('input', function () {
        var precioPagar = parseFloat($("#precio_pagar").val());
        var precioPagado = parseFloat($(this).val()) || 0;
        var valorPendiente = precioPagar - precioPagado;
        $("#valor_pendiente").val(valorPendiente.toFixed(2));
    });
    $("#formRechazo").on("submit", function (e) {
        e.preventDefault(); // Prevenir el envío del formulario por defecto

        var paymentId = $("#id_pago_rechazo").val();

        // Realizar la verificación antes de enviar el formulario
        fetch(`${base_url}admin/pagos/verificarDepositoIncompleto/${paymentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Si ya existe un depósito rechazado con email enviado
                    Swal.fire({
                        title: '¿Estás seguro?',
                        html: `Ya se ha enviado ${data.emailCount} email(s) para este depósito rechazado.<br>¿Deseas enviar otro email?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, enviar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Si el usuario confirma, enviar el formulario
                            this.submit();
                        }
                    });
                } else {
                    // Si no existe un depósito rechazado con email enviado, enviar el formulario directamente
                    this.submit();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema al verificar el depósito rechazado.', 'error');
            });
    });
    $("#formSoloRechazo").on("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Verificar si el motivo de rechazo está vacío
        if (!formData.get('motivo_solo_rechazo').trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, ingrese un motivo de rechazo.'
            });
            return;
        }

        var paymentId = formData.get('id_pago_solo_rechazo');

        // Realizar la verificación antes de enviar el formulario
        fetch(`${base_url}admin/pagos/verificarDepositoRechazado/${paymentId}`)
            .then(response => response.json())
            .then(data => {
                let confirmMessage = '¿Estás seguro de que deseas rechazar este pago?';
                if (data.status === 'success' && data.emailCount > 0) {
                    confirmMessage = `Ya se ha enviado ${data.emailCount} email(s) para este depósito rechazado.<br>¿Deseas enviar otro email?`;
                }

                Swal.fire({
                    title: '¿Estás seguro?',
                    html: confirmMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Procesando...',
                            text: 'Por favor, espere mientras se procesa su solicitud.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(this.action, {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                Swal.close();
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Éxito!',
                                        text: data.message
                                    }).then(() => {
                                        $("#modalSoloRechazo").modal("hide");
                                        reloadSection(); // Asumiendo que tienes una función para recargar la sección
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message || 'Hubo un problema al procesar su solicitud.'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Hubo un problema al procesar su solicitud.'
                                });
                            });
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema al verificar el depósito rechazado.', 'error');
            });
    });

    actualizarMontos();
});