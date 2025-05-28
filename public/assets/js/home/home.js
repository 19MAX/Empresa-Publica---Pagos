document.addEventListener("DOMContentLoaded", function () {
  // Funciones para manejar el token CSRF
  function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  }

  function updateCsrfToken(newToken) {
    if (newToken) {
      document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken);
    }
  }

  // Funciones del preloader
  function showPreloader() {
    document.getElementById("preloader").style.display = "flex";
  }

  function hidePreloader() {
    document.getElementById("preloader").style.display = "none";
  }

  async function obtenerUsuario(userId) {
    try {
      showPreloader();
      const response = await fetch('validar_cedula', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ cedula: userId }),
      });

      // Obtener y actualizar el token CSRF desde los headers de la respuesta
      const newCsrfToken = response.headers.get('X-CSRF-TOKEN');
      updateCsrfToken(newCsrfToken);

      const data = await response.json();
      hidePreloader();

      if (data.status === 'success') {
        return { status: 'success', persona: data.persona };
      } else if (data.status === 'warning') {
        return { status: 'warning', persona: data.persona };
      } else if (data.status === 'validation') {
        return { status: 'validation', message: data.message };
      } else {
        return { status: 'error', persona: null };
      }
    } catch (error) {
      hidePreloader();
      console.error("Error en la solicitud:", error);
      return { status: 'error', persona: null };
    }
  }

  // Función para validar que el campo solo acepte 10 dígitos
  function validarCedulaLongitud(input) {
    input.addEventListener("input", function () {
      // Permitir que el valor tenga un máximo de 13 caracteres (el máximo entre cédula y RUC)
      this.value = this.value.slice(0, 13);
    });
  }

  // Aplicar la validación al campo con ID "numeroCedula"
  const numeroCedulaInput = document.getElementById("numeroCedula");
  validarCedulaLongitud(numeroCedulaInput);


  function llenarCamposPersona(persona) {
    document.getElementById("id_user").value = persona.id;
    document.getElementById("nombresPersona").textContent = persona.nombres;
    document.getElementById("apellidosPersona").textContent = persona.apellidos;
    document.getElementById("emailPersona").textContent = persona.email;
  }
  function llenarCamposPersonaRegistro(persona) {
    document.getElementById("numeroCedulaRegistro").value = persona.id;
    document.getElementById("nombres").value = persona.nombres;
    document.getElementById("apellidos").value = persona.apellidos;
    document.getElementById("telefono").value = persona.phone;
    document.getElementById("direccion").value = persona.address;
    document.getElementById("email").value = persona.email;
    const gender = persona.gender;
    document.getElementById("gender").value = (gender === 'MASCULINO') ? '0' : (gender === 'FEMENINO') ? '1' : '';
  }
  document.getElementById("formRegistroUsuario").addEventListener("submit", async function (event) {
    event.preventDefault();
    showPreloader();
    let formData = new FormData(this);
    let jsonData = {};
    formData.forEach((value, key) => (jsonData[key] = value));

    try {
      const response = await fetch("registrar_usuario", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(jsonData),
      });
      const data = await response.json();
      hidePreloader();
      if (data.success) {
        $("#modalRegistroUsuario").modal("hide");
        $("#modalInscripcion").modal("show");
        Swal.fire({
          title: "<strong>Usuario registrado correctamente!</strong>",
          icon: "success",
          html: `
                    <p>Ahora puede inscribirse al evento.</p>
                `,
          showCloseButton: true,
          confirmButtonText: 'Ok',
        });
      } else {
        let errorMessage = "Error al registrar el usuario.";
        if (typeof data.message === 'object') {
          errorMessage = '<div class="alert alert-danger" role="alert"><ul class="list-unstyled">';
          for (let key in data.message) {
            errorMessage += `<li>${data.message[key]}</li>`;
          }
          errorMessage += '</ul></div>';
        } else {
          errorMessage = `<div class="alert alert-danger" role="alert"><p>${data.message}</p></div>`;
        }

        Swal.fire({
          title: "<strong>Error</strong>",
          icon: "error",
          html: errorMessage,
          showCloseButton: true,
          confirmButtonText: 'Ok',
        });
      }
    } catch (error) {
      hidePreloader();
      console.error("Error:", error);
      Swal.fire({
        title: "<strong>Error</strong>",
        icon: "error",
        html: `
                <div class="alert alert-danger" role="alert">
                    <p>Ocurrió un error al registrar el usuario. Por favor, intente nuevamente más tarde.</p>
                </div>
            `,
        showCloseButton: true,
        confirmButtonText: 'Ok',
      });
    }
  });


  document.getElementById("formInscripcion").addEventListener("submit", async function (event) {
    event.preventDefault();
    let numeroCedula = document.getElementById("numeroCedula").value;
    let eventoId = document.getElementById("eventoId").value;

    if (numeroCedula.length !== 10 && numeroCedula.length !== 13) {
      Swal.fire({
        title: "Error",
        text: "La cédula debe tener exactamente 10 dígitos o el RUC 13 dígitos.",
        icon: "error",
        confirmButtonText: "Entendido",
      });
      return;
    }

    const userResponse = await obtenerUsuario(numeroCedula);
    if (userResponse.status === 'success') {
      llenarCamposPersona(userResponse.persona);
      try {
        const response = await fetch("obtener_datos_evento", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ eventoId: eventoId }),
        });
        const eventData = await response.json();

        if (eventData) {
          // Llenar el modal de evento
          document.getElementById("titleEvent").textContent = eventData.event_name;
          document.getElementById("descripcionEvento").value = eventData.short_description;
          // Manejar las categorías
          if (eventData.category_ids) {
            let categoryIds = eventData.category_ids.split(",");
            let categoryNames = eventData.categories.split(",");
            let categoryPagos = eventData.cantidad_dinero.split(",");

            let categoriaHtml = "<div class='row'><div class='modalContainer col'><div class='row'>";
            for (let i = 0; i < categoryIds.length; i++) {
              categoriaHtml += `
                <div class="col-md-6 col-lg-6">
                  <div class="radio">
                    <label>
                      <input type="radio" name="categoria" value="${categoryIds[i]}" id="categoria${categoryIds[i]}" required>
                      <span>${categoryNames[i]} - $${categoryPagos[i]}</span>
                    </label>
                  </div>
                </div>
              `;
            }

            categoriaHtml += "</div></div></div>";
            document.getElementById("categoria").innerHTML = categoriaHtml;
          } else {
            document.getElementById("categoria").innerHTML = `
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="categoria" value="0" id="sinCategorias" checked required>
                  <label class="form-check-label" for="sinCategorias">Sin categorías</label>
                </div>`;
          }

          $("#modalInscripcion").modal("hide");
          $("#modalDetallesEvento").modal("show");
        } else {
          swal("Ocurrió un error", "No se encontraron datos del evento", "warning");
        }
      } catch (error) {
        console.error("Error:", error);
      }
    } else if (userResponse.status === 'warning') {
      llenarCamposPersonaRegistro(userResponse.persona);
      $("#modalInscripcion").modal("hide");
      $("#modalRegistroUsuario").modal("show");
    } else if (userResponse.status === 'validation') {
      $("#modalInscripcion").modal("hide");
      Swal.fire({
        title: "Error",
        text: userResponse.message,
        icon: "error",
        confirmButtonText: "Entendido",
      });
      return;
    } else {
      document.getElementById("numeroCedulaRegistro").value = numeroCedula;
      $("#modalInscripcion").modal("hide");
      $("#modalRegistroUsuario").modal("show");
    }
  });

  document.getElementById("formDetallesEvento").addEventListener("submit", async function (event) {
    event.preventDefault();
    showPreloader();

    let numeroCedula = document.getElementById("numeroCedula").value;
    let eventoId = document.getElementById("eventoId").value;
    let catId = document.querySelector('input[name="categoria"]:checked').value;

    let jsonData = {
      cedula: numeroCedula,
      eventoId: eventoId,
      catId: catId,
    };

    try {
      const response = await fetch("guardar_inscripcion", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(jsonData),
      });
      const data = await response.json();
      hidePreloader();

      if (data.error) {
        swal("Ups! Algo salió mal!", data.message, "warning");
      } else if (data.success) {
        const fechaLimitePago = new Date(data.payment_time_limit);
        const hoy = new Date();
        const diferenciaMilisegundos = fechaLimitePago - hoy;
        const diasRestantes = Math.ceil(diferenciaMilisegundos / (1000 * 60 * 60 * 24));

        Swal.fire({
          title: "<strong>¡Registro Exitoso!</strong>",
          icon: "success",
          html: `
              <p style="color: #0C244B;">Te has registrado para el evento: </br><b>${data.eventName}</b></p>
               <!--<p>Tienes <b>${diasRestantes}</b> días para realizar el pago.</p>-->
              <p style="color: #0C244B;">Comprobante de registro enviado a: <b>${data.email}</b></p>
              <p><strong style="color: #0C244B;">Tu código de pago es:</strong></p>
              <h2 style="color: #0C244B; border: 2px solid #ff416c; padding: 10px; display: inline-block; border-radius: 30px;">
                ${data.codigoPago}
              </h2>
              <hr>
              <h3 style="color: #ff416c;">¡IMPORTANTE!</h3>
              <p><strong style="color: #ff416c;">Tu inscripción no estará completa hasta que realices el pago.</strong></p>
              <p>Si eliges <b>pago por depósito bancario</b>, deberás subir el comprobante de pago en la plataforma para que sea validado.</p>
              <p><strong style="color: #ff416c;">¡Asegúrate de completar este paso para confirmar tu inscripción!</strong></p>
            `,
          showCloseButton: true,
          confirmButtonText: 'Entendido',
        });
        $("#modalDetallesEvento").modal("hide");
      } else {
        swal("Ups! Algo salió mal!", "La acción no se pudo realizar correctamente!", "error");
      }
    } catch (error) {
      hidePreloader();
      console.error("Error:", error);
      swal("Ups! Algo salió mal!", "Error al comunicarse con el servidor", "error");
    }
  });

  document.getElementById('formMetodo').addEventListener('submit', function (event) {
    event.preventDefault();
    showPreloader();

    const codigoPago = document.getElementById('codigoPagoMetodo').value;
    const cedula = document.getElementById('depositoCedulaMetodo').value;
    const metodoPago = document.querySelector('input[name="metodoPago"]:checked').value;

    let url, data;
    if (metodoPago === 'deposito') {
      url = 'monto_pago';
      data = { codigoPago, cedula };
    } else if (metodoPago === 'tarjeta') {
      url = 'payphone';
      data = { codigoPago, cedula };
    }

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        hidePreloader();
        if (data.success) {
          if (metodoPago === 'deposito') {
            mostrarModalDeposito(data, cedula, codigoPago);
          } else if (metodoPago === 'tarjeta') {
            const payphoneData = data.data;
            mostrarOpcionPayphone(payphoneData);
          }
        } else {
          Swal.fire({
            title: "Error",
            text: data.error || "No se pudo obtener la información de pago",
            icon: "warning",
            confirmButtonText: `Entendido`,
          });
        }
      })
      .catch(error => {
        hidePreloader();
        console.error('Error:', error);
        Swal.fire({
          title: "Error",
          text: "Hubo un problema al procesar su solicitud",
          icon: "error"
        });
      });
  });

  $('#modalDeposito').on('hidden.bs.modal', function () {
    // Limpiar la tabla de depósitos
    const tablaDepositos = document.querySelector('#tabla_depositos');
    tablaDepositos.innerHTML = '';

    // Restablecer los campos del formulario
    document.getElementById('codigoPagoDep').value = '';
    document.getElementById('depositoCedulaDep').value = '';
    document.getElementById('montoDeposito').value = '';
    document.getElementById('comprobante').value = '';
    document.getElementById('dateDeposito').value = '';
    document.getElementById('comprobantePago').value = '';

    // Ocultar y limpiar los mensajes
    const mensajes = ['mensaje_estado', 'mensaje_original', 'mensaje_pagado', 'mensaje_nuevo'];
    mensajes.forEach(id => {
      const elemento = document.getElementById(id);
      elemento.style.display = 'none';
      if (elemento.querySelector('span')) {
        elemento.querySelector('span').textContent = '';
      }
    });
  });

  $('#modalMetodo').on('hidden.bs.modal', function () {
    // Restablecer los campos del formulario
    document.getElementById('codigoPagoMetodo').value = '';
    document.getElementById('depositoCedulaMetodo').value = '';
  });

  // Selecciona todos los inputs con la clase "numTex"
  const numTexInputs = document.querySelectorAll('input.numTex');

  numTexInputs.forEach(input => {
    // Cambia el tipo a "text"
    input.type = 'text';

    // Agrega un event listener para el evento "input"
    input.addEventListener('input', function (e) {
      // Reemplaza cualquier carácter que no sea un número
      this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Opcional: previene la entrada de 'e' (que puede ser usado para notación científica en inputs numéricos)
    input.addEventListener('keydown', function (e) {
      if (e.key === 'e') {
        e.preventDefault();
      }
    });
  });

  function mostrarModalDeposito(data, cedula, codigo) {
    document.getElementById('codigoPagoDep').value = codigo;
    document.getElementById('depositoCedulaDep').value = cedula;
    document.getElementById('montoDeposito').value = `$ ${data.monto}`;

    const mensajeEstado = document.querySelector('#mensaje_estado');
    const mensajeOriginal = document.querySelector('#mensaje_original');
    const mensajePagado = document.querySelector('#mensaje_pagado');
    const mensajeNuevo = document.querySelector('#mensaje_nuevo');
    const tablaDepositos = document.querySelector('#tabla_depositos');

    // Resetear todos los mensajes
    [mensajeEstado, mensajeOriginal, mensajePagado, mensajeNuevo].forEach(elem => {
      if (elem.querySelector('span')) {
        elem.querySelector('span').textContent = '';
      }
      elem.style.display = 'none';
    });

    if (data.error) {
      mensajeEstado.querySelector('span').textContent = data.error;
      mensajeEstado.style.display = 'block';
    }

    if (data.deposits) {
      mostrarTablaDepositos(data.deposits);
    } else {
      tablaDepositos.innerHTML = '<p class="text-muted">No hay depósitos registrados.</p>';
      tablaDepositos.style.display = 'block';
    }

    $('#modalMetodo').modal('hide');
    $('#modalDeposito').modal('show');
  }

  function mostrarOpcionPayphone(data) {
    $('#modalMetodo').modal('hide');
    $('#payModal').modal('show');
    showPaymentBox(data);
  }

  function showPaymentBox(payphoneData) {
    ppb = new PPaymentButtonBox({
      token: payphoneData.token,
      storeId: payphoneData.store,
      clientTransactionId: payphoneData.clientTransactionId,
      amount: payphoneData.amount,
      amountWithoutTax: payphoneData.amountWithoutTax,
      amountWithTax: payphoneData.amountWithTax,
      tax: payphoneData.tax,
      reference: payphoneData.reference,
      service: payphoneData.service,
      tip: payphoneData.tip
    }).render('pp-button');
  }

  function mostrarTablaDepositos(deposits) {
    const tablaDepositos = document.querySelector('#tabla_depositos');
    tablaDepositos.innerHTML = ''; // Limpiar la tabla existente

    if (deposits && deposits.length > 0) {
      const tabla = document.createElement('table');
      tabla.classList.add('table', 'table-sm', 'table-striped', 'table-hover', 'table-bordered');

      // Crear encabezado
      const thead = document.createElement('thead');
      thead.innerHTML = `
            <tr>
                <th>N° Comprobante</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        `;
      tabla.appendChild(thead);

      // Crear cuerpo de la tabla
      const tbody = document.createElement('tbody');
      deposits.forEach(deposit => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
                <td>${deposit.num_comprobante}</td>
                <td>$ ${deposit.monto_deposito}</td>
                <td>${deposit.date_deposito}</td>
                <td>${deposit.status}</td>
            `;
        tbody.appendChild(tr);
      });
      tabla.appendChild(tbody);

      tablaDepositos.appendChild(tabla);
    } else {
      tablaDepositos.innerHTML = '<p class="text-muted">No hay depósitos registrados.</p>';
    }
    tablaDepositos.style.display = 'block';
  }

  // Aplicar la misma validación al input con ID "numeroCedulaRegistro"
  const numeroCedulaRegistroInput = document.getElementById("numeroCedulaRegistro");
  validarCedulaLongitud(numeroCedulaRegistroInput);
  // Obtener los parámetros de la URL
  const urlParams = new URLSearchParams(window.location.search);
  const modal = urlParams.get('modal');
  const codigoPago = urlParams.get('codigoPago');

  // Si el parámetro 'modal' está presente y es 'metodo'
  if (modal === 'metodo') {
    // Asignar el código de pago al campo de texto (si está presente)
    if (codigoPago) {
      document.getElementById('codigoPagoMetodo').value = codigoPago;
    }

    // Mostrar el modal automáticamente
    var myModal = new bootstrap.Modal(document.getElementById('modalMetodo'));
    myModal.show();

    // Eliminar los parámetros de la URL inmediatamente después de mostrar el modal
    const newUrl = window.location.origin + window.location.pathname;
    window.history.replaceState({}, '', newUrl); // Actualiza la URL sin los parámetros
  }

});
