<?php

namespace App\Models;

use App\Services\PermissionService;
use CodeIgniter\Model;
use PaymentStatus;

class PaymentsModel extends Model
{
    protected $table = 'payments';
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

    public function verificarPagoAprobado($cedula, $codigoPago)
    {
        $query = $this->select('
                payments.id AS id_pago,
                payments.payment_cod AS codigo_pago,
                registrations.ic AS cedula,
                registrations.full_name_user AS nombres,
                payments.payment_status,
                payments.num_autorizacion
            ')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->where('registrations.ic', $cedula)
            ->where('payments.payment_cod', $codigoPago)
            ->where('payments.deleted_at IS NULL')
            ->where('registrations.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if ($query) {
            // Verificar si el estado del pago es "Aprobado" (asumiendo que PaymentStatus::Aprobado es 1)
            $query['esta_aprobado'] = ($query['payment_status'] == PaymentStatus::Completado);

            return $query;
        }

        return null;
    }

    public function getPaymentByNumAutorizacion($num_autorizacion)
    {
        $query = $this->db->table('payments')
            ->select('
            events.event_name,
            payments.num_autorizacion,
            payments.id_register,
            registrations.full_name_user AS user,
            registrations.ic AS user_ic,
            payments.date_time_payment AS fecha_emision,
            payments.precio_unitario AS precio_unitario,
            categories.cantidad_dinero AS cantidad_dinero,
            payments.sub_total AS sub_total,
            payments.sub_total_0 AS sub_total_0,
            payments.sub_total_15 AS sub_total_15,
            payments.iva AS iva,
            payments.total AS total,
            registrations.email AS email_user,
            registrations.phone AS user_tel,
            payments.valor_total AS valor_total,
            payments.send_email,
            payments.id,
            payments.amount_pay,
            users.first_name AS operador
        ')
            ->join('registrations', 'payments.id_register = registrations.id', 'left')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('inscripcion_pagos', 'inscripcion_pagos.pago_id = payments.id', 'left')
            ->join('users', 'users.id = inscripcion_pagos.usuario_id', 'left')
            ->where('payments.num_autorizacion', $num_autorizacion)
            ->where('payments.payment_status', 2)
            ->get()
            ->getRowArray();

        return $query;
    }


    public function numeroAutorizacion($num_autorizacion)
    {
        $query = $this->db->table('payments')
            ->select('
            payment_methods.id AS metodo_pago,
            registrations.event_name,
            payments.num_autorizacion,
            payments.id_register,
            registrations.full_name_user AS user,
            registrations.ic AS user_ic,
            registrations.monto_category AS cantidad_dinero,
            payments.date_time_payment AS fecha_emision,
            payments.precio_unitario,
            payments.sub_total,
            payments.sub_total_0,
            payments.sub_total_15,
            payments.iva,
            payments.total,
            registrations.email AS email_user,
            registrations.phone AS user_tel,
            payments.valor_total,
            payments.send_email,
            payments.id,
            payments.amount_pay,
            CONCAT(users.first_name, " ", users.last_name) AS operador
        ')
            ->join('registrations', 'payments.id_register = registrations.id', 'left')
            ->join('payment_methods', 'payments.payment_method_id = payment_methods.id', 'left')
            ->join('inscripcion_pagos', 'payments.id = inscripcion_pagos.pago_id', 'left')
            ->join('users', 'inscripcion_pagos.usuario_id = users.id', 'left')
            ->where('payments.num_autorizacion', $num_autorizacion)
            ->where('payments.payment_status', 2)
            ->get()
            ->getRowArray();

        return $query;
    }

    public function pagoData($id)
    {
        $query = $this->db->table('payments')
            ->select('
            registrations.monto_category AS precio,
            payments.send_email
        ')
            ->join('registrations', 'payments.id_register = registrations.id', 'left')
            ->where('payments.id', $id)
            ->get()
            ->getRowArray();

        return $query;
    }

    public function updatePaymentAndInsertInscripcionPago($id_pago, $datosPago, $id_usuario)
    {
        $this->db->transStart();

        // Intentar actualizar el registro en la tabla payments
        $update = $this->update($id_pago, $datosPago);

        // Si la actualización fue exitosa, insertar el registro en la tabla inscripcion_pagos
        if ($update) {
            $inscripcionPagosData = [
                'pago_id' => $id_pago,
                'fecha_hora' => date('Y-m-d H:i:s'), // Fecha y hora actual
                'usuario_id' => $id_usuario // Nombre del usuario que hizo el cobro
            ];

            // Insertar el nuevo registro en la tabla inscripcion_pagos
            $this->db->table('inscripcion_pagos')->insert($inscripcionPagosData);
        }

        $this->db->transComplete();

        // Verificar si la transacción fue exitosa
        if ($this->db->transStatus() === FALSE) {
            // Si algo salió mal, lanzar una excepción o manejar el error
            throw new \Exception('Error en la actualización del pago o inserción en inscripcion_pagos.');
        }

        return $update;
    }

    public function updatePaymentDeposito($id_pago, $datosPago, $id_usuario)
    {
        try {

            $this->db->transStart();

            // Intentar actualizar el registro en la tabla payments
            $update = $this->update($id_pago, $datosPago);

            // Si la actualización fue exitosa, continuar con las otras operaciones
            if ($update) {
                // Insertar el registro en la tabla inscripcion_pagos
                $inscripcionPagosData = [
                    'pago_id' => $id_pago,
                    'fecha_hora' => date('Y-m-d H:i:s'),
                    'usuario_id' => $id_usuario
                ];
                $this->db->table('inscripcion_pagos')->insert($inscripcionPagosData);

                // Actualizar solo el último registro en la tabla deposits relacionado con el payment_id
                $depositData = [
                    'status' => 'Aprobado',
                    'approved_by' => $id_usuario
                ];

                // Obtener el ID del último depósito relacionado con el payment_id
                $lastDeposit = $this->db->table('deposits')
                    ->select('id')
                    ->where('payment_id', $id_pago)
                    ->orderBy('created_at', 'DESC') // Asumiendo que tienes una columna 'created_at' para ordenar
                    ->get()
                    ->getRow();

                if ($lastDeposit) {
                    // Actualizar solo el último depósito
                    $this->db->table('deposits')
                        ->where('id', $lastDeposit->id)
                        ->update($depositData);
                }
            }

            $this->db->transComplete();

            return $update;
        } catch (\Exception $e) {

            $this->db->transRollback();

            throw new \Exception($e->getMessage());
        }
    }

    public function updatePaymentRechazo($id_pago, $estado, $id_usuario, $motivo_rechazo, $precio_pagado = null, $tipo_rechazo = 'General')
    {
        $this->db->transStart();

        try {
            // Actualizar sólo los campos necesarios en la tabla payments
            $update = $this->db->table('payments')
                ->where('id', $id_pago)
                ->update([
                    'payment_status' => $estado,
                    // 'amount_pay' => $precio_pagado // ya no actualizo el valor del pago
                ]);

            // Verificar si la actualización fue exitosa
            if (!$update) {
                throw new \Exception('Error al actualizar la tabla payments.');
            }

            // Obtener el ID del último depósito relacionado con el payment_id
            $lastDeposit = $this->db->table('deposits')
                ->select('id')
                ->where('payment_id', $id_pago)
                ->orderBy('created_at', 'DESC') // Asumiendo que tienes una columna 'created_at' para ordenar
                ->get()
                ->getRow();

            if ($lastDeposit) {
                // Determinar el estado del depósito basado en el tipo de rechazo
                $depositStatus = ($tipo_rechazo === 'General') ? 'Rechazado' : 'Incompleto';

                // Actualizar solo el último depósito con el estado correspondiente
                $updateDeposits = $this->db->table('deposits')
                    ->where('id', $lastDeposit->id)
                    ->update(['status' => $depositStatus]);

                // Verificar si la actualización de deposits fue exitosa
                if (!$updateDeposits) {
                    throw new \Exception('Error al actualizar el depósito.');
                }
            } else {
                throw new \Exception('No se encontró el último depósito para el payment_id dado.');
            }

            // Insertar el registro en la tabla rejection_reasons
            $rejectionReasonsData = [
                'payment_id' => $id_pago,
                'user_id' => $id_usuario,
                'reason' => $motivo_rechazo,
                'rejection_date' => date('Y-m-d H:i:s'),
                'rejection_type' => $tipo_rechazo,
                'send_email' => 1 // Siempre enviar el correo
            ];

            $insertRejectionReasons = $this->db->table('rejection_reasons')->insert($rejectionReasonsData);

            // Verificar si la inserción en rejection_reasons fue exitosa
            if (!$insertRejectionReasons) {
                throw new \Exception('Error al insertar en la tabla de rechazos.');
            }

            $this->db->transComplete();

            // Verificar si la transacción fue exitosa
            if ($this->db->transStatus() === FALSE) {
                throw new \Exception('Error en la transacción. Revisa los logs para más detalles.');
            }

            return ['update' => $update, 'should_send_email' => true];

        } catch (\Exception $e) {
            $this->db->transRollback();
            // Lanzar una excepción personalizada con el mensaje específico
            throw new \Exception('Error durante la operación: ' . $e->getMessage());
        }
    }



    public function getPaymentsWithDetailsAndDeposits()
    {
        return $this->select('
        payments.id as id_pago,
        payments.amount_pay,
        payments.payment_status,
        payments.payment_cod as codigo_pago,
        payments.payment_time_limit,
        payments.num_autorizacion,
        registrations.full_name_user,
        registrations.ic,
        registrations.address,
        registrations.phone,
        registrations.email,
        categories.category_name,
        categories.cantidad_dinero,
        events.event_name
    ')
            ->distinct()
            ->join('deposits', 'deposits.payment_id = payments.id', 'inner')
            ->join('registrations', 'payments.id_register = registrations.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->where('payments.payment_status', 4)
            ->findAll();
    }

    public function getPaymentsCompleted()
    {
        return $this->select('
        payments.id as id_pago,
        payments.amount_pay,
        payments.payment_status,
        payments.payment_cod as codigo_pago,
        payments.payment_time_limit,
        payments.num_autorizacion,
        registrations.full_name_user,
        registrations.ic,
        registrations.address,
        registrations.phone,
        registrations.email,
        categories.category_name,
        categories.cantidad_dinero,
        events.event_name
    ')
            ->distinct()
            ->join('deposits', 'deposits.payment_id = payments.id', 'inner')
            ->join('registrations', 'payments.id_register = registrations.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->where('payments.payment_status', 2)
            ->findAll();
    }

    public function getPaymentsRechazados()
    {
        return $this->select('
        payments.id as id_pago,
        payments.amount_pay,
        payments.payment_status,
        payments.payment_cod as codigo_pago,
        payments.payment_time_limit,
        payments.num_autorizacion,
        registrations.full_name_user,
        registrations.ic,
        registrations.address,
        registrations.phone,
        registrations.email,
        categories.category_name,
        categories.cantidad_dinero,
        events.event_name
    ')
            ->distinct()
            ->join('deposits', 'deposits.payment_id = payments.id', 'inner')
            ->join('registrations', 'payments.id_register = registrations.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->where('payments.payment_status', 6)
            ->findAll();
    }

    public function getPaymentsIncompletos()
    {
        return $this->select('
        payments.id as id_pago,
        payments.amount_pay,
        payments.payment_status,
        payments.payment_cod as codigo_pago,
        payments.payment_time_limit,
        payments.num_autorizacion,
        registrations.full_name_user,
        registrations.ic,
        registrations.address,
        registrations.phone,
        registrations.email,
        categories.category_name,
        categories.cantidad_dinero,
        events.event_name
    ')
            ->distinct()
            ->join('deposits', 'deposits.payment_id = payments.id', 'inner')
            ->join('registrations', 'payments.id_register = registrations.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->where('payments.payment_status', 5)
            ->findAll();
    }
    public function getPaymentUser($id)
    {
        return $this->select('
        payments.id ,
        payments.amount_pay,
        payments.payment_status,
        payments.payment_cod as codigo_pago,
        payments.payment_time_limit,
        payments.num_autorizacion,
        registrations.full_name_user as name,
        registrations.ic,
        registrations.address,
        registrations.phone,
        registrations.email,
        categories.category_name as categoria,
        categories.cantidad_dinero as monto_total,
        events.event_name as evento
    ')
            ->distinct()
            ->join('deposits', 'deposits.payment_id = payments.id', 'inner')
            ->join('registrations', 'payments.id_register = registrations.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->where('payments.id', $id)
            ->findAll();
    }
    public function isPaymentCompleted($paymentId)
    {
        $payment = $this->select('payment_status')
            ->where('id', $paymentId)
            ->first();

        if ($payment && $payment['payment_status'] == 2) {
            return true;
        }

        return false;
    }
    public function isPaymentStatusCompleted($paymentId)
    {
        $payment = $this->select('num_autorizacion')
            ->where('id', $paymentId)
            ->where('payment_status', 2)
            ->get()
            ->getRowArray();

        if ($payment) {
            return ['completed' => true, 'num_autorizacion' => $payment['num_autorizacion']];
        }

        return ['completed' => false, 'num_autorizacion' => null];
    }

    public function getDailyRevenue($date = null)
    {
        $date = $date ?: date('Y-m-d');
        return $this->select('SUM(amount_pay) as daily_revenue')
            ->where('DATE(date_time_payment)', $date)
            ->where('payment_status', 2) // Asumiendo que 2 es el estado de pago completado
            ->get()
            ->getRow()
            ->daily_revenue;
    }
    public function getDailyRevenueMy($date = null, $id = null)
    {
        $date = $date ?: date('Y-m-d');

        return $this->select('SUM(payments.amount_pay) as daily_revenue')
            ->join('inscripcion_pagos', 'inscripcion_pagos.pago_id = payments.id', 'inner')
            ->join('users', 'users.id = inscripcion_pagos.usuario_id', 'inner')
            ->where('DATE(payments.date_time_payment)', $date)
            ->where('users.id', $id)
            ->where('payments.payment_status', 2)
            ->get()
            ->getRow()
            ->daily_revenue;
    }


    public function getTotalRevenue()
    {
        return $this->select('SUM(amount_pay) as total_revenue')
            ->where('payment_status', 2)
            ->get()
            ->getRow()
            ->total_revenue;
    }

    public function getTotalRevenueByUser($userId)
    {
        return $this->select('SUM(payments.amount_pay) as total_revenue')
            ->join('inscripcion_pagos', 'inscripcion_pagos.pago_id = payments.id', 'inner')
            ->join('users', 'users.id = inscripcion_pagos.usuario_id', 'inner')
            ->where('users.id', $userId)
            ->where('payments.payment_status', 2)
            ->get()
            ->getRow()
            ->total_revenue;
    }


    public function getRevenueByUser($role = 2)
    {
        return $this->select('users.id, users.first_name, SUM(payments.amount_pay) as user_revenue')
            ->join('inscripcion_pagos', 'inscripcion_pagos.pago_id = payments.id')
            ->join('users', 'users.id = inscripcion_pagos.usuario_id')
            ->where('users.rol_id', $role)
            ->where('payments.payment_status', 2)
            ->groupBy('users.id')
            ->get()
            ->getResultArray();
    }

    public function datosUserInscrito($id_pago)
    {
        $query = $this->select('
                categories.cantidad_dinero,
                payments.payment_status,
                payments.amount_pay,
                payments.id as payment_id,
                payments.payment_cod as payment_cod,
                registrations.full_name_user as nombresInscrito,
                registrations.email as emailInscrito,
                registrations.event_name as nombreEvento
            ')
            ->join('registrations', 'payments.id_register = registrations.id', 'left')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->join('events', 'registrations.event_cod = events.id', 'left')
            ->where('payments.id', $id_pago)
            ->first();

        return $query;
    }

    public function getPaymentMethodStats()
    {
        return $this->select('payment_methods.method_name, COUNT(*) as count')
            ->join('payment_methods', 'payments.payment_method_id = payment_methods.id')
            ->where('payments.payment_status', 2) // Asumiendo que 2 es el estado de pago completado
            ->groupBy('payments.payment_method_id')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getPaymentDetailsByRegistrationId($registrationId)
    {
        $query = $this->db->table('payments')
            ->select('
            payments.payment_cod AS codigo_pago,
            registrations.event_name AS evento,
            registrations.monto_category AS precio,
            categories.category_name AS categoria,
            payments.payment_time_limit AS fecha_limite_pago
        ')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('categories', 'registrations.cat_id = categories.id', 'left')
            ->where('registrations.id', $registrationId)
            ->get()
            ->getFirstRow('array');

        return $query;
    }

    public function isPaymentCompletedByRegistrationId($registrationId)
    {
        $payment = $this->select('payment_status')
            ->where('id_register', $registrationId)
            ->first();

        if ($payment && $payment['payment_status'] == 2) {
            return true;
        }

        return false;
    }


    public function getRecaudacionesOnline()
    {
        $builder = $this->db->table('payments');

        $builder->select('
        payments.id AS payment_id,
        payments.amount_pay,
        payments.date_time_payment,
        payments.payment_cod AS codigo,
        payments.num_autorizacion AS num_autorizacion,
        registrations.full_name_user AS participante_name,
        registrations.ic AS participante_cedula,
        registrations.address AS participante_direccion,
        registrations.phone AS participante_telefono,
        registrations.email AS participante_email,
        registrations.event_name AS event_name,
        registrations.monto_category AS precio,
        pago_linea.transaction_date AS fecha_transaction,
        pago_linea.authorization_code
    ');

        // Unir con la tabla de registros
        $builder->join('registrations', 'payments.id_register = registrations.id');
        // Unir con la tabla pago_linea para obtener solo los pagos online
        $builder->join('pago_linea', 'payments.id = pago_linea.payment_id');
        // Ordenar por la fecha de pago
        $builder->orderBy('payments.date_time_payment', 'DESC');

        // Ejecutar la consulta
        $query = $builder->get();
        return $query->getResultArray();
    }


    public function getRecaudado()
    {
        // Obtener el valor de 'additional_charge' de la tabla config
        $configModel = new ConfigModel(); // Asumiendo que tienes un modelo para la tabla config
        $additionalCharge = $configModel->where('key', 'additional_charge')->first();
        $additionalChargeValue = floatval($additionalCharge['value']);

        // Construir la consulta para obtener los datos
        $builder = $this->select('
        payments.id AS payment_id,
        (payments.amount_pay - ' . $additionalChargeValue . ') AS amount_pay,
        payments.date_time_payment,
        payments.payment_cod AS codigo,
        payments.num_autorizacion AS num_autorizacion,
        registrations.full_name_user AS participante_name,
        registrations.ic AS participante_cedula,
        registrations.address AS participante_direccion,
        registrations.phone AS participante_telefono,
        registrations.email AS participante_email,
        registrations.event_name AS event_name,
        (registrations.monto_category - ' . $additionalChargeValue . ') AS precio,
        payment_methods.method_name AS method_pago,
    ');
        $builder->join('registrations', 'payments.id_register = registrations.id');
        $builder->join('payment_methods', 'payment_methods.id = payments.payment_method_id');
        // $builder->where('users.rol_id', 2);
        $builder->where('payments.payment_status', 2); // Asumiendo que 2 es el estado de pago completado
        $builder->orderBy('payments.date_time_payment', 'DESC');

        $query = $builder->get();
        return $query->getResultArray();
    }
    public function getRecaudadoWithPermissions($userId, $filters = [])
    {
        $permissionService = new PermissionService();
        $allowedEvents = $permissionService->getUserAllowedEvents($userId);

        if (empty($allowedEvents)) {
            return ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        }

        // Extraer IDs de eventos permitidos
        $allowedEventIds = array_column($allowedEvents, 'id');

        // Obtener el valor de 'additional_charge'
        $configModel = new ConfigModel();
        $additionalCharge = $configModel->where('key', 'additional_charge')->first();
        $additionalChargeValue = floatval($additionalCharge['value']);

        // Construir la consulta base
        $builder = $this->select('
            payments.id AS payment_id,
            (payments.amount_pay - ' . $additionalChargeValue . ') AS amount_pay,
            payments.date_time_payment,
            payments.payment_cod AS codigo,
            payments.num_autorizacion AS num_autorizacion,
            payments.payment_status,
            registrations.full_name_user AS participante_name,
            registrations.ic AS participante_cedula,
            registrations.address AS participante_direccion,
            registrations.phone AS participante_telefono,
            registrations.email AS participante_email,
            registrations.event_name AS event_name,
            categories.category_name AS nombre_categoria,
            events.id AS event_cod,
            authors.name AS author_name,
            (categories.cantidad_dinero - ' . $additionalChargeValue . ') AS precio,
            payment_methods.method_name AS method_pago
        ');

        $builder->join('registrations', 'payments.id_register = registrations.id');
        $builder->join('events', 'registrations.event_cod = events.id');
        $builder->join('categories', 'registrations.cat_id = categories.id', "left");
        $builder->join('authors', 'events.author_id = authors.id');
        $builder->join('payment_methods', 'payment_methods.id = payments.payment_method_id', 'left');

        // Filtrar solo por eventos permitidos
        $builder->whereIn('events.id', $allowedEventIds);

        // Filtrar por pagos Completados o Pendientes
        $builder->whereIn('payments.payment_status', [
            PaymentStatus::Completado,
            PaymentStatus::Pendiente
        ]);

        // Aplicar filtros adicionales
        if (!empty($filters['event_id'])) {
            $builder->where('events.id', $filters['event_id']);
        }

        if (!empty($filters['category_id'])) {
            $builder->where('registrations.cat_id', $filters['category_id']);
        }

        if (!empty($filters['payment_status'])) {
            $builder->where('payments.payment_status', $filters['payment_status']);
        }

        // Búsqueda general (para el search de DataTables)
        if (!empty($filters['search'])) {
            $searchValue = $filters['search'];
            $builder->groupStart()
                ->like('registrations.full_name_user', $searchValue)
                ->orLike('registrations.ic', $searchValue)
                ->orLike('registrations.email', $searchValue)
                ->orLike('events.event_name', $searchValue)
                ->orLike('payments.payment_cod', $searchValue)
                ->orLike('registrations.monto_category', $searchValue)
                ->groupEnd();
        }

        // Contar registros filtrados
        $recordsFiltered = $builder->countAllResults(false);

        // Ordenamiento
        if (!empty($filters['order_column']) && !empty($filters['order_dir'])) {
            $builder->orderBy($filters['order_column'], $filters['order_dir']);
        } else {
            $builder->orderBy('payments.date_time_payment', 'DESC');
        }

        // Paginación
        if (isset($filters['start']) && isset($filters['length'])) {
            $start = is_numeric($filters['start']) ? (int) $filters['start'] : 0;
            $length = is_numeric($filters['length']) ? (int) $filters['length'] : 10;
            $builder->limit($length, $start);
        }

        $data = $builder->get()->getResultArray();

        // Contar registros totales (sin filtros)
        $builderTotal = $this->select('payments.id')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('events', 'registrations.event_cod = events.id')
            ->whereIn('events.id', $allowedEventIds)
            ->whereIn('payments.payment_status', [
                PaymentStatus::Completado,
                PaymentStatus::Pendiente
            ]);

        $recordsTotal = $builderTotal->countAllResults();

        return [
            'data' => $data,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ];
    }

    // Método adicional para obtener categorías de un evento
    public function getCategoriesByEventId($eventId)
    {
        $configModel = new ConfigModel();
        $additionalCharge = $configModel->where('key', 'additional_charge')->first();
        $additionalChargeValue = floatval($additionalCharge['value']);

        $builder = $this->db->table('categories');
        $builder->select('
            categories.id,
            categories.category_name,
            (categories.cantidad_dinero - ' . $additionalChargeValue . ') AS cantidad_dinero
        ');
        $builder->join('event_category', 'event_category.cat_id = categories.id', 'left');
        $builder->where('event_category.event_id', $eventId);
        $query = $builder->get();

        return $query->getResultArray();
    }


    public function getRecaudadoWithEvent($userId)
    {
        $permissionService = new PermissionService();
        $allowedEvents = $permissionService->getUserEvents($userId);


        $id_event = $allowedEvents[0]['id'];

        // Obtener el valor de 'additional_charge'
        $configModel = new ConfigModel();
        $additionalCharge = $configModel->where('key', 'additional_charge')->first();
        $additionalChargeValue = floatval($additionalCharge['value']);

        // Construir la consulta
        $builder = $this->select('
            payments.id AS payment_id,
            (payments.amount_pay - ' . $additionalChargeValue . ') AS amount_pay,
            payments.date_time_payment,
            payments.payment_cod AS codigo,
            payments.num_autorizacion AS num_autorizacion,
            registrations.full_name_user AS participante_name,
            registrations.ic AS participante_cedula,
            registrations.address AS participante_direccion,
            registrations.phone AS participante_telefono,
            registrations.email AS participante_email,
            registrations.event_name AS event_name,
            events.id AS event_cod,
            authors.name AS author_name,
            (registrations.monto_category - ' . $additionalChargeValue . ') AS precio,
            payment_methods.method_name AS method_pago
        ');

        $builder->join('registrations', 'payments.id_register = registrations.id');
        $builder->join('events', 'registrations.event_cod = events.id');
        $builder->join('authors', 'events.author_id = authors.id');
        $builder->join('payment_methods', 'payment_methods.id = payments.payment_method_id');

        // Filtrar solo por eventos permitidos
        $builder->where('events.id', $id_event);
        $builder->where('payments.payment_status', 2);
        $builder->orderBy('payments.date_time_payment', 'DESC');

        return $builder->get()->getResultArray();
    }

}
