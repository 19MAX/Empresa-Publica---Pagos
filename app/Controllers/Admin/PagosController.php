<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PaymentsModel;
use ModulosAdmin;

class PagosController extends BaseController
{
    public function index()
    {
        $paymentModel = new PaymentsModel();
        $depositos = $paymentModel->getPaymentsWithDetailsAndDeposits();
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $modulo = ModulosAdmin::PAGOS;
        $data = [
            'modulo' => $modulo,
            'depositos' => $depositos,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];
        return view('admin/pagos/depositos/pagos_depositos', $data);
    }
    public function completados()
    {

        $paymentModel = new PaymentsModel();
        $depositos = $paymentModel->getPaymentsCompleted();
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $modulo = ModulosAdmin::PAGOS_COMPLETOS;
        $data = [
            'modulo' => $modulo,
            'depositos' => $depositos,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];
        return view('admin/pagos/depositos/pagos_dep_completados', $data);
    }

    public function rechazados()
    {

        $paymentModel = new PaymentsModel();
        $depositos = $paymentModel->getPaymentsRechazados();
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $modulo = ModulosAdmin::PAGOS_RECHAZADOS;
        $data = [
            'modulo' => $modulo,
            'depositos' => $depositos,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];
        return view('admin/pagos/depositos/pagos_dep_rechazados', $data);
    }

    public function incompletos()
    {

        $paymentModel = new PaymentsModel();
        $depositos = $paymentModel->getPaymentsIncompletos();
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $modulo = ModulosAdmin::PAGOS_INCOMPLETOS;
        $data = [
            'modulo' => $modulo,
            'depositos' => $depositos,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];
        return view('admin/pagos/depositos/pagos_dep_incompletos', $data);
    }
}