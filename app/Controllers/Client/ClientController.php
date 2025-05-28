<?php
namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\EventsModel;
use App\Services\ApiPrivadaService;

class ClientController extends BaseController
{
    private $apiPrivadaService;
    public function __construct()
    {
        $this->apiPrivadaService = new ApiPrivadaService();
    }

    public function index()
    {
        helper('date');

        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $eventModel = new EventsModel();
        $active_events = $eventModel->getActiveAndCurrentEvents();

        // Formatear las fechas de los eventos
        foreach ($active_events as &$event) {
            $event['formatted_event_date'] = format_event_date($event['event_date']);
        }

        $data = [
            'events' => $active_events,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
        ];
        return view('client/home', $data);
    }

    public function obtenerUsuario()
    {
        $data = $this->request->getJSON();
        $cedula = trim($data->cedula);

        if (!$cedula) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cédula no enviada'
            ]);
        }

        try {
            // Usar el servicio para obtener los datos del usuario
            $persona = $this->apiPrivadaService->getDataUser($cedula);

            if ($persona && $persona['success'] && isset($persona['data'])) {
                // Extraer datos de la respuesta
                $personaData = $persona['data'];
                $email = $personaData['email'];

                // if ($email=='@' || !$email) {

                //     return $this->response->setJSON([
                //         'success' => false,
                //         'message' => 'Usuario no encontrado'
                //     ]);
                // }
                // Crear un nuevo array con los datos requeridos y censurados
                $datosPersona = [
                    'id' => $personaData['id'],
                    'name' => $personaData['name'],
                    'surname' => $personaData['surname'],
                    'full_name' => $personaData['full_name'],
                    'identification' => $personaData['identification'],
                    'address' => $personaData['address'],
                    'mobile' => $personaData['mobile'],
                    'email' => $personaData['email'],
                    'phone' => $personaData['phone'],
                    'gender' => $personaData['gender'],
                    'place_of_birth' => $personaData['place_of_birth'],
                    'date_of_birth' => $personaData['date_of_birth'],
                    'citizen_status' => $personaData['citizen_status'],
                    'civil_status' => $personaData['civil_status'],
                    'profession' => $personaData['profession'],
                    'type_identification' => $personaData['type_identification']
                ];

                return $this->response->setJSON([
                    'success' => true,
                    'persona' => $datosPersona
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


}