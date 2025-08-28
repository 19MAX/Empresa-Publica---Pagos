<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

// ADMIN
$routes->group('admin', static function ($routes) {

    $routes->get('comprobantes/(:any)', 'ComprobanteController::mostrarComprobante/$1');

    $routes->get('dashboard', 'Admin\DashboardController::index');

    $routes->group('autores', static function ($autores) {
        $autores->get('/', 'Admin\AutoresController::index');
        $autores->post('add', 'Admin\AutoresController::add');
        $autores->post('update/(:num)', 'Admin\AutoresController::update/$1');
        $autores->post('delete/(:num)', 'Admin\AutoresController::delete/$1');
    });

    $routes->post('buscar', 'Admin\FiltrosController::buscarPorCedula');
    $routes->get('inscripciones/', 'Admin\FiltrosController::index');
    $routes->get('inscripciones/(:num)', 'Admin\InscripcionesController::index/$1');
    $routes->get('inscripciones/(:num)/(:alpha)', 'Admin\InscripcionesController::index/$1/$2');
    $routes->post('pago/', 'Admin\InscripcionesController::pago');


    $routes->get('config', 'Config\ConfigController::index');
    $routes->post('config/add', 'Config\ConfigController::add');
    $routes->post('config/update', 'Config\ConfigController::update');

    $routes->get('deposito_all', 'Admin\DepositosController::deposits_all');
    $routes->group('pagos', static function ($pagos) {

        $pagos->get('/', 'Admin\PagosController::index');
        $pagos->get('(:num)', 'Admin\DepositosController::index/$1');
        $pagos->get('getDatosPgDeposito/(:num)', 'Admin\DepositosController::getDatosPagoDeposito/$1');
        $pagos->post('actualizarEstado/', 'Admin\DepositosController::actualizarEstado');
        $pagos->get('obtener_depositos/(:num)', 'Admin\DepositosController::obtenerDeposito/$1');
        $pagos->post('aprobar/', 'Admin\DepositosController::aprobar_deposito');
        $pagos->post('incompleto/', 'Admin\DepositosController::pago_incompleto');
        $pagos->post('rechazar/', 'Admin\DepositosController::rechazar');
        $pagos->get('verificarDepositoRechazado/(:num)', 'Admin\DepositosController::verificarDepositoRechazado/$1');
        $pagos->get('verificarDepositoIncompleto/(:num)', 'Admin\DepositosController::verificarDepositoIncompleto/$1');

        $pagos->get('completados', 'Admin\PagosController::completados');
        $pagos->get('rechazados', 'Admin\PagosController::rechazados');
        $pagos->get('incompletos', 'Admin\PagosController::incompletos');
    });

    $routes->group('event', static function ($categories) {
        $categories->get('/', 'Admin\EventsController::index');
        $categories->get('get/(:num)', 'Admin\EventsController::get/$1');
        $categories->get('new', 'Admin\EventsController::new_event');
        $categories->get('edit/(:num)', 'Admin\EventsController::edit_event/$1');
        $categories->post('add', 'Admin\EventsController::add');
        $categories->post('update', 'Admin\EventsController::update');
        $categories->post('delete', 'Admin\EventsController::delete');
        $categories->post('deleteAll', 'Admin\EventsController::deleteAll');

        $categories->get('get_event', 'Admin\EventsController::get_event');
        $categories->get('get_categories_by_event/(:num)', 'Admin\EventsController::get_categories_by_event/$1');

        $categories->group('inscritos', static function ($inscritos) {
            $inscritos->get('(:num)', 'Admin\RegistrationsController::inscritos/$1');
            $inscritos->post('(:num)', 'Admin\RegistrationsController::inscritos/$1');
            $inscritos->post('add', 'Admin\RegistrationsController::add');
            $inscritos->post('update', 'Admin\RegistrationsController::update');
            $inscritos->post('delete', 'Admin\RegistrationsController::delete');
        });

        $categories->group('trash', static function ($trash) {
            $trash->get('/', 'Admin\EventsController::trash');
            $trash->post('restore', 'Admin\EventsController::restore');
        });
    });

    $routes->group('users-events', static function ($authors) {
        $authors->get('/', 'Admin\UsersEventsController::index');
        $authors->post('add', 'Admin\UsersEventsController::add');
        $authors->post('update', 'Admin\UsersEventsController::update');
        $authors->post('delete', 'Admin\UsersEventsController::delete');
    });

    $routes->group('users', static function ($categories) {
        $categories->get('/', 'Admin\UsersController::index');
        $categories->post('add', 'Admin\UsersController::add');
        $categories->post('update', 'Admin\UsersController::update');
        $categories->post('delete', 'Admin\UsersController::delete');
        $categories->post('recover_password', 'Admin\UsersController::recoverPassword');

    });

    $routes->group('inscritos', static function ($inscritos) {
        $inscritos->get('/', 'Admin\RegistrationsController::allInscritos');
        $inscritos->post('update', 'Admin\InscripcionesController::update');
        $inscritos->post('delete', 'Admin\InscripcionesController::delete');
        $inscritos->get('trash', 'Admin\RegistrationsController::trash');
        $inscritos->post('restore', 'Admin\RegistrationsController::restore');
        $inscritos->post('deleteAll', 'Admin\RegistrationsController::deleteAll');
    });
    $routes->group('recaudaciones', static function ($recaudaciones){
        $recaudaciones->get('/', 'Admin\UsersController::recaudaciones');
        $recaudaciones->get('online', 'Admin\UsersController::online');
        $recaudaciones->get('usuarios', 'Admin\UsersController::all_recaudaciones');
        $recaudaciones->post('filtrar_recaudaciones', 'Admin\UsersController::all_recaudaciones');
    });

    $routes->group('category', static function ($categories) {
        $categories->get('/', 'Admin\CategoriesController::index');
        $categories->post('add', 'Admin\CategoriesController::add');
        $categories->post('update', 'Admin\CategoriesController::update');
        $categories->post('delete', 'Admin\CategoriesController::delete');
    });
});

//Punto de pago
$routes->group('punto/pago', static function ($routes) {

    $routes->get('/', 'Payments\DashboardController::index');
    $routes->get('inscripciones/', 'Payments\FiltrosController::index');
    $routes->get('inscripciones/(:num)', 'Payments\InscripcionesController::index/$1');
    $routes->get('inscripciones/(:num)/(:alpha)', 'Payments\InscripcionesController::index/$1/$2');
    $routes->post('pago/', 'Payments\InscripcionesController::pago');
    $routes->post('aprobar_deposito/', 'Payments\DepositosController::aprobar');
    $routes->post('pago_rechazado/', 'Payments\DepositosController::rechazar');
    $routes->post('pago_incompleto/', 'Payments\DepositosController::pagoIncompleto');
    $routes->post('buscar', 'Payments\FiltrosController::buscarPorCedula');
    // $routes->group('depositos', static function ($depositos) {
    //     $depositos->get('/', 'Payments\DepositosController::index');
    //     $depositos->get('completados', 'Payments\DepositosController::completados');
    //     $depositos->get('rechazados', 'Payments\DepositosController::rechazados');
    //     $depositos->get('incompletos', 'Payments\DepositosController::incompletos');
    // });
    $routes->group('user', static function ($depositos) {
        $depositos->get('/', 'Payments\UserController::index');
        $depositos->post('update', 'Payments\UserController::update');
        $depositos->post('recoverPassword', 'Payments\UserController::recoverPassword');
    });

    $routes->group('recaudaciones', static function ($recaudaciones){
        $recaudaciones->get('/', 'Payments\UserController::recaudaciones');
    });

    // $routes->get('deposito/(:num)', 'Payments\PocesosDepController::index/$1');
    // $routes->get('getDatosPgDeposito/(:num)', 'Payments\PocesosDepController::getDatosPagoDeposito/$1');
    // $routes->post('actualizarEstado/', 'Payments\PocesosDepController::actualizarEstado');
    // $routes->get('obtener_depositos/(:num)', 'Payments\PocesosDepController::obtenerDeposito/$1');
    // $routes->post('aprobar/', 'Payments\PocesosDepController::aprobar_deposito');
    // $routes->post('incompleto/', 'Payments\PocesosDepController::pago_incompleto');
    // $routes->post('rechazar/', 'Payments\PocesosDepController::rechazar');
    // $routes->get('verificarDepositoRechazado/(:num)', 'Payments\PocesosDepController::verificarDepositoRechazado/$1');
    // $routes->get('verificarDepositoIncompleto/(:num)', 'Payments\PocesosDepController::verificarDepositoIncompleto/$1');
});

// PROSERVI
$routes->group('proservi',  ['filter' => 'AuthorPermission'], static function ($routes) {

    $routes->get('reportes', 'Proservi\ReportesController::index');

    $routes->group('users', static function ($categories) {
        $categories->get('/', 'Proservi\UsersController::index');
        $categories->post('add', 'Proservi\UsersController::add');
        $categories->post('update', 'Proservi\UsersController::update');
        $categories->post('delete', 'Proservi\UsersController::delete');
        $categories->post('recover', 'Proservi\UsersController::recover');

    });

});

$routes->group('user-event', static function ($routes) {
    $routes->get('/', 'UserEvent\ReporteController::index');
});


//Pdf
$routes->get('pdf/(:hash)', 'Payments\InscripcionesController::showPDF/$1');
//Payphone
$routes->post('payphone', 'Payphone\PayphoneController::index');
$routes->get('respuesta', 'Payphone\PayphoneController::respuesta');
// $routes->get('respuesta_manual', 'Payphone\PayphoneController::respuesta_manual');
$routes->get('completado/(:num)/(:segment)', 'Payphone\PayphoneController::completado/$1/$2');

//Client
$routes->get('/', 'Client\ClientController::authors');
$routes->get('authors', 'Client\ClientController::authors');
$routes->post('validar_cedula', 'Client\InscripcionController::validarCedula',['filter' => 'csrf']);
$routes->post('obtener_datos_evento', 'Client\InscripcionController::obtenerDatosEvento');
$routes->post('guardar_inscripcion', 'Client\InscripcionController::guardarInscripcion');
$routes->post('registrar_usuario', 'Client\InscripcionController::registrarUsuario', ['filter' => 'antibot']);
$routes->post('deposito', 'Client\DepositosController::deposito');
$routes->post('monto_pago', 'Client\DepositosController::fetchMontoDeposito');
$routes->post('limpiar_persona', 'Client\InscripcionController::limpiarSesionPersona');

//Auth
$routes->get('login', 'Auth\LoginController::index');
$routes->get('logout', 'Auth\LoginController::logout');
$routes->post('validate_login', 'Auth\LoginController::login');
// $routes->get('forgotPassword', 'Auth\LoginController::forgotPassword');
// $routes->get('register', 'Auth\LoginController::register');
