<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableInscripcionPagos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pago_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'usuario_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'fecha_hora' => [
                'type' => 'DATETIME',
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
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pago_id', 'payments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('usuario_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inscripcion_pagos');
    }

    public function down()
    {
        $this->forge->dropTable('inscripcion_pagos');
    }
}
