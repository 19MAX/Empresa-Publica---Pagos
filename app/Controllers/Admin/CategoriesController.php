<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ConfigModel;
use ModulosAdmin;

class CategoriesController extends BaseController
{
    private function redirectView($validation = null, $flashMessages = null, $last_data = null, $last_action = null)
    {
        return redirect()->to('admin/category')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data)->
            with('last_action', $last_action);
    }

    public function index()
    {
        // get flash data
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $categoryModel = new CategoryModel();
        $all_category = $categoryModel->findAll();


        // Obtener el valor de additional_charge
        $configModel = new ConfigModel();
        $additional_charge = $configModel->getAdditionalCharge();

        $modulo = ModulosAdmin::CATEGORY_LIST;
        $data = [
            'categories' => $all_category,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
            'additional_charge' => $additional_charge,
        ];
        return view('admin/categories/category', $data);
    }

    public function add()
    {
        $category_name = $this->request->getPost('category_name');
        $short_description = $this->request->getPost('short_description');
        $category_value = $this->request->getPost('category_value');

        // Obtener el valor adicional de la configuración
        $configModel = new ConfigModel();
        $additionalCharge = $configModel->getAdditionalCharge();

        // Sumar el valor adicional al precio de la categoría
        $total_value = floatval($category_value) + floatval($additionalCharge);

        $data = [
            'category_name' => trim($category_name),
            'short_description' => trim($short_description),
            'cantidad_dinero' => $total_value,
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'category_name' => [
                        'label' => 'Nombre de la categoría',
                        'rules' => 'required|min_length[3]',
                    ],
                    'cantidad_dinero' => [
                        'label' => 'Valor de la categoría',
                        'rules' => 'required|numeric',
                    ],
                    'short_description' => [
                        'label' => 'Descripción de la categoría',
                        'rules' => 'min_length[5]|permit_empty',
                    ]
                ]
            );

            if ($validation->run($data)) {

                // Guardar los datos en la DB
                $categoryModel = new CategoryModel();
                $new_category = $categoryModel->insert($data);

                if (!$new_category) {
                    return $this->redirectView(null, [['No fue posible guardar la categoría', 'warning']], $data, 'insert');
                } else {
                    return $this->redirectView(null, [['Categoría agregada exitosamente', 'success']]);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'insert');
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo registrar la categoría', 'danger']]);
        }
    }

    public function update()
    {
        $id_category = $this->request->getPost('id');
        $category_name = $this->request->getPost('category_name');
        $short_description = $this->request->getPost('short_description');
        $category_value = $this->request->getPost('category_value');

        $data = [
            'id_category' => $id_category,
            'category_name' => trim($category_name),
            'short_description' => trim($short_description),
            'cantidad_dinero' => $category_value,
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'category_name' => [
                        'label' => 'Nombre de la categoría',
                        'rules' => 'required|min_length[3]',
                    ],
                    'cantidad_dinero' => [
                        'label' => 'Valor de la categoría',
                        'rules' => 'required|numeric',
                    ],
                    'short_description' => [
                        'label' => 'Descripción de la categoría',
                        'rules' => 'min_length[5]|permit_empty',
                    ]
                ]
            );

            if ($validation->run($data)) {

                // Actualizar los datos en la DB
                $categoryModel = new CategoryModel();
                unset($data['id_category']);
                $update_category = $categoryModel->update($id_category, $data);

                if (!$update_category) {
                    return $this->redirectView(null, [['No fue posible actualizar la categoría', 'warning']], $data, 'update');
                } else {
                    return $this->redirectView(null, [['Categoría actualizar exitosamente', 'success']]);
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, 'update');
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo actualizar la categoría', 'danger']]);
        }
    }

    public function delete()
    {
        $id_category = $this->request->getPost('id');

        $data = [
            'id' => $id_category,
        ];

        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'id' => [
                        'label' => 'Error',
                        'rules' => 'required|numeric',
                    ],
                ]
            );

            if ($validation->run($data)) {

                // Eliminar los datos en la DB
                $categoryModel = new CategoryModel();
                $delete_category = $categoryModel->delete($id_category);

                if (!$delete_category) {
                    return $this->redirectView(null, [['No fue posible eliminar la categoría', 'warning']], $data, 'delete');
                } else {
                    return $this->redirectView(null, [['Categoría eliminada exitosamente', 'success']]);
                }
            } else {
                return $this->redirectView($validation, [['Error al intentar eliminar la categoría', 'warning']], $data, 'delete');
            }
        } catch (\Exception $e) {
            return $this->redirectView(null, [['No se pudo eliminar la categoría', 'danger']]);
        }
    }
}
