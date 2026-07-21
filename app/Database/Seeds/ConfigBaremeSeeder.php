<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConfigBaremeSeeder extends Seeder
{
    public function run()
    {
        $maintenant = date('Y-m-d H:i:s');

        $tranches = [
            ['min' => 100,      'max' => 1000,    'frais' => 50,   'gain' => 0],
            ['min' => 1001,     'max' => 5000,    'frais' => 50,   'gain' => 0],
            ['min' => 5001,     'max' => 10000,   'frais' => 100,  'gain' => 0],
            ['min' => 10001,    'max' => 25000,   'frais' => 200,  'gain' => 0],
            ['min' => 25001,    'max' => 50000,   'frais' => 400,  'gain' => 0],
            ['min' => 50001,    'max' => 100000,  'frais' => 800,  'gain' => 0],
            ['min' => 100001,   'max' => 250000,  'frais' => 1500, 'gain' => 0],
            ['min' => 250001,   'max' => 500000,  'frais' => 1500, 'gain' => 0],
            ['min' => 500001,   'max' => 1000000, 'frais' => 2500, 'gain' => 0],
            ['min' => 1000001,  'max' => 2000000, 'frais' => 3000, 'gain' => 0],
        ];

        foreach ($tranches as &$tranche) {
            $tranche['created_at'] = $maintenant;
            $tranche['updated_at'] = $maintenant;
        }
        unset($tranche);

        $this->db->table('config')->insertBatch($tranches);
    }
}
