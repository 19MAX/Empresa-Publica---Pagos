<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuthorsModel;
use App\Models\PaymentsModel;
use App\Models\RegistrationsModel;
use ModulosAdmin;

class AutoresController extends BaseController
{
    protected $authorsModel;

    private function redirectView($validation = null, $flashMessages = null, $last_data = null)
    {
        return redirect()->to('admin/autores')->
            with('flashValidation', isset($validation) ? $validation->getErrors() : null)->
            with('flashMessages', $flashMessages)->
            with('last_data', $last_data);
    }

    public function __construct()
    {
        helper('image');
        // Load necessary models
        $this->authorsModel = new AuthorsModel();
    }
    public function index()
    {

        // get flash data
        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $all_authors = $this->authorsModel->findAll();

        $modulo = ModulosAdmin::AUTORES;
        $data = [
            'authors' => $all_authors,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
            'modulo' => $modulo,
        ];

        return view('admin/autores/index', $data);
    }

    public function add()
    {
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $image = $this->request->getFile('image');

        $data = [
            'name' => trim($name),
            'description' => trim($description),
        ];

        // Iniciar la transacción
        $db = \Config\Database::connect();
        try {
            $validation = \Config\Services::validation();
            $validation->setRules(
                [
                    'name' => [
                        'label' => 'Nombre del autor',
                        'rules' => 'required|min_length[3]|is_unique[authors.name]',
                    ],
                    'description' => [
                        'label' => 'Descripción del autor',
                        'rules' => 'permit_empty',
                    ],
                    'image' => [
                        'label' => 'Imagen',
                        'rules' => 'uploaded[image]|is_image[image]|max_size[image,1024]',
                    ],
                ]
            );

            if ($validation->run($data)) {
                $db->transStart();
                // Guardar los datos en la DB
                $new_author = $this->authorsModel->insert($data);

                if ($new_author) {
                    $image = $this->request->getFile('image');
                    $imagePath = uploadImage($image, 'assets/images/authors/');

                    if ($imagePath !== false) {
                        $data['image'] = $imagePath;
                        $this->authorsModel->update($new_author, ['img' => $data['image']]);

                        $db->transComplete();

                        return $this->redirectView(null, [['Autor agregado exitosamente', 'success']], null, );
                    } else {
                        $db->transRollback();
                        return $this->redirectView(null, [['Error en la subida de la imagen', 'danger']], $data, );
                    }
                } else {
                    $db->transRollback();
                    return $this->redirectView(null, [['No fue posible guardar al autor', 'warning']], $data, );
                }
            } else {
                return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data, );
            }
        } catch (\Exception $e) {
            // Si ocurre alguna excepción, revertir la transacción
            $db->transRollback();
            return $this->redirectView(null, [['No se pudo registrar al autor ' . $e->getMessage(), 'danger']], null);
        }
    }

    public function update($id)
    {
        $author = $this->authorsModel->find($id);

        if (!$author) {
            return $this->redirectView(null, [['Autor no encontrado', 'danger']]);
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $image = $this->request->getFile('image');

        $data = [
            'name' => trim($name),
            'description' => trim($description),
        ];

        $validation = \Config\Services::validation();
        $rules = [
            'name' => [
                'label' => 'Nombre del autor',
                'rules' => 'required|min_length[3]|is_unique[authors.name,id,' . $id . ']',
            ],
            'description' => [
                'label' => 'Descripción del autor',
                'rules' => 'permit_empty',
            ],
        ];

        // Validar solo si hay nueva imagen
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $rules['image'] = [
                'label' => 'Imagen',
                'rules' => 'is_image[image]|max_size[image,1024]',
            ];
        }

        $validation->setRules($rules);

        if (!$validation->run($data)) {
            return $this->redirectView($validation, [['Error en los datos enviados', 'warning']], $data);
        }

        $db = \Config\Database::connect();
        try {
            $db->transStart();

            // Si hay nueva imagen
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $newImagePath = uploadImage($image, 'assets/images/authors/');

                if ($newImagePath) {
                    // Borrar imagen anterior si existe
                    if (!empty($author['img']) && file_exists(FCPATH . $author['img'])) {
                        unlink(FCPATH . $author['img']);
                    }
                    $data['img'] = $newImagePath;
                } else {
                    $db->transRollback();
                    return $this->redirectView(null, [['Error al subir la nueva imagen', 'danger']], $data);
                }
            }

            $this->authorsModel->update($id, $data);
            $db->transComplete();

            return $this->redirectView(null, [['Autor actualizado exitosamente', 'success']]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->redirectView(null, [['Error al actualizar: ' . $e->getMessage(), 'danger']], $data);
        }
    }

    public function delete($id)
    {
        $author = $this->authorsModel->find($id);

        if (!$author) {
            return $this->redirectView(null, [['Autor no encontrado', 'danger']]);
        }

        $db = \Config\Database::connect();
        try {
            $db->transStart();

            // Borrar imagen si existe
            if (!empty($author['img']) && file_exists(FCPATH . $author['img'])) {
                unlink(FCPATH . $author['img']);
            }

            // Eliminar autor
            $this->authorsModel->delete($id);
            $db->transComplete();

            return $this->redirectView(null, [['Autor eliminado exitosamente', 'success']]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->redirectView(null, [['Error al eliminar: ' . $e->getMessage(), 'danger']]);
        }
    }

}