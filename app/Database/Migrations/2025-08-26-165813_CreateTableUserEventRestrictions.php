<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableUserEventRestrictions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'event_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'assigned_by' => [
                'type' => 'INT',
                'unsigned' => true,
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_by', 'users', 'id', 'CASCADE', 'CASCADE');

        // Índice único para evitar restricciones duplicadas
        $this->forge->addUniqueKey(['user_id', 'event_id']);

        $this->forge->createTable('user_event_restrictions');
    }

    public function down()
    {
        $this->forge->dropTable('user_event_restrictions');
    }
}
