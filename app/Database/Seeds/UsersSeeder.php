<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data=[
            //Admin
            [
            'rol_id' => '1',
            'ic' => '0000000000',
            'first_name' => 'Admin',
            'last_name' => 'B',
            'phone_number' => '0989026071',
            'email' => 'info@softecsa.com',
            'password' => password_hash('Softec2024Apps', PASSWORD_DEFAULT),
            'address'=>'Guaranda',
            ],
            //Admin pagos
            // [
            // 'rol_id' => '2',
            // 'ic' => '0245638562',
            // 'first_name' => 'Carlos Pepe',
            // 'last_name' => 'Perez Sanchez',
            // 'phone_number' => '0987857289',
            // 'email' => 'perez@exaple.com',
            // 'password' => password_hash('password', PASSWORD_DEFAULT),
            // 'address'=>'Guanujo'
            // ]
        ];
        $this->db->table('users')->insertBatch($data);
    }
}
