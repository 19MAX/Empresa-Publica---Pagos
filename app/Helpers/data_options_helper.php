<?php
class RolesOptions
{
    const AdminPrincipal = 1;
    const AdministradorDePagos = 2;
    const AdministradorProservi = 3;
    const UsuarioPublico = 4;
}

class ModulosAdmin
{
    const DASHBOARD = 'DASHBOARD';
    const CATEGORIES = 'CATEGORIES';
    const CATEGORY_LIST = 'CATEGORY_LIST';
    const CATEGORY_ADD = 'CATEGORY_ADD';
    const EVENTS = 'EVENTS';
    const EVENTS_LIST = 'EVENTS_LIST';
    const EVENTS_ADD = 'EVENTS_ADD';
    const PAGOS = 'PAGOS';
    const DEPOSITO_ALL = 'DEPOSITO_ALL';
    const PAGOS_COMPLETOS = 'PAGOS_COMPLETOS';
    const PAGOS_RECHAZADOS = 'PAGOS_RECHAZADOS';
    const PAGOS_INCOMPLETOS = 'PAGOS_INCOMPLETOS';
    const USERS = 'USERS';
    const INSCRIPCIONES = 'INSCRIPCIONES';
    const INSCRIPCIONES_ELIMINADAS = 'INSCRIPCIONES_ELIMINADAS';
    const MIS_RECAUDACIONES = 'MIS_RECAUDACIONES';
    const RECAUDACIONES = 'RECAUDACIONES';
    const RECAUDACIONES_ONLINE = 'RECAUDACIONES_ONLINE';
    const AUTORES = 'AUTORES';
}

class ModulosAdminPagos
{
    const DASHBOARD = 'DASHBOARD';
    const INSCRIPCIONES = 'INSCRIPCIONES';
    const CATEGORY_LIST = 'CATEGORY_LIST';
    const CATEGORY_ADD = 'CATEGORY_ADD';
    const EVENTS = 'EVENTS';
    const EVENTS_LIST = 'EVENTS_LIST';
    const EVENTS_ADD = 'EVENTS_ADD';
    const PAGOS = 'PAGOS';
    const PAGOS_COMPLETOS = 'PAGOS_COMPLETOS';
    const PAGOS_RECHAZADOS = 'PAGOS_RECHAZADOS';
    const PAGOS_INCOMPLETOS = 'PAGOS_INCOMPLETOS';
    const USERS = 'USERS';
    const MIS_RECAUDACIONES = 'MIS_RECAUDACIONES';
}

class PaymentStatus
{
    const Pendiente = 1;
    const Completado = 2;
    const Fallido = 3;
    const EnProceso = 4;
    const Incompleto = 5;
    const Rechazado = 6;
}

class Modalidad
{
    const Presencial = 'Presencial';
    const Virtual = 'Virtual';
    const Hibrida = 'Hibrida';

}

if (!function_exists('getPaymentStatusText')) {
    function getPaymentStatusText($status)
    {
        switch ($status) {
            case PaymentStatus::Pendiente:
                return 'Pendiente';
            case PaymentStatus::Completado:
                return 'Completado';
            case PaymentStatus::Fallido:
                return 'Fallido';
            case PaymentStatus::EnProceso:
                return 'EnProceso';
            case PaymentStatus::Incompleto:
                return 'Incompleto';
            case PaymentStatus::Rechazado:
                return 'Rechazado';
            default:
                return 'Desconocido';
        }
    }
}
if (!function_exists('style_estado')) {
    function style_estado($estado_pago)
    {
        switch (strtolower($estado_pago)) {
            case PaymentStatus::Pendiente:
                return 'label label-primary';
            case PaymentStatus::Completado:
                return 'label label-success';
            case PaymentStatus::Fallido:
                return 'label label-danger';
            case PaymentStatus::EnProceso:
                return 'label label-info';
            case PaymentStatus::Incompleto:
                return 'label label-warning text-black';
            case PaymentStatus::Rechazado:
                return 'label label-danger';
            default:
                return '';
        }
    }
}
if (!function_exists('mapEstadoToValue')) {
    function mapEstadoToValue($estadoText)
    {
        switch (strtolower($estadoText)) {
            case 'pendiente':
                return PaymentStatus::Pendiente;
            case 'completado':
                return PaymentStatus::Completado;
            case 'fallido':
                return PaymentStatus::Fallido;
            case 'enproceso':
                return PaymentStatus::EnProceso;
            case 'Incompleto':
                return PaymentStatus::Incompleto;
            default:
                return null; // Estado inválido
        }
    }
}
if (!function_exists('mapValueToEstado')) {
    function mapValueToEstado($value)
    {
        switch ($value) {
            case PaymentStatus::Pendiente:
                return 'pendiente';
            case PaymentStatus::Completado:
                return 'completado';
            case PaymentStatus::Fallido:
                return 'fallido';
            case PaymentStatus::EnProceso:
                return 'enproceso';
            case PaymentStatus::Incompleto:
                return 'Incompleto';
            default:
                return 'desconocido';
        }
    }

}

if (!function_exists('getRolesOptions')) {
    function getRolesOptions()
    {
        return [
            RolesOptions::AdminPrincipal => 'Admin Principal',
            RolesOptions::AdministradorDePagos => 'Administrador de Pagos',
            RolesOptions::AdministradorProservi => 'Usuario Proservi',
            // RolesOptions::UsuarioPublico => 'Usuario Público',
        ];
    }
}
if (!function_exists('getListRolesOptions')) {
    function getListRolesOptions($rol)
    {
        switch ($rol) {
            case RolesOptions::AdminPrincipal:
                return 'Admin Principal';
            case RolesOptions::AdministradorDePagos:
                return 'Administrador de Pagos';
            case RolesOptions::AdministradorProservi:
                return 'Usuario Proservi';
            default:
                return null; // Estado inválido
        }
    }
}