<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConfigFrais extends Seeder
{
    public function run()
    {
        $data = [
            [
                'idOperateur'     => 1,
                'idTypeOperation' => 1,
                'montant_min'     => 1.00,
                'montant_max'     => 10000.00,
                'valeur_frais'    => 200.00,
                'pourcentage'     => 0.00,
                'created_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'idOperateur'     => 1,
                'idTypeOperation' => 1,
                'montant_min'     => 10001.00,
                'montant_max'     => 50000.00,
                'valeur_frais'    => 500.00,
                'pourcentage'     => 0.00,
                'created_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'idOperateur'     => 1,
                'idTypeOperation' => 1,
                'montant_min'     => 50001.00,
                'montant_max'     => 99999999.00,
                'valeur_frais'    => 0.00,
                'pourcentage'     => 1.00,
                'created_at'      => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('config_bareme')->insertBatch($data);
    }
}