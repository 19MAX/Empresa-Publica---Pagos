<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'method_name' => 'Deposito',
                'description' => 'Pago por deposito'
            ],
            [
                'method_name' => 'Pago físico',
                'description' => 'Pago en puntos de pago'
            ],
            [
                'method_name' => 'Pago en linea',
                'description' => 'Pago en el sistema de inscripción'
            ]
        ];
        $this->db->table('payment_methods')->insertBatch($data);
    }
}
