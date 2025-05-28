<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTablePaymentMethod extends Migration
{
    public function up()
    {
        // Deshabilitar chequeo de claves foráneas temporalmente
        $this->db->disableForeignKeyChecks();

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'method_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'activo' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('payment_methods');

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


        // Habilitar chequeo de claves foráneas después de la creación de la tabla
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {

        $this->forge->dropTable('payment_methods');
    }
}
