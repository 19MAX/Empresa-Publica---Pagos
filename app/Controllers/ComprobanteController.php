<?php
namespace App\Controllers;

class ComprobanteController extends BaseController
{
    public function mostrarComprobante($fileName)
    {
        $filePath = WRITEPATH . 'uploads/comprobantes/' . $fileName;

        if (file_exists($filePath)) {
            $mimeType = mime_content_type($filePath);
            header('Content-Type: ' . $mimeType);
            readfile($filePath);
            exit;
        } else {
            // Mostrar un mensaje o imagen predeterminada si no existe el comprobante
            return $fileName .' Archivo no encontrado.';
        }
    }
}
