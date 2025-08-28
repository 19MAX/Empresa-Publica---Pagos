<?php

namespace App\Controllers\UserEvent;

use App\Controllers\BaseController;
use App\Models\EventsModel;
use App\Models\PaymentsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class ReporteController extends BaseController
{
    private $usersModel;
    private $paymentsModel;
    private $eventsModel;

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
        $this->eventsModel = new EventsModel();
    }

    public function index()
    {
        $userId = session('id');

        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        // Otros usuarios ven según sus permisos
        $all_inscriptions = $this->paymentsModel->getRecaudadoWithEvent($userId);

        $data = [
            'users' => $all_inscriptions,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
        ];

        return view("userEventos/index", $data);
    }
}
