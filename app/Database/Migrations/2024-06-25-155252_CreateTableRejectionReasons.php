<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableRejectionReasons extends Migration
{
    public function up()
    {
        // Deshabilitar chequeo de claves foráneas temporalmente
        $this->db->disableForeignKeyChecks();

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'payment_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'rejection_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'rejection_type' => [
                'type' => 'ENUM',
                'constraint' => ['General', 'Incompleto'],
                'default' => 'General',
                'null' => false,
            ],
            'send_email' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('payment_id', 'payments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rejection_reasons');
    }

    public function down()
    {
        $this->forge->dropTable('rejection_reasons');
    }
}