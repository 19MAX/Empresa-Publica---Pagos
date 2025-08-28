<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuthorsModel;
use App\Models\EventsModel;
use App\Models\PaymentsModel;
use App\Models\UserAuthorPermissionModel;
use App\Models\UserEventRestrictionModel;
use App\Models\UsersModel;
use ModulosAdmin;
use RolesOptions;

class UsersController extends BaseController
{

    private $usersModel;
    private $paymentsModel;
    private $authorsModel;

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $last_action = null)
    {
        return redirect()->to('admin/users/')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_action', $last_action)->
            with('last_data', $last_data);
    }

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->paymentsModel = new PaymentsModel();
        $this->authorsModel = new AuthorsModel();
    }

    public function index()
    {

        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $all_users = $this->usersModel->usersAll();
        $modulo = ModulosAdmin::USERS;

        $all_authors = $this->authorsModel->findAll();

        $data = [
            'authors' => $all_authors,
            'users' => $all_users,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];
        return view("admin/users/index.php", $data);
    }

    public function add()
    {
        $cedula = $this->request->getPost('ic');
        $first_name = $this->request->getPost('first_name');
        $last_name = $this->request->getPost('last_name');
        $phone_number = $this->request->getPost('phone_number');
        $email = $this->request->getPost('email');
        $address = $this->request->getPost('address');
        $password = $this->request->getPost('password');
        $password_repeat = $this->request->getPost('password_repeat');
        $rol_id = $this->request->getPost('rol_id') ?? "";
        $author_id = $this->request->getPost('author_id') ?? "";
        $event_id = $this->request->getPost('event_id') ?? ""; // Nuevo campo para evento específico
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
            'rol_id' => trim($rol_id),
            'author_id' => trim($author_id),
            'event_id' => trim($event_id),
        ];

        try {
            $validation = \Config\Services::validation();

            $rules =
                [
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
                        'label' => 'Coreo Electrónico',
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
                    'rol_id' => [
                        'label' => 'Roles de usuario',
                        'rules' => 'required|in_list[' . implode(',', [
                            RolesOptions::AdminPrincipal,
                            RolesOptions::AdministradorDePagos,
                            RolesOptions::AdministradorProservi,
                        ]) . ']',
                    ],
                    'event_id' => [
                        'label' => 'Evento específico',
                        'rules' => 'permit_empty|numeric|is_not_unique[events.id]',
                    ],
                ];


            // Validar author_id solo si el rol es Proservi (3)
            if ($rol_id === RolesOptions::AdministradorProservi) {
                $rules['author_id'] = [
                    'label' => 'Autor asociado',
                    'rules' => 'required|numeric|is_not_unique[authors.id]',
                ];
            }

            $validation->setRules($rules);
            if ($validation->run($data)) {
                // Verificar permisos del usuario actual para asignar este autor/evento
                $currentUserId = session('id');
                $currentUserRol = session('rol');

                // Validar permisos según el rol y lógica de negocio
                if (!$this->canUserAssignAuthor($currentUserId, $currentUserRol, $author_id, $event_id)) {
                    return $this->redirectView(null, [['No tienes permisos para asignar este autor/evento', 'danger']], null);
                }

                // Remover campos que no van a la tabla users
                $userData = $data;
                unset($userData['password_repeat']);
                unset($userData['password_match']);
                unset($userData['author_id']); // No va a la tabla users
                unset($userData['event_id']);  // No va a la tabla users

                // Iniciar transacción para asegurar consistencia
                $this->usersModel->db->transStart();

                // Insertar usuario
                $insert_user = $this->usersModel->insert($userData);

                if ($insert_user) {
                    $newUserId = $this->usersModel->getInsertID();

                    // Cargar modelos de permisos
                    $eventRestrictionModel = new UserEventRestrictionModel();

                    // Determinar tipo de permiso según si se especificó evento
                    $permissionLevel = !empty($event_id) ? 'restricted' : 'full';

                    // Si el rol requiere autor, validamos que no esté ya asignado
                    if ($rol_id == RolesOptions::AdministradorProservi) {
                        $authorPermissionModel = new UserAuthorPermissionModel();

                        if ($authorPermissionModel->isAuthorAssigned($author_id)) {
                            return $this->redirectView(
                                null,
                                [['Este autor ya está asignado a otro usuario, seleccione otro autor.', 'danger']],
                                null
                            );
                        }

                        // Aquí sí se hace el assignAuthorToUser normalmente
                        $result = $authorPermissionModel->assignAuthorToUser(
                            $newUserId,
                            $author_id,
                            $permissionLevel,
                            $currentUserId
                        );

                        if (!$result) {
                            $this->usersModel->db->transRollback();
                            return $this->redirectView(null, [['Error al asignar autor al usuario', 'danger']], null);
                        }
                    }


                    // Si se especificó un evento, asignarlo también
                    if (!empty($event_id)) {
                        $eventAssigned = $eventRestrictionModel->assignEventToUser(
                            $newUserId,
                            $event_id,
                            $currentUserId
                        );

                        if (!$eventAssigned) {
                            $this->usersModel->db->transRollback();
                            return $this->redirectView(null, [['Error al asignar evento específico al usuario', 'danger']], null);
                        }
                    }

                    // Confirmar transacción
                    $this->usersModel->db->transComplete();

                    if ($this->usersModel->db->transStatus() === FALSE) {
                        return $this->redirectView(null, [['Error en la transacción al crear usuario', 'danger']], null);
                    } else {
                        $successMessage = !empty($event_id)
                            ? 'Usuario agregado exitosamente con acceso a evento específico'
                            : 'Usuario agregado exitosamente con acceso completo al autor';

                        return $this->redirectView(null, [[$successMessage, 'success']], null);
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
            // Asegurar rollback en caso de excepción
            if ($this->usersModel->db->transStatus() !== FALSE) {
                $this->usersModel->db->transRollback();
            }

            log_message('error', 'Error al crear usuario: ' . $e->getMessage());
            return $this->redirectView(null, [['No se pudo agregar al nuevo usuario: ' . $e->getMessage(), 'danger']]);
        }
    }


    /**
     * Verificar si el usuario actual puede asignar un autor/evento específico
     */
    private function canUserAssignAuthor($currentUserId, $currentUserRol, $authorId, $eventId = null)
    {
        // Super admin siempre puede
        if ($currentUserRol == RolesOptions::AdminPrincipal) {
            return true;
        }

        // Cargar modelo de permisos
        $authorPermissionModel = new UserAuthorPermissionModel();

        // Verificar si tiene permisos sobre el autor
        $authorPermission = $authorPermissionModel->hasAuthorPermission($currentUserId, $authorId);

        if (!$authorPermission) {
            return false;
        }

        // Si se especificó un evento, verificar que pertenezca al autor y que el usuario tenga permisos
        if (!empty($eventId)) {
            $eventModel = new EventsModel();
            $event = $eventModel->find($eventId);

            if (!$event || $event['author_id'] != $authorId) {
                return false;
            }

            // Solo usuarios con permiso 'full' pueden asignar eventos específicos
            return $authorPermission['permission_level'] === 'full';
        }

        // Para asignar autor completo, debe tener permiso 'full'
        return $authorPermission['permission_level'] === 'full';
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $cedula = $this->request->getPost('cedula');
        $first_name = $this->request->getPost('first_name');
        $last_name = $this->request->getPost('last_name');
        $phone_number = $this->request->getPost('phone_number');
        $email = $this->request->getPost('email');
        $address = $this->request->getPost('address');
        $rol_id = $this->request->getPost('rol_id');
        $author_id = $this->request->getPost('author_id') ?? "";

        $data = [
            'id' => $id,
            'ic' => trim($cedula),
            'first_name' => trim($first_name),
            'last_name' => trim($last_name),
            'phone_number' => trim($phone_number),
            'email' => $email,
            'address' => $address,
            'rol_id' => trim($rol_id),
            'author_id' => trim($author_id),
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
                'rol_id' => [
                    'label' => 'Roles de usuario',
                    'rules' => 'required|in_list[' . implode(',', [
                        RolesOptions::AdminPrincipal,
                        RolesOptions::AdministradorDePagos,
                        RolesOptions::AdministradorProservi,
                    ]) . ']',
                ]
            ];

            // Validar author_id solo si el rol es Proservi (3)
            if ($rol_id === RolesOptions::AdministradorProservi) {
                $rules['author_id'] = [
                    'label' => 'Autor asociado',
                    'rules' => 'required|numeric|is_not_unique[authors.id]',
                ];
            }

            $validation->setRules($rules);

            if ($validation->run($data)) {
                // Verificar permisos del usuario actual
                $currentUserId = session('id');
                $currentUserRol = session('rol');

                // Validar permisos según el rol y lógica de negocio
                if ($rol_id === RolesOptions::AdministradorProservi && !empty($author_id)) {
                    if (!$this->canUserAssignAuthor($currentUserId, $currentUserRol, $author_id)) {
                        return $this->redirectView(null, [['No tienes permisos para asignar este autor', 'danger']], null);
                    }
                }

                // Remover campos que no van a la tabla users
                $userData = $data;
                unset($userData['author_id']); // No va a la tabla users

                // Iniciar transacción
                $this->usersModel->db->transStart();

                // Actualizar usuario
                $update_user = $this->usersModel->update($id, $userData);

                if ($update_user) {
                    // Si es rol Proservi, manejar la asignación de autor
                    if ($rol_id == RolesOptions::AdministradorProservi && !empty($author_id)) {
                        $authorPermissionModel = new UserAuthorPermissionModel();

                        // Verificar si el autor ya está asignado a otro usuario
                        $existingAssignment = $authorPermissionModel->isAuthorAssigned($author_id);

                        if ($existingAssignment && is_array($existingAssignment) && $existingAssignment['user_id'] != $id) {
                            // Si el autor está asignado a otro usuario, reasignarlo al usuario actual
                            // Primero eliminar la asignación anterior
                            $authorPermissionModel->removeAuthorFromUser($existingAssignment['user_id'], $author_id);
                        }

                        // Primero eliminar cualquier asignación anterior del usuario
                        $authorPermissionModel->removeUserAuthorPermissions($id);

                        // Asignar el nuevo autor
                        $result = $authorPermissionModel->assignAuthorToUser(
                            $id,
                            $author_id,
                            'full', // nivel de permiso por defecto
                            $currentUserId
                        );

                        if (!$result) {
                            $this->usersModel->db->transRollback();
                            return $this->redirectView(null, [['Error al asignar autor al usuario', 'danger']], null);
                        }
                    } elseif ($rol_id != RolesOptions::AdministradorProservi) {
                        // Si cambió el rol y ya no es Proservi, eliminar permisos de autor
                        $authorPermissionModel = new UserAuthorPermissionModel();
                        $authorPermissionModel->removeUserAuthorPermissions($id);
                    }

                    // Confirmar transacción
                    $this->usersModel->db->transComplete();

                    if ($this->usersModel->db->transStatus() === FALSE) {
                        return $this->redirectView(null, [['Error en la transacción al actualizar usuario', 'danger']], null);
                    } else {
                        return $this->redirectView(null, [['Datos del usuario actualizado exitosamente', 'success']], null);
                    }
                } else {
                    $this->usersModel->db->transRollback();
                    return $this->redirectView(null, [['No se pudo actualizar los datos del usuario', 'danger']], null);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'update');
            }
        } catch (\Exception $e) {
            // Asegurar rollback en caso de excepción
            if ($this->usersModel->db->transStatus() !== FALSE) {
                $this->usersModel->db->transRollback();
            }

            log_message('error', 'Error al actualizar usuario: ' . $e->getMessage());
            return $this->redirectView(null, [['No se pudo actualizar los datos del usuario: ' . $e->getMessage(), 'danger']]);
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
                        'rules' => "required|numeric",
                    ],
                ]
            );

            if ($validation->run($data)) {
                // Primero verificamos si el usuario tiene rol_id = 1
                $user = $this->usersModel->find($id);

                if (!$user) {
                    return $this->redirectView(null, [['Usuario no encontrado', 'warning']], null);
                }

                if ($user['rol_id'] == 1) {
                    return $this->redirectView(null, [['No se pueden eliminar usuarios con rol administrador', 'warning']], null);
                }

                $delete_user = $this->usersModel->delete($id);

                if ($delete_user) {
                    return $this->redirectView(null, [['Usuario eliminado exitosamente', 'success']], null);
                } else {
                    return $this->redirectView(null, [['No se pudo eliminar al usuario', 'danger']], null);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data);
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo eliminar al usuario', 'danger']]);
        }
    }

    public function recoverPassword()
    {
        $id = $this->request->getPost('id');
        $password = $this->request->getPost('password');
        $password_repeat = $this->request->getPost('password_repeat');
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'id' => trim($id),
            'password' => $hashed_password,
            'password_match' => $password,
            'password_repeat' => $password_repeat,
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'password' => [
                        'label' => 'Contraseña',
                        'rules' => 'required|min_length[6]',
                    ],
                    'password_repeat' => [
                        'label' => 'Repetir contraseña',
                        'rules' => 'required|min_length[6]|matches[password_match]',
                    ],
                ]
            );

            if ($validation->run($data)) {

                unset($data['password_repeat']);
                unset($data['password_match']);
                $insert_user = $this->usersModel->update($id, $data);

                if ($insert_user) {
                    return $this->redirectView(null, [['Contraseña cambiada exitosamente', 'success']], null);
                } else {
                    return $this->redirectView(null, [['No se logro el cambio de contraseña', 'danger']], null);
                }
            } else {
                unset($data['password']);
                $data['password'] = $password;
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'recover');
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo cambiar la contraseña', 'danger']]);
        }
    }

    public function online()
    {

        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $all_recaudaciones_online = $this->paymentsModel->getRecaudacionesOnline();
        $modulo = ModulosAdmin::RECAUDACIONES_ONLINE;

        $data = [
            'users' => $all_recaudaciones_online,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];
        return view("admin/users/recaudaciones/online_recaudaciones", $data);
    }

    public function recaudaciones()
    {

        $id = session('id');
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $all_users = $this->usersModel->getUserCollections($id);
        $modulo = ModulosAdmin::MIS_RECAUDACIONES;

        $data = [
            'users' => $all_users,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];
        return view("admin/users/recaudaciones/recaudaciones", $data);
    }

    public function all_recaudaciones()
    {

        $id = $this->request->getPost('id');
        // $id = session('id');
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $all_users = $this->usersModel->getUserRecaudado();
        $modulo = ModulosAdmin::RECAUDACIONES;

        $data = [
            'users' => $all_users,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];
        return view("admin/users/recaudaciones/all_recaudaciones", $data);
    }
}