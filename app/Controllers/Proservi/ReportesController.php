<?php

namespace App\Controllers\Proservi;

use App\Controllers\BaseController;
use App\Models\PaymentsModel;
use App\Models\UsersModel;
// use ModulosAdmin;

class ReportesController extends BaseController
{

    private $usersModel;
    private $paymentsModel;

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $last_action = null)
    {
        return redirect()->to('proservi/reportes')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_action', $last_action)->
            with('last_data', $last_data);
    }

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->paymentsModel = new PaymentsModel();
    }

    public function index()
    {

        // $id = session('id');
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $all_inscriptions = $this->paymentsModel->getRecaudado();
        // $modulo = ModulosAdmin::RECAUDACIONES;

        $data = [
            'users' => $all_inscriptions,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            // 'modulo' => $modulo,
        ];
        return view("proservi/reportes", $data);
    }

}