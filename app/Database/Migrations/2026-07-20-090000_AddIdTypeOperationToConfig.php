<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdTypeOperationToConfig extends Migration
{
    public function up()
    {
        $this->forge->addColumn('config', [
            'idTypeOperation' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'gain',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('config', 'idTypeOperation');
    }
}
