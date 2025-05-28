<?php
if (!function_exists('formatear_nombre_apellido')) {
    function formatear_nombre_apellido($nombres, $apellidos)
    {
        // Normalizar cadenas a UTF-8 (por si vienen con caracteres especiales mal codificados)
        $nombres = mb_convert_encoding($nombres, 'UTF-8', 'UTF-8');
        $apellidos = mb_convert_encoding($apellidos, 'UTF-8', 'UTF-8');

        // Remover caracteres especiales innecesarios (opcional, si deseas limpiar más)
        $nombres = preg_replace('/[^\p{L}\s]/u', '', $nombres);
        $apellidos = preg_replace('/[^\p{L}\s]/u', '', $apellidos);

        // Nombres
        $partes_nombre = explode(" ", $nombres);
        if (count($partes_nombre) > 1) {
            $segundo_nombre_formateado = mb_substr($partes_nombre[1], 0, 1) . str_repeat("x", max(0, 7));  // 1 letra + 7 'x'
            $nombre_formateado = $partes_nombre[0] . ' ' . $segundo_nombre_formateado;
        } else {
            $nombre_formateado = $partes_nombre[0] . str_repeat("x", max(0, 8 - mb_strlen($partes_nombre[0])));
        }

        // Apellidos
        $partes_apellido = explode(" ", $apellidos);
        if (count($partes_apellido) > 1) {
            $apellido1_formateado = mb_substr($partes_apellido[0], 0, 1) . str_repeat("x", 7);
            $apellido2_formateado = mb_substr($partes_apellido[1], 0, 1) . str_repeat("x", 7);
            $apellidos_formateados = $apellido1_formateado . " " . $apellido2_formateado;
        } else {
            $apellidos_formateados = mb_substr($partes_apellido[0], 0, 1) . str_repeat("x", 7);
        }

        return [
            'nombres' => $nombre_formateado,
            'apellidos' => $apellidos_formateados
        ];
    }
}
