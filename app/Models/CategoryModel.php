<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
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


    // protected function setCreatedBy($data)
    // {
    //     $data['data']['created_by'] = session('id') ?? 1;
    //     return $data;
    // }

    // protected function setUpdatedBy($data)
    // {
    //     $data['data']['updated_by'] = session('id') ?? 1;
    //     return $data;
    // }

    // protected function setDeletedBy($data)
    // {
    //     $this->set(['deleted_by' => session('id') ?? 1])->update($data['id']);
    //     return $data;
    // }

    public function getCategoriesByEventId($eventId)
    {
        return $this->select('categories.id, categories.category_name, categories.cantidad_dinero')
            ->join('event_category', 'event_category.cat_id = categories.id', 'left')
            ->where('event_category.event_id', $eventId)
            ->get()
            ->getResultArray();
    }

}