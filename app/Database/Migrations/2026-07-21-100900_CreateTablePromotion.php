<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTablePromotion extends Migration
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
            'pourcentage' => [
                'type'          => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('promotion');
    }

    public function down()
    {
        $this->forge->dropTable('config_bareme');
    }
}