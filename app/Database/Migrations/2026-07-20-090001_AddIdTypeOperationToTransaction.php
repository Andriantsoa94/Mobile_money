<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdTypeOperationToTransaction extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction', [
            'idTypeOperation' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'idOperateur',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'idTypeOperation');
    }
}
