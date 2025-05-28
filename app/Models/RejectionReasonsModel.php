<?php

namespace App\Models;

use CodeIgniter\Model;

class RejectionReasonsModel extends Model
{
    protected $table = 'rejection_reasons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    protected $allowedFields = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Nuevo método para verificar depósitos incompletos o rechazados con send_email = 1 por payment_id
    public function getRejectedDepositsWithEmail($paymentId, $rejection_type = "General")
    {
        return $this->where('send_email', 1)
            ->where('payment_id', $paymentId)
            ->where('rejection_type', $rejection_type)
            ->findAll();
    }

}
