<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConfigBaremeTable extends Migration
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
                'null'       => true, // null si le barème s'applique à TOUS les opérateurs
            ],
            'idTypeOperation' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'montant_min' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'montant_max' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'valeur_frais' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'pourcentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);

        // Clés étrangères vers vos tables existantes
        $this->forge->addForeignKey('idOperateur', 'operateur', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('idTypeOperation', 'typeOperation', 'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('config_bareme');
    }

    public function down()
    {
        $this->forge->dropTable('config_bareme');
    }
}