<?php
namespace App\Controllers\Proservi;

use App\Controllers\BaseController;
use App\Models\EventsModel;
use App\Models\PaymentsModel;
use App\Models\UsersModel;
use App\Services\PermissionService;
use PaymentStatus;

class ReportesController extends BaseController
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

        // Obtener eventos permitidos para los filtros
        $permissionService = new PermissionService();
        $allowedEvents = $permissionService->getUserAllowedEvents($userId);

        $data = [
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'allowedEvents' => $allowedEvents,
        ];

        return view("proservi/reportes", $data);
    }

    public function getPaymentsDataAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acceso no autorizado']);
        }

        $userId = session('id');

        // Obtener parámetros de DataTables y convertir a int
        $draw = intval($this->request->getPost('draw'));
        $start = intval($this->request->getPost('start'));
        $length = intval($this->request->getPost('length'));
        $searchValue = $this->request->getPost('search')['value'] ?? '';

        // Obtener filtros personalizados
        $eventId = $this->request->getPost('event_id');
        $categoryId = $this->request->getPost('category_name');
        $paymentStatus = $this->request->getPost('payment_status');


        // Construir array de filtros
        $filters = [
            'start' => $start,
            'length' => $length,
            'search' => $searchValue,
            'event_id' => $eventId,
            'category_id' => $categoryId,
            'payment_status' => $paymentStatus,
        ];

        // Obtener datos
        $result = $this->paymentsModel->getRecaudadoWithPermissions($userId, $filters);

        // Formatear datos para DataTables
        $data = [];
        foreach ($result['data'] as $row) {
            $data[] = [
                'codigo' => $row['codigo'],
                'participante_cedula' => $row['participante_cedula'],
                'participante_name' => $row['participante_name'],
                'participante_telefono' => $row['participante_telefono'],
                'participante_email' => $row['participante_email'],
                'participante_direccion' => $row['participante_direccion'],
                'event_name' => $row['event_name'],
                'precio' => "<span class='font-weight-bold'>{$row['nombre_categoria']} </span>-<span class='font-weight-bold'> {$row['precio']}</span>",
                'estado' => "<span class='" . style_estado($row['payment_status']) . "'>" . getPaymentStatusText($row['payment_status']) . "</span>",
                'amount_pay' => number_format($row['amount_pay'], 2),
                'method_pago' => $row['method_pago'] ?? 'No registra pago',
                'payment_status' => $row['payment_status'],
                'payment_id' => $row['payment_id'],
                'actions' => $this->generateActionButtons($row)
            ];
        }

        // Preparar respuesta para DataTables
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }

    public function getCategoriesByEvent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acceso no autorizado']);
        }

        $eventId = $this->request->getPost('event_id');

        if (empty($eventId)) {
            return $this->response->setJSON([]);
        }

        $categories = $this->paymentsModel->getCategoriesByEventId($eventId);

        return $this->response->setJSON($categories);
    }

    private function generateActionButtons($row)
    {
        $buttons = '<div class="btn-group" role="group">';


        if ($row['payment_status'] == PaymentStatus::Pendiente) {
            $buttons .=
                '<button type="button"
                    class="js-mytooltip btn btn-sm btn-success view-payment"
                    data-id="' . $row['payment_id'] . '"
                    data-mytooltip-custom-class="align-center"
                    data-mytooltip-direction="top"
                    data-mytooltip-theme="success"
                    data-mytooltip-content="Contactar por WhatsApp"
                    title="Contactar por WhatsApp"
                    onclick="window.open(\'https://wa.me/' . $row['participante_telefono'] . '\', \'_blank\');">
                    <i class="fa fa-whatsapp"></i>
                </button>';

        } else {

            $route_comprobante = base_url("/pdf/{$row['num_autorizacion']}");
            // Botón imprimir comprobante
            $buttons .=
                '<a href="' . $route_comprobante . '"
                    class="js-mytooltip btn btn-sm btn-danger"
                    data-mytooltip-custom-class="align-center"
                    data-mytooltip-direction="top"
                    data-mytooltip-theme="danger"
                    data-mytooltip-content="Comprobante"
                    title="Comprobante"
                    target="_blank">
                    <i class="fa fa-print"></i>
                </a>';

        }



        $buttons .= '</div>';

        return $buttons;
    }
}