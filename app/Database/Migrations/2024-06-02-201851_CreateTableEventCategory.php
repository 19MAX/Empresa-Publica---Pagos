<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableEventCategory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'event_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false
            ],
            'cat_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cat_id', 'categories', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('event_category');
    }

    public function down()
    {
        $this->forge->dropTable('event_category');
    }
}
