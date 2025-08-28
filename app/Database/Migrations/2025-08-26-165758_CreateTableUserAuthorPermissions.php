<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableUserAuthorPermissions extends Migration
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
            'author_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'permission_level' => [
                'type' => 'ENUM',
                'constraint' => ['full', 'restricted'],
                'default' => 'restricted',
                'comment' => 'full: ve todos los eventos del autor, restricted: solo eventos específicos'
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
        $this->forge->addForeignKey('author_id', 'authors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_by', 'users', 'id', 'CASCADE', 'CASCADE');

        // Índice único para evitar permisos duplicados
        $this->forge->addUniqueKey(['user_id', 'author_id']);

        $this->forge->createTable('user_author_permissions');
    }

    public function down()
    {
        $this->forge->dropTable('user_author_permissions');
    }
}
