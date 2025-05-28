<?php

if (!function_exists('uploadImage')) {
    function uploadImage($file, $path = 'assets/images/events/')
    {
        // Ruta de subida completa
        $uploadPath = ROOTPATH . 'public/' . $path;

        // Verificar si la ruta existe, si no, crearla
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        try {
            // Validar que el archivo es válido y no se ha movido
            if ($file->isValid() && !$file->hasMoved()) {
                // Generar un nombre aleatorio para la imagen
                $newName = $file->getRandomName();
                // Mover la imagen al destino
                $file->move($uploadPath, $newName);
                // Retornar la ruta de la imagen excluyendo "public"
                return $path . $newName;
            }
            return false;
        } catch (\Exception $e) {
            log_message('error', 'Error uploading image: ' . $e->getMessage());
            return false;
        }
    }
}
