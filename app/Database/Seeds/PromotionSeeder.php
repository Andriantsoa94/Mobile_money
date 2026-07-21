<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('promotion')->insertBatch([
            ['pourcentage' => 50.0],
        ]);
    }
}
