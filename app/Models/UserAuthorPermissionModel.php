<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAuthorPermissionModel extends Model
{
    protected $table = 'user_author_permissions';
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
     * Verificar si un usuario tiene permisos sobre un autor
     */
    public function hasAuthorPermission($userId, $authorId)
    {
        return $this->where([
            'user_id' => $userId,
            'author_id' => $authorId
        ])->first();
    }

    /**
     * Obtener autores asignados a un usuario
     */
    public function getUserAuthors($userId)
    {
        return $this->select('authors.*, user_author_permissions.permission_level')
            ->join('authors', 'authors.id = user_author_permissions.author_id')
            ->where('user_author_permissions.user_id', $userId)
            ->findAll();
    }

    /**
     * Verificar si un autor ya está asignado a algún usuario
     */
    public function isAuthorAssigned($authorId)
    {
        return $this->where('author_id', $authorId)->first();
    }

    /**
     * Asignar autor a usuario
     */
    public function assignAuthorToUser($userId, $authorId, $permissionLevel, $assignedBy)
    {
        return $this->insert([
            'user_id' => $userId,
            'author_id' => $authorId,
            'permission_level' => $permissionLevel,
            'assigned_by' => $assignedBy
        ]);
    }

    /**
     * Obtener usuarios que pueden gestionar un autor específico
     */
    public function getAuthorManagers($authorId, $permissionLevel = 'full')
    {
        return $this->select('users.*, user_author_permissions.permission_level')
            ->join('users', 'users.id = user_author_permissions.user_id')
            ->where('user_author_permissions.author_id', $authorId)
            ->where('user_author_permissions.permission_level', $permissionLevel)
            ->findAll();
    }


    /**
     * Eliminar todos los permisos de autor de un usuario
     */
    public function removeUserAuthorPermissions($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }

    public function removeAuthorFromUser($userId, $authorId)
    {
        return $this->where('user_id', $userId)
            ->where('author_id', $authorId)
            ->delete();
    }

}
