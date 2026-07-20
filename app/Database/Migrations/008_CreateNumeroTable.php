<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNumeroTable extends Migration
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
            'numero' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'iduser' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true); // clé primaire
        $this->forge->addForeignKey('iduser', 'user', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('numero');
    }

    public function down()
    {
        $this->forge->dropTable('numero');
    }
}