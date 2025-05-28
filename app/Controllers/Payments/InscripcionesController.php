<?php
namespace App\Controllers\Payments;

use App\Controllers\BaseController;
use App\Models\PaymentsModel;
use App\Models\RegistrationsModel;
use App\Services\PaymentAprobarService;
use ModulosAdminPagos;
use PaymentStatus;

class InscripcionesController extends BaseController
{


    private $paymentAprobarService;

    public function __construct(){

        $this->paymentAprobarService = new PaymentAprobarService();
    }

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $ic = null, $estado = null, $uniqueCode = null)
    {
        return redirect()->to('/punto/pago/inscripciones/' . $ic . '/' . $estado)->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data)->
            with('uniqueCode', $uniqueCode);
    }

    private function redirectError($validation = null, $flashMessages = null, $last_data = null)
    {
        return redirect()->to('punto/pago/inscripciones')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data);
    }

    public function showPDF($num_autorizacion)
    {
        helper('facture');
        $paymentsModel = new PaymentsModel();
        $payment = $paymentsModel->numeroAutorizacion($num_autorizacion);
        if (!$payment) {
            return view('errors/html/facture');
        }

        // Generar el PDF
        $pdfData = generate_pdf($payment);
        $pdf = $pdfData['pdf'];

        // Enviar el PDF al navegador
        $pdf->stream('comprobante.pdf', ['Attachment' => false]);
    }

    public function pago()
    {

        $id_usuario = session('id');
        $id_pago = $this->request->getPost('id');
        $cedula = $this->request->getPost('cedula');
        $estado_pago = $this->request->getPost('estado_pago');
        // $precio = $this->request->getPost('precio');
        if (!$id_pago) {
            return $this->redirectView(null, [['El id no existe', 'warning']], null, $cedula, $estado_pago);
        }

        // Usar el servicio para aprobar el pago
        $result = $this->paymentAprobarService->approvePayment($id_pago, $id_usuario,'2');

        // Verificar el resultado del servicio
        if ($result['success']) {
            return $this->redirectView(null, [['Pago aprobado y email enviado correctamente', 'success']], null, $cedula, 'Completado', $result['uniqueCode']);
        } else {
            return $this->redirectView(null, [[$result['message'], 'warning']], null, $cedula, $estado_pago);
        }

    }

    public function index($cedula = null, $estado = null)
    {
        $data = [
            'cedula' => $cedula,
        ];

        // Obtener datos flash
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $registrationModel = new RegistrationsModel();

        // Convertir el texto del estado a su valor entero correspondiente
        $estadoValue = mapEstadoToValue($estado);

        $validStates = [
            PaymentStatus::Pendiente,
            PaymentStatus::Completado,
            PaymentStatus::Fallido,
            PaymentStatus::EnProceso,
            PaymentStatus::Incompleto
        ];

        // Verificar que el estado sea obligatorio y válido
        if (is_null($estadoValue) || !in_array($estadoValue, $validStates)) {
            $data = ['cedula' => $cedula];
            return $this->redirectError(null, [['El estado es obligatorio y debe ser válido', 'warning']], $data);
        }

        // Primero, obtener todas las inscripciones por cédula
        $all_registrations = $registrationModel->getInscripcionesByCedula($cedula);

        if (empty($all_registrations)) {
            // Si no se encuentran registros para la cédula, redirigir a una vista de error
            return $this->redirectError(null, [['El número de cédula ingresado no está inscrito', 'warning']], $data);
        }

        // Si se proporciona un estado, obtener inscripciones filtradas por cédula y estado
        if (!is_null($estadoValue) && in_array($estadoValue, $validStates)) {
            $filtered_registrations = $registrationModel->getMisInscripcionesByCedulaYEstado($cedula, $estadoValue);

            if (empty($filtered_registrations)) {
                $data['estado'] = $estadoValue;
                // Si no se encuentran registros para el estado específico, redirigir con un mensaje específico
                return $this->redirectError(null, [['No se encontraron registros para el estado especificado', 'warning']], $data);
            }

            // Si se encuentran registros para el estado específico, utilizar estos registros
            $all_registrations = $filtered_registrations;
        }

        // Continuar con la vista normal si se encuentran registros
        $modulo = ModulosAdminPagos::INSCRIPCIONES;
        $data = [
            'registrations' => $all_registrations,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];
        return view('payments/inscripciones', $data);
    }

}