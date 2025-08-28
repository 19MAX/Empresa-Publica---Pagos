<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $this->renderSection('title'); ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />

    <!-- v4.0.0-alpha.6 -->
    <link rel="stylesheet" href="<?= base_url("dist/bootstrap/css/bootstrap.min.css") ?>">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url("dist/css/style.css") ?>">
    <link rel="stylesheet" href="<?= base_url("dist/css/font-awesome/css/font-awesome.min.css") ?>">
    <link rel="stylesheet" href="<?= base_url("dist/css/et-line-font/et-line-font.css") ?>">
    <link rel="stylesheet" href="<?= base_url("dist/css/themify-icons/themify-icons.css") ?>">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css">
    <!-- DataTables responsivo -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.css">
    <!-- Botones de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

    <!-- Notificaciones -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- tooltip -->
    <link rel="stylesheet" href="<?= base_url('dist/plugins/tooltip/tooltip.css') ?>">
    <!-- Datatables -->
    <link rel="stylesheet" href="<?= base_url("dist/plugins/datatables/css/dataTables.bootstrap.min.css") ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/datatables.css") ?>">

    <!-- Preloader -->
    <link rel="stylesheet" href="<?= base_url("assets/css/preloader.css") ?>">
    <?= $this->renderSection('css'); ?>

</head>

<body class="hold-transition skin-blue sidebar-mini">
    <!-- Preloader HTML -->
    <div id="preloader" style="display: none;">
        <div class="spinner"></div>
        <p class="loading-text">Cargando<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
        </p>
    </div>
    <div class="wrapper boxed-wrapper">
        <header class="main-header">
            <!-- Logo -->
            <a href="<?= base_url("admin/dashboard") ?>" class="logo blue-bg">
            <span class="logo-mini"><img class="img-fluid" style="width:35px; border-radius:10px;"  src="<?= base_url("assets/images/icono-sin-fondo.png") ?>" alt=""></span>
                <span class="logo-lg"><img style="width:120px; border-radius:10px;"
                        src="<?= base_url("assets/images/logo-ep.png") ?>" alt=""></span> </a>
            <nav class="navbar blue-bg navbar-static-top">
                <!-- Sidebar toggle button-->
                <ul class="nav navbar-nav pull-left">
                    <li><a class="sidebar-toggle" data-toggle="push-menu" href=""></a> </li>
                </ul>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu p-ph-res">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img
                                    src="<?= base_url('assets/images/admin_letter.jpeg') ?>" class="user-image"
                                    alt="User Image"> <span class="hidden-xs"><?= session('first_name') . ' ' . session('last_name') ?></span> </a>
                            <ul class="dropdown-menu">

                                <li class="user-header">
                                    <div class="pull-left user-img"><img
                                            src="<?= base_url('assets/images/admin_letter.jpeg') ?>"
                                            class="img-responsive" alt="User"></div>
                                    <p class="text-left"><?= session('first_name') . ' ' . session('last_name') ?>
                                        <small><?= session('user_email') ?></small>
                                    </p>
                                    <!-- <div class="view-link text-left"><a href="#">Ver perfil</a> </div> -->
                                </li>
                                <hr>
                                <!-- <li><a href="#"><i class="icon-profile-male"></i> Mi perfil</a></li> -->
                                <li role="separator" class="divider"></li>
                                <li><a href="<?= base_url("admin/config") ?>"><i class="icon-gears"></i>
                                        Configuración</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?= base_url("logout") ?>"><i title="Cerrar Sesión"
                                            class="fa fa-power-off"></i>Cerrar Sesión</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <aside class="main-sidebar">
            <div class="sidebar">

                <div class="user-panel">
                    <div class="image text-center">
                        <img src="<?= base_url('assets/images/admin_letter.jpeg') ?>" class="img-circle"
                            alt="User Image">
                    </div>
                    <div class="info text-black">
                        <p><?= session('first_name') . ' ' . session('last_name') ?></p>
                        <a href="<?= base_url("admin/config") ?>" title="Configuración"><i class="fa fa-lg fa-cog"></i></a> <a
                            href="<?= base_url("logout") ?>" title="Cerrar Sesión"><i class="fa fa-lg fa-power-off"></i></a>
                    </div>
                </div>
                <ul class="sidebar-menu" data-widget="tree">
                    <li
                        class="treeview <?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::DASHBOARD)) ? 'active' : '' ?>">
                        <a href="#"> <i class="fa fa-dashboard"></i> <span>Panel</span>
                            <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
                        <ul class="treeview-menu">
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::DASHBOARD)) ? 'active' : '' ?>">
                                <a href="<?= base_url("admin/dashboard") ?>">Estadísticas</a></li>
                        </ul>
                    </li>
                    <li class="header">

                        <a class="px-0 py-1" href="<?= base_url('admin/inscripciones') ?>">
                            <i class="fa fa-usd" aria-hidden="true"></i><span>Cobrar con efectivo</span> <span
                                class="pull-right-container"> </span>
                        </a>
                    </li>
                    <li
                        class="treeview  <?= (isset($modulo) && in_array($modulo, [ModulosAdmin::DEPOSITO_ALL,ModulosAdmin::PAGOS, ModulosAdmin::PAGOS_COMPLETOS, ModulosAdmin::PAGOS_RECHAZADOS, ModulosAdmin::PAGOS_INCOMPLETOS])) ? 'active' : '' ?>">
                        <a href="#"> <i class="fa fa-credit-card-alt" aria-hidden="true"></i> <span>Cobrar con
                                depósito</span> <span class="pull-right-container"> <i
                                    class="fa fa-angle-left pull-right"></i> </span> </a>
                        <ul class="treeview-menu">
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::PAGOS)) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/pagos') ?>">Ingresados</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::PAGOS_COMPLETOS)) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/pagos/completados') ?>">Completados</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::PAGOS_RECHAZADOS)) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/pagos/rechazados') ?>">Rechazados</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::PAGOS_INCOMPLETOS)) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/pagos/incompletos') ?>">Incompletos</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::DEPOSITO_ALL)) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/deposito_all') ?>">Todos los depósitos</a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="treeview <?= (isset($modulo) && in_array($modulo, [ModulosAdmin::INSCRIPCIONES,ModulosAdmin::INSCRIPCIONES_ELIMINADAS])) ? 'active' : '' ?>">
                        <a href="#"> <i class="fa fa-users"></i>
                        <span>Inscripciones</span>
                            <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
                        <ul class="treeview-menu">
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::INSCRIPCIONES)) ? 'active' : '' ?>">
                                <a href="<?= base_url("admin/inscritos") ?>">Todas</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::INSCRIPCIONES_ELIMINADAS)) ? 'active' : '' ?>">
                                <a href="<?= base_url("admin/inscritos/trash") ?>">Eliminadas</a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="treeview <?= (isset($modulo) && in_array($modulo, [ModulosAdmin::EVENTS, ModulosAdmin::EVENTS_LIST, ModulosAdmin::EVENTS_ADD,ModulosAdmin::CATEGORY_LIST])) ? 'active' : '' ?>">
                        <a href="#"> <i class="fa fa-ticket"></i> <span>Eventos</span> <span
                                class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
                        <ul class="treeview-menu">
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::EVENTS_ADD)) ? 'active' : '' ?>">
                                <a href="<?= base_url("admin/event/new") ?>">Nuevo evento</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::EVENTS_LIST)) ? 'active' : '' ?>">
                                <a href="<?= base_url("admin/event") ?>">Lista de eventos</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::CATEGORY_LIST)) ? 'active' : '' ?>">
                                <a href="<?= base_url("admin/category") ?>">Categorías de eventos</a>
                            </li>
                        </ul>
                    </li>

                    <li class="treeview <?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::AUTORES)) ? 'active' : '' ?>">
                        <a href="#"> <i class="fa fa-building"></i> <span>Autores</span>
                            <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::AUTORES)) ? 'active' : '' ?>">
                                <a href="<?= base_url("admin/autores") ?>">Lista de autores</a>
                            </li>
                        </ul>
                    </li>


                    <li
                        class="treeview <?= (isset($modulo) && in_array($modulo, [ModulosAdmin::MIS_RECAUDACIONES,ModulosAdmin::RECAUDACIONES,ModulosAdmin::RECAUDACIONES_ONLINE])) ? 'active' : '' ?>">
                        <a href="#"> <i class="fa fa-book"></i>

                        <span>Reportes</span>
                            <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
                        <ul class="treeview-menu">

                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::RECAUDACIONES)) ? 'active' : '' ?>">
                                <a href="<?= base_url(relativePath: 'admin/recaudaciones/usuarios'); ?>">Consolidado</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::MIS_RECAUDACIONES)) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/recaudaciones'); ?>">Mis recaudaciones</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::RECAUDACIONES_ONLINE)) ? 'active' : '' ?>">
                                <a href="<?= base_url(relativePath: 'admin/recaudaciones/online'); ?>">En linea</a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="treeview <?= (isset($modulo) && in_array($modulo, [ModulosAdmin::USERS,ModulosAdmin::USERS_EVENTS])) ? 'active' : '' ?>">
                        <a href="#"> <i class="fa fa-user-o"></i> <span>Usuarios</span> <span
                                class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
                        <ul class="treeview-menu">
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::USERS)) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/users'); ?>">Lista</a>
                            </li>
                            <li
                                class="<?= (isset($modulo) && checkActiveModule($modulo, ModulosAdmin::USERS_EVENTS)) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/users-events'); ?>">Eventos</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <?= $this->renderSection('content'); ?>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <!-- v4.0.0-alpha.6 -->

    <!-- Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- template -->
    <script src="<?= base_url("dist/js/niche.js") ?>"></script>
    <script src="<?= base_url("dist/bootstrap/js/bootstrap.min.js") ?>"></script>


    <!-- DataTables JS-->
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>


    <!-- DataTables Bootstrap -->
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>

    <!-- DataTables responsive -->
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.js"></script>
    <!-- Botones de DataTables JS-->
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>

    <!-- Exportar a PDF, Excel y CSV -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script src="<?= base_url('assets/js/flashMessages.js') ?>"></script>
    <script>
        // var flashMessages = <?= getFlashMessages(isset($flashMessages) ? $flashMessages : null, true) ?>;
        // for (let element in flashMessages) {
        //     showFlashMessage(flashMessages[element][0], flashMessages[element][1]);
        // }
        // Global variables
        var base_url = "<?= base_url() ?>"
        var current_url = "<?= uri_string() ?>";
    </script>

    <!-- tooltip -->
    <script src="<?= base_url('dist/plugins/tooltip/tooltip.js') ?>"></script>
    <script src="<?= base_url('dist/plugins/tooltip/script.js') ?>"></script>
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showAlert(type, message, uniqueCode = null) {
            if (type === 'success') {
                let html = `<div>${message}</div>`;
                if (uniqueCode) {
                    html += `<div class="mt-3"><a class="btn btn-outline-danger" href="${base_url}pdf/${uniqueCode}" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver PDF</a></div>`;
                }
                Swal.fire({
                    title: "<strong>¡Éxito!</strong>",
                    icon: "success",
                    html: html,
                    showCloseButton: true,
                    showCancelButton: false,
                    focusConfirm: false,
                    confirmButtonText: `<i class="fa fa-thumbs-up"></i> Entendido!`,
                    confirmButtonAriaLabel: "Thumbs up, great!",
                });
            } else {
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
    </script>
    <!-- DataTable -->
    <script src="<?= base_url("dist/plugins/datatables/jquery.dataTables.min.js") ?>"></script>
    <script src="<?= base_url("dist/plugins/datatables/dataTables.bootstrap.min.js") ?>"></script>

    <script src="<?= base_url("assets/js/datatables.js") ?>"></script>

    <script src="<?= base_url("assets/js/preloader.js") ?>"></script>
    <?= $this->renderSection('scripts'); ?>
</body>

</html>