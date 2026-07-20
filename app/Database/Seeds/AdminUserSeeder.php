<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Rôles
        $this->db->table('role')->insertBatch([
            ['type' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'client', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $roleAdmin  = $this->db->table('role')->where('type', 'admin')->get()->getRowArray();
        $idRoleAdmin = $roleAdmin['id'];

        // User admin (numéro whitelisté 0330000000)
        $this->db->table('user')->insertBatch([
            ['nom' => 'Jean', 'CIN' => '1234567890', 'idrole' => $idRoleAdmin, 'created_at' => $now, 'updated_at' => $now],
        ]);
        $idUserAdmin = $this->db->table('user')->where('nom', 'Jean')->get()->getRowArray()['id'];

        $this->db->table('numero')->insertBatch([
            ['numero' => '0330000000', 'iduser' => $idUserAdmin, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Opérateur + préfixes autorisés (033, 037)
        $this->db->table('operateur')->insertBatch([
            ['nom' => 'Telma', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $idOperateur = $this->db->table('operateur')->where('nom', 'Telma')->get()->getRowArray()['id'];

        $this->db->table('prefixe')->insertBatch([
            ['numero' => '033', 'idoperateur' => $idOperateur, 'created_at' => $now, 'updated_at' => $now],
            ['numero' => '037', 'idoperateur' => $idOperateur, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
