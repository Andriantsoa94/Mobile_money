<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPourcentageToComission extends Migration
{
    public function up()
    {
        $champs = $this->db->getFieldNames('comission');

        if (! in_array('pourcentage', $champs, true)) {
            $this->db->query('ALTER TABLE comission ADD COLUMN pourcentage DECIMAL(5,2) NOT NULL DEFAULT 0');
        }
    }

    public function down()
    {
        $champs = $this->db->getFieldNames('comission');

        if (in_array('pourcentage', $champs, true)) {
            $this->forge->dropColumn('comission', 'pourcentage');
        }
    }
}
