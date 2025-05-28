<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableEvents extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' =>[
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'event_name' =>[
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'short_description' =>[
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'event_date' =>[
                'type' => 'DATE',
                'null' => true,
            ],
            'modality' => [
                'type' => 'ENUM',
                'constraint' => ['Presencial', 'Virtual', 'Hibrida'],
            ],
            'event_duration' =>[
                'type' => 'INT',
                'null' => true,
            ],
            'address' =>[
                'type' => 'TEXT',
                'null' => true,
            ],
            'registrations_start_date' =>[
                'type' => 'DATE',
                'null' => true,
            ],
            'registrations_end_date' =>[
                'type' => 'DATE',
                'null' => true,
            ],
            'event_status' =>[
                'type' => 'ENUM',
                'constraint' => ['Activo', 'Desactivado'],
                'default' => 'Desactivado',
            ],
            'image' =>[
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'null' => true,
                'default' => 1,
            ],
            'updated_by' => [
                'type' => 'INT',
                'null' => true,
            ],
            'deleted_by' => [
                'type' => 'INT',
                'null' => true,
            ],
        ]);
        $this->forge->addkey('id',true);
        $this->forge->createTable('events');

    }

    public function down()
    {
        $this->forge->dropTable('events');
    }
}
