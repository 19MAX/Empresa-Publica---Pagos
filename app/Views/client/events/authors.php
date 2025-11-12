<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="description"
        content="EventoPagos: Inscripciones y pagos de eventos académicos de diferentes autores. Regístrate fácilmente y paga en línea, depósito o en puntos autorizados.">
    <meta name="keywords"
        content="eventos, autores, inscripciones, pagos en línea, depósito, puntos de recaudación, Ecuador">
    <link rel="icon" href="<?=base_url("favicon.ico")?>">
    <meta property="og:title" content="EventoPagos - Inscripción y Pago de Eventos de Autores">
    <meta property="og:description"
        content="Descubre eventos organizados por diferentes autores. Inscríbete y paga de forma segura con múltiples métodos de pago.">
    <meta property="og:url" content="<?=base_url("/")?>">
    <meta property="og:type" content="website">

    <title>EventoPagos - Inscripción y Pago de Eventos Académicos</title>
    <!-- <link rel="icon" href="<?= base_url("assets/images/icono.jpeg"); ?>" type="image/jpeg"> -->
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url("assets/css/styles.css") ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="<?= base_url("assets/css/home.css") ?>">
    <!-- Payphone -->
    <link rel="stylesheet" href="<?= base_url("assets/css/payphone.css") ?>">
    <!-- Preloader -->
    <link rel="stylesheet" href="<?= base_url("assets/css/preloader.css") ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/whatsapp.css") ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/authors.css") ?>">
    <script type="text/javascript">
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });
    </script>
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Preloader HTML -->
    <div id="preloader" style="display: none;">
        <div class="spinner"></div>
        <p class="loading-text">Cargando<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
        </p>
    </div>

    <style>
        .no-events {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
            text-align: center;
            font-size: 1.5rem;
            color: #555;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .no-events h2 {
            color: #0C244B;
        }

        .no-events p {
            color: #888;
        }
    </style>
    <main class="flex-grow-1" style="background-color: #f5f7fa;">

        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0C244B;">
            <div class="container-fluid">
                <a class="navbar-brand ms-lg-4" href="">
                    <!-- <h4>PROSERVI UEB-EP </h4> -->
                    <img loading="lazy" class="img-fluid" width="90px"
                        src="<?= base_url("assets/images/logo-ep.png") ?>" alt="EventoPagos">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <!-- Añadir este botón dentro del <ul class="navbar-nav"> junto a los otros botones -->
                        <!-- <li class="nav-item">
                            <button title="Puntos de Recaudación" class="btn btn-outline-light me-2 mb-2 mb-lg-0"
                                data-bs-toggle="modal" data-bs-target="#modalPuntosRecaudacion" type="button">
                                <i class="fa-solid fa-location-dot"></i> PUNTOS DE RECAUDACIÓN
                            </button>
                        </li> -->
                        <li class="nav-item">
                            <button title="Información Bancaria" class="btn btn-outline-light me-2 mb-2 mb-lg-0"
                                data-bs-toggle="modal" data-bs-target="#modalCuentasBancarias" type="button"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Ver información bancaria">
                                <i class="fa-solid fa-circle-info"></i> INFORMACIÓN BANCARIA
                            </button>
                        </li>
                        <li class="nav-item">
                            <button title="Consultar Voucher" class="btn btn-outline-light mb-2 mb-lg-0"
                                data-bs-toggle="modal" data-bs-target="#modalVoucher" type="button"
                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="Consultar detalles del voucher">
                                <i class="fa-solid fa-file-pdf"></i> CONSULTAR VOUCHER
                            </button>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-4 mb-5">
            <!-- Sección de Filtro por Autores -->
            <div class="filter-section fade-in">
                <p class="section-subtitle">Selecciona un organizador para ver sus eventos disponibles</p>

                <div class="row" id="authorsContainer">
                    <?php if (!empty($authors)): ?>
                        <?php foreach ($authors as $author): ?>
                            <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-3 mobile-author-col">
                                <div class="author-card pt-3 text-center"
                                    onclick="filterEventsByAuthor(<?= $author['id'] ?>, this, '<?= htmlspecialchars($author['name']) ?>')"
                                    data-author-id="<?= $author['id'] ?>">
                                    <div class="card-body d-flex flex-column align-items-center">
                                        <?php if (!empty($author['img'])): ?>
                                            <div class="author-image-wrapper mb-3">
                                                <?php if (!empty($author['img'])): ?>
                                                    <img loading="lazy" src="<?= base_url($author['img']) ?>"
                                                        alt="<?= $author['name'] ?>" class="author-image">
                                                <?php else: ?>
                                                    <i class="fa-solid fa-building fa-xl text-muted"></i>
                                                <?php endif; ?>
                                            </div>

                                        <?php else: ?>
                                            <div
                                                class="author-image mb-3 d-flex align-items-center justify-content-center bg-light">
                                                <i class="fa-solid fa-building fa-lg text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <h5 class="author-name"><?= $author['name'] ?></h5>
                                        <p class="event-count"><?= $author['event_count'] ?> evento(s)</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mensaje de Bienvenida (mostrado inicialmente) -->
            <div class="welcome-section" id="welcomeSection">
                <i class="fa-solid fa-hand-wave"></i>
                <h3>¡Bienvenido a nuestros eventos!</h3>
                <p>Selecciona un organizador arriba para descubrir todos los eventos emocionantes que tiene disponibles
                    para ti.</p>
            </div>

            <!-- Sección de Eventos -->
            <div class="events-section" id="eventsSection" style="display: none;">

                <div class="row" id="eventsContainer">
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $event): ?>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 p-3 event-item"
                                data-author-id="<?= $event['author_id'] ?>" style="display: none;">
                                <div class="bg-white shadow rounded-2">
                                    <figure style="cursor: pointer;" class="p-1">
                                        <img loading="lazy" src="<?= base_url("") . $event['image']; ?>" alt="Imagen del Curso"
                                            class="img-fluid imagen-pequena rounded-2">
                                    </figure>

                                    <section class="px-2">
                                        <article class="date__start__content">
                                            <?= $event['formatted_event_date'] ?>
                                        </article>
                                        <section class="card__icons__container">
                                            <?php if ($event['modality'] == 'Virtual' || $event['modality'] == 'Hibrida'): ?>
                                                <article class="text-center">
                                                    <p><i class="fa-regular fa-clock"></i> DURACIÓN</p>
                                                    <span><?= $event['event_duration'] ?> Horas</span>
                                                </article>
                                            <?php endif; ?>
                                            <article class="text-center">
                                                <p><i class="fa fa-users"></i> MODALIDAD</p>
                                                <span><?= $event['modality'] ?></span>
                                            </article>
                                        </section>
                                        <section class="pt-3 pb-1">
                                            <button class="btn btn-danger mb-2 btn-inscribirse" data-bs-toggle="modal"
                                                data-bs-target="#modalInscripcion" data-evento="<?= $event['event_name'] ?>"
                                                data-event-id="<?= $event['id'] ?>" type="button" style="width:100%;">
                                                INSCRIBIRSE
                                            </button>
                                            <button class="btn card__button mb-2" type="button" data-bs-toggle="modal"
                                                data-bs-target="#modalMetodo" style="width:100%;">
                                                PAGAR
                                            </button>
                                        </section>
                                    </section>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-events">
                            <div>
                                <i class="fa-solid fa-calendar-xmark"></i>
                                <h3>No hay eventos disponibles</h3>
                                <p class="text-muted">Actualmente no hay eventos registrados. Por favor, vuelve más tarde.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <style>
                        @media (min-width: 550px) and (max-width: 767.98px) {
                            .col-12.col-sm-12 {
                                flex: 0 0 50%;
                                max-width: 50%;
                            }
                        }

                        .modal-lg {
                            max-width: 900px;
                        }

                        .card {
                            transition: transform 0.2s ease-in-out;
                            overflow: hidden;
                        }

                        .card:hover {
                            transform: translateY(-5px);
                        }

                        .rounded-circle {
                            width: 50px;
                            height: 50px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }

                        .card-img-wrapper {
                            position: relative;
                            overflow: hidden;
                            height: 200px;
                        }

                        .sucursal-img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            transition: transform 0.3s ease;
                        }

                        .card:hover .sucursal-img {
                            transform: scale(1.1);
                        }

                        .img-overlay {
                            position: absolute;
                            bottom: 0;
                            left: 0;
                            right: 0;
                            background: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%);
                            padding: 20px;
                            transition: all 0.3s ease;
                        }

                        .card:hover .img-overlay {
                            background: linear-gradient(0deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.1) 100%);
                        }

                        .modal-content {
                            border-radius: 15px;
                            overflow: hidden;
                        }

                        .card {
                            border-radius: 10px;
                        }

                        .list-unstyled li {
                            padding: 8px 0;
                            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                        }

                        .list-unstyled li:last-child {
                            border-bottom: none;
                        }

                        .text-home {
                            color: #0C244B !important;
                        }

                        .bg-home {
                            background-color: #0C244B !important;
                        }
                    </style>
                </div>

                <!-- Mensaje cuando no hay eventos del autor seleccionado -->
                <div class="no-events" id="noEventsMessage" style="display: none;">
                    <div>
                        <i class="fa-solid fa-search"></i>
                        <h3>No hay eventos para este organizador</h3>
                        <p class="text-muted">Este organizador no tiene eventos activos en este momento.</p>
                        <button class="btn btn-back mt-3" onclick="showWelcome()">
                            <i class="fa-solid fa-arrow-left"></i> Volver a organizadores
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Puntos de Recaudación -->
    <div class="modal fade" id="modalPuntosRecaudacion" tabindex="-1" aria-labelledby="modalPuntosRecaudacionLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalPuntosRecaudacionLabel">
                        <i class="fa-solid fa-location-dot"></i> Puntos de Recaudación
                    </h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Sucursal Principal -->
                        <div class="col-md-12">
                            <div class="card h-100 border-0 shadow">
                                <div class="card-img-wrapper">
                                    <img loading="lazy" src="<?= base_url('assets/images/oficina_principal.jpeg') ?>"
                                        alt="Oficina principal" class="card-img-top sucursal-img">
                                    <div class="img-overlay">
                                        <h6 class="text-white mb-0">Softec</h6>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-home rounded-circle p-3 me-3">
                                            <i class="fa-solid fa-building text-white fs-4"></i>
                                        </div>
                                        <h5 class="card-title mb-0">Oficina Principal (Softec)</h5>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fa-solid fa-location-arrow text-home me-2"></i>
                                            C. 7 de mayo y C. olmedo, Guaranda
                                        </li>
                                        <li class="mb-2">
                                            <i class="fa-solid fa-clock text-home me-2"></i>
                                            Lunes a Viernes 8:00 - 18:00
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-phone text-home me-2"></i>
                                            (+593) 989026071
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Sucursal Secundaria -->
                        <!-- <div class="col-md-6">
                            <div class="card h-100 border-0 shadow">
                                <div class="card-img-wrapper">
                                    <img src="/api/placeholder/800/400" alt="Sucursal Secundaria"
                                        class="card-img-top sucursal-img">
                                    <div class="img-overlay">
                                        <h6 class="text-white mb-0">Sucursal Campus</h6>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success rounded-circle p-3 me-3">
                                            <i class="fa-solid fa-building-columns text-white fs-4"></i>
                                        </div>
                                        <h5 class="card-title mb-0">Campus UEB</h5>
                                    </div>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fa-solid fa-location-arrow text-success me-2"></i>
                                            Av. Universitaria y Aurelio Espinoza Pólit
                                        </li>
                                        <li class="mb-2">
                                            <i class="fa-solid fa-clock text-success me-2"></i>
                                            Lunes a Viernes: 9:00 AM - 4:00 PM
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-phone text-success me-2"></i>
                                            (593) 3-228-0948
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Cuentas Bancarias -->
    <div class="modal fade" id="modalCuentasBancarias" tabindex="-1" aria-labelledby="modalCuentasBancariasLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <!-- style="background: linear-gradient(to right, #BC157C, #FFD700); color: white;" -->
                    <h5 class="modal-title" id="modalCuentasBancariasLabel"><i class="fa-solid fa-circle-info"></i>
                        Información de Cuentas Bancarias</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <div class="col">
                            <h5 class="text-center mb-3">Cuenta Bancaria</h5>
                            <div class="table-responsive text-start">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                        <tr style="background:#ffdd00;">
                                            <th scope="row">Banco</th>
                                            <td >Banco de Pichincha</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Tipo de Cuenta</th>
                                            <td>Corriente</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Nombre</th>
                                            <td>EMPRESA PUBLICA PROSERVI UEB EP</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Cuenta Bancaria</th>
                                            <td>2100238825</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">RUC</th>
                                            <td>0260024190001</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- <div class="col">
                            <h5 class="text-center mb-3">Cuenta Bancaria 2</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Banco</th>
                                            <td>[Nombre del segundo banco]</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Tipo de Cuenta</th>
                                            <td>[Tipo de cuenta]</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Nombre</th>
                                            <td>[Nombre del titular]</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Cuenta Bancaria</th>
                                            <td>[Número de cuenta]</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">RUC</th>
                                            <td>[Número de RUC]</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de inscripción -->
    <div class="modal fade" id="modalInscripcion" tabindex="-1" aria-labelledby="modalInscripcionLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInscripcionLabel">Inscripción al Congreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formInscripcion">
                        <input type="hidden" id="eventoId" name="eventoId">
                        <div class="mb-3">
                            <label for="nombreEvento" class="form-label">Nombre del Evento</label>
                            <input type="text" class="form-control" id="nombreEvento" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="numeroCedula" class="form-label">Número de Cédula/Ruc</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-id-card"></i></div>

                                <input type="text" class="form-control numTex" id="numeroCedula" name="numeroCedula"
                                    required>
                            </div>
                        </div>
                        <div class="float-end">
                            <button type="submit" class="btn btn-success me-1">Inscribirse</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de registro de usuario -->
    <div class="modal fade" id="modalRegistroUsuario" tabindex="-1" aria-labelledby="modalRegistroUsuarioLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRegistroUsuarioLabel">Registro de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formRegistroUsuario">
                        <input type="text" name="website" style="display:none;" tabindex="-1" autocomplete="off">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="numeroCedulaRegistro" class="form-label">Número de Cédula</label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                                    <input type="text" class="form-control" id="numeroCedulaRegistro"
                                        name="numeroCedula" readonly>
                                </div>
                            </div>
                            <div class="col">
                                <label for="nombres" class="form-label">Nombres</label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="fas fa-user"></i></div>
                                    <input type="text" class="form-control" id="nombres" name="nombres" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="fas fa-user"></i></div>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                                </div>
                            </div>
                            <div class="col">
                                <label for="telefono" class="form-label">Número de teléfono o celular</label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="fas fa-phone"></i></div>
                                    <input type="text" class="form-control" id="telefono" name="telefono" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="fas fa-envelope"></i></div>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col">
                                <label for="direccion" class="form-label">Dirección</label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="fas fa-map-marker-alt"></i></div>
                                    <input type="text" class="form-control" id="direccion" name="direccion" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="email" class="form-label">Genero</label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="fa-solid fa-venus-mars"></i></div>
                                    <select class="form-select" name="gender" id="gender" required>
                                        <option value="0">Masculino</option>
                                        <option value="1">Femenino</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button form="formRegistroUsuario" type="submit" class="btn btn-primary">Registrar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles del evento -->
    <div class="modal fade" id="modalDetallesEvento" tabindex="-1" aria-labelledby="modalDetallesEventoLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetallesEventoLabel" style="color: #0C244B;">
                        <span id="titleEvent"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formDetallesEvento">
                        <div class="mb-3">
                            <label for="descripcionEvento" class="form-label">Descripción del Evento</label>
                            <textarea rows="3" class="form-control" id="descripcionEvento" readonly></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Seleccione la categoría</label>
                            <div id="categoria"></div>
                        </div>
                        <div class="alert alert-success" role="alert">
                            <p>Estudiante: <span id="nombresPersona" class="text-primary"></span> <span
                                    id="apellidosPersona" class="text-primary"></span></p>
                            Cuando finalices se te enviara un
                            código a tu correo electrónico: <span id="emailPersona" class="text-primary"></span> que
                            deberás usarlo para realizar el pago
                        </div>
                        <div class="mb-3 row">
                            <input type="hidden" id="id_user">
                        </div>
                        <div class="float-end">
                            <button type="submit" class="btn btn-success me-1">Finalizar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de depósito -->
    <div class="modal fade" id="modalDeposito" tabindex="-1" aria-labelledby="modalDepositoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDepositoLabel">Registrar Depósito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="formDeposito" method="post" action="<?= base_url("deposito") ?>"
                                enctype="multipart/form-data">
                                <div class="row">
                                    <div class="mb-3 col-md-6 col-lg-6 col-xl-6">
                                        <label for="codigoPagoDep" class="form-label">Código de pago <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="fas fa-receipt"></i></div>
                                            <input type="text" class="form-control numTex" id="codigoPagoDep"
                                                name="codigoPago"
                                                value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'codigoPago') : '' ?>"
                                                required readonly>
                                        </div>
                                        <span class="text-danger">
                                            <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'codigoPago') : '' ?>
                                        </span>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-6 col-xl-6">
                                        <label for="depositoCedulaDep" class="form-label">Número de cédula <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                                            <input type="text" class="form-control numTex" id="depositoCedulaDep"
                                                name="depositoCedula"
                                                value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'depositoCedula') : '' ?>"
                                                required readonly>
                                        </div>
                                        <span class="text-danger">
                                            <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'depositoCedula') : '' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6 col-lg-6 col-xl-6">
                                        <label for="comprobante" class="form-label">Número de comprobante <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="fa-solid fa-hashtag"></i></div>
                                            <input type="text" class="form-control numTex" id="comprobante"
                                                name="comprobante"
                                                value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'comprobante') : '' ?>"
                                                required>
                                        </div>
                                        <span class="text-danger">
                                            <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'comprobante') : '' ?>
                                        </span>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-6 col-xl-6">
                                        <label for="dateDeposito" class="form-label">Fecha del deposito <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                                            <input type="date" class="form-control" id="dateDeposito"
                                                name="dateDeposito"
                                                value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'dateDeposito') : '' ?>"
                                                required>
                                        </div>
                                        <span class="text-danger">
                                            <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'dateDeposito') : '' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="montoDeposito" class="form-label"><strong style="color: #ff416c;">Monto
                                            a depositar <span class="text-danger">*</span></strong></label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
                                        <input type="text" class="form-control" id="montoDeposito" name="montoDeposito"
                                            value="<?= (isset($last_data) && ($last_action ?? null) == 'insert') ? display_data($last_data, 'montoDeposito') : '' ?>"
                                            readonly>
                                    </div>
                                    <p class="text-danger mt-2"><strong>Por favor, asegúrate de depositar exactamente
                                            esta cantidad para evitar problemas con tu inscripción.</strong></p>
                                    <p id="mensaje_estado" style="display: none;">Estado: <span
                                            class="text-danger"></span></p>
                                    <p id="mensaje_original" style="display: none;">Monto original: <span
                                            class="text-danger"></span></p>
                                    <p id="mensaje_pagado" style="display: none;">Monto pagado: <span
                                            class="text-danger"></span></p>
                                    <p id="mensaje_nuevo" style="display: none;">Nuevo monto a pagar: <span
                                            class="text-danger"></span></p>
                                </div>
                                <div class="mb-3">
                                    <label for="comprobantePago" class="form-label">Subir Comprobante de Pago <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="fas fa-upload"></i></div>
                                        <input type="file" class="form-control" id="comprobantePago"
                                            name="comprobantePago" accept="image/*,application/pdf" required>
                                    </div>
                                    <span class="text-danger">
                                        <?= (isset($validation) && ($last_action ?? null) == 'insert') ? display_data($validation, 'comprobantePago') : '' ?>
                                    </span>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 modal-dialog-scrollable">
                            <h6>Historial de Depósitos</h6>
                            <div id="tabla_depositos" style="max-height: 300px; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" form="formDeposito" class="btn btn-success me-1">Registrar Depósito</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Autorización-->
    <div class="modal fade" id="modalVoucher" tabindex="-1" aria-labelledby="modalVoucherLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalVoucherLabel"><i class="fa-solid fa-file-pdf"></i> Consultar
                        Voucher</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formVoucher" method="GET">
                        <div class="mb-3">
                            <label for="numero_auto" class="form-label">Número de autorización</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fa-solid fa-hashtag"></i></div>
                                <input type="text" class="form-control" id="numero_auto" name="numero_auto">
                            </div>
                        </div>
                        <div class="float-end">
                            <button type="button" class="btn btn-success me-1"
                                onclick="submitVoucherForm()">Buscar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMetodo" tabindex="-1" aria-labelledby="modalMetodoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalMetodoLabel">Método de pago</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formMetodo">
                        <div class="row mb-3">
                            <div class="mb-3 col-md-6 col-lg-6 col-xl-6">
                                <label for="codigoPagoMetodo" class="form-label">Código de pago <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="fas fa-receipt"></i></div>
                                    <input type="text" class="form-control numTex" id="codigoPagoMetodo"
                                        name="codigoPago" value="" required>
                                </div>
                            </div>
                            <div class="mb-3 col-md-6 col-lg-6 col-xl-6">
                                <label for="depositoCedulaMetodo" class="form-label">Número de cédula <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                                    <input type="text" class="form-control numTex" id="depositoCedulaMetodo"
                                        name="depositoCedula" value="" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="container col">
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="metodoPago" value="deposito" checked>
                                                <span>Pago por deposito</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="metodoPago" value="tarjeta">
                                                <span>Pago con tarjeta <img loading="lazy"
                                                        src="<?= base_url('assets/images/iconoPayphone.webp') ?>"
                                                        alt="PayPhone Icon"
                                                        style="width: 20px; height: 20px; vertical-align: middle; margin-left: 1px;"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="float-end">
                            <button type="submit" class="btn btn-success me-1">Continuar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payphone -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="payModalLabel">Realizar el Pago</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div>
                        <div id="pp-button"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="imagen-ampliada" class="imagen-grande" style="display: none;">
        <img loading="lazy" src="" alt="Imagen ampliada">
    </div>

    <!-- <a href="https://wa.me/+593993847289?text=Hola%2C%20necesito%20ayuda%20con%20el%20evento%20X" class="Btn text-decoration-none" target="_blank" title="¿Necesitas ayuda?">
        <div class="sign">
            <i class="fa-brands fa-whatsapp"></i>
        </div>
        <div class="text">Soporte</div>
    </a> -->

    <!-- Botón flotante para volver al inicio -->
    <button class="back-to-top" id="backToTopBtn" onclick="scrollToTop()" title="Volver al inicio">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <!-- Footer-->
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container-fluid text-center">
            <div class="small m-0 text-white p-1">Copyright 2025 &copy; PROSERVI UEB-EP|
                <a href="https://softecsa.com/" class="text-decoration-none link-light">Softec Apps S.A.S</a>
            </div>
        </div>
    </footer>
    <!-- Payphone -->
    <script type='module' src='https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.js'></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url("assets/js/home/main.js") ?>"></script>
    <script>
        // Función para mostrar la alerta SweetAlert2
        function showAlert(type, message, uniqueCode = null) {
            if (type === 'success') {
                Swal.fire({
                    title: "<strong>¡Éxito!</strong>",
                    icon: "success",
                    html: `<div>${message}</div>`,
                    showCloseButton: true,
                    showCancelButton: false,
                    focusConfirm: false,
                    confirmButtonText: `<i class="fa fa-thumbs-up"></i> Entendido`,
                    confirmButtonAriaLabel: "Thumbs up, great!",
                });
            }
            else if (type === 'error') {
                Swal.fire({
                    title: "<strong>Error</strong>",
                    icon: "error",
                    html: `<div>${message}</div>`,
                    showCloseButton: true,
                    showCancelButton: false,
                    focusConfirm: false,
                    confirmButtonText: `<i class="fa fa-thumbs-up"></i> Entendido`,
                });
            }
            else if (type === 'pdf') {
                Swal.fire({
                    title: "<strong>Completado</strong>",
                    icon: "success",
                    html: `
                    <div>${message}</div>
                    ${uniqueCode ? `<div class="mt-3"><a class="btn btn-outline-danger" href="<?= base_url("") ?>pdf/${uniqueCode}" target="_blank"><i class="fa-solid fa-receipt"></i> Voucher </a></div>` : ''}
                `,
                    showCloseButton: true,
                    showCancelButton: false,
                    focusConfirm: false,
                    confirmButtonText: `<i class="fa fa-thumbs-up"></i> Entendido`,
                });
            }
            else {
                Swal.fire({
                    icon: type,
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            }
        }

        // Verificar si hay mensajes de éxito, advertencia o error
        <?php if (session()->has('flashMessages')): ?>
            <?php foreach (session('flashMessages') as $message): ?>
                <?php
                $type = $message[1];
                $msg = $message[0];
                $uniqueCode = isset($message[2]) ? $message[2] : null;
                ?>
                showAlert('<?= $type ?>', '<?= $msg ?>', '<?= $uniqueCode ?>');
            <?php endforeach; ?>
        <?php endif; ?>

        // Mostrar el modal de depósito si es necesario
        <?php if ('insert' == ($last_action ?? '')): ?>
            var myModal = new bootstrap.Modal(document.getElementById('modalDeposito'));
            myModal.show();
        <?php endif; ?>

        function submitVoucherForm() {
            var numeroAuto = document.getElementById('numero_auto').value.replace(/\s/g, '');

            if (numeroAuto.length > 19) {
                numeroAuto = numeroAuto.slice(13);
                numeroAuto = numeroAuto.slice(0, -6);
            }

            var url = '<?= base_url("pdf/") ?>' + numeroAuto;
            window.location.href = url;
        }
    </script>

    <script src="<?= base_url("assets/js/home/home.js") ?>"></script>

    <!-- template -->
    <script src="<?= base_url("dist/js/niche.js") ?>"></script>
    <script src="<?= base_url("assets/js/sweetalert/sweetalert.min.js") ?>"></script>
    <script src="<?= base_url("assets/js/preloader.js") ?>"></script>

    <!-- Scripts -->
    <script src="<?= base_url("assets/js/authors.js") ?>"></script>
    <!-- <script src="<?= base_url("assets/js/cursor-manager.js") ?>"></script> -->
</body>

</html>