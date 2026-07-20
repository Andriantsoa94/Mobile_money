<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsActifToTypeOperation extends Migration
{
    public function up()
    {
        $this->forge->addColumn('typeOperation', [
            'isActif' => [
                'type'       => 'BOOLEAN',
                'default'    => 1,
                'null'       => false,
                'after'      => 'isGain',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('typeOperation', 'isActif');
    }
}
