<?php

namespace App\Controllers\Payphone;

use App\Controllers\BaseController;
use App\Services\PaymentAprobarService;
use App\Services\PayphoneService;
use App\Services\PayphoneConfirmService;
use App\Models\RegistrationsModel;
use App\Models\PaymentsModel;
use App\Models\PagoLineaModel;
use PaymentStatus;
use CodeIgniter\I18n\Time;

class PayphoneController extends BaseController
{

    private $payphoneService;
    private $PayphoneConfirmService;
    private $pagosEnLineaModel;
    private $paymentService;
    private $paymentsModel;
    private $registrationsModel;
    private $paymentApprovalService;

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $id = null, $clientTransactionId = null)
    {
        return redirect()->to("completado/$id/$clientTransactionId")->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data);
    }

    public function __construct()
    {
        helper('email');
        // Servicios
        $this->payphoneService = new PayphoneService();
        $this->PayphoneConfirmService = new PayphoneConfirmService();
        $this->paymentApprovalService = new PaymentAprobarService();

        // Modelos
        $this->pagosEnLineaModel = new PagoLineaModel();
        $this->paymentsModel = new paymentsModel();
        $this->registrationsModel = new RegistrationsModel();

    }
    private function generateTimestampId()
    {
        return date('YmdHis');
    }

    public function index()
    {
        $request = $this->request->getJSON();
        $depositoCedula = $request->cedula ?? null;
        $codigoPago = $request->codigoPago ?? null;

        if (!$depositoCedula || !$codigoPago) {
            return $this->response->setJSON(['error' => 'Datos incompletos'], 400);
        }

        try {
            // Primero verificamos si el pago ya está aprobado
            $pagoVerificado = $this->paymentsModel->verificarPagoAprobado($depositoCedula, $codigoPago);

            if ($pagoVerificado && $pagoVerificado['esta_aprobado']) {
                return $this->response->setJSON([
                    'success' => false,
                    'already_paid' => true,
                    'message' => 'Este pago ya ha sido aprobado anteriormente',
                ], 400);
            }

            // Si no está aprobado, procedemos con el proceso de pago
            $result = $this->registrationsModel->MountPayphone($depositoCedula, $codigoPago);

            if (!$result) {
                return $this->response->setJSON(['error' => 'No existen un registro con los datos enviados'], 404);
            }

            // Verificaciones de estado de pago...

            $payment_id = $result['payment_id'];
            $montoBase = $result['cantidad_dinero'];
            $por = 0.07;
            $iva = 1.15;

            $tot = ($montoBase * $por) * $iva;
            $total = round(($montoBase + $tot) * 100);

            $token = $this->payphoneService->getToken();
            $store = $this->payphoneService->getStore();

            $clientTransactionId = $payment_id . '-' . $depositoCedula . '-' . time();

            return $this->response->setJSON([
                'success' => true,
                'already_paid' => false,
                'data' => [
                    'token' => $token,
                    'store' => $store,
                    'amount' => $total,
                    'amountWithoutTax' => $total,
                    'amountWithTax' => 0,
                    'tax' => 0,
                    'service' => 0,
                    'tip' => 0,
                    'reference' => $result['event_name'] ?? 'Pago de inscripción',
                    'clientTransactionId' => $clientTransactionId
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'data' => 'Ocurrió un error al generar el pago.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function respuesta()
    {
        try {
            $id = $this->request->getGet('id') ?? null;
            $clientTransactionId = $this->request->getGet('clientTransactionId') ?? null;

            if (!$id || !$clientTransactionId) {
                return view('client/errors/error_datos_incompletos');
            }

            if (!preg_match('/^(\d+)-(\d+)-(\d+)$/', $clientTransactionId, $matches)) {
                return view('client/errors/error_pago_no_encontrado');
            }

            [$full, $paymentId, $cedula, $timestamp] = $matches;

            $payment = $this->paymentsModel->find($paymentId);
            if (!$payment) {
                return view('client/errors/error_pago_no_encontrado');
            }

            // Verificar si el pago ya ha sido aprobado
            $pagoVerificado = $this->paymentsModel->verificarPagoAprobado($cedula, $payment['payment_cod']);
            $yaAprobado = $pagoVerificado && $pagoVerificado['esta_aprobado'];

            $result = $this->PayphoneConfirmService->confirmTransaction($id, $clientTransactionId);

            if (!isset($result['data'])) {
                return view('client/errors/error_pago_no_encontrado');
            }

            $dataAPI = $result['data'];

            // Aprobar solo si aún no ha sido aprobado y el estado es "Approved"
            if (!$yaAprobado && $dataAPI['transactionStatus'] === 'Approved' && $dataAPI['statusCode'] == 3) {
                $this->paymentApprovalService->approvePayment($paymentId, null, '3');
            }

            // Solo insertar si aún no existe el registro de esta transacción
            $existePago = $this->pagosEnLineaModel
                ->where('transaction_id', $dataAPI['transactionId'])
                ->first();

            if (!$existePago) {
                $this->pagosEnLineaModel->insert([
                    'status_code' => $dataAPI['statusCode'],
                    'payment_id' => $paymentId,
                    'transaction_status' => $dataAPI['transactionStatus'],
                    'client_transaction_id' => $dataAPI['clientTransactionId'],
                    'authorization_code' => $dataAPI['authorizationCode'] ?? null,
                    'transaction_id' => $dataAPI['transactionId'],
                    'email' => $dataAPI['email'] ?? null,
                    'phone_number' => $dataAPI['phoneNumber'] ?? null,
                    'document' => $dataAPI['document'] ?? null,
                    'amount' => $dataAPI['amount'],
                    'card_type' => $dataAPI['cardType'] ?? null,
                    'card_brand' => $dataAPI['cardBrand'] ?? null,
                    'message' => $dataAPI['message'] ?? null,
                    'message_code' => $dataAPI['messageCode'] ?? null,
                    'currency' => $dataAPI['currency'],
                    'transaction_date' => $dataAPI['date'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $paymentSearch = $this->paymentsModel->find($paymentId);
            // Preparar datos para la vista completado, sin importar estado
            $data = [
                'status_code' => $dataAPI['statusCode'],
                'transaction_status' => payphone_status($dataAPI['transactionStatus']),
                'client_transaction_id' => $dataAPI['clientTransactionId'],
                'authorization_code' => $dataAPI['authorizationCode'] ?? null,
                'transaction_id' => $dataAPI['transactionId'],
                'email' => mask_email($dataAPI['email'] ?? ''),
                'phone_number' => mask_phone($dataAPI['phoneNumber'] ?? ''),
                'document' => $dataAPI['document'] ?? null,
                'amount' => $dataAPI['amount'],
                'card_type' => $dataAPI['cardType'] ?? null,
                'cardBrand' => $dataAPI['cardBrand'] ?? null,
                'message' => $dataAPI['message'] ?? null,
                'message_code' => $dataAPI['messageCode'] ?? null,
                'currency' => $dataAPI['currency'],
                'transaction_date' => $dataAPI['date'],
                'created_at' => date('Y-m-d H:i:s'),
                'store_name' => $dataAPI['storeName'] ?? null,
                'region_iso' => $dataAPI['regionIso'] ?? null,
                'transaction_type' => $dataAPI['transactionType'] ?? null,
                'reference' => $dataAPI['reference'] ?? null,
                'last_digits' => $dataAPI['lastDigits'] ?? null,
                'numAutorizacion' => $paymentSearch['num_autorizacion'] ?? null,
            ];

            return view('client/completado', $data);

        } catch (\Exception $e) {
            log_message('error', 'Error en respuesta payphone: ' . $e->getMessage());
            return view('client/errors/error_payphone');
        }
    }

    // public function completado($id, $clientTransactionId)
    // {
    //     // Extraer el paymentId y la cédula del clientTransactionId
    //     if (preg_match('/^(\d+)-(\d+)-(\d+)$/', $clientTransactionId, $matches)) {
    //         $paymentId = $matches[1];
    //     } else {
    //         // Si no se encuentra el patrón esperado, mostramos un error
    //         return view('client/errors/error_pago_no_encontrado');
    //     }
    //     $payment = $this->paymentsModel->find($paymentId);

    //     $result = $this->PayphoneConfirmService->confirmTransaction($id, $clientTransactionId);
    //     if (!isset($result['data']['transactionStatus']) || !isset($result['data']['statusCode'])) {
    //         // Redirige a la vista de error si alguna de las claves no existe
    //         return view('client/errors/error_payphone');
    //     }
    //     $transaction_status = $result['data']['transactionStatus'];
    //     $statusCode = $result['data']['statusCode'];

    //     if ($result['success'] && ($transaction_status == 'Approved' || $statusCode == 2)) {
    //         // Actualizar el estado del pago en la base de datos

    //         helper('email');
    //         $data = [
    //             'status_code' => $result['data']['statusCode'],
    //             'transaction_status' => payphone_status($result['data']['transactionStatus']),
    //             'client_transaction_id' => $result['data']['clientTransactionId'],
    //             'authorization_code' => $result['data']['authorizationCode'] ?? null,
    //             'transaction_id' => $result['data']['transactionId'],
    //             'email' => mask_email($result['data']['email']) ?? null,
    //             'phone_number' => mask_phone($result['data']['phoneNumber']) ?? null,
    //             'document' => $result['data']['document'] ?? null,
    //             'amount' => $result['data']['amount'],
    //             'card_type' => $result['data']['cardType'] ?? null,
    //             'cardBrand' => $result['data']['cardBrand'] ?? null,
    //             'message' => $result['data']['message'] ?? null,
    //             'message_code' => $result['data']['messageCode'] ?? null,
    //             'currency' => $result['data']['currency'],
    //             'transaction_date' => $result['data']['date'],
    //             'created_at' => date('Y-m-d H:i:s'),
    //             'store_name' => $result['data']['storeName'],
    //             'region_iso' => $result['data']['regionIso'],
    //             'transaction_type' => $result['data']['transactionType'],
    //             'reference' => $result['data']['reference'],
    //             'last_digits' => $result['data']['lastDigits'],
    //         ];
    //         $data['numAutorizacion'] = $payment;

    //         return view('client/completado', $data);
    //     } else {
    //         return view('client/errors/error_payphone');
    //         // return $this->response->setJSON(['error' => 'No se pudo confirmar el pago'], 400);
    //     }
    // }

}
