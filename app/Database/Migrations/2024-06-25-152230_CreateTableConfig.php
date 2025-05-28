<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableConfig extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'key' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('config');

        // Insertar el monto de cobro
        $data = [
            'key' => 'additional_charge',
            'value' => '0.59',
            'description' => 'Monto adicional por cada pago',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('config')->insert($data);
    }

    public function down()
    {
        $this->forge->dropTable('config');
    }
}