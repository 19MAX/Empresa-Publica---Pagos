<?php
use CodeIgniter\I18n\Time;
use Dompdf\Dompdf;
use Dompdf\Options;

if (!function_exists('send_email_with_pdf')) {
    function send_email_with_pdf($to, $subject, $message, $htmlContent, $pdfFilename = 'document.pdf')
    {
        // Configurar las opciones de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        // Crear una nueva instancia de Dompdf
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        // Guardar el PDF temporalmente en el servidor
        $tempPdfPath = WRITEPATH . 'uploads/' . $pdfFilename;
        file_put_contents($tempPdfPath, $pdfOutput);

        // Configurar y enviar el correo electrónico
        $email = \Config\Services::email();
        $email->setTo($to);
        $email->setSubject($subject);

        // Aquí se adjunta el contenido HTML al mensaje
        $email->setMessage($message . '<br><br>' . $htmlContent); // Aquí se incluye el contenido HTML

        // Adjuntar el PDF también
        $email->attach($tempPdfPath);

        // Intentar enviar el correo
        if ($email->send()) {
            // Eliminar el archivo temporal después de enviar el correo
            unlink($tempPdfPath);
            return "success";
        } else {
            // Obtener cualquier error del correo
            $error = $email->printDebugger(['headers']);
            log_message('error', 'Error enviando correo: ' . $error);

            // Eliminar el archivo temporal en caso de error
            unlink($tempPdfPath);
            return $error;
        }
    }
}

if (!function_exists('send_email_with_pdf_from_path')) {
    function send_email_with_pdf_from_path($to, $subject, $message, $pdfFilePath)
    {
        // Configurar y enviar el correo electrónico
        $email = \Config\Services::email();
        // $email->setFrom('inscripciones@test.com', 'TEST');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);
        $email->attach($pdfFilePath);

        // Intentar enviar el correo
        if ($email->send()) {
            return "success";
        } else {
            // Obtener cualquier error del correo
            $error = $email->printDebugger(['headers']);
            log_message('error', 'Error enviando correo: ' . $error);
            return $error;
        }
    }
}

if (!function_exists('send_rejection_email')) {
    function send_rejection_email($to, $subject, $rejectionReason, $names, $codigoPago, $nombreEvento, $view, $valor_pendiente = null)
    {
        // Configurar y enviar el correo electrónico
        $email = \Config\Services::email();
        $email->setTo($to);
        $email->setSubject($subject);

        // Preparar datos para la vista
        $data = [
            'rejectionReason' => $rejectionReason,
            'names' => $names,
            'codigoPago' => $codigoPago,
            'nombreEvento' => $nombreEvento
        ];

        // Solo agregar 'valor_pendiente' si está definido
        if ($valor_pendiente !== null) {
            $data['valor_pendiente'] = $valor_pendiente;
        }

        // Cargar la vista con el contenido HTML del email
        $htmlContent = view($view, $data);
        $email->setMessage($htmlContent);

        // Intentar enviar el correo
        if ($email->send()) {
            return "success";
        } else {
            // Obtener cualquier error del correo
            $error = $email->printDebugger(['headers']);
            log_message('error', 'Error enviando correo de rechazo: ' . $error);
            return $error;
        }
    }

}


if (!function_exists('email_rechazo_general')) {
    function email_rechazo_general($to, $subject, $rejectionReason, $names, $codigoPago, $nombreEvento, $view)
    {
        // Configurar y enviar el correo electrónico
        $email = \Config\Services::email();
        $email->setTo($to);
        $email->setSubject($subject);



        // Preparar datos para la vista
        $data = [
            'rejectionReason' => $rejectionReason,
            'names' => $names,
            'codigoPago' => $codigoPago,
            'nombreEvento' => $nombreEvento,
        ];

        // Cargar la vista con el contenido HTML del email
        $htmlContent = view($view, $data);
        $email->setMessage($htmlContent);

        // Intentar enviar el correo
        if ($email->send()) {
            return "success";
        } else {
            // Obtener cualquier error del correo
            $error = $email->printDebugger(['headers']);
            log_message('error', 'Error enviando correo de rechazo: ' . $error);
            return $error;
        }
    }

}

if (!function_exists('mask_email')) {
    function mask_email($email)
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = isset($parts[1]) ? $parts[1] : '';

        // Mostrar las primeras 4 letras del nombre
        $name_length = strlen($name);
        $masked_name = substr($name, 0, min(4, $name_length)) . str_repeat('*', max(0, $name_length - 4));

        // Mostrar la primera letra y última letra del dominio (sin tocar la extensión)
        $domain_parts = explode('.', $domain);
        $masked_domain = '';

        if (count($domain_parts) > 1) {
            $domain_name = $domain_parts[0];
            $domain_extension = $domain_parts[1];

            $domain_length = strlen($domain_name);
            $masked_domain = substr($domain_name, 0, 1) . str_repeat('*', max(0, $domain_length - 2)) . substr($domain_name, -1) . '.' . $domain_extension;
        } else {
            // En caso de que no haya una extensión
            $masked_domain = $domain;
        }

        return $masked_name . '@' . $masked_domain;
    }
}
if (!function_exists('mask_phone')) {
    function mask_phone($phone)
    {
        $length = strlen($phone);

        // Mostrar los últimos 4 dígitos
        $masked_phone = str_repeat('*', max(0, $length - 4)) . substr($phone, -4);

        return $masked_phone;
    }
}
if (!function_exists('payphone_status')) {
    function payphone_status($status)
    {
        if ($status == 'Approved') {
            return 'Aprobado';
        } elseif ($status == 'Cancela') {
            return 'Cancelado';
        }
    }
}

?>