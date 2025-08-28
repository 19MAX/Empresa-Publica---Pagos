<?php

namespace App\Controllers\Proservi;

use App\Controllers\BaseController;
use App\Models\EventsModel;
use App\Models\PaymentsModel;
use App\Models\UserAuthorPermissionModel;
use App\Models\UserEventRestrictionModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ModuloProservi;
use RolesOptions;

class UsersController extends BaseController
{

    private $usersModel;
    private $paymentsModel;
    private $eventsModel;
    private $userEventRestrictionModel;
    private $user_author_permissions;

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $last_action = null)
    {
        return redirect()->to('proservi/users')->
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
        $this->userEventRestrictionModel = new UserEventRestrictionModel();
        $this->user_author_permissions = new UserAuthorPermissionModel();
    }
    public function index()
    {
        // Verificar que el usuario tenga permisos (debe ser Proservi con author_id)
        $currentUserId = session('id');
        $currentUserRol = session('rol');
        $authorId = session('author_id');

        if ($currentUserRol != RolesOptions::AdministradorProservi || empty($authorId)) {
            return redirect('login')->with('flashMessages', [['No tienes permisos para acceder a esta sección', 'danger']]);
        }

        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $modulo = ModuloProservi::USERS;

        // Obtener usuarios de eventos que están asignados a eventos del autor actual
        $eventUsers = $this->usersModel
            ->select('users.id, users.ic, users.first_name, users.last_name, users.email, users.phone_number, users.address, events.event_name as event_name, events.id as event_id')
            ->join('user_event_restrictions', 'user_event_restrictions.user_id = users.id')
            ->join('events', 'events.id = user_event_restrictions.event_id')
            ->where('users.rol_id', RolesOptions::UsuarioEventos)
            ->where('events.author_id', $authorId)
            ->where('events.event_status', 'Activo')
            ->where('users.created_by', $currentUserId)
            ->findAll();

        // Obtener eventos del autor actual
        $authorEvents = $this->eventsModel
            ->where('author_id', $authorId)
            ->where('event_status', 'Activo')
            ->findAll();

        $data = [
            'modulo' => $modulo,
            'eventUsers' => $eventUsers,
            'authorEvents' => $authorEvents,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
        ];

        return view("proservi/users/index.php", $data);
    }

    public function add()
    {
        $currentUserId = session('id');
        $currentUserRol = session('rol');
        $authorId = session('author_id');

        if ($currentUserRol != RolesOptions::AdministradorProservi || empty($authorId)) {
            return redirect('login');
        }

        $cedula = $this->request->getPost('ic');
        $first_name = $this->request->getPost('first_name');
        $last_name = $this->request->getPost('last_name');
        $phone_number = $this->request->getPost('phone_number');
        $email = $this->request->getPost('email');
        $address = $this->request->getPost('address');
        $password = $this->request->getPost('password');
        $password_repeat = $this->request->getPost('password_repeat');
        $event_id = $this->request->getPost('event_id') ?? "";

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'ic' => trim($cedula),
            'first_name' => trim($first_name),
            'last_name' => trim($last_name),
            'phone_number' => trim($phone_number),
            'email' => $email,
            'password' => $hashed_password,
            'password_match' => $password,
            'password_repeat' => $password_repeat,
            'address' => $address,
            'event_id' => trim($event_id),
        ];

        try {
            $validation = \Config\Services::validation();

            $rules = [
                'ic' => [
                    'label' => 'Número de cédula',
                    'rules' => "required|numeric|min_length[10]|max_length[10]|is_unique[users.ic]",
                ],
                'first_name' => [
                    'label' => 'Nombres del usuario',
                    'rules' => 'required|min_length[4]',
                ],
                'last_name' => [
                    'label' => 'Apellidos del usuario',
                    'rules' => 'required|min_length[4]',
                ],
                'phone_number' => [
                    'label' => 'Teléfono',
                    'rules' => 'required|numeric|min_length[10]|max_length[10]',
                ],
                'email' => [
                    'label' => 'Correo Electrónico',
                    'rules' => 'required|valid_email',
                ],
                'address' => [
                    'label' => 'Dirección',
                    'rules' => 'required|min_length[3]',
                ],
                'password' => [
                    'label' => 'Contraseña',
                    'rules' => 'required|min_length[6]',
                ],
                'password_repeat' => [
                    'label' => 'Repetir contraseña',
                    'rules' => "required|min_length[6]|matches[password_match]",
                ],
                'event_id' => [
                    'label' => 'Evento',
                    'rules' => 'required|numeric|is_not_unique[events.id]',
                ],
            ];

            $validation->setRules($rules);

            if ($validation->run($data)) {

                // Verificar que el evento pertenece al autor del usuario actual
                $event = $this->eventsModel->find($event_id);
                if (!$event || $event['author_id'] != $authorId) {
                    return $this->redirectView(null, [['No tienes permisos para asignar este evento', 'danger']], null);
                }

                // Preparar datos del usuario (rol será UsuarioEventos)
                $userData = $data;
                unset($userData['password_repeat']);
                unset($userData['password_match']);
                unset($userData['event_id']);
                $userData['rol_id'] = RolesOptions::UsuarioEventos;
                $userData['created_by'] = $currentUserId;

                // Iniciar transacción
                $this->usersModel->db->transStart();

                // Insertar usuario
                $insert_user = $this->usersModel->insert($userData);

                if ($insert_user) {
                    $newUserId = $this->usersModel->getInsertID();

                    // Asignar evento al usuario
                    $eventAssigned = $this->userEventRestrictionModel->assignEventToUser(
                        $newUserId,
                        $event_id,
                        $currentUserId
                    );

                    if (!$eventAssigned) {
                        $this->usersModel->db->transRollback();
                        return $this->redirectView(null, [['Error al asignar evento al usuario', 'danger']], null);
                    }

                    // Confirmar transacción
                    $this->usersModel->db->transComplete();

                    if ($this->usersModel->db->transStatus() === FALSE) {
                        return $this->redirectView(null, [['Error en la transacción al crear usuario', 'danger']], null);
                    } else {
                        return $this->redirectView(null, [['Usuario de evento creado exitosamente', 'success']], null);
                    }
                } else {
                    $this->usersModel->db->transRollback();
                    return $this->redirectView(null, [['No se pudo agregar al nuevo usuario', 'danger']], null);
                }
            } else {
                unset($data['password']);
                $data['password'] = $password;
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'insert');
            }
        } catch (\Exception $e) {
            if ($this->usersModel->db->transStatus() !== FALSE) {
                $this->usersModel->db->transRollback();
            }

            log_message('error', 'Error al crear usuario de evento: ' . $e->getMessage());
            return $this->redirectView(null, [['No se pudo agregar al nuevo usuario: ' . $e->getMessage(), 'danger']]);
        }
    }

    public function update()
    {
        $currentUserId = session('id');
        $currentUserRol = session('rol');
        $authorId = session('author_id');

        if ($currentUserRol != RolesOptions::AdministradorProservi || empty($authorId)) {
            return redirect('login');
        }

        $id = $this->request->getPost('id');
        $cedula = $this->request->getPost('cedula');
        $first_name = $this->request->getPost('first_name');
        $last_name = $this->request->getPost('last_name');
        $phone_number = $this->request->getPost('phone_number');
        $email = $this->request->getPost('email');
        $address = $this->request->getPost('address');
        $event_id = $this->request->getPost('event_id') ?? "";

        $data = [
            'id' => $id,
            'ic' => trim($cedula),
            'first_name' => trim($first_name),
            'last_name' => trim($last_name),
            'phone_number' => trim($phone_number),
            'email' => $email,
            'address' => $address,
            'event_id' => trim($event_id),
        ];

        try {
            $validation = \Config\Services::validation();

            $rules = [
                'id' => [
                    'label' => 'Id',
                    'rules' => "required|numeric",
                ],
                'ic' => [
                    'label' => 'Número de cédula',
                    'rules' => "required|numeric|max_length[10]|is_unique[users.ic,id,{$id}]",
                ],
                'first_name' => [
                    'label' => 'Nombres',
                    'rules' => 'required|min_length[4]',
                ],
                'last_name' => [
                    'label' => 'Apellidos',
                    'rules' => 'required|min_length[4]',
                ],
                'phone_number' => [
                    'label' => 'Teléfono',
                    'rules' => 'required|numeric|min_length[10]|max_length[10]',
                ],
                'email' => [
                    'label' => 'Correo Electrónico',
                    'rules' => 'required|valid_email',
                ],
                'address' => [
                    'label' => 'Dirección',
                    'rules' => 'required|min_length[4]',
                ],
                'event_id' => [
                    'label' => 'Evento',
                    'rules' => 'required|numeric|is_not_unique[events.id]',
                ],
            ];

            $validation->setRules($rules);

            if ($validation->run($data)) {
                // Verificar que el evento pertenece al autor del usuario actual
                $event = $this->eventsModel->find($event_id);
                if (!$event || $event['author_id'] != $authorId) {
                    return $this->redirectView(null, [['No tienes permisos para asignar este evento', 'danger']], null);
                }

                // Verificar que el usuario a actualizar es de tipo UsuarioEventos
                $user = $this->usersModel->find($id);
                if (!$user || $user['rol_id'] != RolesOptions::UsuarioEventos) {
                    return $this->redirectView(null, [['Usuario no válido para actualizar', 'danger']], null);
                }

                if (!($user['created_by'] == $currentUserId)) {
                    return $this->redirectView(null, [['No tienes permisos para actualizar este usuario', 'danger']], null);
                }

                // Preparar datos del usuario
                $userData = $data;
                unset($userData['event_id']);

                // Iniciar transacción
                $this->usersModel->db->transStart();

                // Actualizar usuario
                $update_user = $this->usersModel->update($id, $userData);

                if ($update_user) {
                    // Eliminar restricciones anteriores del usuario
                    $this->userEventRestrictionModel->where('user_id', $id)->delete();

                    // Asignar nuevo evento
                    $eventAssigned = $this->userEventRestrictionModel->assignEventToUser(
                        $id,
                        $event_id,
                        $currentUserId
                    );

                    if (!$eventAssigned) {
                        $this->usersModel->db->transRollback();
                        return $this->redirectView(null, [['Error al asignar evento al usuario', 'danger']], null);
                    }

                    // Confirmar transacción
                    $this->usersModel->db->transComplete();

                    if ($this->usersModel->db->transStatus() === FALSE) {
                        return $this->redirectView(null, [['Error en la transacción al actualizar usuario', 'danger']], null);
                    } else {
                        return $this->redirectView(null, [['Usuario actualizado exitosamente', 'success']], null);
                    }
                } else {
                    $this->usersModel->db->transRollback();
                    return $this->redirectView(null, [['No se pudo actualizar el usuario', 'danger']], null);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'update');
            }
        } catch (\Exception $e) {
            if ($this->usersModel->db->transStatus() !== FALSE) {
                $this->usersModel->db->transRollback();
            }

            log_message('error', 'Error al actualizar usuario de evento: ' . $e->getMessage());
            return $this->redirectView(null, [['No se pudo actualizar el usuario: ' . $e->getMessage(), 'danger']]);
        }
    }

    public function delete()
    {
        $currentUserId = session('id');
        $currentUserRol = session('rol');
        $authorId = session('author_id');

        if ($currentUserRol != RolesOptions::AdministradorProservi || empty($authorId)) {
            return redirect('login');
        }

        $id = $this->request->getPost('id');

        try {
            // Verificar que el usuario existe y es de tipo UsuarioEventos
            $user = $this->usersModel->find($id);
            if (!$user || $user['rol_id'] != RolesOptions::UsuarioEventos) {
                return $this->redirectView(null, [['Usuario no válido para eliminar', 'danger']], null);
            }

            if (!($user['created_by'] == $currentUserId)) {
                return $this->redirectView(null, [['No tienes permisos para eliminar este usuario', 'danger']], null);
            }

            // Verificar que el usuario está asignado a un evento del autor actual
            $userEvents = $this->userEventRestrictionModel
                ->join('events', 'events.id = user_event_restrictions.event_id')
                ->where('user_event_restrictions.user_id', $id)
                ->where('events.author_id', $authorId)
                ->findAll();

            if (empty($userEvents)) {
                return $this->redirectView(null, [['No tienes permisos para eliminar este usuario', 'danger']], null);
            }

            // Iniciar transacción
            $this->usersModel->db->transStart();

            // Eliminar restricciones de eventos
            $this->userEventRestrictionModel->where('user_id', $id)->delete();

            // Eliminar usuario
            $delete_user = $this->usersModel->delete($id);

            if ($delete_user) {
                // Confirmar transacción
                $this->usersModel->db->transComplete();

                if ($this->usersModel->db->transStatus() === FALSE) {
                    return $this->redirectView(null, [['Error en la transacción al eliminar usuario', 'danger']], null);
                } else {
                    return $this->redirectView(null, [['Usuario eliminado exitosamente', 'success']], null);
                }
            } else {
                $this->usersModel->db->transRollback();
                return $this->redirectView(null, [['No se pudo eliminar el usuario', 'danger']], null);
            }
        } catch (\Exception $e) {
            if ($this->usersModel->db->transStatus() !== FALSE) {
                $this->usersModel->db->transRollback();
            }

            log_message('error', 'Error al eliminar usuario de evento: ' . $e->getMessage());
            return $this->redirectView(null, [['No se pudo eliminar el usuario: ' . $e->getMessage(), 'danger']]);
        }
    }

    public function recover()
    {
        $currentUserId = session('id');
        $currentUserRol = session('rol');
        $authorId = session('author_id');

        if ($currentUserRol != RolesOptions::AdministradorProservi || empty($authorId)) {
            return redirect('login');
        }

        $id = $this->request->getPost('id');

        try {
            // Verificar que el usuario existe y es de tipo UsuarioEventos
            $user = $this->usersModel->find($id);
            if (!$user || $user['rol_id'] != RolesOptions::UsuarioEventos) {
                return $this->redirectView(null, [['Usuario no válido para recuperar contraseña', 'danger']], null);
            }

            if (!($user['created_by'] == $currentUserId)) {
                return $this->redirectView(null, [['No tienes permisos para eliminar este usuario', 'danger']], null);
            }

            // Verificar que el usuario está asignado a un evento del autor actual
            $userEvents = $this->userEventRestrictionModel
                ->join('events', 'events.id = user_event_restrictions.event_id')
                ->where('user_event_restrictions.user_id', $id)
                ->where('events.author_id', $authorId)
                ->findAll();

            if (empty($userEvents)) {
                return $this->redirectView(null, [['No tienes permisos para recuperar la contraseña de este usuario', 'danger']], null);
            }

            // Generar nueva contraseña temporal
            $newPassword = $this->generateRandomPassword();
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Actualizar contraseña en la base de datos
            $updateResult = $this->usersModel->update($id, ['password' => $hashedPassword]);

            if ($updateResult) {
                // Aquí puedes agregar lógica para enviar la nueva contraseña por email
                // Por ahora, la mostramos en el mensaje de éxito
                return $this->redirectView(null, [
                    ["Contraseña recuperada exitosamente. Nueva contraseña temporal: {$newPassword}", 'success']
                ], null);
            } else {
                return $this->redirectView(null, [['No se pudo recuperar la contraseña', 'danger']], null);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al recuperar contraseña: ' . $e->getMessage());
            return $this->redirectView(null, [['Error al recuperar la contraseña: ' . $e->getMessage(), 'danger']]);
        }
    }

    /**
     * Generar contraseña aleatoria
     */
    private function generateRandomPassword($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
