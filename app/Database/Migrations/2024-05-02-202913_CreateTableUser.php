<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableUser extends Migration
{
    public function up()
    {
        // Deshabilitar chequeo de claves foráneas temporalmente
        $this->db->disableForeignKeyChecks();

        $this->forge->addField([
            'id' =>[
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'rol_id' =>[
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'ic' =>[
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
            ],
            'first_name' =>[
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'last_name' =>[
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'phone_number' =>[
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'email' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'password' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['Masculino', 'Femenino', 'Otro'],
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
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
