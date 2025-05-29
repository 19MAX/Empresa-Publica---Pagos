<?php

namespace App\Services;

use App\Models\ConfigModel;
use App\Models\PaymentsModel;
use App\Models\PaymentMethodsModel;
use CodeIgniter\I18n\Time;
use PaymentStatus;

class PaymentAprobarService
{
    protected $paymentsModel;
    protected $paymentMethodModel;
    protected $configModel;

    public function __construct()
    {
        $this->paymentsModel = new PaymentsModel();
        $this->paymentMethodModel = new PaymentMethodsModel();
        $this->configModel = new ConfigModel();
    }

    public function approvePayment($paymentId, $userId, $metodoPago)
    {
        helper('ramdom');
        $uniqueCode = generateUniqueNumericCode(50);

        $payment = $this->paymentsModel->pagoData($paymentId);
        // Obtener el valor de additional_charge
        $additional_charge = $this->configModel->getAdditionalCharge();
        if (!$payment) {
            return ['success' => false, 'message' => 'Pago no encontrado'];
        }

        // Validación para evitar el reenvío de email si ya se envió
        if ($payment['send_email'] == 1) {
            return ['success' => false, 'message' => 'El email ya ha sido enviado previamente'];
        }


        $local = $this->paymentMethodModel->paymentLocal(2);
        if (!$local) {
            return ['success' => false, 'message' => 'Método de pago físico desactivado'];
        }
        $local = $this->paymentMethodModel->paymentLocal(3);
        if (!$local) {
            return ['success' => false, 'message' => 'Método de pago en linea desactivado'];
        }

        $datosPago = $this->calculatePaymentDetails($payment['precio'], $uniqueCode,$additional_charge,$metodoPago);

        try {
            $this->paymentsModel->updatePaymentAndInsertInscripcionPago($paymentId, $datosPago, $userId);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error en la actualización del pago: ' . $e->getMessage()];
        }

        $pdfResult = $this->generateAndSendPDF($uniqueCode);
        if ($pdfResult !== 'success') {
            return ['success' => false, 'message' => 'Error al generar o enviar el PDF: ' . $pdfResult];
        }

        return ['success' => true, 'message' => 'Pago aprobado y email enviado correctamente', 'uniqueCode' => $uniqueCode];
    }

    protected function calculatePaymentDetails($precio, $uniqueCode,$adicional,$metodoPago)
    {
        $cantidad = 1;
        $fecha_emision = Time::now();
        $precio_unitario = $adicional / 1.15;
        $sub_total_0 = $precio-$adicional;
        $subtotal = $sub_total_0 + $precio_unitario;
        $subtotal_15 = $precio_unitario;
        $iva_15 = $precio_unitario * 0.15;
        $total = $precio_unitario + $iva_15;

        return [
            "num_autorizacion" => $uniqueCode,
            "date_time_payment" => $fecha_emision,
            "payment_status" => PaymentStatus::Completado,
            "amount_pay" => $precio,
            "precio_unitario" => $precio_unitario,
            "sub_total" => $subtotal,
            "sub_total_0" => $sub_total_0,
            "sub_total_15" => $subtotal_15,
            "iva" => $iva_15,
            "valor_total" => $precio_unitario,
            "total" => $total,
            "payment_method_id" => $metodoPago,
        ];
    }

    protected function generateAndSendPDF($num_autorizacion)
    {
        $payment = $this->paymentsModel->numeroAutorizacion($num_autorizacion);
        if (!$payment) {
            return 'Pago no encontrado';
        }

        // Datos para correo de tipo comprobante de pago
        $emailDataFacture = [
            'to' => $payment['email_user'],
            'subject' => 'Comprobante de Pago',
            'message' => 'Estimado ' . $payment['user'] . ', adjuntamos su comprobante de pago.',
            'htmlContent' => '', // El contenido HTML puede estar vacío si no es necesario
            'pdfFilename' => 'comprobante_pago.pdf',
            'emailType' => 'send_email_facture',
            'paymentData' => [
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
                'operador' => $payment['operador'] ?? 'Payphone',
                'amount_pay' => $payment['amount_pay'],
                'event_name' => $payment['event_name'],
                'metodo_pago' => $payment['metodo_pago'],
            ]
        ];

        // Añadir el trabajo a la cola para factura
        service('queue')->push('emails', 'email', $emailDataFacture);
        return 'success';
    }
}