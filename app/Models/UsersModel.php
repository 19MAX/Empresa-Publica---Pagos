<?php

namespace App\Models;

use CodeIgniter\Model;
use RolesOptions;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    protected $allowedFields = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

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

    public function usersAll()
    {
        return $this->select('users.id, users.rol_id, users.ic, users.first_name, users.last_name, users.phone_number, users.email, users.address, authors.id as author_id, authors.name as author_name')
            ->join('user_author_permissions', 'user_author_permissions.user_id = users.id', 'left')
            ->join('authors', 'authors.id = user_author_permissions.author_id', 'left')
            ->where('users.rol_id !=', RolesOptions::AdminPrincipal)
            ->where('users.rol_id !=', RolesOptions::UsuarioPublico)
            ->where('users.rol_id !=', RolesOptions::UsuarioEventos)
            ->findAll();
    }


    public function usersEvents()
    {
        return $this->select('users.id, users.rol_id, users.ic, users.first_name, users.last_name, users.phone_number, users.email, users.address, authors.id as author_id, authors.name as author_name')
            ->join('user_author_permissions', 'user_author_permissions.user_id = users.id', 'left')
            ->join('authors', 'authors.id = user_author_permissions.author_id', 'left')
            ->where('users.rol_id !=', RolesOptions::AdminPrincipal)
            ->where('users.rol_id !=', RolesOptions::UsuarioPublico)
            ->where('users.rol_id !=', RolesOptions::AdministradorDePagos)
            ->where('users.rol_id !=', RolesOptions::AdministradorProservi)
            ->findAll();
    }

    public function detalleUsuario($id)
    {
        return $this->select('id,rol_id, ic, first_name, last_name, phone_number, email, address')
            ->where('id', $id)
            ->find();
    }

    public function getUserCollections($userId)
    {
        $builder = $this->select('
            users.ic AS ic,
            users.id AS id,
            users.first_name,
            users.last_name,
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
            payment_methods.method_name AS method_pago,
        ');
        $builder->join('inscripcion_pagos', 'users.id = inscripcion_pagos.usuario_id');
        $builder->join('payments', 'inscripcion_pagos.pago_id = payments.id');
        $builder->join('registrations', 'payments.id_register = registrations.id');
        $builder->join('payment_methods', 'payment_methods.id = payments.payment_method_id');
        $builder->where('users.id', $userId);
        $builder->where('payments.payment_status', 2); // Asumiendo que 2 es el estado de pago completado
        $builder->orderBy('payments.date_time_payment', 'DESC');

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getUserRecaudado()
    {
        // Consulta de pagos presenciales
        $presencial = $this->db->table('users')
            ->select([
                'users.ic AS ic',
                'users.id AS id',
                'users.first_name AS operador',
                'users.last_name AS last_operador',
                'payments.id AS payment_id',
                'payments.amount_pay',
                'payments.date_time_payment',
                'payments.payment_cod AS codigo',
                'payments.num_autorizacion AS num_autorizacion',
                'registrations.full_name_user AS participante_name',
                'registrations.ic AS participante_cedula',
                'registrations.address AS participante_direccion',
                'registrations.phone AS participante_telefono',
                'registrations.email AS participante_email',
                'registrations.event_name AS event_name',
                'registrations.monto_category AS precio',
                'payment_methods.method_name AS method_pago',
                'NULL AS fecha_transaction',
            ])
            ->join('inscripcion_pagos', 'users.id = inscripcion_pagos.usuario_id')
            ->join('payments', 'inscripcion_pagos.pago_id = payments.id')
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('payment_methods', 'payment_methods.id = payments.payment_method_id')
            ->where('payments.payment_status', 2)
            ->get()
            ->getResultArray();

        // Consulta de pagos online
        $online = $this->db->table('payments')
            ->select([
                'NULL AS ic',
                'NULL AS id',
                "'Payphone' AS operador",
                'NULL AS last_operador',
                'payments.id AS payment_id',
                'payments.amount_pay',
                'payments.date_time_payment',
                'payments.payment_cod AS codigo',
                'payments.num_autorizacion AS num_autorizacion',
                'registrations.full_name_user AS participante_name',
                'registrations.ic AS participante_cedula',
                'registrations.address AS participante_direccion',
                'registrations.phone AS participante_telefono',
                'registrations.email AS participante_email',
                'registrations.event_name AS event_name',
                'registrations.monto_category AS precio',
                "'Pago en linea' AS method_pago",
                'pago_linea.transaction_date AS fecha_transaction',
            ])
            ->join('registrations', 'payments.id_register = registrations.id')
            ->join('pago_linea', 'payments.id = pago_linea.payment_id')
            ->where('payments.payment_status', 2)
            ->get()
            ->getResultArray();

        // Combinar ambos resultados en un solo array
        $resultados = array_merge($presencial, $online);

        // Ordenar por la fecha de pago
        usort($resultados, function ($a, $b) {
            return strtotime($b['date_time_payment']) - strtotime($a['date_time_payment']);
        });

        // Retornar los resultados combinados
        return $resultados;
    }


}