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

            // Extraer el paymentId y la cédula del clientTransactionId
            if (preg_match('/^(\d+)-(\d+)-(\d+)$/', $clientTransactionId, $matches)) {
                $paymentId = $matches[1];
                $cedula = $matches[2];
                $timestamp = $matches[3];  // Timestamp que también puedes usar si lo necesitas
            } else {
                // Si no se encuentra el patrón esperado, mostramos un error
                return view('client/errors/error_pago_no_encontrado');
            }

            $payment = $this->paymentsModel->find($paymentId);

            if (!$payment) {
                return view('client/errors/error_pago_no_encontrado');
            }

            // Ahora enviamos el $clientTransactionId completo al servicio
            $result = $this->PayphoneConfirmService->confirmTransaction($id, $clientTransactionId);

            // Validar que $result contenga 'data' antes de intentar acceder a sus claves
            if (isset($result['data'])) {
                $transaction_status = $result['data']['transactionStatus'];
                $statusCode = $result['data']['statusCode'];

                // Validar si el pago fue aprobado
                if ($transaction_status == 'Approved' && $statusCode == 3) {
                    // Utilizamos el PaymentApprovalService para aprobar el pago
                    $userId = null; // Asumimos que el ID del usuario está en la sesión o en el pago
                    $approvalResult = $this->paymentApprovalService->approvePayment($paymentId, $userId, '3');

                    if ($approvalResult['success']) {
                        // El pago se aprobó correctamente
                        $data = [
                            'status_code' => $statusCode,
                            'payment_id' => $paymentId,
                            'transaction_status' => $transaction_status,
                            'client_transaction_id' => $result['data']['clientTransactionId'],
                            'authorization_code' => $result['data']['authorizationCode'] ?? null,
                            'transaction_id' => $result['data']['transactionId'],
                            'email' => $result['data']['email'] ?? null,
                            'phone_number' => $result['data']['phoneNumber'] ?? null,
                            'document' => $result['data']['document'] ?? null,
                            'amount' => $result['data']['amount'],
                            'card_type' => $result['data']['cardType'] ?? null,
                            'card_brand' => $result['data']['cardBrand'] ?? null,
                            'message' => $result['data']['message'] ?? null,
                            'message_code' => $result['data']['messageCode'] ?? null,
                            'currency' => $result['data']['currency'],
                            'transaction_date' => $result['data']['date'],
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                        $this->pagosEnLineaModel->insert($data);

                        return $this->redirectView(null, null, null, $id, $clientTransactionId);
                    } else {
                        // Hubo un error al aprobar el pago
                        return view('client/errors/error_aprobacion_pago', ['message' => $approvalResult['message']]);
                    }
                } else {
                    // Si el pago fue rechazado o no tiene el estado adecuado
                    return view('client/errors/error_pago_rechazado');
                }
            } else {
                // Si la respuesta no contiene los datos esperados
                return view('client/errors/error_pago_no_encontrado');
            }
        } catch (\Exception $e) {
            // Registrar el error en el log
            log_message('error', 'Error en respuesta payphone: ' . $e->getMessage());

            // Mostrar una vista de error general
            return view('client/errors/error_payphone');
        }
    }

    public function completado($id, $clientTransactionId)
    {
        // Extraer el paymentId y la cédula del clientTransactionId
        if (preg_match('/^(\d+)-(\d+)-(\d+)$/', $clientTransactionId, $matches)) {
            $paymentId = $matches[1];
        } else {
            // Si no se encuentra el patrón esperado, mostramos un error
            return view('client/errors/error_pago_no_encontrado');
        }
        $payment = $this->paymentsModel->find($paymentId);

        $result = $this->PayphoneConfirmService->confirmTransaction($id, $clientTransactionId);
        if (!isset($result['data']['transactionStatus']) || !isset($result['data']['statusCode'])) {
            // Redirige a la vista de error si alguna de las claves no existe
            return view('client/errors/error_payphone');
        }
        $transaction_status = $result['data']['transactionStatus'];
        $statusCode = $result['data']['statusCode'];

        if ($result['success'] && ($transaction_status == 'Approved' || $statusCode == 2)) {
            // Actualizar el estado del pago en la base de datos

            helper('email');
            $data = [
                'status_code' => $result['data']['statusCode'],
                'transaction_status' => payphone_status($result['data']['transactionStatus']),
                'client_transaction_id' => $result['data']['clientTransactionId'],
                'authorization_code' => $result['data']['authorizationCode'] ?? null,
                'transaction_id' => $result['data']['transactionId'],
                'email' => mask_email($result['data']['email']) ?? null,
                'phone_number' => mask_phone($result['data']['phoneNumber']) ?? null,
                'document' => $result['data']['document'] ?? null,
                'amount' => $result['data']['amount'],
                'card_type' => $result['data']['cardType'] ?? null,
                'cardBrand' => $result['data']['cardBrand'] ?? null,
                'message' => $result['data']['message'] ?? null,
                'message_code' => $result['data']['messageCode'] ?? null,
                'currency' => $result['data']['currency'],
                'transaction_date' => $result['data']['date'],
                'created_at' => date('Y-m-d H:i:s'),
                'store_name' => $result['data']['storeName'],
                'region_iso' => $result['data']['regionIso'],
                'transaction_type' => $result['data']['transactionType'],
                'reference' => $result['data']['reference'],
                'last_digits' => $result['data']['lastDigits'],
            ];
            $data['numAutorizacion'] = $payment;

            return view('client/completado', $data);
        } else {
            return view('client/errors/error_payphone');
            // return $this->response->setJSON(['error' => 'No se pudo confirmar el pago'], 400);
        }
    }

    // public function respuesta_manual()
    // {
    //     try {
    //         $id = $this->request->getGet('id') ?? null;
    //         $clientTransactionId = $this->request->getGet('clientTransactionId') ?? null;

    //         if (!$id || !$clientTransactionId) {
    //             return view('client/errors/error_datos_incompletos');
    //         }

    //         // Extraer el paymentId antes de los diez primeros ceros
    //         if (preg_match('/^(\d+?)0000000000/', $clientTransactionId, $matches)) {
    //             $paymentId = $matches[1]; // Aquí obtenemos el payment_id extraído

    //             // Quitar los primeros 10 ceros del clientTransactionId
    //             $clientTransactionIdSinCeros = preg_replace('/0000000000/', '', $clientTransactionId, 1);
    //         } else {
    //             // Si no se encuentra el patrón esperado, mostramos un error
    //             return view('client/errors/error_pago_no_encontrado');
    //         }

    //         $payment = $this->paymentsModel->find($paymentId);

    //         if (!$payment) {
    //             return view('client/errors/error_pago_no_encontrado');
    //         }

    //         // Ahora enviamos el $clientTransactionId sin los ceros al servicio
    //         $result = $this->PayphoneConfirmService->confirmTransaction($id, $clientTransactionIdSinCeros);

    //         // Validar que $result contenga 'data' antes de intentar acceder a sus claves
    //         if (isset($result['data'])) {
    //             $transaction_status = $result['data']['transactionStatus'];
    //             $statusCode = $result['data']['statusCode'];

    //             // Validar si el pago fue aprobado
    //             if ($transaction_status == 'Approved' && $statusCode == 3) {
    //                 // Utilizamos el PaymentApprovalService para aprobar el pago
    //                 $userId = null; // Asumimos que el ID del usuario está en la sesión o en el pago
    //                 $approvalResult = $this->paymentApprovalService->approvePayment($paymentId, $userId, '3');

    //                 if ($approvalResult['success']) {
    //                     // El pago se aprobó correctamente
    //                     $data = [
    //                         'status_code' => $statusCode,
    //                         'payment_id' => $paymentId,
    //                         'transaction_status' => $transaction_status,
    //                         'client_transaction_id' => $result['data']['clientTransactionId'],
    //                         'authorization_code' => $result['data']['authorizationCode'] ?? null,
    //                         'transaction_id' => $result['data']['transactionId'],
    //                         'email' => $result['data']['email'] ?? null,
    //                         'phone_number' => $result['data']['phoneNumber'] ?? null,
    //                         'document' => $result['data']['document'] ?? null,
    //                         'amount' => $result['data']['amount'],
    //                         'card_type' => $result['data']['cardType'] ?? null,
    //                         'card_brand' => $result['data']['cardBrand'] ?? null,
    //                         'message' => $result['data']['message'] ?? null,
    //                         'message_code' => $result['data']['messageCode'] ?? null,
    //                         'currency' => $result['data']['currency'],
    //                         'transaction_date' => $result['data']['date'],
    //                         'created_at' => date('Y-m-d H:i:s'),
    //                     ];
    //                     $this->pagosEnLineaModel->insert($data);

    //                     return $this->redirectView(null, null, null, $id, $clientTransactionIdSinCeros);
    //                 } else {
    //                     // Hubo un error al aprobar el pago
    //                     return view('client/errors/error_aprobacion_pago', ['message' => $approvalResult['message']]);
    //                 }
    //             } else {
    //                 // Si el pago fue rechazado o no tiene el estado adecuado
    //                 return view('client/errors/error_pago_rechazado');
    //             }
    //         } else {
    //             // Si la respuesta no contiene los datos esperados
    //             return view('client/errors/error_pago_no_encontrado');
    //         }
    //     } catch (\Exception $e) {
    //         // Registrar el error en el log
    //         log_message('error', 'Error en respuesta payphone: ' . $e->getMessage());

    //         // Mostrar una vista de error general
    //         return view('client/errors/error_payphone');
    //     }
    // }


}
