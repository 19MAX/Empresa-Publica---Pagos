<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventsSeeder extends Seeder
{
    public function run()
    {
        $data=[
            [
            'event_name' => 'Congreso internacional de ciencia, tecnología, innovación y emprendimiento (VII CTIE)',
            'short_description' => 'Evento internacional de ciencia y tecnología que realiza la UEB',
            'event_date' => '2024-06-30',
            'modality' => '1',
            'event_time' => '',
            'address' => 'Universidad Estatal de Bolívar (UEB)',
            'registrations_start_date' => '2024-06-14',
            'registrations_end_date' => '2024-06-20',
            'event_status' => 'Activo',
            'image' => 'assets/images/events/1718289673_8e477fa52c0bf8181751.jpg',
            ]
        ];
        $this->db->table('events')->insertBatch($data);
    }
}