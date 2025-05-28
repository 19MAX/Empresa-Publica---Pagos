<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTablePagoLinea extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'payment_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'amount' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'client_transaction_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'status_code' => [
                'type' => 'INT',
                'null' => false,
            ],
            'transaction_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
            'authorization_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'message' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'message_code' => [
                'type' => 'INT',
                'null' => true,
            ],
            'transaction_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'document' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => false,
            ],
            'transaction_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'card_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'card_brand' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('payment_id');
        $this->forge->addForeignKey('payment_id', 'payments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pago_linea');
    }

    public function down()
    {
        $this->forge->dropTable('pago_linea');
    }
}
