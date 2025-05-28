<?php

namespace App\Controllers\Config;

use App\Models\ConfigModel;
use CodeIgniter\Controller;
use App\Models\PaymentMethodsModel;

class ConfigController extends Controller
{

    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $last_action = null)
    {
        return redirect()->to('admin/config')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_action', $last_action)->
            with('last_data', $last_data);
    }

    public function index()
    {
        $configModel = new ConfigModel();

        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $additional_charge = $configModel->where('key', 'additional_charge')->first();
        $data = [
            'additional_charge' => $additional_charge,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
        ];

        return view('config/index.php', $data);
    }

    public function update()
    {

        $configModel = new ConfigModel();

        $additional_charge = $this->request->getPost('additional_charge');

        $data = [
            'additional_charge' => $additional_charge,
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'additional_charge' => [
                        'label' => 'Monto adicional por cada pago',
                        'rules' => "required|numeric",
                    ]
                ]
            );

            if ($validation->run($data)) {
                // Actualizar los valores en la base de datos

                $update_config = $configModel->update($configModel->where('key', 'additional_charge')->first()['id'], ['value' => $additional_charge]);

                if ($update_config) {
                    return $this->redirectView(null, [['Configuración actualizada exitosamente', 'success']], null);
                } else {
                    return $this->redirectView(null, [['No se pudo actualizar la configuración', 'danger']], null);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'update');
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo actualizar los datos de configuración', 'danger']]);
        }

    }
}