<?php

namespace App\Jobs;

use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;

class Email extends BaseJob implements JobInterface
{
    protected int $retryAfter = 60;
    protected int $tries = 3;

    public function process()
    {
        $to = $this->data['to'];
        $subject = $this->data['subject'];
        $message = $this->data['message'];
        $htmlContent = $this->data['htmlContent'];
        $pdfFilename = $this->data['pdfFilename'] ?? null;
        $paymentData = $this->data['paymentData'] ?? null; // Datos de pago si aplica
        try {

            //No generar pdf y solo enviar el correo

            if (empty($pdfFilename)) {
                $email = service('email', null, false);
                $email->setTo($to);
                $email->setSubject($subject);
                $email->setMessage($message . '<br><br>' . $htmlContent); // Incluir contenido HTML
                $result = $email->send(false);
                if (!$result) {
                    throw new Exception($email->printDebugger(['headers']));
                }
                return $result;
            }

            // Generar PDF según el tipo de correo
            if ($this->data['emailType'] === 'send_email_facture' && $paymentData) {
                // Generar el PDF de la factura
                $pdfData = $this->generate_pdf($paymentData);
                $pdfOutput = $pdfData['output'];
            } else {
                // Generar un PDF estándar desde el contenido HTML proporcionado
                $options = new Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isRemoteEnabled', true);
                $options->set('defaultFont', 'Arial');
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($htmlContent);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $pdfOutput = $dompdf->output();
            }

            // Guardar el PDF temporalmente en el servidor
            $tempPdfPath = WRITEPATH . 'uploads/' . $pdfFilename;
            file_put_contents($tempPdfPath, $pdfOutput);

            // Configurar y enviar el correo electrónico
            $email = service('email', null, false);
            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessage($message . '<br><br>' . $htmlContent); // Incluir contenido HTML
            $email->attach($tempPdfPath); // Adjuntar PDF

            // Intentar enviar el correo
            $result = $email->send(false);

            // Eliminar el archivo temporal después de enviar el correo
            unlink($tempPdfPath);

            if (!$result) {
                throw new Exception($email->printDebugger(['headers']));
            }

            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error al enviar el email: ' . $e->getMessage());
            // Lanzar la excepción para que el job falle y pueda ser reintentado
            throw $e;
        }
    }
    public function generate_pdf($payment)
    {
        // Configurar las opciones de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        // Crear una nueva instancia de Dompdf con las opciones configuradas
        $pdf = new Dompdf($options);

        // Contenido HTML de la factura
        $html = view('email/facture', [
            'num_autorizacion' => $payment['num_autorizacion'],
            'user' => $payment['user'],
            'user_ic' => $payment['user_ic'],
            'fecha_emision' => $payment['fecha_emision'],
            'precio_unitario' => $payment['precio_unitario'],
            'valor_total' => $payment['valor_total'],
            'sub_total' => $payment['sub_total'],
            'sub_total_0' => $payment['sub_total_0'],
            'sub_total_15' => $payment['sub_total_15'],
            'iva' => $payment['iva'],
            'total' => $payment['total'],
            'email_user' => $payment['email_user'],
            'user_tel' => $payment['user_tel'],
            'operador' => $payment['operador'],
            'valor_final' => $payment['amount_pay'],
            'event_name' => $payment['event_name'],
            'metodo_pago' => $payment['metodo_pago'],
        ]);

        $pdf->loadHtml($html);

        // Tamaño de la página en puntos (58 x 210 mm)
        $customPaper = array(0, 0, 164.41, 595.51); // 58mm = 164.41pt, 210mm = 595.51pt
        $pdf->setPaper($customPaper);

        // Renderizar el PDF
        $pdf->render();

        // Obtener la salida del PDF como una cadena
        $output = $pdf->output();

        // Devolver tanto el objeto Dompdf como el contenido del PDF
        return ['pdf' => $pdf, 'output' => $output];
    }
}
