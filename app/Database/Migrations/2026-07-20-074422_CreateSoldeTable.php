<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSoldeTable extends Migration
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
            'value' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'idUser' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true); // clé primaire
        $this->forge->addForeignKey('idUser', 'user', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('solde');
    }

    public function down()
    {
        $this->forge->dropTable('solde');
    }
}
