<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConfigTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'min' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'max' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'gain' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true); // clé primaire
        $this->forge->createTable('config');
    }

    public function down()
    {
        $this->forge->dropTable('config');
    }
}