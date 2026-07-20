<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFraisToTransaction extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction', [
            'frais' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0,
                'after'      => 'valeur',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'frais');
    }
}
