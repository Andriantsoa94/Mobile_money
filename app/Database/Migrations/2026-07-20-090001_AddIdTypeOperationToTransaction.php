<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdTypeOperationToTransaction extends Migration
{
    public function up()
    {
        // Ajout des backticks pour éviter le bug de mot réservé sous SQLite
        $this->forge->addColumn('`transaction`', [
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
        // C'est ici que SQLite plantait : les backticks corrigent le "near transaction: syntax error"
        $this->forge->dropColumn('`transaction`', 'idTypeOperation');
    }
}