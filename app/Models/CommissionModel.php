<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    // La table est nommée "comission" en base (orthographe de la migration).
    protected $table         = 'comission';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['idOperateur', 'commission'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Montant de commission défini pour un opérateur donné.
     * Retourne 0 si aucune commission n'est configurée pour cet opérateur.
     */
    public function pourOperateur(?int $idOperateur): float
    {
        if ($idOperateur === null) {
            return 0.0;
        }

        $ligne = $this->where('idOperateur', $idOperateur)->first();

        return (float) ($ligne['commission'] ?? 0);
    }
}
