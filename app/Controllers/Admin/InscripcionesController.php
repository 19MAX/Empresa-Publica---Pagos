<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\EventsModel;
use App\Models\PaymentsModel;
use App\Models\RegistrationsModel;
use App\Services\PaymentAprobarService;
use CodeIgniter\I18n\Time;
use PaymentStatus;

class InscripcionesController extends BaseController
{
    private $paymentAprobarService;
    private $paymentsModel;

    public function __construct()
    {

        $this->paymentAprobarService = new PaymentAprobarService();
        $this->paymentsModel = new PaymentsModel();
    }

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $ic = null, $estado = null, $uniqueCode = null)
    {
        return redirect()->to('admin/inscripciones/' . $ic . '/' . $estado)->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data)->
            with('uniqueCode', $uniqueCode);
    }

    private function redirectViewInscripcion($validation = null, $flashMessages = null, $last_data = null)
    {
        return redirect()->to('admin/inscritos')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data);
    }

    private function redirectError($validation = null, $flashMessages = null, $last_data = null)
    {
        return redirect()->to('admin/inscripciones')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data);
    }

    public function pago()
    {

        $id_usuario = session('id');
        $id_pago = $this->request->getPost('id');
        $cedula = $this->request->getPost('cedula');
        $estado_pago = $this->request->getPost('estado_pago');

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
            $filtered_registrations = $registrationModel->getInscripcionesByCedulaYEstado($cedula, $estadoValue);

            if (empty($filtered_registrations)) {
                $data['estado'] = $estadoValue;
                // Si no se encuentran registros para el estado específico, redirigir con un mensaje específico
                return $this->redirectError(null, [['No se encontraron registros para el estado especificado', 'warning']], $data);
            }

            // Si se encuentran registros para el estado específico, utilizar estos registros
            $all_registrations = $filtered_registrations;
        }

        $data = [
            'registrations' => $all_registrations,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
        ];
        return view('admin/pagos/recauda_inscripciones', $data);
    }

    public function update()
    {
        $id_registro = $this->request->getPost('id');
        $id_event = $this->request->getPost('event_id');
        $category_id = $this->request->getPost('category_id');
        $full_name_user = $this->request->getPost('full_name_user');
        $ic = $this->request->getPost('ic');
        $address = $this->request->getPost('address');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');

        // Obtener el nombre del evento y el monto de la categoría
        $eventModel = new EventsModel(); // Asegúrate de tener este modelo
        $categoryModel = new CategoryModel(); // Asegúrate de tener este modelo

        $event = $eventModel->find($id_event);
        $category = $categoryModel->find($category_id);

        $name_event = $event['event_name'] ?? ''; // Asume que el campo se llama 'name'
        $monto_category = $category['cantidad_dinero'] ?? 0; // Asume que el campo se llama 'monto'

        $data = [
            'full_name_user' => trim($full_name_user),
            'ic' => trim($ic),
            'address' => trim($address),
            'phone' => trim($phone),
            'email' => trim($email),
            'event_cod' => $id_event,
            'cat_id' => $category_id,
            'event_name' => $name_event,
            'monto_category' => $monto_category
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'full_name_user' => [
                        'label' => 'Nombres del usuario inscrito',
                        'rules' => 'required|min_length[3]',
                    ],
                    'ic' => [
                        'label' => 'Cédula/Ruc',
                        'rules' => 'required|numeric',
                    ],
                    'phone' => [
                        'label' => 'Teléfono',
                        'rules' => 'required|numeric',
                    ],
                    'email' => [
                        'label' => 'Correo electrónico',
                        'rules' => 'required|valid_email',
                    ],
                    'event_cod' => [
                        'label' => 'Evento',
                        'rules' => 'required',
                    ],
                    'cat_id' => [
                        'label' => 'Categoría',
                        'rules' => 'required',
                    ],
                ]
            );

            if ($validation->run($data)) {
                $paymentCompleted = $this->paymentsModel->isPaymentCompletedByRegistrationId($id_registro);
                if ($paymentCompleted) {
                    return $this->redirectViewInscripcion(null, [['La inscripción no se puede actualizar por que ya se encuentra aprobada', 'error']], null);
                }
                // Obtener el registro actual antes de la actualización
                $registrationsModel = new RegistrationsModel();
                $currentData = $registrationsModel->find($id_registro);

                // Verificar si hubo cambios
                $changes = array_diff_assoc($data, $currentData);

                if (empty($changes)) {
                    // No hubo cambios
                    return $this->redirectViewInscripcion(null, [['No se han realizado cambios en el registro', 'info']], $data);
                }

                // Hubo cambios, proceder a la actualización
                $update_registration = $registrationsModel->update($id_registro, $data);

                if (!$update_registration) {
                    return $this->redirectViewInscripcion(null, [['No fue posible actualizar los datos del usuario inscrito', 'warning']], $data);
                } else {
                    // Obtener los datos del pago usando PaymentsModel
                    $paymentsModel = new PaymentsModel();
                    $paymentData = $paymentsModel->getPaymentDetailsByRegistrationId($id_registro);

                    // Enviar el correo solo si se realizaron cambios
                    $emailData = [
                        'to' => $email,
                        'subject' => 'Actualización de inscripción',
                        'message' => 'Estimado ' . $full_name_user . ', su inscripción ha sido actualizada correctamente.',
                        'htmlContent' => view('email/update_codigo', [
                            'user' => $full_name_user,
                            'evento' => $paymentData['evento'],
                            'categoria' => $paymentData['categoria'],
                            'precio' => $paymentData['precio'],
                            'codigoPago' => $paymentData['codigo_pago'],
                            'fecha_limite_pago' => $paymentData['fecha_limite_pago'],
                            'fechaEmision' => Time::now()->toDateTimeString(),
                        ]),
                        'pdfFilename' => 'comprobante_actualizado.pdf',
                        'emailType' => 'send_email_registro'
                    ];

                    // Añadir el trabajo a la cola
                    $jobId = service('queue')->push('emails', 'email', $emailData);

                    if ($jobId) {
                        return $this->redirectViewInscripcion(null, [['Datos del usuario actualizados y correo enviado', 'success']], null);
                    } else {
                        return $this->redirectViewInscripcion(null, [['Datos actualizados, pero no se pudo enviar el correo', 'warning']], null);
                    }
                }
            } else {

                // Obtener los errores específicos de la validación
                $errors = $validation->getErrors();

                // Transformar los errores en el formato esperado
                $errorMessages = [];
                foreach ($errors as $field => $errorMessage) {
                    $errorMessages[] = [$errorMessage, 'warning'];
                }

                // Redirigir pasando los errores específicos
                return $this->redirectViewInscripcion(null, $errorMessages, $data);
                // return $this->redirectViewInscripcion($validation, [['Error en los datos enviados', 'warning']], $data);
            }
        } catch (\Exception $e) {
            log_message('warning', $e->getMessage());
            return $this->redirectViewInscripcion(null, [['No se pudo actualizar los datos del usuario', 'error']], null);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        $data = [
            'id' => $id,
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'id' => [
                        'label' => 'Id',
                        'rules' => 'required|numeric',
                    ],
                ]
            );

            if ($validation->run($data)) {

                // Actualizar los datos en la DB
                $registrationsModel = new RegistrationsModel();
                $paymentCompleted = $this->paymentsModel->isPaymentCompletedByRegistrationId($id);
                if ($paymentCompleted) {
                    return $this->redirectViewInscripcion(null, [['La inscripción no se puede eliminar por que ya esta aprobada', 'error']], null);
                }
                $delete_registration = $registrationsModel->delete($id);
                $delete_payment = $this->paymentsModel->delete($id);

                if (!$delete_registration || !$delete_payment) {
                    return $this->redirectViewInscripcion(null, [['No fue posible eliminar la inscripción', 'warning']], $data);
                } else {
                    return $this->redirectViewInscripcion(null, [['Inscripción eliminada exitosamente', 'success']], null);
                }
            } else {
                return $this->redirectViewInscripcion($validation, [['Error en los datos enviados', 'warning']], $data);
            }
        } catch (\Exception $e) {
            log_message("warning", $e->getMessage());
            return $this->redirectViewInscripcion(null, [['No se pudo eliminar el registro', 'danger']], null);
        }
    }
}