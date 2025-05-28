<?php
namespace App\Controllers\Payments;

use App\Controllers\BaseController;
use App\Models\PaymentsModel;
use ModulosAdminPagos;

class DashboardController extends BaseController
{
    public function index()
    {
        $paymentsModel = new PaymentsModel();

        $modulo = ModulosAdminPagos::DASHBOARD;
        $data = [
            'modulo' => $modulo,
            'mis_ingresos' => $paymentsModel->getDailyRevenueMy(null,session('id')),
            'mis_ingresos_totales' => $paymentsModel->getTotalRevenueByUser(session('id')),
            'paymentMethodStats' => $paymentsModel->getPaymentMethodStats(),
        ];
        return view('payments/index',$data);
    }
}
