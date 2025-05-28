<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\EventCategoryModel;
use App\Models\EventsModel;
use ModulosAdmin;
use Modalidad;

class EventsController extends BaseController
{
    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $route = null, $id = null)
    {
        return redirect()->to('admin/event/' . $route . '/' . $id)->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data);
    }

    public function __construct()
    {
        helper('image');
    }

    public function index()
    {
        // get flash data
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $eventModel = new EventsModel();
        $all_events = $eventModel->getAllEventsWithCategories();

        $modulo = ModulosAdmin::EVENTS_LIST;
        $data = [
            'events' => $all_events,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];

        return view('admin/events/event', $data);
    }

    public function add()
    {
        $event_name = $this->request->getPost('event_name');
        $short_description = $this->request->getPost('short_description');
        $event_date = $this->request->getPost('event_date');
        $address = $this->request->getPost('address');
        $modality = $this->request->getPost('modality');
        $event_duration = $this->request->getPost('event_duration');
        $registrations_start_date = $this->request->getPost('registrations_start_date');
        $registrations_end_date = $this->request->getPost('registrations_end_date');
        $image = $this->request->getFile('image');
        $categories = $this->request->getPost('id_cat');

        $data = [
            'event_name' => trim($event_name),
            'short_description' => trim($short_description),
            'event_date' => $event_date,
            'address' => trim($address),
            'modality' => $modality,
            'event_duration' => $event_duration,
            'registrations_start_date' => $registrations_start_date,
            'registrations_end_date' => $registrations_end_date,
            'categories' => $categories,
        ];

        // Iniciar la transacción
        $db = \Config\Database::connect();
        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'event_name' => [
                        'label' => 'Nombre del evento',
                        'rules' => 'required|min_length[3]|is_unique[events.event_name]',
                    ],
                    'short_description' => [
                        'label' => 'Descripción del evento',
                        'rules' => 'required|min_length[5]',
                    ],
                    'event_date' => [
                        'label' => 'Fecha del evento',
                        'rules' => 'required|valid_date|fecha_evento',
                    ],
                    'address' => [
                        'label' => 'Dirección',
                        'rules' => 'required',
                    ],
                    'modality' => [
                        'label' => 'Modalidad',
                        'rules' => 'required|in_list[' . implode(',', [
                            Modalidad::Presencial,
                            Modalidad::Virtual,
                            Modalidad::Hibrida,
                        ]) . ']',
                    ],
                    'event_duration' => [
                        'label' => 'Duración del evento',
                        'rules' => 'validarDuracion',
                    ],
                    'registrations_start_date' => [
                        'label' => 'Fecha de inicio de inscripciones',
                        'rules' => 'required|valid_date|fecha_inicio_inscripcion[registrations_end_date,event_date]',
                    ],
                    'registrations_end_date' => [
                        'label' => 'Fecha de fin de inscripciones',
                        'rules' => 'required|valid_date|fecha_fin_inscripcion[registrations_start_date,event_date]',
                    ],
                    'image' => [
                        'label' => 'Imagen',
                        'rules' => 'uploaded[image]|is_image[image]|max_size[image,1024]',
                    ],
                    'categories' => [
                        'label' => 'Categorías del evento',
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Debe seleccionar al menos una categoría.'
                        ]
                    ]
                ]
            );

            if ($validation->run($data)) {
                $db->transStart();

                unset($data['categories']);
                // Guardar los datos en la DB
                $eventsModel = new EventsModel;
                $new_event = $eventsModel->insert($data);

                if ($new_event) {
                    $image = $this->request->getFile('image');
                    $imagePath = uploadImage($image);

                    if ($imagePath !== false) {
                        $data['image'] = $imagePath;
                        $eventsModel->update($new_event, ['image' => $data['image']]);

                        // Guardar categorías
                        $eventCategoryModel = new EventCategoryModel();
                        foreach ($categories as $category_id) {
                            $eventCategoryModel->insert([
                                'event_id' => $new_event,
                                'cat_id' => $category_id,
                            ]);
                        }

                        $db->transComplete();

                        return $this->redirectView(null, [['Evento agregado exitosamente', 'success']], null, 'new');
                    } else {
                        $db->transRollback();
                        return $this->redirectView(null, [['Error en la subida de la imagen', 'danger']], $data, 'new');
                    }
                } else {
                    $db->transRollback();
                    return $this->redirectView(null, [['No fue posible guardar el evento', 'warning']], $data, 'new');
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'new');
            }
        } catch (\Exception $e) {
            // Si ocurre alguna excepción, revertir la transacción
            $db->transRollback();
            return $this->redirectView(null, [['No se pudo registrar el evento ' . $e->getMessage(), 'danger']], null, 'new');
        }
    }

    public function new_event()
    {
        // get flash data
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $categoryModel = new CategoryModel();
        $all_category = $categoryModel->findAll();

        $modulo = ModulosAdmin::EVENTS_ADD;
        $data = [
            'categories' => $all_category,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];
        return view('admin/events/nuevo_evento', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $event_name = $this->request->getPost('event_name');
        $short_description = $this->request->getPost('short_description');
        $event_date = $this->request->getPost('event_date');
        $address = $this->request->getPost('address');
        $modality = $this->request->getPost('modality');
        $event_duration = $this->request->getPost('event_duration');
        $registrations_start_date = $this->request->getPost('registrations_start_date');
        $registrations_end_date = $this->request->getPost('registrations_end_date');
        $image = $this->request->getFile('image');
        $categories = $this->request->getPost('id_cat');
        $status = $this->request->getPost('event_status');

        $data = [
            'event_name' => trim($event_name),
            'short_description' => trim($short_description),
            'event_date' => $event_date,
            'address' => trim($address),
            'modality' => $modality,
            'event_duration' => $event_duration,
            'registrations_start_date' => $registrations_start_date,
            'registrations_end_date' => $registrations_end_date,
            'categories' => $categories,
            'event_status' => $status
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'event_name' => [
                        'label' => 'Nombre del evento',
                        'rules' => "required|min_length[3]|is_unique[events.event_name,id,{$id}]",
                    ],
                    'short_description' => [
                        'label' => 'Descripción corta',
                        'rules' => 'min_length[5]|permit_empty',
                    ],
                    'event_date' => [
                        'label' => 'Fecha del evento',
                        'rules' => 'required|valid_date',
                    ],
                    'address' => [
                        'label' => 'Dirección',
                        'rules' => 'required',
                    ],
                    'modality' => [
                        'label' => 'Modalidad',
                        'rules' => 'required|in_list[' . implode(',', [
                            Modalidad::Presencial,
                            Modalidad::Virtual,
                            Modalidad::Hibrida,
                        ]) . ']',
                    ],
                    'event_duration' => [
                        'label' => 'Duración del evento',
                        'rules' => 'validarDuracion',
                    ],
                    'registrations_start_date' => [
                        'label' => 'Fecha de inicio de inscripciones',
                        'rules' => 'required|valid_date',
                    ],
                    'registrations_end_date' => [
                        'label' => 'Fecha de fin de inscripciones',
                        'rules' => 'required|valid_date',
                    ],
                    'image' => [
                        'label' => 'Imagen',
                        'rules' => 'is_image[image]|max_size[image,1024]',
                    ],
                    'categories' => [
                        'label' => '
                         del evento',
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Debe seleccionar al menos una categoría.'
                        ]
                    ],
                    'event_status' => [
                        'label' => 'Estado',
                        'rules' => 'required',
                    ],
                ]
            );

            if ($validation->run($data)) {


                $eventsModel = new EventsModel();
                $newImagePath = null;
                $oldImagePath = null;
                unset($data['categories']);

                // Obtener la imagen actual antes de cualquier cambio
                $current_event = $eventsModel->find($id);
                $oldImagePath = $current_event['image'] ?? null;

                // Manejar la nueva imagen usando el helper
                if ($image->isValid()) {
                    $newImagePath = uploadImage($image);
                    if (!$newImagePath) {
                        return $this->redirectView(null, [['Error subiendo la imagen', 'warning']], null, 'edit', $id);
                    }
                }

                // Llamar al método del modelo para actualizar el evento
                $result = $eventsModel->updateEvent($id, $data, $categories, $newImagePath, $oldImagePath);

                if ($result) {
                    return $this->redirectView(null, [['Evento actualizado exitosamente', 'success']], null, 'edit', $id);
                } else {
                    // Si la actualización falló, la nueva imagen ya se habrá eliminado en updateEvent
                    return $this->redirectView(null, [['No se pudo actualizar el evento', 'danger']], null, 'edit', $id);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'edit', $id);
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo actualizar el evento', 'danger']], 'edit', $id);
        }
    }

    public function edit_event($id)
    {
        // get flash data
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $categoryModel = new CategoryModel();
        $all_category = $categoryModel->findAll();
        $eventModel = new EventsModel();
        $event = $eventModel->getEventDetailsById($id);
        $data = [
            'categories' => $all_category,
            'event' => $event,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
        ];
        return view('admin/events/edit_evento', $data);
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        // Verificar que el ID no esté vacío
        if (empty($id)) {
            return $this->redirectView(null, [['El ID del evento es requerido', 'danger']]);
        }

        try {
            $eventsModel = new EventsModel();
            // Buscar el evento por ID
            $event = $eventsModel->find($id);

            // Verificar si el evento existe
            if ($event) {
                $eventsModel->delete($id);

                return $this->redirectView(null, [['Evento eliminado exitosamente', 'success']]);
            } else {
                return $this->redirectView(null, [['Evento no encontrado', 'danger']]);
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo eliminar el evento', 'danger']]);
        }
    }

    public function trash()
    {
        // get flash data
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $eventModel = new EventsModel();

        $all_events = $eventModel->onlyDeleted()->findAll();
        // $all_events = $eventModel->getAllEventsWithCategories();

        $data = [
            'events' => $all_events,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages
        ];

        return view('admin/events/trash/trash', $data);
    }

    private function redirectTrashView($validation = null, $flashMessages = null, $last_data = null, )
    {

        return redirect()->to('admin/event/trash')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data);
    }

    public function restore()
    {
        try {
            $category_id = $this->request->getPost('id');
            $eventsModel = new EventsModel();

            $client = $eventsModel->onlyDeleted()->find($category_id);
            if (isset($client)) {
                $eventsModel->withDeleted()->set(['deleted_at' => null])->update($category_id);
                return $this->redirectTrashView(null, [['Evento restaurada correctamente', 'success']]);
            } else {
                return $this->redirectTrashView(null, [['No existe el evento buscado', 'warning']]);
            }
        } catch (\Exception $e) {
            return $this->redirectTrashView(null, [['No fue posible restaurar el evento', 'danger']]);
        }
    }

    public function get_event()
    {
        $eventsModel = new EventsModel;

        // Obtiene el término de búsqueda desde la solicitud
        $searchTerm = $this->request->getVar('q');

        // Filtra los eventos por nombre, dependiendo del término de búsqueda
        if ($searchTerm) {
            $events = $eventsModel->like('event_name', $searchTerm)->findAll();
        } else {
            $events = $eventsModel->findAll(); // Si no hay término de búsqueda, devuelve todos los eventos
        }

        // Prepara los eventos en formato compatible con Select2
        $formatted_events = [];
        foreach ($events as $event) {
            $formatted_events[] = [
                'id' => $event['id'],
                'text' => $event['event_name']
            ];
        }

        return $this->response->setJSON($formatted_events);
    }

    public function get_categories_by_event($eventId)
    {
        $categoriesModel = new CategoryModel;
        $categories = $categoriesModel->getCategoriesByEventId($eventId);

        $formatted_categories = [];
        foreach ($categories as $category) {
            $formatted_categories[] = [
                'id' => $category['id'], // ID de la categoría
                'text' => $category['category_name'], // Nombre de la categoría
                'precio' => $category['cantidad_dinero']
            ];
        }

        return $this->response->setJSON($formatted_categories);
    }

    public function deleteAll()
    {
        $eventId = $this->request->getPost('id');

        // Verificar que el ID no esté vacío
        if (empty($eventId)) {
            return $this->redirectView(null, [['El ID del evento es requerido', 'error']]);
        }

        try {
            $eventsModel = new EventsModel();
            // Buscar el evento por ID
            $event = $eventsModel->withDeleted()->find($eventId);

            // Verificar si el evento existe
            if ($event) {
                $eventsModel->deleteEventWithRelations($eventId);

                return $this->redirectTrashView(null, [['Evento con sus registros eliminados exitosamente', 'success']]);
            } else {
                return $this->redirectTrashView(null, [['Evento no encontrado', 'error']]);
            }
        } catch (\Exception $e) {
            return $this->redirectTrashView(null, [['No se pudo eliminar el evento', 'error']]);
        }
    }
}