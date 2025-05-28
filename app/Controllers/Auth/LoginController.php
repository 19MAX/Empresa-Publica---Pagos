<?php
namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\LoginModel;
use RolesOptions;

class LoginController extends BaseController
{

    private function redirectView($validation = null, $flashMessages = null, $last_data = null)
    {
        return redirect()->to('login')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data);
    }

    public function index()
    {

        // get flash data
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');

        $data = [
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
        ];

        $accessGranted = grantAccess();
        if ($accessGranted) return $accessGranted;
        return view('auth/login',$data);
    }
    public function login()
    {
        $email = $this->request->getPost('login-email');
        $password = $this->request->getPost('login-password');
        $loginModel = new LoginModel();
        $data = [
            'email' => trim($email),
            'password' => trim($password)
        ];
        try {

            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'email' => [
                        'label' => 'Correo electrónico',
                        'rules' => 'required|valid_email',
                    ],
                    'password' => [
                        'label' => 'Contraseña',
                        'rules' => 'required|min_length[4]',
                    ],
                ]
            );
            if ($validation->run($data)) {

                // Buscar al usuario por su correo electrónico
                $user = $loginModel->where('email', $email)->first();
                if ($user) {
                    // Verificar la contraseña
                    if (password_verify($password, $user['password'])) {
                        // Iniciar sesión exitosa
                        $session = session();
                        $session->set([
                            'id' => $user['id'],
                            'user_email' => $user['email'],
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'rol' => $user['rol_id']
                        ]);

                        // Redirigir al usuario según su rol
                        switch ($user['rol_id']) {
                            case RolesOptions::AdminPrincipal:
                                return redirect('admin/dashboard');
                            case RolesOptions::AdministradorDePagos:
                                return redirect('punto/pago');
                            case RolesOptions::AdministradorProservi:
                                return redirect('proservi/reportes');
                            // case RolesOptions::UsuarioPublico:
                            //     return redirect('public/dashboard');
                            default:
                                return redirect('login');
                        }
                    } else {
                        return $this->redirectView(null, [['Contraseña incorrecta', 'danger']], $data);
                    }
                } else {
                    return $this->redirectView(null, [['Correo electrónico no registrado', 'danger']], $data);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data);
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['Error en los datos enviados', 'danger']], $data);
        }
    }
    public function forgotPassword()
    {
        $accessGranted = grantAccess();
        if ($accessGranted) return $accessGranted;
        return view('auth/forgot');
    }
    public function register()
    {
        $accessGranted = grantAccess();
        if ($accessGranted) return $accessGranted;
        return view('auth/register');
    }
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}
