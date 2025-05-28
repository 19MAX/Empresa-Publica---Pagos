<?php
namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\RegistrationsModel;
use App\Models\PaymentsModel;
use App\Models\DepositsModel;
use PaymentStatus;

class DepositosController extends BaseController
{
    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $last_action = null)
    {
        return redirect()->to('/')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data)->with('last_action', $last_action);
    }
    public function deposito()
    {
        $depositoCedula = $this->request->getPost('depositoCedula');
        $codigoPago = $this->request->getPost('codigoPago');
        $comprobantePago = $this->request->getFile('comprobantePago');
        $comprobante = $this->request->getPost('comprobante');
        $dateDeposito = $this->request->getPost('dateDeposito');
        $montoDeposito = $this->request->getPost('montoDeposito');

        $data = [
            'depositoCedula' => $depositoCedula,
            'codigoPago' => $codigoPago,
            'comprobantePago' => $comprobantePago->getName(),
            'comprobante' => $comprobante,
            'montoDeposito' => $montoDeposito,
            'dateDeposito' => $dateDeposito,
        ];

        $validation = \Config\Services::validation();
        $validation->setRules([
            'depositoCedula' => [
                'label' => 'Cédula',
                'rules' => 'required',
            ],
            'codigoPago' => [
                'label' => 'Código de Pago',
                'rules' => 'required',
            ],
            'comprobante' => [
                'label' => 'Número de comprobante',
                'rules' => 'required',
            ],
            'dateDeposito' => [
                'label' => 'Fecha del deposito',
                'rules' => 'required|valid_date',
            ],
            'montoDeposito' => [
                'label' => 'Monto del deposito',
                'rules' => 'permit_empty',
            ],
            'comprobantePago' => [
                'label' => 'Comprobante de pago',
                'rules' => 'uploaded[comprobantePago]|max_size[comprobantePago,10240]|ext_in[comprobantePago,png,jpg,jpeg]',
            ],
        ]);

        if (!$validation->run($data)) {
            return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'insert');
        }

        $registrationsModel = new RegistrationsModel();
        $paymentsModel = new PaymentsModel();
        $depositosModel = new DepositsModel();

        // Iniciar la transacción
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Consulta JOIN para obtener el registro, el pago correspondiente y el precio de la categoría
            $query = $registrationsModel->select('registrations.id as registration_id, registrations.ic, payments.id as payment_id, payments.payment_cod')
                ->join('payments', 'payments.id_register = registrations.id', 'left')
                ->join('categories', 'categories.id = registrations.cat_id', 'left')
                ->where('registrations.ic', $depositoCedula)
                ->where('payments.payment_cod', $codigoPago)
                ->first();

            if (!$query) {
                $db->transRollback();
                return $this->redirectView(null, [['La cédula o el código de pago ingresados no son válidos', 'warning']]);
            }

            if ($depositosModel->existsPendingDeposit($codigoPago)) {
                $db->transRollback();
                return $this->redirectView(null, [['Existe un deposito ya ingresado que esta siendo verificado, espere hasta que termine de ser verificado', 'error']]);
            }

            // Verificar si el pago ya está completado con el nuevo método
            $result = $depositosModel->isPaymentCompletedWithAuthorization($query['payment_id']);
            if ($result['completed']) {
                $db->transRollback();
                return $this->redirectView(null, [['El pago de su inscripción ha sido recibido y registrado correctamente. No es necesario subir más comprobantes', 'warning']]);
            }
            // Verificar si el estado del pago es 'completado' (2) con el nuevo método
            $paymentStatus = $paymentsModel->isPaymentStatusCompleted($query['payment_id']);
            if ($paymentStatus['completed']) {
                $db->transRollback();
                return $this->redirectView(null, [['El pago de su inscripción ya está completado.', 'pdf', $paymentStatus['num_autorizacion']]]);
            }

            // Verificar si el número de comprobante y la fecha del depósito ya existen
            if ($depositosModel->existsComprobanteAndDate($comprobante, $dateDeposito)) {
                $db->transRollback();
                return $this->redirectView(null, [['El número de comprobante ya ha sido ingresado', 'warning']]);
            }

            // Validar si el archivo subido es una imagen
            if (!$comprobantePago->isValid() || !in_array($comprobantePago->getMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])) {
                $db->transRollback();
                return $this->redirectView(null, [['El archivo subido no es una imagen válida', 'warning']]);
            }

            // Actualizar el estado del pago a 'En proceso' (4)
            $paymentsModel->update($query['payment_id'], ['payment_status' => PaymentStatus::EnProceso]);

            // Guardar el monto del depósito y el comprobante de pago en la tabla inscripcion_pagos
            $nombreComprobante = $comprobantePago->getRandomName();
            $ruta = WRITEPATH . 'uploads/comprobantes/';


            if ($comprobantePago->move($ruta, $nombreComprobante)) {
                $rutaComprobante = 'uploads/comprobantes/' . $nombreComprobante;

                $deposits = [
                    'payment_id' => $query['payment_id'],
                    'deposit_cedula' => $depositoCedula,
                    'codigo_pago' => $codigoPago,
                    'comprobante_pago' => $rutaComprobante,
                    'num_comprobante' => $comprobante,
                    'date_deposito' => $dateDeposito,
                ];

                $depositosModel->insert($deposits);
                $db->transComplete(); // Confirmar la transacción

                return $this->redirectView(null, [['El depósito pasará por un proceso de verificación, cuando se verifique el depósito se te enviará el comprobante a tu correo electrónico', 'success']]);
            } else {
                $db->transRollback(); // Revertir la transacción en caso de error al mover la imagen
                return $this->redirectView(null, [['No se pudo guardar el comprobante de pago', 'danger']]);
            }
        } catch (\Exception $e) {
            log_message('warning',$e->getMessage());
            $db->transRollback(); // Revertir la transacción en caso de error
            return $this->redirectView(null, [['No se pudo registrar el depósito', 'danger']]);
        }
    }


    public function fetchMontoDeposito()
    {
        $request = $this->request->getJSON();
        $depositoCedula = $request->cedula ?? null;
        $codigoPago = $request->codigoPago ?? null;

        if (!$depositoCedula || !$codigoPago) {
            return $this->response->setJSON(['error' => 'Datos incompletos'], 400);
        }

        $registrationsModel = new RegistrationsModel();

        try {
            $result = $registrationsModel->getAmountByPaymentCode($depositoCedula, $codigoPago);
            if ($result) {
                if ($result['cancelado']) {
                    return $this->response->setJSON([
                        'success' => true,
                        'monto' => $result['monto'],
                        'montoOriginal' => number_format($result['montoOriginal'], 2),
                        'montoPagado' => number_format($result['montoPagado'], 2),
                        'nuevoMonto' => number_format($result['monto'], 2),
                        'cancelado' => true,
                        'deposits' => $result['deposits']
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => true,
                        'monto' => number_format($result['monto'], 2),
                        'cancelado' => false,
                        'deposits' => $result['deposits']
                    ]);
                }
            } else {
                return $this->response->setJSON(['error' => 'Datos no encontrados'], 404);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}