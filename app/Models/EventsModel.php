<?php

namespace App\Models;

use CodeIgniter\Model;

class EventsModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = false;
    protected $allowedFields = [];


    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = false;
    protected $beforeInsert = ['setCreatedBy'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['setUpdatedBy'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = ['setDeletedBy'];
    protected $afterDelete = [];

    public function getAuthorsWithEventCount()
    {
        return $this->db->query("
        SELECT
            authors.id,
            authors.name,
            authors.img,
            COUNT(events.id) as event_count
        FROM authors
        LEFT JOIN events ON events.author_id = authors.id
        WHERE events.event_status = 'Activo'
        GROUP BY authors.id, authors.name, authors.img
        HAVING event_count > 0
        ORDER BY authors.name ASC
    ")->getResultArray();
    }

    public function getEventsByAuthor($authorId)
    {
        return $this->select('
        events.*,
        GROUP_CONCAT(categories.category_name) AS categories,
        authors.name AS author_name,
        authors.img AS author_image
    ')
            ->join('event_category', 'event_category.event_id = events.id', 'left')
            ->join('categories', 'categories.id = event_category.cat_id', 'left')
            ->join('authors', 'authors.id = events.author_id', 'left')
            ->where('events.event_status', 'Activo')
            ->where('events.author_id', $authorId)
            ->groupBy('events.id')
            ->findAll();
    }

    public function getAllEventsWithCategories()
    {
        return $this->select('events.*, GROUP_CONCAT(DISTINCT categories.category_name) AS categories, GROUP_CONCAT(DISTINCT categories.cantidad_dinero) AS prices')
            ->join('event_category', 'event_category.event_id = events.id', 'left')
            ->join('categories', 'categories.id = event_category.cat_id', 'left')
            ->groupBy('events.id')
            ->findAll();
    }

    public function getActiveAndCurrentEvents()
    {
        return $this->select('
                events.*,
                GROUP_CONCAT(categories.category_name) AS categories,
                authors.name AS author_name,
                authors.img AS author_image
            ')
            ->join('event_category', 'event_category.event_id = events.id', 'left')
            ->join('categories', 'categories.id = event_category.cat_id', 'left')
            ->join('authors', 'authors.id = events.author_id', 'left')
            ->where('events.event_status', 'Activo')
            ->groupBy('events.id')
            ->findAll();
    }

    public function getEventById($id)
    {
        return $this->select('events.id, events.event_name, events.short_description, events.event_date, events.modality, events.address, events.registrations_start_date, events.registrations_end_date, events.event_status, events.image, GROUP_CONCAT(categories.id) AS category_ids, GROUP_CONCAT(categories.category_name) AS categories, GROUP_CONCAT(categories.cantidad_dinero) AS cantidad_dinero')
            ->join('event_category', 'event_category.event_id = events.id', 'left')
            ->join('categories', 'categories.id = event_category.cat_id', 'left')
            ->where('events.id', $id)
            ->groupBy('events.id')
            ->first();
    }

    public function getEventNameAndCategories($eventId, $categoryId)
    {
        return $this->select('events.id, events.event_name, events.event_date, categories.category_name, categories.cantidad_dinero AS cantidad_dinero')
            ->join('event_category', 'event_category.event_id = events.id', 'left')
            ->join('categories', 'categories.id = event_category.cat_id', 'left')
            ->where('events.id', $eventId)
            ->where('categories.id', $categoryId)
            ->first();
    }

    public function getEventDetailsById($id)
    {
        return $this->select('events.id, events.author_id, events.event_name, events.short_description, events.event_date, events.modality, events.event_duration, events.address, events.registrations_start_date, events.registrations_end_date, events.event_status, events.image, GROUP_CONCAT(categories.id) AS category_ids, GROUP_CONCAT(categories.category_name) AS category_names')
            ->join('event_category', 'event_category.event_id = events.id', 'left')
            ->join('categories', 'categories.id = event_category.cat_id', 'left')
            ->where('events.id', $id)
            ->groupBy('events.id')
            ->first();
    }

    public function updateEvent($id, $data, $categories, $newImagePath = null, $oldImagePath = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        try {
            // Actualizar los datos del evento
            $this->update($id, $data);

            // Eliminar las categorías actuales
            $eventCategoryModel = new EventCategoryModel();
            $eventCategoryModel->where('event_id', $id)->delete();

            // Guardar las nuevas categorías
            foreach ($categories as $category_id) {
                $eventCategoryModel->insert([
                    'event_id' => $id,
                    'cat_id' => $category_id,
                ]);
            }

            // Manejar la actualización de la imagen si se proporciona
            if ($newImagePath !== null) {
                $this->update($id, ['image' => $newImagePath]);
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción de la base de datos');
            }

            // Eliminar la imagen anterior solo si la transacción se completó con éxito
            if ($oldImagePath && file_exists(ROOTPATH . 'public/' . $oldImagePath) && $newImagePath !== null) {
                if (!unlink(ROOTPATH . 'public/' . $oldImagePath)) {
                    log_message('error', 'No se pudo eliminar la imagen anterior: ' . $oldImagePath);
                    // Aquí podrías considerar lanzar una excepción si la eliminación de la imagen es crítica
                }
            }

            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error al actualizar el evento: ' . $e->getMessage());

            // Si hubo un error, eliminar la nueva imagen si se subió
            if ($newImagePath && file_exists(ROOTPATH . 'public/' . $newImagePath)) {
                unlink(ROOTPATH . 'public/' . $newImagePath);
            }

            return false;
        }
    }

    public function deleteEventWithRelations($eventId)
    {
        $db = \Config\Database::connect();

        // Iniciar una transacción
        $db->transStart();

        try {
            // Eliminar depósitos relacionados con los pagos del evento
            $db->table('deposits')
                ->whereIn('payment_id', function ($builder) use ($eventId) {
                    return $builder->select('payments.id')
                        ->from('payments')
                        ->join('registrations', 'registrations.id = payments.id_register')
                        ->where('registrations.event_cod', $eventId);
                })
                ->delete();

            // Eliminar pagos en línea relacionados con los pagos del evento
            $db->table('pago_linea')
                ->whereIn('payment_id', function ($builder) use ($eventId) {
                    return $builder->select('payments.id')
                        ->from('payments')
                        ->join('registrations', 'registrations.id = payments.id_register')
                        ->where('registrations.event_cod', $eventId);
                })
                ->delete();

            // Eliminar registros de inscripcion_pagos relacionados con los pagos del evento
            $db->table('inscripcion_pagos')
                ->whereIn('pago_id', function ($builder) use ($eventId) {
                    return $builder->select('payments.id')
                        ->from('payments')
                        ->join('registrations', 'registrations.id = payments.id_register')
                        ->where('registrations.event_cod', $eventId);
                })
                ->delete();

            // Eliminar pagos relacionados con las inscripciones del evento
            $db->table('payments')
                ->whereIn('id_register', function ($builder) use ($eventId) {
                    return $builder->select('registrations.id')
                        ->from('registrations')
                        ->where('registrations.event_cod', $eventId);
                })
                ->delete();

            // Eliminar inscripciones relacionadas con el evento
            $db->table('registrations')
                ->where('event_cod', $eventId)
                ->delete();

            // Finalmente, eliminar el evento
            $db->table('events')
                ->where('id', $eventId)
                ->delete();

            // Completar la transacción
            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                // Si la transacción falla, se revierte
                throw new \Exception('Error eliminando el evento y sus registros relacionados');
            }

            return true; // Todo fue exitoso
        } catch (\Exception $e) {
            // Manejar errores
            log_message('error', $e->getMessage());
            $db->transRollback(); // Hacer rollback en caso de error
            return false;
        }
    }

}