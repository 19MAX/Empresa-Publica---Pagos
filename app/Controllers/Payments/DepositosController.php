<?php
namespace App\Controllers\Payments;

use ModulosAdminPagos;
use PaymentStatus;
use App\Controllers\BaseController;
use App\Models\PaymentsModel;

class DepositosController extends BaseController
{
    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $uniqueCode = null, $last_action = null)
    {
        return redirect()->to('punto/pago/depositos')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data)->
            with('uniqueCode', $uniqueCode)->with('last_action', $last_action);
    }
    public function index()
    {
        $paymentModel = new PaymentsModel();
        $depositos = $paymentModel->getPaymentsWithDetailsAndDeposits();
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $modulo = ModulosAdminPagos::PAGOS;
        $data = [
            'modulo' => $modulo,
            'depositos' => $depositos,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];
        return view('payments/depositos/pendiente', $data);
    }
    public function completados()
    {

        $paymentModel = new PaymentsModel();
        $depositos = $paymentModel->getPaymentsCompleted();
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $modulo = ModulosAdminPagos::PAGOS_COMPLETOS;
        $data = [
            'modulo' => $modulo,
            'depositos' => $depositos,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];
        return view('payments/depositos/completado', $data);
    }

    public function rechazados()
    {

        $paymentModel = new PaymentsModel();
        $depositos = $paymentModel->getPaymentsRechazados();
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $modulo = ModulosAdminPagos::PAGOS_RECHAZADOS;
        $data = [
            'modulo' => $modulo,
            'depositos' => $depositos,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];
        return view('payments/depositos/rechazado', $data);
    }

    public function incompletos()
    {

        $paymentModel = new PaymentsModel();
        $depositos = $paymentModel->getPaymentsIncompletos();
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $modulo = ModulosAdminPagos::PAGOS_INCOMPLETOS;
        $data = [
            'modulo' => $modulo,
            'depositos' => $depositos,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];
        return view('payments/depositos/incompleto', $data);
    }
}