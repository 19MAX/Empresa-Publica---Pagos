<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentsModel;
use App\Models\RegistrationsModel;
use ModulosAdmin;

class DashboardController extends BaseController
{
    public function index()
    {
        $paymentsModel = new PaymentsModel();
        $registrationsModel = new RegistrationsModel();
        $modulo = ModulosAdmin::DASHBOARD;

        $data = [
            'modulo' => $modulo,
            'mis_ingresos' => $paymentsModel->getDailyRevenueMy(null, session('id')),
            'mis_ingresos_totales' => $paymentsModel->getTotalRevenueByUser(session('id')),
            'dailyRevenue' => $paymentsModel->getDailyRevenue(),
            'totalRevenue' => $paymentsModel->getTotalRevenue(),
            'revenueByUser' => $paymentsModel->getRevenueByUser(),
            'totalRegistrations' => $registrationsModel->getTotalRegistrations(),
            'registrationsByStatus' => $registrationsModel->getRegistrationsByPaymentStatus(),
            'paymentMethodStats' => $paymentsModel->getPaymentMethodStats(),
        ];

        return view('admin/index', $data);
    }
}