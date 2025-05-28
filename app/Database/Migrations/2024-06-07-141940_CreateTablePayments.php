<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTablePayments extends Migration
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
            'id_register' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'amount_pay' =>[
                'type' => 'NUMERIC',
                'constraint' => '10,2',
                'null' => true,
            ],
            'payment_status' => [
                'type' => 'INT',
                'comment' => 'Estado del pago (Pendiente,Completado,Fallido,EnProceso,Cancelado)',
                'null' => true,
            ],
            'date_time_payment' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'payment_cod' => [
                'type' => 'INT',
                'null' => false,
            ],
            'address_payment' => [
                'type' => 'INT',
                'null' => true,
            ],
            'payment_time_limit' => [
                'type' => 'DATE',
                'null' => false
            ],
            'payment_method_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'num_autorizacion' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'precio_unitario' => [
                'type' => 'DOUBLE',
                'constraint' => '10,2',
                'null' => true,
            ],
            'valor_total' => [
                'type' => 'DOUBLE',
                'constraint' => '10,2',
                'null' => true,
            ],
            'total' => [
                'type' => 'DOUBLE',
                'constraint' => '10,2',
                'null' => true,
            ],
            'sub_total' => [
                'type' => 'DOUBLE',
                'constraint' => '10,2',
                'null' => true,
            ],
            'sub_total_0' => [
                'type' => 'DOUBLE',
                'constraint' => '10,2',
                'null' => true,
            ],
            'sub_total_15' => [
                'type' => 'DOUBLE',
                'constraint' => '10,2',
                'null' => true,
            ],
            'iva' => [
                'type' => 'DOUBLE',
                'constraint' => '10,2',
                'null' => true,
            ],
            'send_email' => [
                'type' => 'TINYINT',
                'constraint' => 1,
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
        $this->forge->addkey('id', true);
        // Agregar la llave foránea para la columna 'id_register' que referencia a la columna 'id' de la tabla registrations
        $this->forge->addForeignKey('id_register', 'registrations', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('payment_method_id', 'payment_methods', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('payments');
    }

    public function down()
    {

        $this->forge->dropTable('payments');
    }
}
