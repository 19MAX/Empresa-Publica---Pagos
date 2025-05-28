<?php
if (!function_exists('get_payment_status_class')) {
    function get_payment_status_class($estado_pago) {
        switch (strtolower($estado_pago)) {
            case 'completado':
                return 'label label-success';
            case 'pendiente':
                return 'label label-warning';
            case 'rechazado':
                return 'label label-danger';
            default:
                return 'label label-default';
        }
    }
}
if (!function_exists('get_payment_deposit_status')) {
    function get_payment_deposit_status($estado_pago) {
        switch (strtolower($estado_pago)) {
            case 'aprobado':
                return 'label label-success';
            case 'pendiente':
                return 'label label-info text-black';
            case 'rechazado':
                return 'label label-danger';
            default:
                return 'label label-default';
        }
    }
}
?>