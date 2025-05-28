<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    public function run()
    {

        $data = [
            [
                'event_id' => 1,
                'cat_id' => 1,
            ]
        ];
        $this->db->table('event_category')->insertBatch($data);
    }
}
