<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $data=[
            [
            'category_name' => 'Estudiante',
            'short_description' => 'Categoría para estudiantes',
            'cantidad_dinero' => 10.00,
            ]
        ];
        $this->db->table('categories')->insertBatch($data);
    }
}