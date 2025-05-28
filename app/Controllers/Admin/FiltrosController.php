<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use PaymentStatus;

class FiltrosController extends BaseController
{
    private function redirectError($validation = null, $flashMessages = null, $last_data = null)
    {
        return redirect()->to('admin/inscripciones')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data);
    }
    public function index()
    {
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');
        $data = [
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];
        return view('admin/pagos/buscar_cedula', $data);
    }

    public function buscarPorCedula()
    {
        $cedula = $this->request->getPost('cedula');
        $estado = $this->request->getPost('estado');
        $data = [
            'cedula' => $cedula,
            'estado' => $estado,
        ];

        if ($cedula && preg_match('/^\d{10}$/', $cedula)) {
            // Verificar si el estado es válido
            $validStates = [
                PaymentStatus::Pendiente,
                PaymentStatus::Completado,
                PaymentStatus::Fallido,
                PaymentStatus::EnProceso,
                PaymentStatus::Incompleto
            ];

            if (in_array($estado, $validStates)) {
                // Estado válido, redirigir a la ruta con cédula y estado
                $estadoText = getPaymentStatusText($estado);
                return redirect()->to("admin/inscripciones/{$cedula}/{$estadoText}");
            } else {
                // Estado inválido, redirigir solo con la cédula
                return $this->redirectError(null, [['Seleccione el estado del pago', 'warning']], $data);
                // return redirect()->to("punto/pago/inscripciones/{$cedula}");
            }
        } else {
            $validation = \Config\Services::validation();
            $validation->setError('cedula', 'El número de cédula debe poseer 10 dígitos numéricos');
            return $this->redirectError($validation, [['El número de cédula es inválido', 'warning']], $data);
        }
    }

}
