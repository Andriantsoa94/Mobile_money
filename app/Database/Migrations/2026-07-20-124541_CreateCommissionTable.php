<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommissionTable extends Migration
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
            'idOperateur' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'commission' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true); // clé primaire
        $this->forge->addForeignKey('idOperateur', 'operateur', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('comission');
    }

    public function down()
    {
        $this->forge->dropTable('comission');
    }
}
