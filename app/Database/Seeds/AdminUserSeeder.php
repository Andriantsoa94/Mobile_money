<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');


        $this->db->table('role')->insertBatch([
            ['type' => 'admin'],
            ['type' => 'client'],
        ]);

        $this->db->table('user')->insertBatch([
            ['nom' => 'Jean' , 'idrole' => 1 , 'CIN' => 1234567890],
        ]);

        $this->db->table('numero')->insertBatch([
            ['numero' => 0330000000 , 'iduser' => 1],
        ]);
    }
}
