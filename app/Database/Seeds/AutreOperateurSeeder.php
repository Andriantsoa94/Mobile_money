<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AutreOperateurSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Un autre opérateur (ne nous appartient pas)
        $this->db->table('operateur')->insert([
            'nom'        => 'Orange',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $idAutre = $this->db->table('operateur')->where('nom', 'Orange')->get()->getRowArray()['id'];

        // Préfixe 032 rattaché à cet opérateur, appartenance = 0 (externe)
        $this->db->table('prefixe')->insert([
            'numero'       => '032',
            'idoperateur'  => $idAutre,
            'appartenance' => 0,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        // Commission appliquée pour tout transfert vers cet opérateur
        $this->db->table('comission')->insert([
            'idOperateur' => $idAutre,
            'commission'  => 100,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
    }
}
