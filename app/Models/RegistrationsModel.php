<?php

namespace App\Models;

use CodeIgniter\Model;
use PaymentStatus;

class RegistrationsModel extends Model
{
    protected $table = 'registrations';
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

    public function allInscritos()
    {

        $query = $this->db->table('payments')
            ->select('
                payments.payment_cod AS codigo_pago,
                registrations.ic AS cedula,
                registrations.full_name_user AS nombres,
                events.event_name AS evento,
                categories.category_name AS categoria,
                registrations.address AS direccion,
                registrations.phone AS telefono,
                registrations.email AS email,
                payments.payment_status,
                payments.num_autorizacion,
            ')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->orderBy('payments.payment_cod')
            ->get()
            ->getResultArray();

        // Map the payment status to human-readable format
        foreach ($query as &$row) {
            $row['estado_pago'] = $this->getPaymentStatusText($row['payment_status']);
        }

        return $query;
    }

    public function getInscripcionesByCedula($cedula)
    {
        $query = $this->db->table('payments')
            ->select('
                payments.id AS id_pago,
                payments.payment_cod AS codigo_pago,
                registrations.ic AS cedula,
                registrations.full_name_user AS nombres,
                events.event_name AS evento,
                categories.category_name AS categoria,
                categories.cantidad_dinero AS precio,
                registrations.address AS direccion,
                registrations.phone AS telefono,
                registrations.email AS email,
                payments.payment_status,
                payments.num_autorizacion,
            ')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->where('registrations.ic', $cedula)
            ->orderBy('payments.payment_cod')
            ->get()
            ->getResultArray();

        // Map the payment status to human-readable format
        foreach ($query as &$row) {
            $row['estado_pago'] = getPaymentStatusText($row['payment_status']);
        }

        return $query;
    }

    public function getInscripcionesByCedulaYEstado($cedula, $estado)
    {
        $query = $this->db->table('payments')
            ->select('
            payments.id AS id_pago,
            payments.payment_cod AS codigo_pago,
            registrations.ic AS cedula,
            registrations.full_name_user AS nombres,
            events.event_name AS evento,
            categories.category_name AS categoria,
            categories.cantidad_dinero AS precio,
            registrations.address AS direccion,
            registrations.phone AS telefono,
            registrations.email AS email,
            payments.payment_status,
            payments.num_autorizacion,
        ')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->where('registrations.ic', $cedula)
            ->where('payments.payment_status', $estado)
            ->where('payments.deleted_at IS NULL')  // Filtrar registros no eliminados de payments
            ->where('registrations.deleted_at IS NULL')  // Filtrar registros no eliminados de registrations
            ->orderBy('payments.payment_cod')
            ->get()
            ->getResultArray();

        // Map the payment status to human-readable format
        foreach ($query as &$row) {
            $row['estado_pago'] = getPaymentStatusText($row['payment_status']);
        }

        return $query;
    }

    public function getAmountByPaymentCode($depositoCedula, $codigoPago)
    {
        // Consulta JOIN para obtener el registro, el pago correspondiente y el precio de la categoría
        $query = $this->select('
        categories.cantidad_dinero,
        payments.payment_status,
        payments.amount_pay,
        payments.id as payment_id
    ')
            ->join('payments', 'payments.id_register = registrations.id', 'left')
            ->join('categories', 'categories.id = registrations.cat_id', 'left')
            ->where('registrations.ic', $depositoCedula)
            ->where('payments.payment_cod', $codigoPago)
            ->first();

        if ($query) {
            // Obtener los depósitos relacionados con este pago directamente de la tabla deposits
            $deposits = $this->db->table('deposits')
                ->select('id, codigo_pago, monto_deposito, comprobante_pago, num_comprobante, date_deposito, status, deposit_cedula')
                ->where('payment_id', $query['payment_id'])
                ->get()
                ->getResultArray();

            if ($query['payment_status'] == PaymentStatus::Incompleto) {
                // Calcular la diferencia entre cantidad_dinero y amount_pay
                $diferencia = $query['cantidad_dinero'] - $query['amount_pay'];
                // Asegurarse de que la diferencia no sea negativa
                $montoAPagar = max(0, $diferencia);
                return [
                    'monto' => $montoAPagar,
                    'cancelado' => true,
                    'montoOriginal' => $query['cantidad_dinero'],
                    'montoPagado' => $query['amount_pay'],
                    'deposits' => $deposits
                ];
            } else {
                return [
                    'monto' => $query['cantidad_dinero'],
                    'cancelado' => false,
                    'deposits' => $deposits
                ];
            }
        } else {
            return null;
        }
    }

    public function getTotalRegistrations()
    {
        return $this->countAllResults();
    }
    public function getRegistrationsByPaymentStatus()
    {
        return $this->select('payments.payment_status, COUNT(*) as count')
            ->join('payments', 'payments.id_register = registrations.id')
            ->groupBy('payments.payment_status')
            ->get()
            ->getResultArray();
    }

    public function getInscriptionsByEventWithFilters($eventId, $status = null, $categoryId = null, $metodoPago = null, $fechaRegistro = null)
    {
        $builder = $this->select('
        registrations.*,
        categories.category_name,
        categories.cantidad_dinero,
        payments.payment_status,
        payments.num_autorizacion,
        IFNULL(payment_methods.method_name, "Sin método de pago") AS metodo_pago
    ')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('payments', 'payments.id_register = registrations.id', 'left')
            ->join('payment_methods', 'payments.payment_method_id = payment_methods.id', 'left')
            ->where('registrations.event_cod', $eventId);

        if ($status !== null && $status !== '') {
            $builder->where('payments.payment_status', $status);
        }

        if ($categoryId !== null && $categoryId !== '') {
            $builder->where('registrations.cat_id', $categoryId);
        }

        if ($metodoPago !== null && $metodoPago !== '') {
            $builder->where('payments.payment_method_id', $metodoPago);
        }

        if ($fechaRegistro !== null && $fechaRegistro !== '') {
            $builder->where('DATE(registrations.created_at)', $fechaRegistro); // Filtrar por fecha de registro
        }

        return $builder->findAll();
    }


    public function MountPayphone($cedula, $codigoPago)
    {

        // Consulta JOIN para obtener el registro, el pago correspondiente y el precio de la categoría
        $query = $this->select('
            registrations.event_name,
            categories.cantidad_dinero,
            payments.payment_status,
            payments.amount_pay,
            payments.id as payment_id
                ')
            ->join('payments', 'payments.id_register = registrations.id', 'left')
            ->join('categories', 'categories.id = registrations.cat_id', 'left')
            ->where('registrations.ic', $cedula)
            ->where('payments.payment_cod', $codigoPago)
            ->first();

        return $query;
    }

    public function updateRegistrations($cedula = null, $fullNameUser = null, $data = null)
    {
        try {
            // Crear el constructor de la consulta
            $builder = $this->db->table('registrations');

            // Si se pasa tanto la cédula como el nombre completo, usar OR
            if ($cedula !== null && $fullNameUser !== null) {
                $builder->groupStart()
                    ->where('ic', $cedula)
                    ->orWhere('full_name_user', $fullNameUser)
                    ->groupEnd();
            } elseif ($cedula !== null) {
                // Si solo se pasa la cédula
                $builder->where('ic', $cedula);
            } elseif ($fullNameUser !== null) {
                // Si solo se pasa el nombre completo
                $builder->where('full_name_user', $fullNameUser);
            }

            // Actualizar los registros con los nuevos datos
            return $builder->update($data);
        } catch (\Exception $e) {
            log_message('warning', "Error : " . $e->getMessage());
            return false;
        }
    }

    public function getAllInscriptionsWithPaymentMethodAndStatus()
    {
        $query = $this->db->table('payments')
            ->select('
            registrations.id,
            registrations.event_cod,
            registrations.cat_id,
            payments.payment_cod AS codigo_pago,
            registrations.ic AS ic,
            registrations.full_name_user AS full_name_user,
            events.event_name AS evento,
            categories.category_name AS categoria,
            registrations.address AS address,
            registrations.phone AS phone,
            registrations.email AS email,
            registrations.event_name AS event_name,
            registrations.monto_category AS monto_category,
            payments.payment_status AS estado_pago,
            payments.num_autorizacion,
            IFNULL(payment_methods.method_name, "Sin método de pago") AS metodo_pago
        ')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('payment_methods', 'payments.payment_method_id = payment_methods.id', 'left')
            ->where('registrations.deleted_at', null)
            ->orderBy('registrations.id', 'AS')
            // ->orderBy('payments.payment_cod')
            ->get()
            ->getResultArray();


        return $query;
    }

    public function getMisInscripcionesByCedulaYEstado($cedula, $estado)
    {
        // Obtener el ID de usuario desde la sesión (suponiendo que está almacenado en la sesión)
        // $userId = null;
        $builder = $this->db->table('payments')
            ->select('
            payments.id AS id_pago,
            payments.payment_cod AS codigo_pago,
            registrations.ic AS cedula,
            registrations.full_name_user AS nombres,
            events.event_name AS evento,
            categories.category_name AS categoria,
            categories.cantidad_dinero AS precio,
            registrations.address AS direccion,
            registrations.phone AS telefono,
            registrations.email AS email,
            payments.payment_status,
            payments.num_autorizacion
        ')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('inscripcion_pagos', 'inscripcion_pagos.pago_id = payments.id', 'left') // Relacion con inscripcion_pagos
            ->where('registrations.ic', $cedula)
            ->where('payments.payment_status', $estado);

        // // Si el estado es 2, aplicar filtro por sesión del usuario
        // if ($estado == 2 && $userId) {
        //     $builder->where('inscripcion_pagos.usuario_id', $userId); // Ajustamos para verificar el usuario de inscripcion_pagos
        // }

        $query = $builder->orderBy('payments.payment_cod')
            ->get()
            ->getResultArray();

        // Mapear el estado de pago a un formato legible
        foreach ($query as &$row) {
            $row['estado_pago'] = getPaymentStatusText($row['payment_status']);
        }

        return $query;
    }



}