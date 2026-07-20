<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommissionToTransaction extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction', [
            'commission' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0,
                'after'      => 'gain',
            ],
            'idAutreOperateur' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'commission',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'commission');
        $this->forge->dropColumn('transaction', 'idAutreOperateur');
    }
}
