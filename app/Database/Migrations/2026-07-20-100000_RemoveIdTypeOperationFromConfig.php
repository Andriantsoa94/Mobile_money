<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveIdTypeOperationFromConfig extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('config', 'idTypeOperation');
    }

    public function down()
    {
        $this->forge->addColumn('config', [
            'idTypeOperation' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
    }
}
