<?php

namespace App\Models;

use CodeIgniter\Model;

class DepositsModel extends Model
{
    protected $table = 'deposits';
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

    public function existsPendingDeposit($codigoPago)
    {
        $existingDeposito = $this->where('codigo_pago', $codigoPago)
            ->where('status', 'Pendiente')
            ->first();
        return $existingDeposito !== null;
    }

    public function existsComprobanteAndDate($num_comprobante, $date_deposito)
    {
        return $this->where('num_comprobante', $num_comprobante)
            ->where('date_deposito', $date_deposito)
            ->first();
    }

    public function getPendingDepositsWithDetails()
    {
        return $this->select('
        deposits.*,
        registrations.full_name_user,
        registrations.ic,
        registrations.address,
        registrations.phone,
        registrations.email,
        events.event_name,
        events.event_date,
        categories.category_name,
        payments.payment_status,
        payments.num_autorizacion,
        payments.id as id_pago,
    ')
            ->join('payments', 'deposits.payment_id = payments.id')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('events', 'registrations.event_cod = events.id')
            ->join('categories', 'registrations.cat_id = categories.id')
            ->where('deposits.status', 'Pendiente')
            ->where('payments.payment_status', 4)
            ->findAll();
    }

    public function getDepositsByPaymentId($id_pago)
    {
        return $this->select('id,codigo_pago, monto_deposito, comprobante_pago, num_comprobante, date_deposito, status, deposit_cedula')
            ->where('payment_id', $id_pago)
            ->findAll();
    }

    public function verifyDepositAmounts($paymentId)
    {
        $depositos = $this->select('SUM(monto_deposito) as totalDepositos')
            ->where('payment_id', $paymentId)
            ->first();

        if ($depositos) {
            $totalDepositos = $depositos['totalDepositos'];

            $category = $this->db->table('categories')
                ->select('categories.cantidad_dinero')
                ->join('registrations', 'registrations.cat_id = categories.id')
                ->join('payments', 'payments.id_register = registrations.id')
                ->where('payments.id', $paymentId)
                ->get()
                ->getRowArray();

            if ($category) {
                $montoCategoria = $category['cantidad_dinero'];
                return $totalDepositos == $montoCategoria;
            }
        }

        return false;
    }

    public function isPaymentCompletedWithAuthorization($paymentId)
    {
        // Verificar si el monto total de los depósitos coincide con el monto de la categoría
        $depositos = $this->select('SUM(monto_deposito) as totalDepositos')
            ->where('payment_id', $paymentId)
            ->first();

        if ($depositos) {
            $totalDepositos = $depositos['totalDepositos'];

            $category = $this->db->table('categories')
                ->select('categories.cantidad_dinero, payments.num_autorizacion')
                ->join('registrations', 'registrations.cat_id = categories.id')
                ->join('payments', 'payments.id_register = registrations.id')
                ->where('payments.id', $paymentId)
                ->get()
                ->getRowArray();

            if ($category) {
                $montoCategoria = $category['cantidad_dinero'];
                if ($totalDepositos >= $montoCategoria) {
                    return ['completed' => true, 'num_autorizacion' => $category['num_autorizacion']];
                }
            }
        }

        return ['completed' => false, 'num_autorizacion' => null];
    }

    public function getAllDepositsWithDetails()
    {
        return $this->select('
        deposits.*,
        registrations.full_name_user,
        registrations.ic,
        registrations.address,
        registrations.phone,
        registrations.email,
        registrations.monto_category,
        events.event_name,
        events.event_date,
        categories.category_name,
        payments.payment_status,
        payments.num_autorizacion,
        payments.id as id_pago
    ')
            ->join('payments', 'deposits.payment_id = payments.id')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('events', 'registrations.event_cod = events.id')
            ->join('categories', 'registrations.cat_id = categories.id')
            ->findAll();
    }

}
