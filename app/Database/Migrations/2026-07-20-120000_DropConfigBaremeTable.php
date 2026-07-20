<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropConfigBaremeTable extends Migration
{
    /**
     * La table "config_bareme" (par operateur/type, avec pourcentage) faisait
     * doublon avec la table "config" (barème global par tranche de montant,
     * seul système utilisé et voulu). Elle causait un calcul de frais
     * incorrect côté transfert (200 Ar fixe au lieu du barème réel).
     */
    public function up()
    {
        $this->forge->dropTable('config_bareme', true);
    }

    public function down()
    {
        // Recréation volontairement omise : cette table ne doit plus être utilisée.
    }
}
