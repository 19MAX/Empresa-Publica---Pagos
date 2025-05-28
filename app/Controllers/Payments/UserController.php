<?php

namespace App\Controllers\Payments;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use ModulosAdminPagos;

class UserController extends BaseController
{
    private $usersModel;

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $last_action = null)
    {
        return redirect()->to('punto/pago/user')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_action', $last_action)->
            with('last_data', $last_data);
    }

    public function __construct()
    {
        $this->usersModel = new UsersModel();
    }

    public function index()
    {
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $user_id = session('id');
        $user = $this->usersModel->detalleUsuario($user_id);
        $modulo = ModulosAdminPagos::USERS;

        $data = [
            'user' => $user,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];
        return view("payments/user/perfil", $data);
    }

    public function update()
    {
        $user_id = session('id');
        $cedula = $this->request->getPost('cedula');
        $first_name = $this->request->getPost('first_name');
        $last_name = $this->request->getPost('last_name');
        $phone_number = $this->request->getPost('phone_number');
        $email = $this->request->getPost('email');
        $address = $this->request->getPost('address');
        $rol_id = $this->request->getPost('rol_id');

        $data = [
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
                    'ic' => [
                        'label' => 'Número de cédula',
                        'rules' => "required|numeric|max_length[10]|is_unique[users.ic,id,{$user_id}]",
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
                ]
            );

            if ($validation->run($data)) {
                $update_user = $this->usersModel->update($user_id, $data);

                if ($update_user) {
                    return $this->redirectView(null, [['Datos actualizados exitosamente', 'success']], null);
                } else {
                    return $this->redirectView(null, [['No se pudo actualizar los datos', 'danger']], null);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'update');
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo actualizar los datos', 'danger']]);
        }
    }

    public function recoverPassword()
    {
        $user_id = session('id');
        $password = $this->request->getPost('password');
        $password_repeat = $this->request->getPost('password_repeat');
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $data = [
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
                $update_password = $this->usersModel->update($user_id, $data);

                if ($update_password) {
                    return $this->redirectView(null, [['Contraseña cambiada exitosamente', 'success']], null);
                } else {
                    return $this->redirectView(null, [['No se pudo cambiar la contraseña', 'danger']], null);
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

    public function recaudaciones()
    {

        $id = session('id');
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $all_users = $this->usersModel->getUserCollections($id);
        $modulo = ModulosAdminPagos::MIS_RECAUDACIONES;

        $data = [
            'users' => $all_users,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];
        return view("payments/user/recaudaciones/mis_recaudaciones", $data);
    }
}