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

class UsersEventsController extends BaseController
{

    private $usersModel;
    private $paymentsModel;
    private $authorsModel;

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $last_action = null)
    {
        return redirect()->to('admin/users-events')->
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

        $all_users = $this->usersModel->usersEvents();
        $modulo = ModulosAdmin::USERS_EVENTS;

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
        return view("admin/users/usersEvents", $data);
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

        $data = [
            'id' => $id,
            'ic' => trim($cedula),
            'first_name' => trim($first_name),
            'last_name' => trim($last_name),
            'phone_number' => trim($phone_number),
            'email' => $email,
            'address' => $address,
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
                ]
            ];


            $validation->setRules($rules);

            if ($validation->run($data)) {

                $userData = $data;

                // Iniciar transacción
                $this->usersModel->db->transStart();

                // Actualizar usuario
                $update_user = $this->usersModel->update($id, $userData);

                if ($update_user) {
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

            log_message('error', 'UsersEvents::update-> Error al actualizar usuario: ' . $e->getMessage());
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
}