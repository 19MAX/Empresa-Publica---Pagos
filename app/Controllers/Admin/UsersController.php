<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentsModel;
use App\Models\UsersModel;
use ModulosAdmin;
use RolesOptions;

class UsersController extends BaseController
{

    private $usersModel;
    private $paymentsModel;

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
    }

    public function index()
    {

        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $all_users = $this->usersModel->usersAll();
        $modulo = ModulosAdmin::USERS;

        $data = [
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
        $rol_id = $this->request->getPost('rol_id');
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
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
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
                    ]
                ]
            );

            if ($validation->run($data)) {

                unset($data['password_repeat']);
                unset($data['password_match']);
                $insert_user = $this->usersModel->insert($data);

                if ($insert_user) {
                    return $this->redirectView(null, [['Usuario agregado exitosamente', 'success']], null);
                } else {
                    return $this->redirectView(null, [['No se agregar al nuevo usuario', 'danger']], null);
                }
            } else {
                unset($data['password']);
                $data['password'] = $password;
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'insert');
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo agregar al nuevo usuario', 'danger']]);
        }
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

        $data = [
            'id' => $id,
            'ic' => trim($cedula),
            'first_name' => trim($first_name),
            'last_name' => trim($last_name),
            'phone_number' => trim($phone_number),
            'email' => $email,
            'address' => $address,
            'rol_id' => trim($rol_id),
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
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
                        'label' => 'Coreo Electrónico',
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
                ]
            );

            if ($validation->run($data)) {


                $update_user = $this->usersModel->update($id, $data);

                if ($update_user) {
                    return $this->redirectView(null, [['Datos del usuario actualizado exitosamente', 'success']], null);
                } else {
                    return $this->redirectView(null, [['No se pudo actualizar los datos del usuario', 'danger']], null);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'update');
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo actualizar los datos del usuario', 'danger']]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        $data = [
            'id'=> $id,
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