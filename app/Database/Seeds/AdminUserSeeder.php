<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $this->db->table('role')->insertBatch([
            ['type' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['type' => 'client', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $idRoleAdmin  = $this->db->table('role')->where('type', 'admin')->get()->getRowArray()['id'];
        $idRoleClient = $this->db->table('role')->where('type', 'client')->get()->getRowArray()['id'];

        $this->db->table('operateur')->insertBatch([
            ['nom' => 'Telma','appartenance' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
        $idOperateur = $this->db->table('operateur')->where('nom', 'Telma')->get()->getRowArray()['id'];

        $this->db->table('prefixe')->insertBatch([
            ['numero' => '033', 'idoperateur' => $idOperateur, 'created_at' => $now, 'updated_at' => $now , 'appartenance' => 1],
            ['numero' => '037', 'idoperateur' => $idOperateur, 'created_at' => $now, 'updated_at' => $now , 'appartenance' => 1],
        ]);

        $this->db->table('user')->insertBatch([
            ['nom' => 'Jean',   'CIN' => '1234567890', 'idrole' => $idRoleAdmin,  'created_at' => $now, 'updated_at' => $now],
            ['nom' => 'Marie',  'CIN' => '1111111111', 'idrole' => $idRoleClient, 'created_at' => $now, 'updated_at' => $now],
            ['nom' => 'Paul',   'CIN' => '2222222222', 'idrole' => $idRoleClient, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $idAdmin   = $this->db->table('user')->where('nom', 'Jean')->get()->getRowArray()['id'];
        $idClient1 = $this->db->table('user')->where('nom', 'Marie')->get()->getRowArray()['id'];
        $idClient2 = $this->db->table('user')->where('nom', 'Paul')->get()->getRowArray()['id'];

        $this->db->table('numero')->insertBatch([
            ['numero' => '0330000000', 'iduser' => $idAdmin,   'created_at' => $now, 'updated_at' => $now], // admin
            ['numero' => '0331111111', 'iduser' => $idClient1, 'created_at' => $now, 'updated_at' => $now], // client Marie
            ['numero' => '0372222222', 'iduser' => $idClient2, 'created_at' => $now, 'updated_at' => $now], // client Paul
        ]);

        $this->db->table('solde')->insertBatch([
            ['idUser' => $idClient1, 'value' => 50000, 'created_at' => $now, 'updated_at' => $now],
            ['idUser' => $idClient2, 'value' => 15000, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}