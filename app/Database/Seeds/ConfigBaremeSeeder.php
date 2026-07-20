<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConfigBaremeSeeder extends Seeder
{
    public function run()
    {
        $maintenant = date('Y-m-d H:i:s');

        $tranches = [
            ['min' => 100,      'max' => 1000,    'gain' => 50],
            ['min' => 1001,     'max' => 5000,    'gain' => 50],
            ['min' => 5001,     'max' => 10000,   'gain' => 100],
            ['min' => 10001,    'max' => 25000,   'gain' => 200],
            ['min' => 25001,    'max' => 50000,   'gain' => 400],
            ['min' => 50001,    'max' => 100000,  'gain' => 800],
            ['min' => 100001,   'max' => 250000,  'gain' => 1500],
            ['min' => 250001,   'max' => 500000,  'gain' => 1500],
            ['min' => 500001,   'max' => 1000000, 'gain' => 2500],
            ['min' => 1000001,  'max' => 2000000, 'gain' => 3000],
        ];

        foreach ($tranches as &$tranche) {
            $tranche['created_at'] = $maintenant;
            $tranche['updated_at'] = $maintenant;
        }
        unset($tranche);

        $this->db->table('config')->insertBatch($tranches);
    }
}
