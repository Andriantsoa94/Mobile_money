<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFraisToConfig extends Migration
{
    public function up()
    {
        $this->forge->addColumn('config', [
            'frais' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0,
                'after'      => 'max',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('config', 'frais');
    }
}
