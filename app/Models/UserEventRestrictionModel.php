<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEventRestrictionModel extends Model
{
    protected $table = 'user_event_restrictions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    protected $allowedFields = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
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
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Obtener eventos específicos asignados a un usuario
     */
    public function getUserRestrictedEvents($userId)
    {
        return $this->select('events.*')
            ->join('events', 'events.id = user_event_restrictions.event_id')
            ->where('user_event_restrictions.user_id', $userId)
            ->where('events.event_status', 'Activo')
            ->findAll();
    }

    /**
     * Asignar evento específico a usuario
     */
    public function assignEventToUser($userId, $eventId, $assignedBy)
    {
        return $this->insert([
            'user_id' => $userId,
            'event_id' => $eventId,
            'assigned_by' => $assignedBy
        ]);
    }

    /**
     * Verificar si un usuario tiene acceso a un evento específico
     */
    public function hasEventAccess($userId, $eventId)
    {
        return $this->where([
            'user_id' => $userId,
            'event_id' => $eventId
        ])->first();
    }
}
