<?php
if (!function_exists('format_event_date')) {
    function format_event_date($date)
    {
        // Convierte la fecha de Y-m-d a una marca de tiempo
        $timestamp = strtotime($date);
        // Formatea la fecha en el formato deseado
        $formattedDate = "Inicia el " . date("d", $timestamp) . " de " . date("F", $timestamp) . " de " . date("Y", $timestamp);

        // Opcional: Traducir los meses al español
        $formattedDate = str_replace(
            ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
            $formattedDate
        );

        return $formattedDate;
    }
}
