<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TypeOperationSeeder extends Seeder
{
    public function run()
    {
        $maintenant = date('Y-m-d H:i:s');

        $this->db->table('typeOperation')->insertBatch([
            ['nom' => 'Dépôt',     'isGain' => 0, 'isActif' => 1, 'created_at' => $maintenant, 'updated_at' => $maintenant],
            ['nom' => 'Retrait',   'isGain' => 1, 'isActif' => 1, 'created_at' => $maintenant, 'updated_at' => $maintenant],
            ['nom' => 'Transfert', 'isGain' => 1, 'isActif' => 1, 'created_at' => $maintenant, 'updated_at' => $maintenant],
        ]);
    }
}
