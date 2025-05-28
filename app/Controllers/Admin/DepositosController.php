<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ConfigModel;
use App\Models\DepositsModel;
use App\Models\PaymentsModel;
use App\Models\PaymentMethodsModel;
use App\Models\RejectionReasonsModel;
use CodeIgniter\I18n\Time;
use ModulosAdmin;
use PaymentStatus;
use DepositoStatus;

class DepositosController extends BaseController
{
    protected $depositsModel;
    protected $paymentsModel;
    protected $paymentMethodsModel;
    protected $configModel;
    private function demoPDF($num_autorizacion)
    {
        try {
            helper('facture');
            $paymentsModel = $this->paymentsModel;
            $payment = $paymentsModel->numeroAutorizacion($num_autorizacion);
            if (!$payment) {
                return 'Registro no encontrado';
            }

            // Generar el PDF
            $pdfData = generate_pdf($payment);
            $pdfOutput = $pdfData['output'];

            // Guardar el PDF en una ruta accesible
            $pdfPath = WRITEPATH . 'uploads/comprobante_recaudacion.pdf';
            file_put_contents($pdfPath, $pdfOutput);

            // Verificar si se debe enviar el correo electrónico
            if (empty($payment['send_email'])) {
                helper('email');
                // Enviar correo electrónico con el PDF adjunto
                $to = $payment['email_user'];
                $subject = 'Comprobante de recaudación';
                $message = 'Adjunto encontrará su comprobante de recaudación.';
                $result = send_email_with_pdf_from_path($to, $subject, $message, $pdfPath);

                if ($result === 'success') {
                    // Actualizar el campo send_email en la base de datos
                    $paymentsModel->update($payment['id'], ['send_email' => 1]);
                    // Eliminar el archivo PDF temporal
                    unlink($pdfPath);
                    return 'success';
                } else {
                    // Ocurrió un error al enviar el correo
                    return $result;
                }
            }

            // Eliminar el archivo PDF temporal
            unlink($pdfPath);
            return 'success';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $last_action = null, $id = null, $uniqueCode = null)
    {
        return redirect()->to('/admin/pagos/' . $id)
            ->with('flashValidation', isset($validation) ? $validation->getErrors() : null)
            ->with('flashMessages', $flashMessages)
            ->with('last_data', $last_data)
            ->with('last_action', $last_action)
            ->with('uniqueCode', $uniqueCode);
    }

    public function __construct()
    {
        $this->depositsModel = new DepositsModel();
        $this->paymentsModel = new PaymentsModel();
        $this->paymentMethodsModel = new PaymentMethodsModel();
        $this->configModel = new ConfigModel();
    }

    public function index($id_pago)
    {
        // get flash data
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');
        $uniqueCode = session()->getFlashdata('uniqueCode');

        $datos_pago = $this->paymentsModel->getPaymentUser($id_pago);
        $depositosUser = $this->depositsModel->getDepositsByPaymentId($id_pago);


        $data = [
            'datosPago' => $datos_pago,
            'depositosUser' => $depositosUser,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'uniqueCode' => $uniqueCode,
        ];
        return view('admin/pagos/depositos/pagos_depositos_individuales', $data);
    }
    public function getDatosPagoDeposito($id_pago)
    {
        $depositosUser = $this->depositsModel->getDepositsByPaymentId($id_pago);

        return $this->response->setJSON($depositosUser);
    }

    public function obtenerDeposito($id)
    {
        $depositosUser = $this->depositsModel->getDepositsByPaymentId($id);

        // Asegúrate de que los datos estén en el formato correcto para DataTables
        $formattedDeposits = array_map(function ($deposito) {
            return [
                'fecha_deposito' => date('Y-m-d', strtotime($deposito['date_deposito'])),
                'monto_deposito' => number_format($deposito['monto_deposito'], 2, '.', ''),
                'estado' => $deposito['status'],
                'url_comprobante' => base_url($deposito['comprobante_pago']) // Asegúrate de que esta ruta sea correcta
                // Agrega más campos según sea necesario
            ];
        }, $depositosUser);

        // Devolver los datos como JSON
        return $this->response->setJSON($formattedDeposits);
    }
    public function actualizarEstado()
    {
        // Obtener datos del JSON recibido
        $json = $this->request->getJSON();

        $id_deposito = $json->id_deposito;
        $estado = $json->estado;
        $monto_deposito = $json->monto_deposito;

        if (!$id_deposito) {
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'Hubo problemas con el id'
            ]);
        }

        // Verificar si el pago asociado está completado
        $deposito = $this->depositsModel->find($id_deposito);
        if (!$deposito) {
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'Depósito no encontrado'
            ]);
        }

        $paymentCompleted = $this->paymentsModel->isPaymentCompleted($deposito['payment_id']);
        if ($paymentCompleted) {
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'No se puede actualizar el estado y el monto del deposito porque el pago ya está completado'
            ]);
        }

        $data = [
            'id' => $id_deposito,
            'status' => $estado,
            'monto_deposito' => $monto_deposito,
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'status' => [
                        'label' => 'Estado del deposito',
                        'rules' => 'required|in_list[' . implode(',', [
                            DepositoStatus::Pendiente,
                            DepositoStatus::Incompleto,
                            DepositoStatus::Aprobado,
                            DepositoStatus::Rechazado
                        ]) . ']',
                    ],
                    'monto_deposito' => [
                        'label' => 'Monto del depósito',
                        'rules' => 'required|numeric',
                    ],
                ]
            );

            if ($validation->run($data)) {
                $updateEstado = $this->depositsModel->update($id_deposito, $data);

                if (!$updateEstado) {
                    return $this->response->setJSON([
                        'status' => 'warning',
                        'message' => 'No fue posible actualizar los datos del deposito'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Deposito actualizado exitosamente'
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'warning',
                    'message' => 'Error en los datos enviados',
                    'errors' => $validation->getErrors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'danger',
                'message' => 'No se pudo actualizar el deposito'
            ]);
        }
    }

    public function aprobar_deposito()
    {
        try {
            $paymentsModel = $this->paymentsModel;
            $payment_methodModel = $this->paymentMethodsModel;
            // Obtener el valor de additional_charge
            $adicional = $this->configModel->getAdditionalCharge();
            helper('ramdom');
            $uniqueCode = generateUniqueNumericCode(50);

            $id_usuario = session('id');
            $id_pago = $this->request->getPost('id');
            $precio = $this->request->getPost('precio');
            if (!$id_pago) {
                return $this->redirectView(null, [['Error con el id no fue enviado', 'warning']], null, null, $id_pago);
            }

            $paymentCompleted = $this->paymentsModel->isPaymentCompleted($id_pago);
            if ($paymentCompleted) {
                return $this->redirectView(null, [['El pago no puede aprobarse porque ya se encuentra aprobado', 'danger']], null, null, $id_pago);
            }

            // Verificar si los depósitos completan el monto del pago
            if (!$this->depositsModel->verifyDepositAmounts($id_pago)) {
                return $this->redirectView(null, [['El monto de los depósitos no coincide con el monto requerido', 'danger']], null, null, $id_pago);
            }

            $local = $payment_methodModel->paymentLocal(1);
            if (!$local) {
                return $this->redirectView(null, [['Método de pago desactivado', 'warning']], null, null, $id_pago);
            }

            // Obtener el registro del pago
            $payment = $paymentsModel->find($id_pago);
            if (!$payment) {
                return $this->redirectView(null, [['Pago no encontrado', 'warning']], null, null, $id_pago);
            }
            $cantidad = 1;
            $fecha_emision = Time::now();
            $precio_unitario = $adicional / 1.15;
            $sub_total_0 = $precio-$adicional;
            $subtotal = $sub_total_0 + $precio_unitario;
            $subtotal_15 = $precio_unitario;
            $iva_15 = $precio_unitario * 0.15;
            $total = $precio_unitario + $iva_15;
            $datosPago = [
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
                "payment_method_id" => 1,
            ];

            if (($payment['payment_status'] == PaymentStatus::Completado)||($payment['payment_status'] == PaymentStatus::Fallido)) {
                return $this->redirectView(null, [['El pago no puede ser completado, estado invalido', 'danger']], null, null, $id_pago);
            }
            // Verificar si el campo num_autorizacion ya tiene un valor
            if (isset($payment['num_autorizacion']) && !empty($payment['num_autorizacion']) && ($payment['payment_status'] !== PaymentStatus::Completado)) {
                // El campo num_autorizacion ya tiene un valor, usar el existente
                $uniqueCode = $payment['num_autorizacion'];
                $paymentsModel->update($id_pago, ["payment_status" => PaymentStatus::Completado, "payment_method_id" => 1]);

            } else {
                // El campo num_autorizacion está vacío, actualizar con el nuevo valor
                try {
                    $paymentsModel->updatePaymentDeposito($id_pago, $datosPago, $id_usuario);
                } catch (\Exception $e) {
                    return $this->redirectView(null, [['Error en la creación del pdf:' . $e->getMessage(), 'warning']], null, null, $id_pago);
                }
            }
            // Llamar al método demoPDF() para generar y enviar el PDF
            $result = $this->demoPDF($uniqueCode);

            // Verificar el resultado de demoPDF()
            if ($result === 'success') {
                return $this->redirectView(
                    null,
                    [['Email enviado con el PDF correctamente', 'success', $uniqueCode]],
                    'Completado',
                    null,
                    $id_pago,
                    $uniqueCode
                );
            } else {
                return $this->redirectView(null, [['Error al enviar el email', 'danger']], null, null, $id_pago);
            }

        } catch (\Exception $e) {
            return $this->redirectView(null, [[$e->getMessage(), 'danger']], null, $id_pago);
        }
    }

    public function pago_incompleto()
    {
        helper('email');
        $id_pago_rechazo = $this->request->getPost('id_pago_rechazo');
        $motivo_rechazo = $this->request->getPost('motivo_rechazo');
        $precio_pagar = $this->request->getPost('precio_pagar');
        $precio_pagado = $this->request->getPost('precio_pagado');
        $valor_pendiente = $this->request->getPost('valor_pendiente');
        $email = $this->request->getPost('email');
        $names = $this->request->getPost('names');
        $id_usuario = session('id');

        $data = [
            'id_pago_rechazo' => $id_pago_rechazo,
            'motivo_rechazo' => trim($motivo_rechazo),
            'precio_pagar' => $precio_pagar,
            'precio_pagado' => $precio_pagado,
            'valor_pendiente' => $valor_pendiente,
            'names' => $names,
            'email' => $email,
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'names' => [
                        'label' => 'Nombre del participante',
                        'rules' => 'required|min_length[5]',
                    ],
                    'motivo_rechazo' => [
                        'label' => 'Motivo del rechazo',
                        'rules' => 'required|min_length[5]',
                    ],
                    'precio_pagar' => [
                        'label' => 'Precio a pagar',
                        'rules' => 'required|numeric',
                    ],
                    'precio_pagado' => [
                        'label' => 'Precio pagado',
                        'rules' => 'required|numeric|less_than[' . $precio_pagar . ']',
                    ],
                    'valor_pendiente' => [
                        'label' => 'Valor pendiente',
                        'rules' => 'required|numeric',
                    ],
                    'email' => [
                        'label' => 'Email',
                        'rules' => 'required|valid_email',
                    ]
                ]
            );

            if (!$validation->run($data)) {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'update', $id_pago_rechazo);
            }

            if (!$id_pago_rechazo) {
                return $this->redirectView(null, [['Error inténtenlo más tarde', 'warning']], null, null, $id_pago_rechazo);
            }

            if (!$email) {
                return $this->redirectView(null, [['Error con el email inténtenlo más tarde', 'warning']], null, null, $id_pago_rechazo);
            }

            $paymentCompleted = $this->paymentsModel->isPaymentCompleted($id_pago_rechazo);
            if ($paymentCompleted) {
                return $this->redirectView(null, [['El pago no puede cambiarse a incompleto por que ya se encuentra aprobado', 'danger']], null, null, $id_pago_rechazo);
            }

            $db = \Config\Database::connect();
            $db->transBegin();

            $estado_pago = PaymentStatus::Incompleto;
            $paymentsModel = new PaymentsModel();
            $paymentResult = $paymentsModel->updatePaymentRechazo($id_pago_rechazo, $estado_pago, $id_usuario, $motivo_rechazo, $precio_pagado, 'Incompleto');


            $datosInscrito= $this->paymentsModel->datosUserInscrito($id_pago_rechazo);
            $codigoPago = $datosInscrito['payment_cod'] ?? null;
            $names = $datosInscrito['nombresInscrito'] ?? null;
            $email = $datosInscrito['emailInscrito'] ?? null;
            $nombreEvento = $datosInscrito['nombreEvento']??null;

            if (!$paymentResult['update']) {
                $db->transRollback();
                return $this->redirectView(null, [['Error insertando datos', 'warning']], null, null, $id_pago_rechazo);
            }

            // Siempre enviar el correo de rechazo
            $emailResult = send_rejection_email($email, 'Solicitud Rechazada', $motivo_rechazo, $names, $codigoPago, $nombreEvento, 'email/rechazados', $valor_pendiente);

            if ($emailResult !== 'success') {
                $db->transRollback();
                log_message('error', 'Error enviando correo de rechazo: ' . $emailResult);
                return $this->redirectView(null, [['Error enviando el correo, por favor inténtelo más tarde', 'warning']], null, null, $id_pago_rechazo);
            }

            $db->transCommit();
            return $this->redirectView(null, [['Pago rechazado y registrado exitosamente', 'success']], null, null, $id_pago_rechazo);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->redirectView(null, [['No se pudo registrar el rechazo del pago incompleto ' . $id_pago_rechazo . ' ' . $e->getMessage(), 'error']], $data, null, $id_pago_rechazo);
        }
    }

    public function rechazar()
    {
        helper('email');
        $id_pago_rechazo = $this->request->getPost('id_pago_solo_rechazo');
        $motivo_rechazo = $this->request->getPost('motivo_solo_rechazo');
        $email = $this->request->getPost('email');
        $names = $this->request->getPost('names');
        $id_usuario = session('id');

        $data = [
            'id_pago_rechazo' => $id_pago_rechazo,
            'motivo_rechazo' => trim($motivo_rechazo),
            'names' => $names,
            'email' => $email,
        ];

        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_pago_rechazo' => 'required|numeric',
            'motivo_rechazo' => 'required|min_length[5]',
            'email' => 'required|valid_email',
            'names' => 'required|min_length[5]',
        ]);

        if (!$validation->run($data)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error en los datos enviados',
                'errors' => $validation->getErrors()
            ]);
        }

        $paymentCompleted = $this->paymentsModel->isPaymentCompleted($id_pago_rechazo);
        if ($paymentCompleted) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'El pago no puede rechazarse por que el pago ya se encuentra aprobado'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transBegin();

            // Actualizar el estado del pago a rechazado
            $estado_pago = PaymentStatus::Rechazado;
            $paymentsModel = new PaymentsModel();
            $paymentResult = $paymentsModel->updatePaymentRechazo($id_pago_rechazo, $estado_pago, $id_usuario, $motivo_rechazo);
            $datosInscrito= $this->paymentsModel->datosUserInscrito($id_pago_rechazo);
            $codigoPago = $datosInscrito['payment_cod'] ?? null;
            $names = $datosInscrito['nombresInscrito'] ?? null;
            $email = $datosInscrito['emailInscrito'] ?? null;
            $nombreEvento = $datosInscrito['nombreEvento']??null;

            if (!$paymentResult['update']) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Error al actualizar el estado del pago'
                ]);
            }

            // Enviar el correo de rechazo
            $emailResult = email_rechazo_general($email, 'Solicitud Rechazada', $motivo_rechazo, $names, $codigoPago,$nombreEvento, 'email/rechazo_general');

            if ($emailResult !== 'success') {
                $db->transRollback();
                log_message('error', 'Error enviando correo de rechazo: ' . $emailResult);
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Error al enviar el correo de rechazo'
                ]);
            }

            $db->transCommit();
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pago rechazado y correo enviado exitosamente'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error en rechazar(): ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ocurrió un error al procesar la solicitud'
            ]);
        }
    }

    public function verificarDepositoRechazado($paymentId)
    {
        $rejectionReasonsModel = $this->rejectionModel = new RejectionReasonsModel();
        $rejectedDeposits = $rejectionReasonsModel->getRejectedDepositsWithEmail($paymentId);

        if ($rejectedDeposits) {
            // Existe un depósito rechazado con send_email = 1 para el payment_id proporcionado
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Se encontró un depósito rechazado con notificación de correo para el pago especificado',
                'emailCount' => count($rejectedDeposits)
            ]);
        } else {
            // No existe un depósito rechazado con send_email = 1 para el payment_id proporcionado
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'No se encontró ningún depósito rechazado con notificación de correo para el pago especificado',
                'emailCount' => 0
            ]);
        }
    }


    public function verificarDepositoIncompleto($paymentId)
    {
        $rejectionReasonsModel = $this->rejectionModel = new RejectionReasonsModel();
        $rejectedDeposits = $rejectionReasonsModel->getRejectedDepositsWithEmail($paymentId, 'Incompleto');

        if ($rejectedDeposits) {
            // Existe un depósito rechazado con send_email = 1 para el payment_id proporcionado
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Se encontró un depósito rechazado con notificación de correo para el pago especificado',
                'emailCount' => count($rejectedDeposits)
            ]);
        } else {
            // No existe un depósito rechazado con send_email = 1 para el payment_id proporcionado
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'No se encontró ningún depósito rechazado con notificación de correo para el pago especificado',
                'emailCount' => 0
            ]);
        }
    }


    public function deposits_all()
    {
        // get flash data
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');
        $uniqueCode = session()->getFlashdata('uniqueCode');

        $modulo = ModulosAdmin::DEPOSITO_ALL;
        $depositos = $this->depositsModel->getAllDepositsWithDetails();

        $data = [
            'modulo' => $modulo,
            'depositos' => $depositos,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'uniqueCode' => $uniqueCode,
        ];
        return view('admin/pagos/depositos/deposits_all', $data);
    }

}